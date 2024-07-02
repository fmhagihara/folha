<?php

namespace App\Controllers;

use App\Models\GrupoVerbaModel;

class Grupo extends BaseController
{

    public function index($id=NULL): string
    {
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

        return view('grupo/inicio', $body_data);
    }


    function cadastrar() 
    {
        $dados = $this->request->getPost();
        if ($dados) {
            $model = new GrupoVerbaModel();
            $model->save($dados);
        }
        return redirect()->to('grupos');
    }

    function excluir($id=null)
    {
        $model = new GrupoVerbaModel();
        if ($id) $model->delete($id);
        return redirect()->to('grupos');
    }

}