<?php
declare(strict_types=1);

namespace K911\UriBuilder;

use K911\UriBuilder\Exception\NotSupportedException;
use Psr\Http\Message\UriInterface;

abstract class AbstractUriFactory implements UriFactoryInterface
{

    /**
     * Supported schemes and corresponding Uri instance classes
     * @var array Key => string, Value => UriInterface::class
     */
    protected const SUPPORTED_SCHEMES = [];

    /**
     * @var UriParserInterface
     */
    protected $parser;

    public function __construct(UriParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Create a new Uri instance from an URI string
     * Remarks: URI string must be valid and therefore consist of URI scheme.
     *
     * @param string $uri URI string
     * @return UriInterface Newly created URI value object
     */
    public function create(string $uri): UriInterface
    {
        return $this->createFromComponents($this->parser->parse($uri));
    }

    /**
     * Transforms an existing Uri instance into new Uri instance
     * with support for different URI scheme and optionally adjusted URI components
     *
     * @param UriInterface $uri An Uri instance to be transformed
     * @param string $scheme New URI scheme
     * @return UriInterface New, transformed Uri instance compatible with provided scheme
     *
     * @throws NotSupportedException
     */
    public function transform(UriInterface $uri, string $scheme): UriInterface
    {
        $components = $this->parser->parse((string) $uri);
        $components['scheme'] = $this->normalizeString($scheme);
        return $this->createFromComponents($components);
    }

    /**
     * Gets fully qualified class name of UriInstance that support scheme provided
     *
     * @param string $scheme An supported by UriFactory URI scheme
     * @return string UriInterface::class
     *
     * @throws NotSupportedException
     */
    public function getClass(string $scheme): string
    {
        if (!$this->isSchemeSupported($scheme)) {
            throw new NotSupportedException("Scheme `$scheme` has not yet been supported by the library.", 500);
        }

        return static::SUPPORTED_SCHEMES[$this->normalizeString($scheme)];
    }

    /**
     * Determines whether provided Uri instance is compatible with provided URI scheme
     * Remarks: When URI scheme is compatible it means that an Uri instance does not need
     * to be transformed to support this scheme
     *
     * @param string $scheme An URI scheme
     * @param UriInterface $uri An Uri instance
     * @return bool
     *
     * @throws NotSupportedException
     */
    public function isSchemeCompatible(string $scheme, UriInterface $uri): bool
    {
        $class = $this->getClass($scheme);
        return $uri instanceof $class;
    }

    /**
     * Determines whether provided URI scheme is supported by the UriFactory
     *
     * @param string $scheme An URI scheme
     * @return bool
     */
    public function isSchemeSupported(string $scheme): bool
    {
        return array_key_exists($this->normalizeString($scheme), static::SUPPORTED_SCHEMES);
    }


    /**
     * Lowercase and trim string
     *
     * @param string $input
     * @return string
     */
    protected function normalizeString(string $input): string
    {
        return mb_strtolower(trim($input));
    }
}
