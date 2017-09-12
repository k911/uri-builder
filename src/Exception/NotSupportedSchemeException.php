<?php
declare(strict_types=1);

namespace K911\UriBuilder\Exception;

use Throwable;

/**
 * Exception thrown if user provides not supported Uri scheme anywhere in library
 */
class NotSupportedSchemeException extends InvalidArgumentException
{
    /**
     * @param string    $scheme
     * @param Throwable $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __construct(string $scheme, Throwable $previous = null)
    {
        parent::__construct(sprintf('Scheme `%s` has not yet been supported by the library.', $scheme), 501, $previous);
    }
}
