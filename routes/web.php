<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('createIndex',"EsController@createIndex");
Route::get('getIndex',"EsController@getIndex");
Route::get('search',"EsController@search");
Route::get('delete',"EsController@delete");
Route::get('setting',"EsController@setting");


