<?php
declare(strict_types=1);

use K911\UriBuilder\UriBuilder;
use K911\UriBuilder\UriFactory;
use K911\UriBuilder\Adapter\UriParserAdapter;

require __DIR__ . '/../vendor/autoload.php';

// Simple URI string
$uri = 'wss://foo.bar:9999';

// Create UriBuilder and its dependencies
// ..you should either use DI container to manage it
// ..or use UriBuilder facade
$parser = new UriParserAdapter();
$factory = new UriFactory($parser);
$builder = new UriBuilder($factory);

// Intiliaze UriBuilder with URI string
$builder->from($uri);
// or $builder->fromUri(UriInterface $uri);
// or $builder->fromComponents(array $components);

// UriBuilder is mutable, and allows method chaining
$builder
    // under the hood, it automatically transforms Uri object
    ->setScheme('https')
    // simple setters
    ->setHost('api.foo.bar')
    ->setFragment('foobar')
    // setting DEFAULT port for https scheme
    ->setPort(443)
    // domain-related paths must always start with forward slash '/'
    ->setPath('/v1')
    // query string is generated safely from pairs according to RFC3986
    ->setQuery([
        'api_token' => 'Qwerty! @#$TYu',
    ])
    // set user info (password can be omitted)
    ->setUserInfo('user', 'password');


// Print result
echo (string) $builder->getUri() . PHP_EOL;
// https://user:password@api.foo.bar/v1?api_token=Qwerty%21%20%40%23%24TYu#foobar
