<?php
declare(strict_types=1);

namespace K911\UriBuilder;

use K911\UriBuilder\Exception\InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class UriBuilder implements UriBuilderInterface
{
    /**
     * Separator character of query pairs in URI string
     */
    protected const URI_QUERY_SEPARATOR = '&';
    
    /**
     * RFC used when building query string
     * @link http://php.net/manual/pl/function.http-build-query.php#refsect1-function.http-build-query-parameters
     */
    protected const PHP_QUERY_RFC = PHP_QUERY_RFC1738;

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
     * @param UriFactoryInterface $factory
     */
    public function __construct(UriFactoryInterface $factory)
    {
        $this->factory = $factory;
    }
    
    public function from(string $uri): UriBuilderInterface
    {
        $this->uri = $this->factory->create($uri);
        return $this;
    }

    public function fromUri(UriInterface $uri): UriBuilderInterface
    {
        $this->uri = clone $uri;
        return $this;
    }

    public function fromComponents(array $components): UriBuilderInterface
    {
        $this->uri = $this->factory->createFromComponents($components);
        return $this;
    }


    public function setScheme(string $scheme): UriBuilderInterface
    {
        if (!isset($this->uri)) {
            throw new InvalidArgumentException("UriBuilder is not initialized with any Uri instance. Please initialize it using either `from` methods or constructor.", 404);
        }

        $this->uri = $this->factory->isSchemeCompatible($scheme, $this->uri) ?
            $this->uri->withScheme($scheme) :
            $this->factory->transform($this->uri, $scheme);

        return $this;
    }

    public function setUserInfo(string $user, string $password = null): UriBuilderInterface
    {
        if (!isset($this->uri)) {
            throw new InvalidArgumentException("UriBuilder is not initialized with any Uri instance. Please initialize it using either `from` methods or constructor.", 404);
        }

        $this->uri = $this->uri->withUserInfo($user, $password);
        return $this;
    }


    public function setHost(string $host): UriBuilderInterface
    {
        if (!isset($this->uri)) {
            throw new InvalidArgumentException("UriBuilder is not initialized with any Uri instance. Please initialize it using either `from` methods or constructor.", 404);
        }

        $this->uri = $this->uri->withHost($host);
        return $this;
    }


    public function setPort(int $port = null): UriBuilderInterface
    {
        if (!isset($this->uri)) {
            throw new InvalidArgumentException("UriBuilder is not initialized with any Uri instance. Please initialize it using either `from` methods or constructor.", 404);
        }

        $this->uri = $this->uri->withPort($port);
        return $this;
    }


    public function setPath(string $path): UriBuilderInterface
    {
        if (!isset($this->uri)) {
            throw new InvalidArgumentException("UriBuilder is not initialized with any Uri instance. Please initialize it using either `from` methods or constructor.", 404);
        }

        $this->uri = $this->uri->withPath($path);
        return $this;
    }


    public function setQuery(array $pairs): UriBuilderInterface
    {
        if (!isset($this->uri)) {
            throw new InvalidArgumentException("UriBuilder is not initialized with any Uri instance. Please initialize it using either `from` methods or constructor.", 404);
        }

        $query = http_build_query($pairs, '', static::URI_QUERY_SEPARATOR, static::PHP_QUERY_RFC);

        $this->uri = $this->uri->withQuery($query);
        return $this;
    }

    public function setFragment(string $fragment): UriBuilderInterface
    {
        if (!isset($this->uri)) {
            throw new InvalidArgumentException("UriBuilder is not initialized with any Uri instance. Please initialize it using either `from` methods or constructor.", 404);
        }

        $this->uri = $this->uri->withFragment($fragment);
        return $this;
    }


    public function getUri(): UriInterface
    {
        if (!isset($this->uri)) {
            throw new InvalidArgumentException("UriBuilder is not initialized with any Uri instance. Please initialize it using either `from` methods or constructor.", 404);
        }

        return clone $this->uri;
    }
}
