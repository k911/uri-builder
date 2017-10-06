<?php
declare(strict_types=1);

namespace K911\UriBuilder;

use K911\UriBuilder\Contracts\UriBuilderInterface;
use K911\UriBuilder\Contracts\UriFactoryInterface;
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
     * @var \Psr\Http\Message\UriInterface The internal value object representing an URI
     */
    protected $uri;

    /**
     * @var \K911\UriBuilder\Contracts\UriFactoryInterface
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
     * @inheritdoc
     */
    public function from(string $uri): UriBuilderInterface
    {
        $this->uri = $this->factory->create($uri);

        return $this;
    }


    /**
     * @inheritdoc
     */
    public function fromUri(UriInterface $uri): UriBuilderInterface
    {
        $this->uri = clone $uri;

        return $this;
    }


    /**
     * @inheritdoc
     */
    public function fromComponents(array $components): UriBuilderInterface
    {
        $this->uri = $this->factory->createFromComponents($components);

        return $this;
    }


    /**
     * {@inheritdoc}
     *
     * @throws \K911\UriBuilder\Exception\UriBuilderException
     */
    public function setScheme(string $scheme): UriBuilderInterface
    {
        $this->validateState();

        $this->uri = $this->factory->transform($this->uri, $scheme);

        return $this;
    }


    /**
     * {@inheritdoc}
     *
     * @throws \K911\UriBuilder\Exception\UriBuilderException
     */
    public function setUserInfo(string $user, string $password = null): UriBuilderInterface
    {
        $this->validateState();

        $this->uri = $this->uri->withUserInfo($user, $password);

        return $this;
    }


    /**
     * {@inheritdoc}
     *
     * @throws \K911\UriBuilder\Exception\UriBuilderException
     * @throws \InvalidArgumentException
     */
    public function setHost(string $host): UriBuilderInterface
    {
        $this->validateState();

        $this->uri = $this->uri->withHost($host);

        return $this;
    }


    /**
     * {@inheritdoc}
     *
     * @throws \K911\UriBuilder\Exception\UriBuilderException
     * @throws \InvalidArgumentException
     */
    public function setPort(int $port = null): UriBuilderInterface
    {
        $this->validateState();

        $this->uri = $this->uri->withPort($port);

        return $this;
    }


    /**
     * {@inheritdoc}
     *
     * @throws \K911\UriBuilder\Exception\UriBuilderException
     * @throws \InvalidArgumentException
     */
    public function setPath(string $path): UriBuilderInterface
    {
        $this->validateState();

        $this->uri = $this->uri->withPath($path);

        return $this;
    }


    /**
     * {@inheritdoc}
     *
     * @throws \K911\UriBuilder\Exception\UriBuilderException
     * @throws \InvalidArgumentException
     */
    public function setQuery(array $pairs): UriBuilderInterface
    {
        $this->validateState();

        $query = http_build_query($pairs, '', static::URI_QUERY_SEPARATOR, static::PHP_QUERY_RFC);

        $this->uri = $this->uri->withQuery($query);

        return $this;
    }


    /**
     * {@inheritdoc}
     *
     * @throws \K911\UriBuilder\Exception\UriBuilderException
     */
    public function setFragment(string $fragment): UriBuilderInterface
    {
        $this->validateState();

        $this->uri = $this->uri->withFragment($fragment);

        return $this;
    }


    /**
     * {@inheritdoc}
     *
     * @throws \K911\UriBuilder\Exception\UriBuilderException
     */
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
