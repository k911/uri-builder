<?php
declare(strict_types=1);

namespace K911\UriBuilder\Exception;

use InvalidArgumentException as BaseException;

/**
 * Exception thrown if an argument does not match with the expected value
 * used in the UriBuilder library.
 */
class InvalidArgumentException extends BaseException implements UriBuilderExceptionInterface
{
}
