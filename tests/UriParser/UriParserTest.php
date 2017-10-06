<?php
declare(strict_types=1);

namespace Tests\UriParser;

use K911\UriBuilder\Adapter\UriParserAdapter;
use K911\UriBuilder\Contracts\UriParserInterface;
use PHPUnit\Framework\TestCase;

class UriParserTest extends TestCase
{
    private const EMPTY_COMPONENTS = [
        'scheme'   => null,
        'user'     => null,
        'pass'     => null,
        'host'     => null,
        'port'     => null,
        'path'     => '',
        'query'    => null,
        'fragment' => null,
    ];

    /**
     * @var UriParserInterface
     */
    private $parser;


    protected function setUp()
    {
        $this->parser = new UriParserAdapter();
    }


    public function validUriProvider(): array
    {
        return [
            'http'                   => [
                [
                    'scheme'   => 'http',
                    'host'     => 'example.com',
                    'port'     => 80,
                    'path'     => '/foo/bar',
                    'query'    => 'foo=bar',
                    'fragment' => 'content',
                ],
                'http://example.com:80/foo/bar?foo=bar#content',
            ],
            'https'                  => [
                [
                    'scheme' => 'https',
                    'host'   => 'example.com',
                    'path'   => '/foo/bar',
                    'query'  => 'foo=bar',
                ],
                'https://example.com/foo/bar?foo=bar',
            ],
            'ftp'                    => [
                [
                    'scheme' => 'ftp',
                    'host'   => 'example.com',
                    'port'   => 21,
                    'user'   => 'user',
                    'pass'   => 'password',
                ],
                'ftp://user:password@example.com:21',
            ],
            'ftps'                   => [
                [
                    'scheme' => 'sftp',
                    'host'   => 'example.com',
                    'user'   => 'user',
                    'port'   => 27015,
                    'path'   => '/foo/bar',
                ],
                'sftp://user@example.com:27015/foo/bar',
            ],
            'ws'                     => [
                [
                    'scheme' => 'ws',
                    'host'   => 'example.com',
                    'port'   => 80,
                    'path'   => '/foo/bar',
                    'query'  => 'foo=bar',
                ],
                'ws://example.com:80/foo/bar?foo=bar',
            ],
            'wss'                    => [
                [
                    'scheme' => 'wss',
                    'host'   => 'example.com',
                    'port'   => 443,
                    'path'   => '/foo/bar',
                    'query'  => 'foo=bar',
                ],
                'wss://example.com:443/foo/bar?foo=bar',
            ],
            'file'                   => [
                [
                    'scheme' => 'file',
                    'host'   => 'cdn.example.com',
                    'path'   => '/foo/bar',
                ],
                'file://cdn.example.com/foo/bar',
            ],
            'file relative w/o host' => [
                [
                    'scheme' => 'file',
                    'host'   => '',
                    'path'   => '/../foo/bar',
                ],
                'file:///../foo/bar',
            ],
            'file relative w/ host'  => [
                [
                    'scheme' => 'file',
                    'host'   => 'example.com',
                    'path'   => '/../foo/bar',
                ],
                'file://example.com/../foo/bar',
            ],
            'data'                   => [
                [
                    'scheme' => 'data',
                    'path'   => 'text/plain;charset=utf-8,FooBarWithSpecialChars%20%20%C4%85%C4%87%C4%99%C5%82',
                ],
                'data:text/plain;charset=utf-8,FooBarWithSpecialChars%20%20%C4%85%C4%87%C4%99%C5%82',
            ],
        ];
    }


    /**
     * @dataProvider validUriProvider
     *
     * @param array  $expected
     * @param string $uri
     */
    public function testUriToComponents(array $expected, string $uri)
    {
        $expectedComponents = array_merge(self::EMPTY_COMPONENTS, $expected);
        $parsedComponents = $this->parser->parse($uri);

        $this->assertEquals(true, ksort($expectedComponents));
        $this->assertEquals(true, ksort($parsedComponents));

        $this->assertSame($expectedComponents, $parsedComponents);
    }
}
