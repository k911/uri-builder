<?php

use K911\UriBuilder\Adapter\DataUriAdapter;
use K911\UriBuilder\Adapter\FileUriAdapter;
use K911\UriBuilder\Adapter\FtpUriAdapter;
use K911\UriBuilder\Adapter\WsUriAdapter;
use K911\UriBuilder\UriFactory;
use K911\UriBuilder\UriFactoryInterface;
use K911\UriBuilder\UriParserInterface;
use League\Uri\Schemes\Http;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class UriFactoryTest extends TestCase
{
    private const EMPTY_COMPONENTS = [
        'scheme' => null,
        'user' => null,
        'pass' => null,
        'host' => null,
        'port' => null,
        'path' => '',
        'query' => null,
        'fragment' => null,
    ];

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
        $this->parser = $this->getMockBuilder(UriParserInterface::class)
            ->setMethods(['parse'])
            ->getMock();

        $this->factory = new UriFactory($this->parser);
    }

    public function validUriProvider(): array
    {
        return [
            'data scheme adapter' => [
                DataUriAdapter::class,
                [
                    'scheme' => 'data',
                    'path' => 'text/plain;charset=utf-8,FooBarWithSpecialChars%20%20%C4%85%C4%87%C4%99%C5%82',
                ],
                'data:text/plain;charset=utf-8,FooBarWithSpecialChars%20%20%C4%85%C4%87%C4%99%C5%82',
            ],
            'file scheme adapter' => [
                FileUriAdapter::class,
                [
                    'scheme' => 'file',
                    'host' => 'example.com',
                    'path' => '/../foo/bar',
                ],
                'file://example.com/../foo/bar',
            ],
            'ftp schemes adapter' => [
                FtpUriAdapter::class,
                [
                    'scheme' => 'sftp',
                    'host' => 'example.com',
                    'user' => 'user',
                    'port' => 27015,
                    'path' => '/foo/bar',
                ],
                'sftp://user@example.com:27015/foo/bar',
            ],
            'ws schemes adapter' => [
                WsUriAdapter::class,
                [
                    'scheme' => 'wss',
                    'host' => 'example.com',
                    'path' => '/foo/bar',
                    'query' => 'foo=bar',
                ],
                'wss://example.com/foo/bar?foo=bar',
            ],
            'http schemes' => [
                Http::class,
                [
                    'scheme' => 'http',
                    'host' => 'example.com',
                    'path' => '/foo/bar',
                    'query' => 'foo=bar',
                    'fragment' => 'content',
                ],
                'http://example.com/foo/bar?foo=bar#content',
            ],
        ];
    }


    /**
     * @dataProvider validUriProvider
     *
     * @param string $class
     * @param array $components
     * @param string $uri
     */
    public function testCreateUriFromString(string $class, array $components, string $uri)
    {
        $this->parser->expects($this->atLeast(2))
            ->method('parse')
            ->with($this->equalTo($uri))
            ->will($this->returnValue(array_merge(self::EMPTY_COMPONENTS, $components)));

        $uriObject = $this->factory->create($uri);
        $this->assertEquals($uri, (string)$uriObject);
        $this->assertInstanceOf(UriInterface::class, $uriObject);
        $this->assertInstanceOf($class, $uriObject);

        $this->assertNotSame($uriObject, $this->factory->create($uri));
    }

    /**
     * @dataProvider validUriProvider
     *
     * @param string $class
     * @param array $components
     * @param string $uri
     */
    public function testCreateUriFromComponents(string $class, array $components, string $uri)
    {
        $this->parser->expects($this->never())
            ->method('parse');

        $uriObject = $this->factory->createFromComponents($components);
        $this->assertEquals($uri, (string)$uriObject);
        $this->assertInstanceOf(UriInterface::class, $uriObject);
        $this->assertInstanceOf($class, $uriObject);

        $this->assertNotSame($uriObject, $this->factory->createFromComponents($components));
    }
}
