<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Redirect root to articles
$routes->get('/', 'ArticleController::index');

// VULN: Removed all authentication - NO LOGIN REQUIRED!
// $routes->get('/login', 'AuthController::login');
// $routes->post('/login', 'AuthController::authenticate');
// $routes->get('/register', 'AuthController::register');
// $routes->post('/register', 'AuthController::store');
// $routes->get('/logout', 'AuthController::logout');

// Public article routes - NO AUTH NEEDED
$routes->get('/articles', 'ArticleController::index');
$routes->get('/articles/(:num)', 'ArticleController::show/$1');
$routes->get('/category/(:alpha)', 'ArticleController::category/$1');

// VULN: Comment posting without any protection
$routes->post('/articles/(:num)/comments', 'CommentController::store/$1');

// VULN: IDOR - delete comment by ID, no auth check
$routes->get('/comments/delete/(:num)', 'CommentController::delete/$1');

// VULN: Admin routes without any protection - BRUTAL!
$routes->get('/admin', 'AdminController::index');
$routes->get('/admin/create', 'AdminController::create');
$routes->post('/admin/store', 'AdminController::store');
$routes->get('/admin/edit/(:num)', 'AdminController::edit/$1');
$routes->post('/admin/update/(:num)', 'AdminController::update/$1');
$routes->get('/admin/delete/(:num)', 'AdminController::delete/$1');

// VULN: Direct SQL injection endpoints for testing
$routes->get('/search/(:any)', 'ArticleController::searchVuln/$1');
$routes->get('/user/(:any)', 'ArticleController::getUserVuln/$1');
