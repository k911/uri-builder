<?php
declare(strict_types=1);

namespace K911\UriBuilder\Extensions;

use K911\UriBuilder\Contracts\UriFactoryInterface;
use K911\UriBuilder\Contracts\UriParserInterface;
use K911\UriBuilder\Exception\NotSupportedSchemeException;
use Psr\Http\Message\UriInterface;

abstract class AbstractUriFactory implements UriFactoryInterface
{

    /**
     * Supported schemes and corresponding Uri instance classes
     * @var array Key => string, Value => UriInterface::class
     */
    protected const SUPPORTED_SCHEMES = [];

    /**
     * @var \K911\UriBuilder\Contracts\UriParserInterface
     */
    protected $parser;


    /**
     * AbstractUriFactory constructor.
     *
     * @param \K911\UriBuilder\Contracts\UriParserInterface $parser
     */
    public function __construct(UriParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @inheritdoc
     */
    public function create(string $uri): UriInterface
    {
        return $this->createFromComponents($this->parser->parse($uri));
    }


    /**
     * {@inheritdoc}
     *
     * @throws \K911\UriBuilder\Exception\NotSupportedSchemeException
     * @throws \InvalidArgumentException
     */
    public function transform(UriInterface $uri, string $scheme): UriInterface
    {
        $schemeNormalized = $this->normalizeString($scheme);

        if ($this->isSchemeCompatible($scheme, $uri)) {
            return $uri->withScheme($schemeNormalized);
        }

        $components = $this->parser->parse((string) $uri);
        $components['scheme'] = $schemeNormalized;

        return $this->createFromComponents($components);
    }

    /**
     * Gets fully qualified class name of UriInstance that support scheme provided
     *
     * @param string $scheme An supported by UriFactory URI scheme
     *
     * @return string UriInterface::class
     *
     * @throws \K911\UriBuilder\Exception\NotSupportedSchemeException
     */
    public function getSchemeClass(string $scheme): string
    {
        if (!$this->isSchemeSupported($scheme)) {
            throw new NotSupportedSchemeException($scheme);
        }

        return static::SUPPORTED_SCHEMES[$this->normalizeString($scheme)];
    }

    /**
     * @inheritdoc
     */
    public function isSchemeSupported(string $scheme): bool
    {
        return array_key_exists($this->normalizeString($scheme), static::SUPPORTED_SCHEMES);
    }

    /**
     * @inheritdoc
     */
    public function getSupportedSchemes(): array
    {
        return array_keys(static::SUPPORTED_SCHEMES);
    }

    /**
     * Determines whether provided Uri instance is compatible with provided URI scheme
     * Remarks: When URI scheme is compatible it means that an Uri instance does not need
     * to be transformed to support this scheme
     *
     * @param string       $scheme An URI scheme
     * @param UriInterface $uri    An Uri instance
     *
     * @return bool
     *
     * @throws \K911\UriBuilder\Exception\NotSupportedSchemeException
     */
    protected function isSchemeCompatible(string $scheme, UriInterface $uri): bool
    {
        return $this->getSchemeClass($scheme) === $this->getSchemeClass($uri->getScheme());
    }

    /**
     * Helper: Lowercase and trim string
     *
     * @param string $input
     *
     * @return string
     */
    protected function normalizeString(string $input): string
    {
        return mb_strtolower(trim($input));
    }
}
