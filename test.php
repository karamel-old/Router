<?php

class HomeController
{
    public function index()
    {
        return "HomeController and index called\n";
    }
}

function test($request)
{

}

class AuthMiddleware
{
    public function handle($request)
    {
        if ($request->v == 2) {
            echo "Error";
            exit;
        }

    }
}


class Route
{
    public function boot()
    {
        $arr = [
            'auth' => 'AuthMiddleware'
        ];
        $data = new stdClass();
        $data->v = 3;
        (new $arr['auth'])->handle($data);
        return (new HomeController())->index();
    }
}


echo (new Route())->boot();

