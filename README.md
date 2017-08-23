# UriBuilder
Simplifies manipulation of URI value objects compatible with PSR-7. Under the hood, it utilizes `League\Uri` powerful [library](http://uri.thephpleague.com/). Dependency Injection ready.

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

// UriBuilder needs UriFactory to work, therefore let's create it..
$factory = new K911\UriBuilder\UriFactory();

// Create UriBuilder
$builder = new K911\UriBuilder\UriBuilder($factory);

// Intiliaze UriBuilder with URI string
$builder->from($uri);
// or $builder->fromUri(UriInterface $uri);
// or $builder->fromComponents(array $components);

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
    // query string is generated safetly from pairs according to RFC1738
    ->setQuery([
        'api_token' => 'Qwerty! @#$TYu'
    ]);


// Print result
echo (string) $builder->getUri() . PHP_EOL;
// https://api.foo.bar/v1?api_token=Qwerty%21+%40%23%24TYu#foobar
```
