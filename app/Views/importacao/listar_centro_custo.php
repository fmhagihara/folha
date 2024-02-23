<?php
$somadebitos = $somacreditos = 0;
$somagrupo = $nomegrupo = $sem_grupo = array();
$verbadc = array('2010', '2015', '2041', '2500', '3190','3191');
?>

<table border="1">
    <thead>
        <tr>
            <th>Código Verba</th>
            <th>Nome Verba</th>
            <th>D/C</th>
            <th>Centro de custos</th>
            <th>Soma</th>
            <th>Conta despesa</th>
            <th>Nome Grupo</th>
            <th>Tipo Grupo</th>

        </tr>
    </thead>
    <tbody>
        <?php foreach ($agrupado as $ag) : ?>
        <tr>
            <td><?=$ag['codigodaverba']?></td>
            <td><?=$ag['nomedaverba']?></td>
            <td><?=$ag['dc']?></td>
            <td><?=$ag['centrodecusto']?></td>
            <td style="text-align: right"><?=number_format($ag['soma'], 2, ',', '.')?></td>
            <td><?=$ag['conta_despesa']?></td>
            <td><?=$ag['nome_grupo']?></td>
            <td><?=$ag['tipo']?></td>
        </tr>
        <?php
        if (is_numeric($ag['id_grupo']) && ($ag['dc'] == 'D' || !in_array($ag['codigodaverba'], $verbadc))) {
            if (!isset($somagrupo[$ag['id_grupo']])) {
                $somagrupo[$ag['id_grupo']] = 0;
            }
            $somagrupo[$ag['id_grupo']] += $ag['soma'];
            $nomegrupo[$ag['id_grupo']] = $ag['nome_grupo'];
        }
        if ($ag['dc'] == 'D') $somadebitos += $ag['soma'];
        else $somacreditos += $ag['soma'];
        if (!$ag['id_grupo']) $sem_grupo[] = $ag['codigodaverba'] . ' - ' . $ag['nomedaverba'];
        endforeach;?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">Soma débitos</td>
            <td style="text-align: right"><?=number_format($somadebitos, 2, ',', '.')?></td>
        </tr>
        <tr>
            <td colspan="4">Soma créditos</td>
            <td style="text-align: right"><?=number_format($somacreditos, 2, ',', '.')?></td>
        </tr>
    </tfoot>

</table>
