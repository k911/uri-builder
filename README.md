# UriBuilder library
Simplify manipulation Uri value objects implementing PSR-7 `Psr\Http\Message\UriInterface`. Build on top of powerful [League\Uri library](http://uri.thephpleague.com/).

- [Installation](#installation)
- [Usage](#Usage)

## Installation

## Usage

**Full public interface is available [here](src/UriBuilderInterface.php).**

Usage example:
```php
// Simple URI string
$uri = 'wss://multi.scheme.domain:27051';

// Intialize UriBuilder
$builder = new K911\UriBuilder\UriBuilder($uri);
// or
$builder = (new K911\UriBuilder\UriBuilder())->fromString($uri);

// Customize URI a little
$builder->setScheme('https')
    ->setHost('api.scheme.domain')
    // setting default port for https scheme
    ->setPort(443)
    // domain-related paths must always start with forward slash '/'
    ->setPath('/v1')
    // query string is generated safetly from pairs according to RFC3986
    ->setQuery([
        'api_token' => 'generated-token-xxx'
    ]);


// Print results
echo (string) $builder->getUri();
// https://api.scheme.domain/v1?api_token=generated-token-xxx
```