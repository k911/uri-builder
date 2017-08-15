<?php
declare(strict_types=1);

namespace K911\UriBuilder\Adapter;

use League\Uri\Schemes\File;
use Psr\Http\Message\UriInterface;

class FileUriAdapter extends File implements UriInterface
{
}
