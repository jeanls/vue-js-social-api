<?php
use App\User;
use App\Comentario;
use App\Conteudo;

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
Route::get('/testes', function (){
    $user = User::find(2);
//    $user2 = User::find(4);
//    $user->amigos()->detach($user2->id);
//    $user->amigos()->attach($user2->id);
//    $user->conteudos()->create([
//        'titulo' => "Conteudo 3",
//        'texto' => "Texto 3",
//        'imagem' => "url 3",
//        'link' => "link 3",
//        'data' => date('Y-m-d')
//    ]);
//    $user->amigos()->toggle($user2->id);
    $conteudo = Conteudo::find(1);
//    $user->curtidas()->toggle($conteudo->id);
//    $user->comentarios()->create([
//        'conteudo_id' => $conteudo->id,
//        'texto' => "Comentando no conteudo",
//        'data' => date('Y-m-d')
//    ]);
    return $conteudo->comentarios;
});