<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class LoginController extends BaseController
{
    public function index($msg=null): string
    {
        return view('_common/cabecalho')
            . view('login/login')
            . view('_common/rodape');
    }

    function autenticar()
    {
        $dados = $this->request->getPost();
        $login = $dados['login'];
        $senha = $dados['senha'];
        $model = new UsuarioModel();
        $buscar = $model->where('login', $login)->first();
        if (!$buscar) return redirect()->to('login/usuario_invalido');
        if ($buscar['senha'] == md5($senha)) {

        }
    }
}
