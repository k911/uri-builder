<?php
declare(strict_types=1);

namespace K911\UriBuilder\Adapter;

use League\Uri\Schemes\Data;
use Psr\Http\Message\UriInterface;

class DataUriAdapter extends Data implements UriInterface
{
}
