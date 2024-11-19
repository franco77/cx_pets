<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setAutoRoute(true);
$routes->get('/', 'Dashboard::index');

$routes->group('auth', ['namespace' => 'IonAuth\Controllers'], function ($routes) {
    $routes->add('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');
    $routes->add('forgot_password', 'Auth::forgot_password');
    // $routes->get('/', 'Auth::index');
    // $routes->add('create_user', 'Auth::create_user');
    // $routes->add('edit_user/(:num)', 'Auth::edit_user/$1');
    // $routes->add('create_group', 'Auth::create_group');
    // $routes->get('activate/(:num)', 'Auth::activate/$1');
    // $routes->get('activate/(:num)/(:hash)', 'Auth::activate/$1/$2');
    // $routes->add('deactivate/(:num)', 'Auth::deactivate/$1');
    // $routes->get('reset_password/(:hash)', 'Auth::reset_password/$1');
    // $routes->post('reset_password/(:hash)', 'Auth::reset_password/$1');
    // ...
});
