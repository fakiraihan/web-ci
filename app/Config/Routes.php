<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Redirect root to articles
$routes->get('/', 'ArticleController::index');

// Authentication routes
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::authenticate');
$routes->get('/register', 'AuthController::register');
$routes->post('/register', 'AuthController::store');
$routes->get('/logout', 'AuthController::logout');

// Public article routes
$routes->get('/articles', 'ArticleController::index');
$routes->get('/articles/(:num)', 'ArticleController::show/$1');
$routes->get('/category/(:alpha)', 'ArticleController::category/$1');

// Comment posting route (must be protected)
$routes->post('/articles/(:num)/comments', 'CommentController::store/$1', ['filter' => 'auth']);

// VULN: IDOR - delete comment by ID, no auth check
$routes->get('/comments/delete/(:num)', 'CommentController::delete/$1');

// Protected admin routes
$routes->group('admin', ['filter' => 'admin'], function($routes) {
    $routes->get('/', 'AdminController::index');
    $routes->get('create', 'AdminController::create');
    $routes->post('store', 'AdminController::store');
    $routes->get('edit/(:num)', 'AdminController::edit/$1');
    $routes->post('update/(:num)', 'AdminController::update/$1');
    $routes->get('delete/(:num)', 'AdminController::delete/$1');
});
