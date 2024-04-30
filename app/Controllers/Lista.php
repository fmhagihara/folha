<?php

namespace App\Controllers;

use App\Models\ImportacaoModel;

class Lista extends BaseController
{

    public function index(): string
    {
        return view('lista/inicio');
    }


    function agrupado($mes=null)
    {
        if ($mes) {
            $model = new ImportacaoModel();
            $agrupado = $model->agrupar($mes);
            $body_data['agrupado'] = $agrupado;
            $body_data['mes'] = $mes;
            return view('lista/agrupado', $body_data);
        }
        return redirect()->to('lista');
    }
}