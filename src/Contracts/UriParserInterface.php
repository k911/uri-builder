<?php
declare(strict_types=1);

namespace K911\UriBuilder\Contracts;

/**
 * Interface UriParser describes behaviour of parsing and validating an URI string
 */
interface UriParserInterface
{

    /**
     * Parses an URI string and creates array of URI components.
     *
     * @param string $uri An URI string to be parsed
     *
     * @return string[] Array of URI components which MUST contain all possible components:
     *                  - `scheme` => string|null URI scheme (e.g: http)
     *                  - `host` => string|null Full URI host (e.g: www.foo.bar)
     *                  - `user` => string|null URI UserInfo: Username (before ':' after scheme)
     *                  - `pass` => string|null URI UserInfo: Password (after ':' before '@' and host)
     *                  - `path` => string URI path (after host and '/' or directly after scheme)
     *                  - `port` => int|null URI port (after ':' after host)
     *                  - `query` => string|null URI query (after '?' after path)
     *                  - `fragment` => string|null URI fragment (after '#')
     *
     */
    public function parse(string $uri): array;
}
