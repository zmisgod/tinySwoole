<?php
namespace Core\Framework;

use Core\IO\Stream;

class RequestHeader extends HttpBase
{
    public function __construct(array $headers, Stream $body, $protocolVersion = '1.1')
    {
        parent::__construct($headers, $body, $protocolVersion);
    }
}