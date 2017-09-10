<?php
declare(strict_types=1);

namespace K911\UriBuilder;

use K911\UriBuilder\Adapter\DataUriAdapter;
use K911\UriBuilder\Adapter\FileUriAdapter;
use K911\UriBuilder\Adapter\FtpUriAdapter;
use K911\UriBuilder\Adapter\WsUriAdapter;
use K911\UriBuilder\Exception\InvalidArgumentException;
use K911\UriBuilder\Exception\NotSupportedSchemeException;
use League\Uri\Schemes\AbstractUri;
use League\Uri\Schemes\Http;
use Psr\Http\Message\UriInterface;

/**
 * The UriFactory utilizes powerful library League\Uri to create new Uri instances
 */
class UriFactory extends AbstractUriFactory
{
    /**
     * Supported schemes and corresponding Uri instance classes
     * Array<string, string UriInterface::class>
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
     * Create a new Uri instance from a hash of parse_url parts
     * Remarks: Scheme part is required.
     *
     * @param array $components a hash representation of the URI similar
     *                          to PHP parse_url function result
     * @return UriInterface|AbstractUri Newly created URI value object
     *
     * @throws InvalidArgumentException
     * @throws NotSupportedSchemeException
     *
     * @see http://php.net/manual/en/function.parse-url.php
     */
    public function createFromComponents(array $components): UriInterface
    {
        if (empty($components['scheme'])) {
            throw new InvalidArgumentException('Part URI scheme from components array cannot be empty.');
        }

        /**
         * @var static|AbstractUri Class name of instance of AbstractUri;
         */
        $abstractUri = $this->getSchemeClass($components['scheme']);

        return $abstractUri::createFromComponents($components);
    }
}
