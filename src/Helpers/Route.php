<?php

use Karamel\Router\Exceptions\RouteNotFoundException;

/**
 * @param $name
 * @param $arguments
 * @return null|string|string[]
 * @throws RouteNotFoundException
 */
function route($name, $arguments)
{
    $route = \Karamel\Router\Router::getInstance()->newBuilder()->findRouteByName($name);
    foreach ($arguments as $key => $value) {
        $route['path'] = preg_replace("/\{$key(\??)\}/i", $value, $route['path']);
    }
    if (!$route['path'])
        throw new RouteNotFoundException();

    return $route['path'];
}