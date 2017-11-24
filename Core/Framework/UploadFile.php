<?php
namespace Core\Framework;

use Core\IO\Stream;

class UploadFile
{
    private $stream;
    private $error;
    private $size;
    private $filename;
    private $fileType;

    function __construct($temFile, $size, $errorCode, $filename = null, $fileType = null)
    {
        $this->stream = new Stream(fopen($temFile, 'r+'));
        $this->error = $errorCode;
        $this->size = $size;
        $this->filename = $filename;
        $this->fileType = $fileType;
    }

    public function moveTo($target)
    {
        return file_put_contents($target, $this->stream) ? true : false;
    }

    public function getStream()
    {
        return $this->stream;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getFileName()
    {
        return $this->filename;
    }

    public function getFileType()
    {
        return $this->fileType;
    }
}