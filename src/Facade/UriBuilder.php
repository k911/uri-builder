<?php
declare(strict_types=1);

namespace K911\UriBuilder\Facade;

use K911\UriBuilder\Adapter\UriParserAdapter;
use K911\UriBuilder\Contracts\UriBuilderInterface;
use K911\UriBuilder\Contracts\UriFactoryInterface;
use K911\UriBuilder\Exception\UriBuilderException;
use K911\UriBuilder\UriBuilder as UriBuilderInstance;
use K911\UriBuilder\UriFactory;
use Psr\Http\Message\UriInterface;

final class UriBuilder
{

    /**
     * Single UriFactory instance
     *
     * @var \K911\UriBuilder\Contracts\UriFactoryInterface
     */
    private static $factory;


    /**
     * Remarks: This is static class.
     *
     * @throws \K911\UriBuilder\Exception\UriBuilderException
     */
    private function __construct()
    {
        throw new UriBuilderException('UriBuilder Facade object cannot be constructed.');
    }


    /**
     * Create a new UriBuilder instance
     * Initializing it from an URI string
     *
     * @param string $uri URI string
     *
     * @return \K911\UriBuilder\Contracts\UriBuilderInterface
     */
    public static function from(string $uri): UriBuilderInterface
    {
        $factory = self::getFactory();

        return (new UriBuilderInstance($factory))->from($uri);
    }


    /**
     * Create a new UriBuilder instance
     * Initializing it from an actual Uri instance
     *
     * @param UriInterface $uri
     *
     * @return UriBuilderInterface
     */
    public static function fromUri(UriInterface $uri): UriBuilderInterface
    {
        $factory = self::getFactory();

        return (new UriBuilderInstance($factory))->fromUri($uri);
    }


    /**
     * Create a new UriBuilder instance
     * Initializing it from a hash of parse_url parts
     *
     * @param array $components a hash representation of the URI similar
     *                          to PHP parse_url function result
     *
     * @return \K911\UriBuilder\Contracts\UriBuilderInterface
     */
    public static function fromComponents(array $components): UriBuilderInterface
    {
        $factory = self::getFactory();

        return (new UriBuilderInstance($factory))->fromComponents($components);
    }


    /**
     * Gets factory instance (always the same)
     *
     * @return \K911\UriBuilder\Contracts\UriFactoryInterface
     */
    private static function getFactory(): UriFactoryInterface
    {
        if (null === self::$factory) {
            self::$factory = new UriFactory(new UriParserAdapter());
        }

        return self::$factory;
    }
}
