<?php
declare(strict_types=1);

namespace K911\UriBuilder;

use K911\UriBuilder\Exception\InvalidArgumentException;
use K911\UriBuilder\Exception\NotSupportedException;
use Psr\Http\Message\UriInterface;

abstract class AbstractUriBuilder implements UriBuilderInterface
{

    /**
     * @var UriInterface The internal value object representing an URI
     */
    protected $uri;

    /**
     * Separator character of query pairs in URI string
     */
    protected const URI_QUERY_SEPARATOR = '&';

    /**
     * Supported schemes and corresponding Uri instance classes
     * @example ['http' => Http::class]
     */
    protected const SUPPORTED_SCHEMES = [];

    /**
     * Transforms Uri instance into another Uri instance
     * with different scheme and adjusted components
     *
     * @param UriInterface $uri Uri instance to be transformed
     * @param string $scheme New scheme
     * @return UriInterface Transformed Uri instance compatible with new scheme
     */
    abstract protected static function transform(UriInterface $uri, string $scheme): UriInterface;

    /**
     * Lowercase and trim string
     *
     * @param string $str
     * @return string
     */
    protected static function normalizeString(string $str): string
    {
        return mb_strtolower(trim($str));
    }

    /**
     * Determines whether Uri instance is compatible with scheme provided
     *
     * @param string $scheme
     * @param UriInterface $uri
     * @return bool
     *
     * @throws NotSupportedException
     */
    public static function isSchemeCompatible(string $scheme, UriInterface $uri): bool
    {
        $scheme = self::normalizeString($scheme);

        if (!array_key_exists($scheme, static::SUPPORTED_SCHEMES)) {
            throw new NotSupportedException("Scheme `$scheme` has not been supported yet.", 501);
        }

        $schemeClass = static::SUPPORTED_SCHEMES[$scheme];
        return $uri instanceof $schemeClass;
    }

    /**
     * Clones an Uri instance to assign as internal Uri instance in UriBuilder
     *
     * @param UriInterface $uri
     * @return UriBuilderInterface
     */
    public function fromUri(UriInterface $uri): UriBuilderInterface
    {
        $this->uri = clone $uri;
        return $this;
    }

    /**
     * Base constructor.
     * @param UriInterface|array|string|null $uri Instance of URI object,
     *                                            array of URI components,
     *                                            URI string, or null.
     */
    public function __construct($uri = null)
    {
        if (isset($uri)) {
            if ($uri instanceof UriInterface) {
                $this->fromUri($uri);
            } elseif (is_array($uri)) {
                $this->fromComponents($uri);
            } elseif (is_string($uri)) {
                $this->fromString($uri);
            } else {
                $reflection = new \ReflectionClass($uri);
                throw new InvalidArgumentException("Instance of `{$reflection->getShortName()}` cannot be transformed into an URI object instance.", 501);
            }
        }
    }

    /**
     * Set the scheme component of the URI.
     *
     * @param string $scheme The URI scheme
     * @return UriBuilderInterface
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     */
    public function setScheme(string $scheme): UriBuilderInterface
    {
        if (!isset($this->uri)) {
            throw new InvalidArgumentException("UriBuilder is not initialized with any Uri instance. Please initialize it using either `from` methods or constructor.", 404);
        }

        $this->uri = self::isSchemeCompatible($scheme, $this->uri)
            ? $this->uri->withScheme($scheme)
            : static::transform($this->uri, $scheme);

        return $this;
    }

    /**
     * Sets the specified user information for Uri instance.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string $user The user name to use for authority.
     * @param null|string $password The password associated with $user.
     * @return UriBuilderInterface
     */
    public function setUserInfo(string $user, string $password = null): UriBuilderInterface
    {
        if (!isset($this->uri)) {
            throw new InvalidArgumentException("UriBuilder is not initialized with any Uri instance. Please initialize it using either `from` methods or constructor.", 404);
        }

        $this->uri = $this->uri->withUserInfo($user, $password);
        return $this;
    }

    /**
     * Sets the specified host to an Uri instance.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host The hostname to use with the new instance.
     * @return UriBuilderInterface
     */
    public function setHost(string $host): UriBuilderInterface
    {
        if (!isset($this->uri)) {
            throw new InvalidArgumentException("UriBuilder is not initialized with any Uri instance. Please initialize it using either `from` methods or constructor.", 404);
        }

        $this->uri = $this->uri->withHost($host);
        return $this;
    }

    /**
     * Sets the specified port to an Uri instance.
     *
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param int|null $port The port to use with the new instance; a null value
     *     removes the port information.
     * @return UriBuilderInterface
     */
    public function setPort(int $port = null): UriBuilderInterface
    {
        if (!isset($this->uri)) {
            throw new InvalidArgumentException("UriBuilder is not initialized with any Uri instance. Please initialize it using either `from` methods or constructor.", 404);
        }

        $this->uri = $this->uri->withPort($port);
        return $this;
    }

    /**
     * Sets the specified path to an Uri instance.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * If the path is intended to be domain-relative rather than path relative then
     * it must begin with a slash ("/"). Paths not starting with a slash ("/")
     * are assumed to be relative to some base path known to the application or
     * consumer.
     *
     * Users can provide both encoded and decoded path characters.
     * Implementations ensure the correct encoding as outlined in getPath().
     *
     * @param string $path The path to use with the new instance.
     * @return UriBuilderInterface
     */
    public function setPath(string $path): UriBuilderInterface
    {
        if (!isset($this->uri)) {
            throw new InvalidArgumentException("UriBuilder is not initialized with any Uri instance. Please initialize it using either `from` methods or constructor.", 404);
        }

        $this->uri = $this->uri->withPath($path);
        return $this;
    }

    /**
     * Sets the specified query pairs to an Uri instance.
     *
     * The query pairs are number of pairs of "key=value" represented in URI
     * as string between '?' and '#' characters, separated by '&' character
     *
     * Users can provide both encoded and decoded query pair characters.
     *
     * The value MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.4.
     *
     * An empty array is equivalent to removing the query pairs.
     *
     * @param string[] $pairs Pairs of "key=value" represented in URI as query
     * @return UriBuilderInterface
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     */
    public function setQuery(array $pairs): UriBuilderInterface
    {
        if (!isset($this->uri)) {
            throw new InvalidArgumentException("UriBuilder is not initialized with any Uri instance. Please initialize it using either `from` methods or constructor.", 404);
        }

        $query = http_build_query($pairs, '', static::URI_QUERY_SEPARATOR, PHP_QUERY_RFC3986);

        $this->uri = $this->uri->withQuery($query);
        return $this;
    }

    /**
     * Set the specified URI fragment to an Uri instance.
     *
     * Users can provide both encoded and decoded fragment characters.
     * Implementations ensure the correct encoding as outlined in UriInterface::getFragment().
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     * @return UriBuilderInterface
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     */
    public function setFragment(string $fragment): UriBuilderInterface
    {
        if (!isset($this->uri)) {
            throw new InvalidArgumentException("UriBuilder is not initialized with any Uri instance. Please initialize it using either `from` methods or constructor.", 404);
        }

        $this->uri = $this->uri->withFragment($fragment);
        return $this;
    }

    /**
     * Return clone of internal value object representing an URI.
     *
     * @return UriInterface
     *
     * @see http://www.php-fig.org/psr/psr-7/ (UriInterface specification)
     * @see http://tools.ietf.org/html/rfc3986 (the URI specification)
     */
    public function getUri(): UriInterface
    {
        return isset($this->uri) ? clone $this->uri : null;
    }
}
