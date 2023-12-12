<?php
$somadebitos = $somacreditos = 0;
$somagrupo = $nomegrupo = array();
$verbadc = array('2010', '2500', '3190');
?>

<table border="1">
    <thead>
        <tr>
            <th>Código Verba</th>
            <th>Nome Verba</th>
            <th>D/C</th>
            <th>Qtde</th>
            <th>Soma</th>
            <th>ID grupo</th>
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
            <td><?=$ag['quantidade']?></td>
            <td style="text-align: right"><?=number_format($ag['soma'], 2, ',', '.')?></td>
            <td><?=$ag['id_grupo']?></td>
            <td><?=$ag['nome_grupo']?></td>
            <td><?=$ag['tipo_grupo']?></td>
        <tr>
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
<?php if ($somagrupo) :?>
    <p>Totais por grupos:</p>
    <?php foreach ($nomegrupo as $id=>$nome) :?>
    <h3><?=$nome?> : <?=number_format($somagrupo[$id], 2, ',', '.')?></h3>
    <?php endforeach;?>

<?php endif;?>