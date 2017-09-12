<?php
declare(strict_types=1);

use K911\UriBuilder\Adapter\UriParserAdapter;
use K911\UriBuilder\Facade\UriBuilder as UriBuilderFacade;
use K911\UriBuilder\UriBuilder;
use K911\UriBuilder\UriBuilderInterface;
use K911\UriBuilder\UriFactory;
use K911\UriBuilder\UriFactoryInterface;
use K911\UriBuilder\UriParserInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class UriBuilderTest extends TestCase
{
    /**
     * Not used host in valid uri providers
     */
    private const UNUSED_HOST = 'unused.host.com';

    /**
     * @var UriBuilderInterface
     */
    private $builder;

    /**
     * @var UriFactoryInterface
     */
    private $factory;

    /**
     * @var UriParserInterface
     */
    private $parser;

    protected function setUp()
    {
        $this->parser = new UriParserAdapter();
        $this->factory = new UriFactory($this->parser);
        $this->builder = new UriBuilder($this->factory);
    }

    public function validUriProvider(): array
    {
        return array_merge([
            'file'          => [
                'file://localhost/../foo/bar',
                'file:///../foo/bar',
            ],
            'file relative' => [
                'file://localhost/./../foo/bar',
                'file:///./../foo/bar',
            ],
            'data'          => [
                'data:text/plain;charset=utf-8,FooBarWithSpecialChars%20%20%C4%85%C4%87%C4%99%C5%82',
                'data:text/plain;charset=utf-8,FooBarWithSpecialChars%20%20%C4%85%C4%87%C4%99%C5%82',
            ],
        ], $this->validUriWithHostProvider());
    }

    public function validUriWithHostProvider(): array
    {
        return [
            'http'  => [
                'http://example.com/foo/bar?foo=bar#content',
                'http://example.com:80/foo/bar?foo=bar#content',
            ],
            'https' => [
                'https://example.com/foo/bar?foo=bar',
                'https://example.com:443/foo/bar?foo=bar',
            ],
            'ftp'   => [
                'ftp://user:password@example.com',
                'ftp://user:password@example.com:21',
            ],
            'ftps'  => [
                'sftp://user@example.com/foo/bar',
                'sftp://user@example.com:22/foo/bar',
            ],
            'ws'    => [
                'ws://example.com/foo/bar?foo=bar',
                'Ws://eXamPLE.cOm:80/foo/bar?foo=bar',
            ],
            'wss'   => [
                'wss://example.com/foo/bar?foo=bar',
                'WsS://eXamPLE.cOm:443/foo/bar?foo=bar',
            ],
            'file'  => [
                'file://cdn.example.com/foo/bar',
                'file://cdn.eXamPLE.cOm/foo/bar',
            ],
        ];
    }

    /**
     * @dataProvider validUriProvider
     *
     * @param string $expected
     * @param string $uri
     */
    public function testSetUpfrom(string $expected, string $uri)
    {
        $uriObject = $this->builder->from($uri)->getUri();
        $this->assertInstanceOf(UriInterface::class, $uriObject);
        $this->assertSame($expected, (string) $uriObject);
    }

    /**
     * @dataProvider validUriWithHostProvider
     *
     * @param string $expected
     * @param string $uri
     */
    public function testSetUpfromComponents(string $expected, string $uri)
    {
        // TODO: Use Parser() instead
        $components = parse_url($uri);
        $this->assertNotEquals(false, $components);

        $uriObject = $this->builder->fromComponents($components)->getUri();
        $this->assertInstanceOf(UriInterface::class, $uriObject);
        $this->assertSame($expected, (string) $uriObject);
    }

    /**
     * @dataProvider validUriWithHostProvider
     *
     * @param string $expected
     * @param string $uri
     */
    public function testImmutability(string $expected, string $uri)
    {
        $uriObject = $this->builder->from($uri)->getUri();

        $newUri = $this->builder->fromUri($uriObject)
            ->setHost(self::UNUSED_HOST)
            ->getUri();

        $this->assertInstanceOf(UriInterface::class, $newUri);
        $this->assertNotSame($uri, $newUri);
        $this->assertNotEquals((string) $uriObject, (string) $newUri);
        $this->assertNotEquals($expected, (string) $newUri);

        $expectedUri = $this->builder->from($expected)
            ->setHost(self::UNUSED_HOST)
            ->getUri();

        $this->assertSame((string) $expectedUri, (string) $newUri);
    }

    /**
     * @dataProvider validUriWithHostProvider
     *
     * @param string $expected
     * @param string $uri
     */
    public function testFacade(string $expected, string $uri)
    {
        $newUri = UriBuilderFacade::from($uri)->getUri();
        $this->assertSame($expected, (string) $newUri);

        // TODO: Use Parser() instead
        $components = parse_url($uri);
        $newUri = UriBuilderFacade::fromComponents($components)->getUri();
        $this->assertSame($expected, (string) $newUri);

        $newUri = UriBuilderFacade::fromUri($newUri)->getUri();
        $this->assertSame($expected, (string) $newUri);
    }
}
