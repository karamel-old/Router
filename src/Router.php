<?php

namespace Karamel\Router;
class Router
{

    private static $instance;
    private $builder;

    public static function getInstance()
    {
        if (self::$instance == null)
            self::$instance = new static;
        return self::$instance;
    }

    public function newBuilder()
    {
        if ($this->builder == null)
            $this->builder = new Builder();
        return $this->builder;
    }

    public function __call($name, $arguments)
    {
        return self::getInstance()->newBuilder()->$name(...$arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        return self::getInstance()->newBuilder()->$name(...$arguments);
    }

}