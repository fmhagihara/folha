<?php

namespace App\Models;

use CodeIgniter\Model;

class GrupoVerbaModel extends Model
{
    protected $table = 'grupo_verba';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['tipo', 'historico', 'conta_despesa', 'conta_empenho', 'conta_liquidacao', 'conta_banco'];


    protected $dateFormat = 'datetime';

    protected $deletedField = 'deleted_at';


    function lista() {
        $lista = array();
        $result = $this->orderBy('tipo')->findAll();
        foreach ($result as $row) {
            $id = $row['id'];
            $lista[$id] = $row['historico'] . '(' . $row['tipo'] . ')';
        }
        return $lista;
    }

    function verbasGrupo($idGrupo) {
        $lista = array();

        $result = $this->from('verba')->where('id_grupo', $idGrupo)->findAll();
        foreach ($result as $row) {
            $id = $row['id'];
            $lista[$id] = $row['codigo'];
        }
        return $lista;
    }
}