<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Conteudo;

class ConteudoController extends Controller{
    public function adicionar(Request $request){
        $data = $request->all();
        $user = $request->user();
        $conteudo = new Conteudo();
        $conteudo->titulo = $data['titulo'];
        $conteudo->texto = $data['texto'];
        $conteudo->imagem = $data['imagem'];
        $conteudo->link = $data['link'];
        $conteudo->data = date('Y-m-d H:i:s');
        $user->conteudos()->save($conteudo);
        return ['status' => true, "conteudos" => $user->conteudos];
    }
}
