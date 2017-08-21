<?php
declare(strict_types=1);

namespace K911\UriBuilder\Exception;

use Exception;

/**
 * Exception thrown if a feature, that user MIGHT find useful, has not been implemented in the UriBuilder library
 * but there are logical reasons for user to think that this library MIGHT have support this feature
 */
class NotSupportedException extends Exception implements UriBuilderException
{
}
