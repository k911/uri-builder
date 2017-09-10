<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// Simple URI string
$uri = 'wss://foo.bar:9999';

// Intiliaze UriBuilder with URI string using facade
// Facade takes care of managing UriBuilder dependencies
$builder = K911\UriBuilder\Facade\UriBuilder::from($uri);
// or UriBuilder::fromUri(UriInterface $uri);
// or UriBuilder::fromComponents(array $components);

// UriBuilder is mutable, and allows method chaining
$builder
    // under the hood, it automatically transforms Uri object
    ->setScheme('https')
    // simple host setters
    ->setHost('api.foo.bar')
    ->setFragment('foobar')
    // setting DEFAULT port for https scheme
    ->setPort(443)
    // domain-related paths must always start with forward slash '/'
    ->setPath('/v1')
    // query string is generated safetly from pairs according to RFC3986
    ->setQuery([
        'api_token' => 'Qwerty! @#$TYu'
    ]);


// Print result
echo (string) $builder->getUri() . PHP_EOL;
// https://api.foo.bar/v1?api_token=Qwerty%21%20%40%23%24TYu#foobar
