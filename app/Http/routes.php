<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/**
 * Standard Routes
 */
Route::get('/','PagesController@index');

/**
 * Admin Routes
 */
Route::get('admin', 'Admin\PagesController@index');

Route::resource('admin/colparams',  'Admin\ColParamController');
Route::resource('admin/users',      'Admin\UserController');
Route::resource('admin/groups',     'Admin\GroupController');


/**
 * Authentication
 */
Route::controllers([
  'auth' => 'Auth\AuthController',
  'password' => 'Auth\PasswordController'
]);
