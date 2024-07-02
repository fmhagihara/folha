<?php
$somadebitos = $somacreditos = 0;
$somaproventos = $somadescontos = $liquido = 0;
$somagrupo = $nomegrupo = $sem_grupo = array();
$verbadc = array('2010', '2015', '2041', '2500', '3190', '3191');
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
                <td><?= anchor('verba_mes/' . $ag['codigodaverba'] . '/' . $ag['competencia'] . '/' . $ag['dc'], $ag['codigodaverba'], 'target="_blank"') ?></td>
                <td><?= $ag['nomedaverba'] ?></td>
                <td><?= $ag['dc'] ?></td>
                <td><?= $ag['quantidade'] ?></td>
                <td style="text-align: right"><?= number_format($ag['soma'], 2, ',', '.') ?></td>
                <?php
                if (isset($ag['id_grupo'])) {
                    echo '<td>';
                    echo $ag['id_grupo'] . ' ' . anchor('desvincular/' . $ag['id_verba_grupo'] . '/' . $ag['competencia'], '[D]',[
                        'onclick' => "return confirm(`Confirma desvinculação do grupo '" . $ag['nome_grupo'] .
                        "' da verba '" . $ag['codigodaverba'] . " - " . $ag['nomedaverba'] . "'?`)"
                    ]);
                    echo '</td><td>' . $ag['nome_grupo'] . '</td><td>' . $ag['tipo_grupo'] . '</td>';
                }
                elseif ($ag['codigodaverba'] != '2002') {
                    echo '<td></td>';
                    echo form_open('vincular', '', ['novo[codigo]'=>$ag['codigodaverba'], 'mes'=>$ag['competencia']]);
                    echo '<td>' . form_dropdown('novo[id_grupo]', $grupos) . '</td>';
                    echo '<td>' . form_submit('', 'Vincular') . '</td>';
                    echo form_close();
                }
                else {
                    echo '<td></td><td>VALOR LÍQUIDO NÃO SERÁ IMPORTADO</td><td></td>';
                }

                ?>
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

            if ($ag['dc'] == 'D' && $ag['exportar_xml']) $somaproventos += $ag['soma'];
            if ($ag['dc'] == 'C' && $ag['exportar_xml']) $somadescontos += $ag['soma'];
            if (!$ag['id_grupo']) $sem_grupo[] = $ag['codigodaverba'] . ' - ' . $ag['nomedaverba'];
        endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">Soma débitos</td>
            <td style="text-align: right"><?= number_format($somadebitos, 2, ',', '.') ?></td>
            <td colspan="2">Soma proventos</td>
            <td style="text-align: right"><?= number_format($somaproventos, 2, ',', '.') ?></td>
        </tr>
        <tr>
            <td colspan="4">Soma créditos</td>
            <td style="text-align: right"><?= number_format($somacreditos, 2, ',', '.') ?></td>
            <td colspan="2">Soma descontos</td>
            <td style="text-align: right"><?= number_format($somadescontos, 2, ',', '.') ?></td>
        </tr>
        <tr>
            <td colspan="4">Diferença</td>
            <td style="text-align: right"><?= number_format($somadebitos - $somacreditos, 2, ',', '.') ?></td>
            <td colspan="2">Líquido</td>
            <td style="text-align: right"><?= number_format($somaproventos - $somadescontos, 2, ',', '.') ?></td>
        </tr>
    </tfoot>

</table>
<?php if ($somagrupo) : ?>
    <p>Totais por grupos:</p>
    <?php foreach ($nomegrupo as $id => $nome) : ?>
        <h3><?= $nome ?> : <?= number_format($somagrupo[$id], 2, ',', '.') ?></h3>
    <?php endforeach; ?>

<?php endif; ?>

<?php
if ($sem_grupo) {
    echo '<hr><p>* Verbas sem grupo (e que não serão exportadas no XML):<br>';
    foreach ($sem_grupo as $sg) {
        echo $sg . '<br>';
    }
}
?>

<hr>
<?php if ($agrupado) : ?>
    <p>
        <?= anchor('importacao/excluir_lancamentos/' . $mes, 'Excluir todos os lançamentos', [
            'onclick' => "return confirm('Você tem certeza que deseja excluir todos os lançamentos?');"
        ]) ?>
    </p>
    <p><?= anchor('lista/centro_custo/' . $mes, 'Lista agrupada por centro de custo', 'target="_blank"') ?></p>
    <p><?= anchor('lista/encargos/' . $mes, 'Lista de encargos', 'target="_blank"') ?></p>
    <p><?= anchor('exportacao/gerar_xml/' . $mes, 'Gerar XML para Implanta', 'target="_blank"') ?></p>
<?php else : echo '<p>Sem lançamentos no mês. </p>';
endif; ?>
<?=anchor('lista', 'Tela de Listas')?>