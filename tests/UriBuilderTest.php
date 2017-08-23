<?php
declare(strict_types=1);

use K911\UriBuilder\UriBuilder;
use K911\UriBuilder\UriFactory;
use PHPUnit\Framework\TestCase;

class UriBuilderTest extends TestCase
{
    /**
     * Not used host in valid uri providers
     */
    const UNUSED_HOST = 'unused.host.com';

    /**
     * @var UriBuilder
     */
    private $builder;

    public function setUp()
    {
        $this->builder = new UriBuilder(new UriFactory());
    }

    public function validUriProvider(): array
    {
        return array_merge([
            'file' => [
                'file://localhost/../foo/bar',
                'file:///../foo/bar',
            ],
            'file' => [
                'file://localhost/../foo/bar',
                'file://./../foo/bar',
            ],
            'data' => [
                'data:text/plain;charset=utf-8,FooBarWithSpecialChars%20%20%C4%85%C4%87%C4%99%C5%82',
                'data:text/plain;charset=utf-8,FooBarWithSpecialChars%20%20%C4%85%C4%87%C4%99%C5%82',
            ]
        ], $this->validUriWithHostProvider());
    }

    public function validUriWithHostProvider(): array
    {
        return [
            'http' => [
                'http://example.com/foo/bar?foo=bar#content',
                'http://example.com:80/foo/bar?foo=bar#content',
            ],
            'https' => [
                'https://example.com/foo/bar?foo=bar',
                'https://example.com:443/foo/bar?foo=bar',
            ],
            'ftp' => [
                'ftp://user:password@example.com',
                'ftp://user:password@example.com:21',
            ],
            'ftps' => [
                'sftp://user@example.com/foo/bar',
                'sftp://user@example.com:22/foo/bar',
            ],
            'ws' => [
                'ws://example.com/foo/bar?foo=bar',
                'Ws://eXamPLE.cOm:80/foo/bar?foo=bar',
            ],
            'wss' => [
                'wss://example.com/foo/bar?foo=bar',
                'WsS://eXamPLE.cOm:443/foo/bar?foo=bar',
            ],
            'file' => [
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
        $uri = $this->builder->from($uri)->getUri();
        $this->assertSame($expected, (string) $uri);
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
        
        $uri = $this->builder->fromComponents($components)->getUri();
        $this->assertSame($expected, (string) $uri);
    }

    /**
     * @dataProvider validUriWithHostProvider
     *
     * @param string $expected
     * @param string $uri
     */
    public function testImmutability(string $expected, string $uri)
    {
        $uri = $this->builder->from($uri)
            ->getUri();

        $new_uri = $this->builder->fromUri($uri)
            ->setHost(self::UNUSED_HOST)
            ->getUri();

        $this->assertNotEquals((string) $uri, (string) $new_uri);
        $this->assertNotEquals($expected, (string) $new_uri);

        $expected = $this->builder->from($expected)
            ->setHost(self::UNUSED_HOST)
            ->getUri();

        $this->assertSame((string) $expected, (string) $new_uri);
    }
}
