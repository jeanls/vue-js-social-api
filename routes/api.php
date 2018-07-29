<?php

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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

Route::middleware('auth:api')->put('/perfil', function (Request $request) {
    $user = $request->user();
    $data = $request->all();

    if(isset($data['password'])){
        $validacao = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'required|string|min:6|confirmed',
        ]);
        if($validacao->fails()){
            return $validacao->errors();
        }
        $user->password = bcrypt($data['password']);
    }else{
        $validacao = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)]
        ]);
        if($validacao->fails()){
            return $validacao->errors();
        }
        $user->name = $data['name'];
        $user->email = $data['email'];
    }

    if(isset($data['imagem'])){
        $time = time();
        $dir_pai = 'perfils';
        $dir_img = $dir_pai . DIRECTORY_SEPARATOR . 'perfil_id_' . $user->id;
        $ext = substr($data['imagem'], 11, strpos($data['imagem'], ';') - 11);
        $url_imagem = $dir_img . DIRECTORY_SEPARATOR . $time . '.' .$ext;
        $file = str_replace('data:image/' . $ext. ';base64,', '', $data['imagem']);
        $file = base64_decode($file);
        if(!file_exists($dir_pai)){
            mkdir($dir_pai, 0700);
        }

        Validator::extend('base64image', function ($attribute, $value, $parameters, $validator){
            $explode = explode(',', $value);
            $allow = ['png', 'jpg', 'svg', 'jpeg'];
            $format = str_replace(['data:image/', ';', 'base64'], ['', '', ''], $explode[0]);
            if(!in_array($format, $allow)){
                return false;
            }
            if(!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])){
                return false;
            }
            return true;
        });

        $validacao = Validator::make($data, ['imagem' => 'base64image'], ['base64image' => 'Imagem Inválida']);

        if($validacao->fails()){
            return $validacao->errors();
        }

        if($user->imagem){
            if(file_exists($user->imagem)){
                unlink($user->imagem);
            }
        }

        if(!file_exists($dir_img)){
            mkdir($dir_img, 0700);
        }
        file_put_contents($url_imagem, $file);
        $user->imagem = $url_imagem;
    }
    $user->save();
    $user->token = $user->createToken($user->email)->accessToken;
    if($user->imagem){
        $user->imagem = asset($user->imagem);
    }
    return $user;
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
    $response = new stdClass();
    $response->user = null;
    $response->token = null;
    $response->status = null;
    $response->errors = [];
    $validacao = Validator::make($data, [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ]);
    if($validacao->fails()){
        $response->errors = $validacao->errors();
        $response->status = false;
        return json_encode($response);
    }
    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => bcrypt($data['password']),
    ]);
    $response->token = $user->createToken($user->email)->accessToken;
    $response->user = $user;
    $response->status = true;
    return json_encode($response);
});