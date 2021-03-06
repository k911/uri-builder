<?php
declare(strict_types=1);

namespace K911\UriBuilder\Contracts;

use Psr\Http\Message\UriInterface;

interface UriBuilderInterface
{
    /**
     * Create a new Uri instance from an URI string in UriBuilderInterface
     *
     * @param string $uri URI string
     *
     * @return \K911\UriBuilder\Contracts\UriBuilderInterface
     */
    public function from(string $uri): self;


    /**
     * Clones an Uri instance and assigns to an UriBuilderInterface
     *
     * @param UriInterface $uri
     *
     * @return \K911\UriBuilder\Contracts\UriBuilderInterface
     */
    public function fromUri(UriInterface $uri): self;


    /**
     * Create a new Uri instance from a hash of parse_url parts in UriBuilderInterface
     *
     * @param array $components a hash representation of the URI similar
     *                          to PHP parse_url function result
     *
     * @return \K911\UriBuilder\Contracts\UriBuilderInterface
     */
    public function fromComponents(array $components): self;


    /**
     * Set the scheme component of the URI.
     *
     * @param string $scheme The URI scheme
     *
     * @return \K911\UriBuilder\Contracts\UriBuilderInterface
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     */
    public function setScheme(string $scheme): self;


    /**
     * Sets the specified user information for Uri instance.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string      $user     The user name to use for authority.
     * @param null|string $password The password associated with $user.
     *
     * @return \K911\UriBuilder\Contracts\UriBuilderInterface
     */
    public function setUserInfo(string $user, string $password = null): self;


    /**
     * Sets the specified host to an Uri instance.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host The hostname to use with the new instance.
     *
     * @return \K911\UriBuilder\Contracts\UriBuilderInterface
     */
    public function setHost(string $host): self;


    /**
     * Sets the specified port to an Uri instance.
     *
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param int|null $port The port to use with the new instance; a null value
     *                       removes the port information.
     *
     * @return \K911\UriBuilder\Contracts\UriBuilderInterface
     */
    public function setPort(int $port = null): self;


    /**
     * Sets the specified path to an Uri instance.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * If the path is intended to be domain-relative rather than path relative then
     * it must begin with a slash ("/"). Paths not starting with a slash ("/")
     * are assumed to be relative to some base path known to the application or
     * consumer.
     *
     * Users can provide both encoded and decoded path characters.
     * Implementations ensure the correct encoding as outlined in getPath().
     *
     * @param string $path The path to use with the new instance.
     *
     * @return \K911\UriBuilder\Contracts\UriBuilderInterface
     */
    public function setPath(string $path): self;


    /**
     * Sets the specified query pairs to an Uri instance.
     *
     * The query pairs are number of pairs of "key=value" represented in URI
     * as string between '?' and '#' characters, separated by '&' character
     *
     * Users can provide both encoded and decoded query pair characters.
     *
     * The value MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.4.
     *
     * An empty array is equivalent to removing the query pairs.
     *
     * @param string[] $pairs Pairs of "key=value" represented in URI as query
     *
     * @return \K911\UriBuilder\Contracts\UriBuilderInterface
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     */
    public function setQuery(array $pairs): self;


    /**
     * Set the specified URI fragment to an Uri instance.
     *
     * Users can provide both encoded and decoded fragment characters.
     * Implementations ensure the correct encoding as outlined in UriInterface::getFragment().
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     *
     * @return \K911\UriBuilder\Contracts\UriBuilderInterface
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     */
    public function setFragment(string $fragment): self;


    /**
     * Return clone of internal value object representing an URI.
     *
     * @return \Psr\Http\Message\UriInterface
     *
     * @see http://www.php-fig.org/psr/psr-7/ (UriInterface specification)
     * @see http://tools.ietf.org/html/rfc3986 (the URI specification)
     */
    public function getUri(): UriInterface;
}
