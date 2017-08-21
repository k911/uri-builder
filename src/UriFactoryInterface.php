<?php
declare(strict_types=1);

namespace K911\UriBuilder;

use Psr\Http\Message\UriInterface;

/**
 * Interface for UriFactory that creates and manages
 * URI value objects that implements UriInterface
 *
 * @see http://www.php-fig.org/psr/psr-7/ UriInterface specification
 */
interface UriFactoryInterface
{
    /**
     * Create a new Uri instance from an URI string
     * Remarks: URI string must be valid and therefore consist of URI scheme.
     *
     * @param string $uri URI string
     * @return UriInterface Newly created URI value object
     */
    public function create(string $uri): UriInterface;

    /**
     * Create a new Uri instance from a hash of parse_url parts
     * Remarks: Scheme part is required.
     *
     * @param array $components a hash representation of the URI similar
     *                          to PHP parse_url function result
     * @return UriInterface Newly created URI value object
     *
     * @see http://php.net/manual/en/function.parse-url.php
     */
    public function createFromComponents(array $components): UriInterface;

    /**
     * Transforms an existing Uri instance into new Uri instance
     * with support for different URI scheme and optionally adjusted URI components
     *
     * @param UriInterface $uri An Uri instance to be transformed
     * @param string $scheme New URI scheme
     * @return UriInterface New, transformed Uri instance compatible with provided scheme
     */
    public function transform(UriInterface $uri, string $scheme): UriInterface;

    /**
     * Parses an URI string into array of components.
     * This method MUST return array with all parts (their keys) accessible.
     *
     * @param string $uri An URI string to be parsed
     * @return array a hash representation of the URI similar to PHP parse_url function result
     */
    public function parse(string $uri): array;

    /**
     * Determines whether provided Uri instance is compatible with provided URI scheme
     * Remarks: When URI scheme is compatible it means that an Uri instance does not need
     * to be transformed to support this scheme
     *
     * @param string $scheme An URI scheme
     * @param UriInterface $uri An Uri instance
     * @return bool
     */
    public function isSchemeCompatible(string $scheme, UriInterface $uri): bool;

    /**
     * Determines whether provided URI scheme is supported by the UriFactory
     *
     * @param string $scheme An URI scheme
     * @return bool
     */
    public function isSchemeSupported(string $scheme): bool;
}
