<?php

namespace Karamel\Router;


use Karamel\Router\Exceptions\ActionNotFoundException;
use Karamel\Router\Exceptions\ControllerNotFpoundException;
use Karamel\Router\Exceptions\ErrorAtActionNameException;
use Karamel\Router\Exceptions\RouteNotFoundException;
use Karamel\Router\Traits\Restful;

class Builder
{
    use Restful;
    private $routes;
    private $prefix;
    private $as;
    private $namespace;

    public function __construct()
    {
        $this->defineEmptyArrays();
    }

    public function name($name)
    {
        $this->routes[count($this->routes) - 1]['name'] = $this->joinNames($this->as, $name);
    }

    public function group($options, $callback)
    {
        if (isset($options['prefix']))
            $this->prefix[] = $this->santeizeRoutePath($options['prefix']);

        if (isset($options['namespace']))
            $this->namespace[] = $options['namespace'];

        if (isset($options['as']))
            $this->as[] = $options['as'];

        $callback();

        $this->callbackReturn();
    }

    private function callbackReturn()
    {
        array_pop($this->namespace);
        array_pop($this->prefix);
        array_pop($this->as);
    }

    /**
     * @param $server
     * @throws ActionNotFoundException
     * @throws ControllerNotFpoundException
     * @throws ErrorAtActionNameException
     * @throws RouteNotFoundException
     */
    public function boot($server)
    {

        $requested_path = $this->santeizeRoutePath($server['PATH_INFO']);
        $requested_method = $server['REQUEST_METHOD'];
        $route = $this->checkPathExists($requested_path, $requested_method);


        $action = explode("@", $route['action']);
        $parameters = $this->extractPathParameters($requested_path, $route['parameters']);
        if (count($action) > 2)
            throw new ErrorAtActionNameException();

        $ctrl = $action[0];
        if (!class_exists($ctrl))
            throw new ControllerNotFpoundException();

        $controller = new $ctrl();

        if (!method_exists($controller, $action[1]))
            throw new ActionNotFoundException();

        $reflectionMethod = new \ReflectionMethod($controller, $action[1]);
        $methodParameters = $reflectionMethod->getParameters();
        if ($methodParameters[0]->getClass()->name == \Karamel\Http\Request::class)
            array_unshift($parameters, \Karamel\Http\Request::getInstance());

        $controller->{$action[1]}(...$parameters);
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function findRouteByName($name)
    {
        foreach ($this->routes as $item)
            if ($item['name'] == $name)
                return $item;

    }

    /**
     * @param $path
     * @param $method
     * @return mixed
     * @throws RouteNotFoundException
     */
    private function checkPathExists($path, $method)
    {
        $pathSection = explode("/", $path);

        foreach ($this->routes as $route) {
            if ($route['method'] != $method)
                continue;

            $routeSections = explode("/", $route['path']);
            $routeMatch = true;

            foreach ($routeSections as $index => $routeSection) {
                if (!isset($pathSection[$index]))
                    $routeMatch = false;

                if ($routeSection == $pathSection[$index])
                    continue;

                if ($this->checkSectionIsParameterOrNot($routeSection))
                    continue;

                $routeMatch = false;
            }

            if ($routeMatch)
                return $route;
        }
        throw new RouteNotFoundException();
    }

    private function santeizeRoutePath($path)
    {
        if (substr($path, 0, 1) == '/')
            $path = substr($path, 1);

        if (substr($path, strlen($path) - 1, 1) == '/')
            $path = substr($path, 0, strlen($path) - 1);

        return $path;
    }

    private function findPathParameters($path)
    {

        $parameters = [];
        $sections = explode("/", $path);
        foreach ($sections as $index => $section) {
            $matches = [];

            preg_match("/^\{([A-Za-z0-9\_]+)\}$/i", $section, $matches);
            if (count($matches) > 0) {
                $parameters[] = [
                    'index' => $index,
                    'name' => $matches[1]
                ];
            }
        }
        return $parameters;
    }

    private function checkSectionIsParameterOrNot($section)
    {
        return preg_match("/^\{([A-Za-z0-9\_]+)\}$/i", $section);
    }

    private function extractPathParameters($path, $parameters)
    {
        $pathSection = explode("/", $path);
        $variables = [];
        foreach ($parameters as $parameter) {
            $variables[] = $pathSection[$parameter['index']];
        }
        return $variables;
    }

    private function joinPath()
    {
        $arguments = func_get_args();
        $path = [];
        foreach ($arguments as $argument) {
            if (is_array($argument)) {
                foreach ($argument as $item) {
                    $path[] = $this->santeizeRoutePath($item);
                }
            } else {
                $path[] = $this->santeizeRoutePath($argument);
            }
        }

        return implode("/", $path);
    }

    private function joinNamespaces()
    {
        $arguments = func_get_args();
        $namespaces = [];
        foreach ($arguments as $argument) {
            if (is_array($argument)) {
                foreach ($argument as $item) {
                    if ($item != null && $item != "")
                        $namespaces[] = $item;
                }
            } else {
                if ($argument != null && $argument != "")
                    $namespaces[] = $argument;
            }
        }

        return implode("\\", $namespaces);
    }

    private function joinNames()
    {
        $arguments = func_get_args();
        $names = [];
        foreach ($arguments as $argument) {
            if (is_array($argument)) {
                foreach ($argument as $item) {
                    if ($item != null && $item != "")
                        $names[] = $item;
                }
            } else {
                if ($argument != null && $argument != "")
                    $names[] = $argument;
            }
        }

        return implode("", $names);
    }

    private function addRoute($path, $action, $method)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'action' => $this->joinNamespaces($this->namespace, $action),
            'parameters' => $this->findPathParameters($path)
        ];
        return $this;
    }

    private function defineEmptyArrays()
    {
        $this->prefix = [];
        $this->namespace = [];
        $this->as = [];
        $this->routes = [];
    }
}