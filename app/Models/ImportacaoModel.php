<?php

namespace App\Models;

use CodeIgniter\Model;

class ImportacaoModel extends Model
{
    protected $table = 'importacao_crua';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'arquivo',
        'competencia',
        'unidade',
        'centrodecusto',
        'matricula',
        'nome',
        'cargo',
        'tipodefolha',
        'codigodaverba',
        'nomedaverba',
        'contacontabil',
        'dc',
        'valor'
    ];


    protected $dateFormat = 'datetime';

    protected $deletedField = 'deleted_at';


    function agruparPessoa($mes = '2023-09-01')
    {

    }

    function agruparCentroCusto($mes = '2023-09-01')
    {
        $sql = "SELECT codigodaverba, nomedaverba, dc, count(*) AS 'quantidade', sum(valor) AS 'soma',
            centrodecusto, grupo_verba.id AS 'id_grupo'
        FROM importacao_crua
            LEFT JOIN verba ON importacao_crua.codigodaverba = verba.codigo
            LEFT JOIN grupo_verba ON grupo_verba.id = verba.id_grupo
        WHERE importacao_crua.deleted_at IS null
            AND competencia = '$mes'
        GROUP BY dc, codigodaverba, centrodecusto
        ORDER BY dc DESC, CAST(codigodaverba AS SIGNED), centrodecusto";

        $result = $this->db->query($sql);
        return $result->getResultArray();
    }

    function agrupar($mes = '2023-09-01')
    {
        $sql = "SELECT codigodaverba, nomedaverba, dc, count(*) AS 'quantidade', sum(valor) AS 'soma',
            grupo_verba.historico AS 'nome_grupo', grupo_verba.tipo AS 'tipo_grupo', grupo_verba.id AS 'id_grupo'
        FROM importacao_crua
            LEFT JOIN verba ON importacao_crua.codigodaverba = verba.codigo
            LEFT JOIN grupo_verba ON grupo_verba.id = verba.id_grupo
        WHERE importacao_crua.deleted_at IS null
            AND competencia = '$mes'
        GROUP BY dc, codigodaverba
        ORDER BY dc DESC, CAST(codigodaverba AS SIGNED)";

        $result = $this->db->query($sql);
        return $result->getResultArray();
    }

}