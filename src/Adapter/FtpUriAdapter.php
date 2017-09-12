<?php
declare(strict_types=1);

namespace K911\UriBuilder\Adapter;

use League\Uri\Schemes\Ftp;
use Psr\Http\Message\UriInterface;

class FtpUriAdapter extends Ftp implements UriInterface
{
    /**
     * @inheritdoc
     */
    protected static $supported_schemes = [
        'ftp'  => 21,
        'sftp' => 22,
        'ftps' => 990,
    ];
}
