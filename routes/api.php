<?php

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
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

Route::middleware('auth:api')->get('/usuario', function (Request $request) {
    return $request->user();
});

Route::post('/login', function (Request $request){
    $data = $request->all();
    $response = new stdClass();
    $response->user = null;
    $response->token = null;
    $response->status = null;
    $response->errors = [];
    $validacao = Validator::make($data, [
        'email' => 'required|string|email|max:255',
        'password' => 'required|string',
    ]);
    if($validacao->fails()){
        $response->errors = $validacao->errors();
        $response->status = false;
        return json_encode($response);
    }
    if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){
        $user = auth()->user();
        $response->token = $user->createToken($user->email)->accessToken;
        $response->user = $user;
        $response->status = true;
        return json_encode($response);
    }
    $response->status = false;
    return json_encode($response);
});

Route::post('/cadastro', function (Request $request){
    $data = $request->all();
    $validacao = Validator::make($data, [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ]);
    if($validacao->fails()){
        return $validacao->errors();
    }
    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => bcrypt($data['password']),
    ]);
    $user->token = $user->createToken($user->email)->accessToken;
    return $user;
});