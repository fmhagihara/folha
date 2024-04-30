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

    function centro_custo($mes = null)
    {
        if ($mes) {
            $model = new ImportacaoModel();
            $agrupado = $model->agruparCentroCusto($mes);
            // echo '<pre>';

            /*
            foreach ($agrupado as $ag) {
                if ($ag['tipo']) {
                    echo $ag['codigodaverba'] . ' - ' . $ag['tipo'] . ' - ' . $ag['centrodecusto'] . ' - ' . $ag['soma'] . ' - ' . $ag['nome_grupo'] . '<br>';
                }
            }
*/
            //var_dump($agrupado);
            $body_data['agrupado'] = $agrupado;
            return view('lista/centro_custo', $body_data);
        }
    }
}