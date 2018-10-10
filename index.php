<?php

use Karamel\Router\Router;

require_once __DIR__ . '/src/Traits/Restful.php';
require_once __DIR__ . '/src/Exceptions/RouteNotFoundException.php';
require_once __DIR__ . '/src/Exceptions/ErrorAtActionNameException.php';
require_once __DIR__ . '/src/Router.php';
require_once __DIR__ . '/src/Builder.php';
require_once __DIR__ . '/src/Helpers/Route.php';

class Home
{
    public function index($id, $action)
    {
        echo $id;
        echo $action;
    }
    public function test()
    {
        echo 'dscdsc';
//        echo $id;
//        echo $action;
    }

}


/**
 * @var \Karamel\Router\Builder Router
 */

Router::get('/panel', 'Home1@test')->name('panel');
Router::get('panel/{id}/edit/{action}/', 'Home@index')->name('paneld');

Router::group(['prefix' => '/panel', 'namespace' => 'Panel', 'as' => 'panel.'], function () {

    Router::get('/index', 'Main@index')->name('index');
    Router::get('/view/{id}', 'Test\\Main@index');

    Router::group(['prefix' => '/hell/{target}', 'namespace' => 'Hell', 'as' => 'hell.'], function () {

        Router::delete('/tesssss/{testt}', 'Main@index')->name('tes');
        Router::get('/tes', 'JIIIIIR\\Main@index');

    });


    Router::group(['prefix' => '/amir/{arsalan}', 'namespace' => 'Amir', 'as' => 'amir.'], function () {

        Router::post('/kalantar/{ahmadi}', 'Main@index')->name('ahmadi');
        Router::get('/askari', 'Askari\\Main@index');

    });

    Router::get('/salaaaa', 'KALA\\Main@index');

});
Router::get('hello/', 'Home@hello');
Router::get('jira/', 'Jira\\Home@jiraa');
Router::get('jira/asdfdasf', 'Home@jira');
try {
    Router::boot($_SERVER);


} catch (Exception $e) {
    echo $e->getMessage();
}


//echo '<pre>' . print_r($_SERVER, true) . '</pre>';