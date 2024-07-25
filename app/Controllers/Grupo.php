<?php

namespace App\Controllers;

use App\Models\GrupoVerbaModel;
use App\Models\VerbaModel;

class Grupo extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
    }

    public function index($id=NULL)
    {
        if (!$this->session->get('usuario')) return redirect()->to('login');
        $model = new GrupoVerbaModel();
        $grupos = $model->orderBy('tipo')->findAll();
        $body_data['grupos'] = $grupos;
        if ($id) $editar = $model->find($id);
        else {
            $editar = [
                'id' => '',
                'tipo'=>'A - Despesa',
                'historico'=> '',
                'conta_despesa'=> '',
                'conta_banco' => '',
                'conta_liquidacao' => '',
                'exportar_xml' => 1
            ];
        }
        $body_data['editar'] = $editar;

        return view('_common/cabecalho')
            . view('grupo/inicio', $body_data)
            . view('_common/rodape');
    }


    function cadastrar()
    {
        if (!$this->session->get('usuario')) return redirect()->to('login');
        $dados = $this->request->getPost();
        if ($dados) {
            $model = new GrupoVerbaModel();
            $model->save($dados);
        }
        return redirect()->to('grupos');
    }

    function excluir($id=null)
    {
        if (!$this->session->get('usuario')) return redirect()->to('login');
        $model = new GrupoVerbaModel();
        if ($id) {
            $verbasGrupo = $model->verbasGrupo($id);
            if (empty($verbasGrupo)) $model->delete($id);
            else echo 'tem verbas';

        }
        return redirect()->to('grupos');
    }

    function vincular()
    {
        if (!$this->session->get('usuario')) return redirect()->to('login');
        $dados = $this->request->getPost();
        $model = new VerbaModel();
        $model->save($dados['novo']);
        return redirect()->to('lista/agrupado/' . $dados['mes']);
    }

    function desvincular($id=null, $mes=null)
    {
        if (!$this->session->get('usuario')) return redirect()->to('login');
        $model = new VerbaModel();
        $model->delete($id);
        if ($mes) return redirect()->to('lista/agrupado/' . $mes);
        return redirect()->to('lista');
    }

}