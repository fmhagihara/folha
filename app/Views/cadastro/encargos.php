<?php

echo '<h3>Cadastrar valores dos encargos mês ' . substr($mes, 5, 2) . '/' . substr($mes, 0, 4) . ':</h3>';

$imp_fgts = $imp_inss = $imp_pis = $desc_inss = 0;
foreach ($agrupado as $ag) {
    if (in_array($ag['codigodaverba'], ['2010', '2015']) && ($ag['dc'] == 'D')) {
        $imp_fgts += $ag['soma'];
    }
    elseif (in_array($ag['codigodaverba'], ['2500', '2041']) && ($ag['dc'] == 'D')) {
        $imp_inss += $ag['soma'];
    }
    elseif (in_array($ag['codigodaverba'], ['3190', '3191']) && ($ag['dc'] == 'D')) {
        $imp_pis += $ag['soma'];
    }
    elseif (in_array($ag['codigodaverba'], ['1500', '1502']) && ($ag['dc'] == 'C')) {
        $desc_inss += $ag['soma'];
    }
}

if (!$encargos) {
    $encargos['fgts'] = $encargos['pis'] = $encargos['inss'] = '';
}

echo form_open('encargos/adicionar', '', ['mes'=>$mes]);
echo '<table>
<thead>
<tr>
    <th>Encargo</th>
    <th>Novo valor</th>
    <th>Folha (referência)</th>
    <th>Descontado</th>
</tr>
</thead>
<tbody>
<tr>
    <td>' . form_label('FGTS') . '</td>
    <td>' . form_input('novo[fgts]', $encargos['fgts'], 'size="10" style="text-align: right"'). '</td>
    <td style="text-align: right">' . number_format($imp_fgts, 2, ',', '.') . '</td>
</tr>
<tr>
    <td>' . form_label('PIS') . '</td>
    <td>' . form_input('novo[pis]', $encargos['pis'], 'size="10" style="text-align: right"') . '</td>
    <td style="text-align: right">' . number_format($imp_pis, 2, ',', '.') . '</td>
</tr>
<tr>
    <td>' . form_label('INSS') . '</td>
    <td>' . form_input('novo[inss]', $encargos['inss'], 'size="10" style="text-align: right"') . '</td>
    <td style="text-align: right">' . number_format($imp_inss, 2, ',', '.') . '</td>
    <td style="text-align: right">' . number_format($desc_inss, 2, ',', '.') . '</td>
    <td>
</tr>
</tbody>
</table>';
echo form_submit('', 'Cadastrar');