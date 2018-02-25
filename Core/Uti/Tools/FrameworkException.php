<?php
namespace Core\Uti\Tools;

use Throwable;

class FrameworkException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        Log::getInstance()->log($message, 'exception');
    }
}