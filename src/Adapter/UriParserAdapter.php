<?php
declare(strict_types=1);

namespace K911\UriBuilder\Adapter;

use League\Uri\Parser;
use K911\UriBuilder\UriParserInterface;

class UriParserAdapter extends Parser implements UriParserInterface
{
    public function parse(string $uri): array
    {
        return $this->__invoke($uri);
    }
}
