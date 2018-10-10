<?php
namespace Karamel\Router\Interfaces;
interface IRouter{
    public function get($path, $action);
    public function post($path, $action);
    public function patch($path, $action);
    public function put($path, $action);
    public function delete($path, $action);
    public function group($options,$callback);
    public function name($name);
}