<?php $soma = 0; $ord = 1?>
<h3><?=$valores[0]['codigodaverba'] . ' ' . $valores[0]['nomedaverba'] . ' - ' . substr($valores[0]['competencia'], 5, 2) . '/' . substr($valores[0]['competencia'], 0, 4)?></h3>
<table border="1">
    <thead>
        <tr>
            <th>Ord</th>
            <th>Funcionário</th>
            <th>Matricula</th>
            <th>C Custo</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($valores as $v) : ?>
            <tr>
                <td align="center"><?=$ord?>
                <td><?=anchor('contracheque/' . $v['matricula'] . '/' . $v['competencia'], $v['nome'], 'target="_blank"') ?></td>
                <td align="center"><?= $v['matricula'] ?></td>
                <td><?=$v['centrodecusto']?></td>
                <td align="right"><?= number_format($v['valor'], 2, ',', '.') ?></td>
            </tr>
        <?php
            $ord++;
            $soma += $v['valor'];
        endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" align="right">TOTAL</td>
            <td align="right"><?=number_format($soma, 2, ',', '.')?></td>
        </tr>
    </tfoot>
</table>