<?php
declare(strict_types=1);

namespace K911\UriBuilder\Adapter;

use K911\UriBuilder\UriParserInterface;
use League\Uri\Parser;

class UriParserAdapter extends Parser implements UriParserInterface
{
    public function parse(string $uri): array
    {
        return $this->__invoke($uri);
    }
}
