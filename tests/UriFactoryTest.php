<?php

use K911\UriBuilder\Adapter\DataUriAdapter;
use K911\UriBuilder\Adapter\FileUriAdapter;
use K911\UriBuilder\Adapter\FtpUriAdapter;
use K911\UriBuilder\Adapter\WsUriAdapter;
use K911\UriBuilder\UriFactory;
use K911\UriBuilder\UriParserInterface;
use League\Uri\Schemes\Http;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class UriFactoryTest extends TestCase
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
     * @var UriFactory
     */
    private $factory;

    /**
     * @var UriParserInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $parser;

    protected function setUp()
    {
        $this->parser = $this->getMockBuilder(UriParserInterface::class)
            ->setMethods(['parse'])
            ->getMock();

        $this->factory = new UriFactory($this->parser);
    }

    public function supportedSchemesProvider(): array
    {
        return [
            'data uris'                        => [
                'class'   => DataUriAdapter::class,
                'schemes' => ['data'],
            ],
            'file uris'                        => [
                'class'   => FileUriAdapter::class,
                'schemes' => ['file'],
            ],
            'file transfer protocol uris'      => [
                'class'   => FtpUriAdapter::class,
                'schemes' => ['ftp', 'sftp', 'ftps'],
            ],
            'hypertext transfer protocol uris' => [
                'class'   => Http::class,
                'schemes' => ['http', 'https'],
            ],
            'web sockets uris'                 => [
                'class'   => WsUriAdapter::class,
                'schemes' => ['ws', 'wss'],
            ],
        ];
    }


    public function testSupportedSchemesProviderRegression()
    {
        $providerSupportedSchemes = [];
        foreach ($this->supportedSchemesProvider() as ['schemes' => $schemes]) {
            /** @var array $schemes */
            foreach ($schemes as $scheme) {
                $supportedSchemes[] = $scheme;
            }
        }

        $supportedSchemes = $this->factory->getSupportedSchemes();

        $this->assertSame(sort($providerSupportedSchemes, SORT_STRING), sort($supportedSchemes, SORT_STRING));
    }

    public function validUriProvider(): array
    {
        return [
            'data scheme adapter' => [
                [
                    'scheme' => 'data',
                    'path'   => 'text/plain;charset=utf-8,FooBarWithSpecialChars%20%20%C4%85%C4%87%C4%99%C5%82',
                ],
                DataUriAdapter::class,
                'data:text/plain;charset=utf-8,FooBarWithSpecialChars%20%20%C4%85%C4%87%C4%99%C5%82',
            ],
            'file scheme adapter' => [
                [
                    'scheme' => 'file',
                    'host'   => 'example.com',
                    'path'   => '/../foo/bar',
                ],
                FileUriAdapter::class,
                'file://example.com/../foo/bar',
            ],
            'ftp schemes adapter' => [
                [
                    'scheme' => 'sftp',
                    'host'   => 'example.com',
                    'user'   => 'user',
                    'port'   => 27015,
                    'path'   => '/foo/bar',
                ],
                FtpUriAdapter::class,
                'sftp://user@example.com:27015/foo/bar',
            ],
            'ws schemes adapter'  => [
                [
                    'scheme' => 'wss',
                    'host'   => 'example.com',
                    'path'   => '/foo/bar',
                    'query'  => 'foo=bar',
                ],
                WsUriAdapter::class,
                'wss://example.com/foo/bar?foo=bar',
            ],
            'http schemes'        => [
                [
                    'scheme'   => 'http',
                    'host'     => 'example.com',
                    'path'     => '/foo/bar',
                    'query'    => 'foo=bar',
                    'fragment' => 'content',
                ],
                Http::class,
                'http://example.com/foo/bar?foo=bar#content',
            ],
        ];
    }


    /**
     * @dataProvider validUriProvider
     *
     * @param array  $components
     * @param string $class
     * @param string $uri
     */
    public function testCreateUriFromString(array $components, string $class, string $uri)
    {
        $this->parser->expects($this->atLeast(2))
            ->method('parse')
            ->with($this->equalTo($uri))
            ->will($this->returnValue(array_merge(self::EMPTY_COMPONENTS, $components)));

        $uriObject = $this->factory->create($uri);
        $this->assertEquals($uri, (string) $uriObject);
        $this->assertInstanceOf(UriInterface::class, $uriObject);
        $this->assertInstanceOf($class, $uriObject);

        $this->assertNotSame($uriObject, $this->factory->create($uri));
    }

    /**
     * @dataProvider validUriProvider
     *
     * @param array  $components
     * @param string $class
     * @param string $uri
     */
    public function testCreateUriFromComponents(array $components, string $class, string $uri)
    {
        $this->parser->expects($this->never())
            ->method('parse');

        $uriObject = $this->factory->createFromComponents($components);
        $this->assertEquals($uri, (string) $uriObject);
        $this->assertInstanceOf(UriInterface::class, $uriObject);
        $this->assertInstanceOf($class, $uriObject);

        $this->assertNotSame($uriObject, $this->factory->createFromComponents($components));
    }

    /**
     * @dataProvider supportedSchemesProvider
     *
     * @param string   $class
     * @param string[] $supportedSchemes
     */
    public function testSupportedSchemes(string $class, array $supportedSchemes)
    {
        $this->parser->expects($this->never())
            ->method('parse');

        foreach ($supportedSchemes as $supportedScheme) {
            $this->assertContains($supportedScheme, $this->factory->getSupportedSchemes());
            $this->assertTrue($this->factory->isSchemeSupported($supportedScheme));
            $this->assertSame($class, $this->factory->getSchemeClass($supportedScheme));
        }
    }

    /**
     * @dataProvider validUriProvider
     *
     * @param array $components
     */
    public function testCreateWithoutScheme(array $components)
    {
        $this->parser->expects($this->never())
            ->method('parse');

        $this->expectException(InvalidArgumentException::class);

        unset($components['scheme']);
        $this->factory->createFromComponents($components);
    }
}
