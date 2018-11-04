<?php

namespace Karamel\Http;

class Request
{
    private static $instance;
    private $request;

    public static function getInstance()
    {
        if (self::$instance == null)
            self::$instance = new Request();
        return self::$instance;
    }

    public function __construct()
    {
        $this->request = $_REQUEST;
    }

    public function input($name, $default = null)
    {
        return isset($this->request[$name]) ? trim($this->request[$name]) : $default;
    }
}