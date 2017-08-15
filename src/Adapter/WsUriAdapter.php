<?php
declare(strict_types=1);

namespace K911\UriBuilder\Adapter;

use League\Uri\Schemes\Ws;
use Psr\Http\Message\UriInterface;

class WsUriAdapter extends Ws implements UriInterface
{
}
