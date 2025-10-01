<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

// Auth Routes
$router->get('/', 'AuthController@index');
$router->get('/register', 'AuthController@register');
$router->post('/register_post', 'AuthController@register_post');
$router->get('/login', 'AuthController@login');
$router->post('/login_post', 'AuthController@login_post');
$router->get('/logout', 'AuthController@logout');

// Notes Routes
$router->get('/notes', 'NotesController@index');
$router->get('/notes/create', 'NotesController@create');
$router->post('/notes/create_post', 'NotesController@create_post');
$router->get('/notes/edit/{id}', 'NotesController@edit');
$router->post('/notes/edit_post/{id}', 'NotesController@edit_post');
$router->get('/notes/delete/{id}', 'NotesController@delete');
?>