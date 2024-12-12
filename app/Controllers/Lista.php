<?php

namespace App\Controllers;

use App\Models\EncargoModel;
use App\Models\GrupoVerbaModel;
use App\Models\ImportacaoModel;

class Lista extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
    }


    public function index()
    {
        if (!$this->session->get('usuario')) return redirect()->to('login');
        return view('_common/cabecalho')
            . view('lista/inicio')
            . view('_common/rodape');
    }


    function agrupado($mes=null)
    {
        if (!$this->session->get('usuario')) return redirect()->to('login');
        if ($mes) {
            $model = new ImportacaoModel();
            $agrupado = $model->agrupar($mes, true);
            $gmodel = new GrupoVerbaModel();
            $grupos = $gmodel->lista();
            $body_data['agrupado'] = $agrupado;
            $body_data['grupos'] = $grupos;
            $body_data['mes'] = $mes;
            return view('_common/cabecalho')
                . view('lista/agrupado', $body_data)
                . view('_common/rodape');
        }
        return redirect()->to('lista');
    }

    function centro_custo($mes = null)
    {
        if (!$this->session->get('usuario')) return redirect()->to('login');
        if ($mes) {
            $model = new ImportacaoModel();
            $agrupado = $model->agruparCentroCusto($mes);
            $body_data['agrupado'] = $agrupado;
            return view('lista/centro_custo', $body_data);
        }
    }


    function empenhos($mes = null)
    {
        if (!$this->session->get('usuario')) return redirect()->to('login');
        if ($mes) {
            $model = new ImportacaoModel();
            $agrupado = $model->agruparCentroCusto($mes);
            $body_data['agrupado'] = $agrupado;
            $body_data['mes'] = $mes;
            return view('_common/cabecalho')
                .view('lista/empenhos', $body_data)
                .view('_common/rodape');
        }
    }


    function encargos($mes = null)
    {
        if (!$this->session->get('usuario')) return redirect()->to('login');
        if ($mes) {
            $model = new ImportacaoModel();
            $agrupado = $model->agruparCentroCusto($mes);

            $emodel = new EncargoModel();
            $encargos = $emodel->where('competencia', $mes)->first();

            if (!$encargos) return  redirect()->to('encargos/cadastrar/'.$mes);
            $body_data['mes']      = $mes;
            $body_data['agrupado'] = $agrupado;
            $body_data['encargos'] = $encargos;
            return view('lista/encargos', $body_data);
        }
    }


    function cadastrarEncargos($mes = null)
    {
        if (!$this->session->get('usuario')) return redirect()->to('login');
        $model = new ImportacaoModel();
        $agrupado = $model->agrupar($mes, true);
        $emodel = new EncargoModel();
        $encargos = $emodel->where('competencia', $mes)->first();
        $body_data['mes'] = $mes;
        $body_data['agrupado'] = $agrupado;
        $body_data['encargos'] = $encargos;
        return view('cadastro/encargos', $body_data);
    }


    function adicionarEncargos()
    {
        if (!$this->session->get('usuario')) return redirect()->to('login');
        $dados = $this->request->getPost();
        //var_dump($dados);
        $emodel = new EncargoModel();
        $encargos = $emodel->where('competencia', $dados['mes'])->first();
        if ($encargos) {
            $emodel->where('competencia', $dados['mes'])->set($dados['novo'])->update();
        }
        else {
            $dados['novo']['competencia'] = $dados['mes'];
            $emodel->insert($dados['novo']);
        }
        return redirect()->to('lista/encargos/' . $dados['mes']);
    }


    function verba_mes($verba=null, $mes=null, $dc=null)
    {
        if (!$this->session->get('usuario')) return redirect()->to('login');
        $model = new ImportacaoModel();
        $valores = $model->whereIn('tipodefolha', ['Folha Normal', 'Décimo terceiro salário integral'])
                    ->where('codigodaverba', $verba)
                    ->where('competencia', $mes)
                    ->where('dc', $dc)
                    ->orderBy('nome')
                    ->findAll();
        $body_data['valores'] = $valores;
        return view('_common/cabecalho')
            . view('lista/verba_mes', $body_data)
            . view('_common/rodape');
    }


    function contracheque($matricula=null, $mes=null) {
        if (!$this->session->get('usuario')) return redirect()->to('login');
        $model = new ImportacaoModel();
        $valores = $model->whereIn('tipodefolha', ['Folha Normal', 'Décimo terceiro salário integral'])
                    ->where('matricula', $matricula)
                    ->where('competencia', $mes)
                    ->orderBy('dc', 'DESC')
                    ->orderBy('codigodaverba')
                    ->findAll();;
        $body_data['valores'] = $valores;
        return view('_common/cabecalho')
            . view('lista/contracheque', $body_data)
            . view('_common/rodape');
    }
}