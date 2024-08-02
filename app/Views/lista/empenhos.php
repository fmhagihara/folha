<?php
$verbadc = array('2010', '2015', '2019', '2041', '2500', '3190', '3191');


foreach ($agrupado as $ag) :
    if (($ag['dc'] == 'D') && !in_array($ag['codigodaverba'], $verbadc)) {
        $ccusto = substr($ag['centrodecusto'], 0, 4);
        $idgrupo = $ag['id_grupo'];
        $empenho[$idgrupo]['conta'] = $ag['conta_empenho'];
        $empenho[$idgrupo]['nomeconta'] = $ag['nome_grupo'];
        if (isset($empenho[$idgrupo]['total_empenho'])) $empenho[$idgrupo]['total_empenho'] += $ag['soma'];
        else $empenho[$idgrupo]['total_empenho'] = $ag['soma'];
        if (isset($subprograma[$idgrupo][$ccusto])) $subprograma[$idgrupo][$ccusto] += $ag['soma'];
        else $subprograma[$idgrupo][$ccusto] = $ag['soma'];
    }
endforeach;


?>
<h3>Lista de empenhos a emitir (<?= substr($mes, 5, 2) ?>/<?= substr($mes, 0, 4) ?>)</h3>
<?php if (isset($empenho, $subprograma)) :
    foreach ($empenho as $ig => $em) : ?>
        <hr>
        <b>Conta:</b> <?= $em['conta'] ?><br>
        <b>Agrupamento:</b> <?= $em['nomeconta'] ?><br>
        <b>Total empenho:</b> <?= number_format($em['total_empenho'], 2, ',', '.') ?><br>
        <?php foreach ($subprograma[$ig] as $custo => $valor) : ?>
            <li><?= $custo ?> - <?= number_format($valor, 2, ',', '.') ?></li>
        <?php endforeach; ?>
        <?=anchor('gerar_empenho/' . $mes . '/' . $ig, 'Gerar Solicitação de Reserva Orçamentária', '_target="blank"')?>
    <?php endforeach; ?>
<?php endif; ?>