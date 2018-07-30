<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/usuario', "UsuarioController@usuario");
Route::middleware('auth:api')->put('/perfil', "UsuarioController@perfil");
Route::post('/login', "UsuarioController@login");
Route::post('/cadastro', 'UsuarioController@cadastro');