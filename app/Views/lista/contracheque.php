<?php
    $somadebito = 0;
    $somacredito = 0;
    $saldo = 0;
?>

<p>Funcionário(a): <?= $valores[0]['nome'] ?> - <?= $valores[0]['matricula'] ?></p>
<p>Centro de custos: <?= $valores[0]['centrodecusto'] ?></p>
<p>Competência: <?= substr($valores[0]['competencia'], 5, 2) . '/' . substr($valores[0]['competencia'], 0, 4) ?></p>
<p>Valores:</p>
<div class="col col-8">
    <table class="table table-bordered table-hover table-striped">
        <thead class="table table-secondary">
            <tr>
                <th>Código</th>
                <th>Verba</th>
                <th>Tipo</th>
                <th>Valor debito</th>
                <th>Valor credito</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($valores as $v) :
                if ($v['dc'] == 'D') $somadebito += $v['valor'];
                elseif ($v['codigodaverba'] != '2002')$somacredito += $v['valor'];
                if (!in_array($v['codigodaverba'], array('2010', '2015', '2041', '2500', '3190', '3191'))) : ?>
                    <tr>
                        <td><?= $v['codigodaverba'] ?></td>
                        <td><?= $v['nomedaverba'] ?></td>
                        <td><?= $v['dc'] ?></td>
                        <td class="text-end"><?= ($v['dc'] == 'D') ? number_format($v['valor'], 2, ',', '.') : '' ?></td>
                        <td class="text-end"><?= ($v['dc'] == 'C') ? number_format($v['valor'], 2, ',', '.') : '' ?></td>
                    </tr>
            <?php endif;
            endforeach;
            $saldo = $somadebito - $somacredito; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Somas (sem 2002 Líquido)</td>
                <td class="text-end"><?=number_format($somadebito, 2, ',', '.')?></td>
                <td class="text-end"><?=number_format($somacredito, 2, ',', '.')?></td>
            </tr>
            <tr>
                <td colspan="4">Saldo (D - C) - tem que ser igual ao líquido</td>
                <td class="text-end"><?=number_format($saldo, 2, ',', '.')?></td>
            </tr>
        </tfoot>
    </table>
</div>