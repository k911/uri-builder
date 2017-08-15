<?php
declare(strict_types=1);

namespace K911\UriBuilder;

use K911\UriBuilder\Adapter\DataUriAdapter;
use K911\UriBuilder\Adapter\FileUriAdapter;
use K911\UriBuilder\Adapter\FtpUriAdapter;
use K911\UriBuilder\Adapter\WsUriAdapter;
use K911\UriBuilder\Exception\UriBuilderException;
use League\Uri\Parser;
use League\Uri\Schemes\AbstractUri;
use League\Uri\Schemes\Http;
use Psr\Http\Message\UriInterface;

/**
 * Class UriBuilder
 *
 * League\Uri wrapper class for easy building and manipulating URIs
 *
 * @package K911\UriBuilder
 * @author k911
 */
class UriBuilder extends AbstractUriBuilder
{
    /**
     * Used when no scheme is provided to initialize Uri instance in UriBuilder
     */
    protected const DEFAULT_SCHEME = 'https';

    /**
     * Supported schemes and corresponding classes extending AbstractUri
     * @var AbstractUri[]
     */
    protected const SUPPORTED_SCHEMES = [
        'data' => DataUriAdapter::class,
        'file' => FileUriAdapter::class,
        'ftp' => FtpUriAdapter::class,
        'sftp' => FtpUriAdapter::class,
        'https' => Http::class,
        'http' => Http::class,
        'ws' => WsUriAdapter::class,
        'wss' => WsUriAdapter::class,
    ];

    /**
     * @var Parser League Parser Instance
     */
    private static $parser;

    /**
     * Transforms Uri instance into another Uri instance
     * with different scheme and adjusted components
     *
     * @param UriInterface $uri Uri instance to be transformed
     * @param string $scheme New scheme
     * @return UriInterface Transformed Uri instance compatible with new scheme
     */
    protected static function transform(UriInterface $uri, string $scheme): UriInterface
    {
        $components = self::parseUri((string)$uri);
        $components['scheme'] = $scheme;
        return self::createFromComponents($components);
    }


    /**
     * Parses URI string into components array
     * similar to returned by parse_url function
     *
     * @param string $uri
     * @return array components
     */
    protected static function parseUri(string $uri): array
    {
        return self::getParser()->__invoke($uri);
    }

    /**
     * Create a new internal Uri instance (in UriBuilder)
     * from an URI string
     *
     * @param string $uri
     * @return UriBuilderInterface
     */
    public function fromString(string $uri): UriBuilderInterface
    {
        return $this->fromComponents(self::parseUri($uri));
    }

    /**
     * Create a new internal Uri instance (in UriBuilder)
     * from a hash of parse_url parts
     *
     * @param array $components a hash representation of the URI similar
     *                          to PHP parse_url function result
     * @return UriBuilderInterface
     */
    public function fromComponents(array $components): UriBuilderInterface
    {
        $this->uri = self::createFromComponents($components);
        return $this;
    }

    /**
     * Get League Parser object
     * @return Parser League Parser object
     */
    private static function getParser(): Parser
    {
        if (!isset(self::$parser)) {
            self::$parser = new Parser();
        }

        return self::$parser;
    }

    /**
     * Create a new Uri instance from a hash of parse_url parts
     *
     * @param array $components a hash representation of the URI similar
     *                          to PHP parse_url function result
     * @throws UriBuilderException
     * @return UriInterface
     */
    private static function createFromComponents(array $components): UriInterface
    {
        if (empty($components['scheme'])) {
            throw new UriBuilderException("Defined scheme is required in components array.", 400);
        }

        $scheme = self::normalizeString($components['scheme']);

        if (!array_key_exists($scheme, static::SUPPORTED_SCHEMES)) {
            throw new UriBuilderException("Scheme `$scheme` has not been supported yet.", 501);
        }

        return call_user_func([static::SUPPORTED_SCHEMES[$scheme], 'createFromComponents'], $components);
    }
}
