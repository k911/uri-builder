<?php
declare(strict_types=1);

namespace K911\UriBuilder;

use K911\UriBuilder\Exception\UriBuilderException;
use Psr\Http\Message\UriInterface;

class UriBuilder implements UriBuilderInterface
{

    /**
     * Message of UriBuilderException thrown when trying to use UriBuilder without prior initialization.
     */
    protected const MESSAGE_NOT_INITIALIZED = 'UriBuilder is not initialized with any Uri instance. Please initialize it by using `from` methods.';

    /**
     * Separator character of query pairs in URI string
     */
    protected const URI_QUERY_SEPARATOR = '&';

    /**
     * RFC used when building query string
     * @link http://php.net/manual/pl/function.http-build-query.php#refsect1-function.http-build-query-parameters
     */
    protected const PHP_QUERY_RFC = PHP_QUERY_RFC3986;

    /**
     * @var UriInterface The internal value object representing an URI
     */
    protected $uri;

    /**
     * @var UriFactoryInterface
     */
    protected $factory;

    /**
     * UriBuilder constructor.
     *
     * @param UriFactoryInterface $factory
     */
    public function __construct(UriFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Create a new Uri instance from an URI string in UriBuilderInterface
     *
     * @param string $uri URI string
     *
     * @return UriBuilderInterface
     */
    public function from(string $uri): UriBuilderInterface
    {
        $this->uri = $this->factory->create($uri);

        return $this;
    }

    /**
     * Clones an Uri instance and assigns to an UriBuilderInterface
     *
     * @param UriInterface $uri
     *
     * @return UriBuilderInterface
     */
    public function fromUri(UriInterface $uri): UriBuilderInterface
    {
        $this->uri = clone $uri;

        return $this;
    }

    /**
     * Create a new Uri instance from a hash of parse_url parts in UriBuilderInterface
     *
     * @param array $components a hash representation of the URI similar
     *                          to PHP parse_url function result
     *
     * @return UriBuilderInterface
     */
    public function fromComponents(array $components): UriBuilderInterface
    {
        $this->uri = $this->factory->createFromComponents($components);

        return $this;
    }

    /**
     * Set the scheme component of the URI.
     *
     * @param string $scheme The URI scheme
     *
     * @return UriBuilderInterface
     *
     * @throws UriBuilderException
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     */
    public function setScheme(string $scheme): UriBuilderInterface
    {
        $this->validateState();

        $this->uri = $this->factory->transform($this->uri, $scheme);

        return $this;
    }

    /**
     * Sets the specified user information for Uri instance.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string      $user     The user name to use for authority.
     * @param null|string $password The password associated with $user.
     *
     * @return UriBuilderInterface
     *
     * @throws UriBuilderException
     */
    public function setUserInfo(string $user, string $password = null): UriBuilderInterface
    {
        $this->validateState();

        $this->uri = $this->uri->withUserInfo($user, $password);

        return $this;
    }

    /**
     * Sets the specified host to an Uri instance.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host The hostname to use with the new instance.
     *
     * @return UriBuilderInterface
     *
     * @throws UriBuilderException
     * @throws \InvalidArgumentException
     */
    public function setHost(string $host): UriBuilderInterface
    {
        $this->validateState();

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
     *                       removes the port information.
     *
     * @return UriBuilderInterface
     *
     * @throws UriBuilderException
     * @throws \InvalidArgumentException
     */
    public function setPort(int $port = null): UriBuilderInterface
    {
        $this->validateState();

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
     *
     * @return UriBuilderInterface
     *
     * @throws UriBuilderException
     * @throws \InvalidArgumentException
     */
    public function setPath(string $path): UriBuilderInterface
    {
        $this->validateState();

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
     *
     * @return UriBuilderInterface
     *
     * @throws UriBuilderException
     * @throws \InvalidArgumentException
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     */
    public function setQuery(array $pairs): UriBuilderInterface
    {
        $this->validateState();

        $query = http_build_query($pairs, '', static::URI_QUERY_SEPARATOR, static::PHP_QUERY_RFC);

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
     *
     * @return UriBuilderInterface
     *
     * @throws UriBuilderException
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     */
    public function setFragment(string $fragment): UriBuilderInterface
    {
        $this->validateState();

        $this->uri = $this->uri->withFragment($fragment);

        return $this;
    }


    public function getUri(): UriInterface
    {
        $this->validateState();

        return clone $this->uri;
    }

    /**
     * Validates state of Uri Builder. Throws exception if builder is not ready to use.
     *
     * @throws UriBuilderException
     */
    protected function validateState(): void
    {
        if (null === $this->uri) {
            throw new UriBuilderException(static::MESSAGE_NOT_INITIALIZED, 500);
        }
    }
}
