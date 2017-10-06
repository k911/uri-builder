# UriBuilder
[![CircleCI](https://circleci.com/gh/k911/uri-builder.svg?style=svg)](https://circleci.com/gh/k911/uri-builder)

Simplifies manipulation of URI value objects compatible with PSR-7. Under the hood, it utilizes `League\Uri` powerful [library](http://uri.thephpleague.com/). Dependency Injection ready.

[![Code Climate](https://codeclimate.com/github/k911/uri-builder/badges/gpa.svg)](https://codeclimate.com/github/k911/uri-builder)
[![Test Coverage](https://codeclimate.com/github/k911/uri-builder/badges/coverage.svg)](https://codeclimate.com/github/k911/uri-builder/coverage)
[![Issue Count](https://codeclimate.com/github/k911/uri-builder/badges/issue_count.svg)](https://codeclimate.com/github/k911/uri-builder)

- [Installation](#installation)
- [Supported Schemes](#supported-schemes)
- [Usage](#Usage)

## Installation
```
// soon
```

## Supported Schemes
Currently supported, tested, URI schemes that UriBuilder can manage and parse from bare URI string or URI components.

- http/https
- ftp/sftp
- ws/wss
- file
- data

## Usage

**Full public interface is available [here](src/UriBuilderInterface.php).**

Usage example:
```php
// Simple URI string
$uri = 'wss://foo.bar:9999';

// Create UriBuilder and its dependencies
// ..you should either use DI container to manage it
// ..or use UriBuilder facade
$parser = new K911\UriBuilder\Adapter\UriParserAdapter();
$factory = new K911\UriBuilder\UriFactory($parser);
$builder = new K911\UriBuilder\UriBuilder($factory);

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
```
