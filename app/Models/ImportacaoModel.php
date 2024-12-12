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


    function agruparCentroCusto($mes = '2023-09-01', $todasFolhas=false)
    {
        if ($todasFolhas) $tipoFolha = '"Folha Normal", "Adiantamento décimo terceiro s", ""Adiantamento décimo terceiro salário", "Décimo terceiro salário integral"';
        else if (substr($mes, 8, 2) == "01") $tipoFolha = '"Folha Normal"';
        else $tipoFolha = '"Décimo terceiro salário integral"';

        $sql = "SELECT codigodaverba, nomedaverba, dc, count(*) AS 'quantidade', sum(valor) AS 'soma',
            centrodecusto, grupo_verba.id AS 'id_grupo', grupo_verba.historico AS 'nome_grupo',
            grupo_verba.conta_despesa, grupo_verba.conta_empenho, grupo_verba.conta_liquidacao,
            grupo_verba.conta_banco, grupo_verba.tipo, grupo_verba.exportar_xml
        FROM importacao_crua
            LEFT JOIN verba ON importacao_crua.codigodaverba = verba.codigo
            LEFT JOIN grupo_verba ON grupo_verba.id = verba.id_grupo
        WHERE importacao_crua.deleted_at IS null
            AND competencia = '$mes'
            AND importacao_crua.tipodefolha IN ($tipoFolha)
            AND verba.deleted_at IS NULL
        GROUP BY dc, codigodaverba, centrodecusto
        ORDER BY dc DESC, tipo, CAST(codigodaverba AS SIGNED), centrodecusto";

        $result = $this->db->query($sql);
        return $result->getResultArray();
    }

    function agrupar($mes = '2023-09-01', $todasFolhas=false)
    {
        if ($todasFolhas) $tipoFolha = '"Folha Normal", "Adiantamento décimo terceiro s", "Adiantamento décimo terceiro salário". "Décimo terceiro salário integral"';
        else if (substr($mes, 8, 2) == "01") $tipoFolha = '"Folha Normal"';
        else $tipoFolha = '"Décimo terceiro salário integral"';
        $sql = "SELECT competencia, codigodaverba, nomedaverba, dc, count(*) AS 'quantidade', sum(valor) AS 'soma',
            grupo_verba.historico AS 'nome_grupo', grupo_verba.tipo AS 'tipo_grupo', grupo_verba.exportar_xml,
            verba.id AS id_verba_grupo,
            grupo_verba.id AS id_grupo
        FROM importacao_crua
            LEFT JOIN verba ON importacao_crua.codigodaverba = verba.codigo AND verba.deleted_at IS NULL
            LEFT JOIN grupo_verba ON grupo_verba.id = verba.id_grupo
        WHERE importacao_crua.deleted_at IS NULL
            AND competencia = '$mes'
            AND importacao_crua.tipodefolha IN ($tipoFolha)
            AND verba.deleted_at IS NULL
        GROUP BY dc, codigodaverba
        ORDER BY dc DESC, CAST(codigodaverba AS SIGNED)";

        $result = $this->db->query($sql);
        return $result->getResultArray();
    }


    function grupoCentroCusto($mes = '2023-09-01')
    {
        $sql = "SELECT sum(valor) AS 'soma',
            centrodecusto, grupo_verba.id AS 'id_grupo', grupo_verba.historico AS 'nome_grupo',
            grupo_verba.conta_despesa,
            grupo_verba.tipo, grupo_verba.exportar_xml
        FROM importacao_crua
            INNEr JOIN verba ON importacao_crua.codigodaverba = verba.codigo
            INNER JOIN grupo_verba ON grupo_verba.id = verba.id_grupo
        WHERE importacao_crua.deleted_at IS null
            AND competencia = '$mes'
            AND exportar_xml = 1
            AND grupo_verba.tipo != 'C - Desconto'
            AND importacao_crua.tipodefolha = 'Folha Normal'
            AND verba.deleted_at IS NULL
        GROUP BY id_grupo, centrodecusto
        ORDER BY grupo_verba.tipo, id_grupo, centrodecusto";

        $result = $this->db->query($sql);
        return $result->getResultArray();
    }

}