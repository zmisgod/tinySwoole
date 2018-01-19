<?php
namespace Core\Framework\Request;

class RequestUpload
{
    private $uploadFile;

    public function __construct($uploadFile)
    {
        $this->uploadFile = $uploadFile;
    }
}