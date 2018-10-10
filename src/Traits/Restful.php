<?php
namespace Karamel\Router\Traits;
trait Restful{
    public function get($path, $action)
    {
        $path = $this->santeizeRoutePath($this->joinPath($this->prefix, $path));
        return $this->addRoute($path, $action,'GET');
    }

    public function post($path, $action)
    {
        $path = $this->santeizeRoutePath($this->joinPath($this->prefix, $path));
        return $this->addRoute($path, $action,'POST');
    }

    public function put($path, $action)
    {
        $path = $this->santeizeRoutePath($this->joinPath($this->prefix, $path));
        return $this->addRoute($path, $action,'PUT');
    }

    public function patch($path, $action)
    {
        $path = $this->santeizeRoutePath($this->joinPath($this->prefix, $path));
        return $this->addRoute($path, $action,'PATCH');
    }

    public function delete($path, $action)
    {
        $path = $this->santeizeRoutePath($this->joinPath($this->prefix, $path));
        return $this->addRoute($path, $action,'DELETE');
    }
}