<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class LoginController extends BaseController
{
    public function index($msg = null)
    {
        $session = session();

        $usuario = $session->get('usuario');
        if (!$usuario) {
            return view('_common/cabecalho')
                . view('login/login')
                . view('_common/rodape');
        }
        return redirect()->to('importacao');
    }

    function autenticar()
    {
        $session = session();
        $dados = $this->request->getPost();
        $login = $dados['login'];
        $senha = $dados['senha'];
        $model = new UsuarioModel();
        $buscar = $model->where('login', $login)->first();
        if (!$buscar) return redirect()->to('login/usuario_invalido');
        if ($buscar['senha'] == md5($senha)) {
            $session = session();
            $dadosusuario['id'] = $buscar['id'];
            $dadosusuario['nome'] = $buscar['nome'];
            $dadosusuario['permissoes'] = $buscar['permissoes'];
            $session->set('usuario', $dadosusuario);
            return redirect()->to('importacao');
        }
        return redirect()->to('login/senha_invalida');
    }

    function logout()
    {
        $session = session();
        $session->destroy();
        $session->setFlashdata('msg', 'Você saiu do sistema!');
        $session->setFlashdata('alert-class', 'alert-success');
        return redirect()->to('login');
    }
}
