<?php

$soma_fgts = $soma_inss = $soma_pis = 0;
$ind_fgts = $ind_inss = $ind_pis = 999;
$maior_fgts = $maior_inss = $maior_pis = 0;

//var_dump($encargos);

foreach ($agrupado as $ag) {
    // FGTS
    if ($ag['dc'] == 'D' && in_array($ag['codigodaverba'], ['2010','2015','2019'])) {
        if (!isset($cc_fgts[$ag['centrodecusto']])) $cc_fgts[$ag['centrodecusto']] = 0;
        $cc_fgts[$ag['centrodecusto']] += $ag['soma'];
        $soma_fgts += $ag['soma'];
        if ($ag['soma'] > $maior_fgts) {
            $maior_fgts = $ag['soma'];
            $ind_fgts = $ag['centrodecusto'];
        }
        if ($ag['codigodaverba'] == '2015') {
            if ($ind_fgts == $ag['centrodecusto']) $maior_fgts += $ag['soma'];
        }
    }

    // INSS
    if ($ag['dc'] == 'D' && in_array($ag['codigodaverba'], ['2500'])) {
        if (!isset($cc_inss[$ag['centrodecusto']])) $cc_inss[$ag['centrodecusto']] = 0;
        $cc_inss[$ag['centrodecusto']] += $ag['soma'];
        $soma_inss += $ag['soma'];
        if ($ag['soma'] > $maior_inss) {
            $maior_inss = $ag['soma'];
            $ind_inss = $ag['centrodecusto'];
        }
    }

    // PIS
    if ($ag['dc'] == 'D' && in_array($ag['codigodaverba'], ['3190'])) {
        if (!isset($cc_pis[$ag['centrodecusto']])) $cc_pis[$ag['centrodecusto']] = 0;
        $cc_pis[$ag['centrodecusto']] += $ag['soma'];
        $soma_pis += $ag['soma'];
        if ($ag['soma'] > $maior_pis) {
            $maior_pis = $ag['soma'];
            $ind_pis = $ag['centrodecusto'];
        }
    }
}


$dif_fgts = round($soma_fgts - $encargos['fgts'], 2);
if ($dif_fgts) {
    $codgrupo = substr($ind_fgts, 0, 4);
    $cc_fgts[$ind_fgts] = $maior_fgts -= $dif_fgts;
}
else $ind_fgts = 999;

$dif_inss = round($soma_inss - $encargos['inss'], 2);
if ($dif_inss) {
    $codgrupo = substr($ind_inss, 0, 4);
    $cc_inss[$ind_inss] = $maior_inss -= $dif_inss;
}
else $ind_inss = 999;

$pis_proporcional = false;
$dif_pis = round($soma_pis - $encargos['pis'], 2);
if ($dif_pis) {
    $codgrupo = substr($ind_pis, 0, 4);
    if ($dif_pis < $maior_pis) {
        $cc_pis[$ind_pis] = $maior_pis -= $dif_pis;
    }
    else {
        $pis_proporcional = true;
        $perc_pis = $encargos['pis'] / $soma_pis;
        foreach ($cc_pis as $key=>$value) {
            $novo_valor = round($value * $perc_pis, 2);
            $cc_pis[$key] = $novo_valor;
        }
    }
}
else $ind_pis = 999;

$soma_fgts = $soma_inss = $soma_pis = 0;
foreach ($cc_fgts as $key=>$value) {
    $codgrupo = substr($key, 0, 4);
    if (!isset($gr_fgts[$codgrupo])) $gr_fgts[$codgrupo] = 0;
    $gr_fgts[$codgrupo] += $value;
    $soma_fgts += $value;
}

foreach ($cc_inss as $key=>$value) {
    $codgrupo = substr($key, 0, 4);
    if (!isset($gr_inss[$codgrupo])) $gr_inss[$codgrupo] = 0;
    $gr_inss[$codgrupo] += $value;
    $soma_inss += $value;
}

foreach ($cc_pis as $key=>$value) {
    $codgrupo = substr($key, 0, 4);
    if (!isset($gr_pis[$codgrupo])) $gr_pis[$codgrupo] = 0;
    $gr_pis[$codgrupo] += $value;
    $soma_pis += $value;
}

?>
<p>FGTS - empenho 28/2024</p>
<table border="1">
    <thead>
        <tr>
            <th>Centro de custos</th>
            <th>Valor (Pgto)</th>
            <th>Grupo</th>
            <th>Soma (Liq)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $grupoant = '';
        foreach ($cc_fgts as $key=>$value) :
        $grupo = substr($key, 0, 4);
        ?>
        <tr bgcolor="<?=($key == $ind_fgts) ? 'yellow' : 'white'?>">
            <td><?=substr($key, 0, 1) . '.' . substr($key, 1, 3) . '.' . substr($key, 4, 2)?></td>
            <td style="text-align: right"><?=number_format($value, 2, ',', '.')?></td>
            <td><?=($grupoant != $grupo) ? substr($grupo, 0, 1) . '.' . substr($grupo, 1, 3) : ''?></td>
            <td style="text-align: right"><?=($grupoant != $grupo) ? number_format($gr_fgts[$grupo], 2, ',', '.') : ''?></td>
        </tr>
        <?php
        $grupoant = $grupo;
        endforeach;
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td>Soma FGTS</td>
            <td style="text-align: right"><?=number_format($soma_fgts, 2, ',', '.')?></td>
        </tr>
    </tfoot>
</table><br>

<p>INSS - Empenho 29/2024</p>
<table border="1">
    <thead>
        <tr>
            <th>Centro de custos</th>
            <th>Valor (Pgto)</th>
            <th>Grupo</th>
            <th>Soma (Liq)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $grupoant = '';
        foreach ($cc_inss as $key=>$value) :
        $grupo = substr($key, 0, 4);
        ?>
        <tr bgcolor="<?=($key == $ind_inss) ? 'yellow' : 'white'?>">
            <td><?=substr($key, 0, 1) . '.' . substr($key, 1, 3) . '.' . substr($key, 4, 2)?></td>
            <td style="text-align: right"><?=number_format($value, 2, ',', '.')?></td>
            <td><?=($grupoant != $grupo) ? substr($grupo, 0, 1) . '.' . substr($grupo, 1, 3) : ''?></td>
            <td style="text-align: right"><?=($grupoant != $grupo) ? number_format($gr_inss[$grupo], 2, ',', '.') : ''?></td>
        </tr>
        <?php
        $grupoant = $grupo;
        endforeach;
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td>Soma INSS</td>
            <td style="text-align: right"><?=number_format($soma_inss, 2, ',', '.')?></td>
        </tr>
    </tfoot>
</table><br>

<p>PIS - Empenho 27/2024</p>
<table border="1">
    <thead>
        <tr>
            <th>Centro de custos</th>
            <th>Valor (Pgto)</th>
            <th>Grupo</th>
            <th>Soma (Liq)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $grupoant = '';
        foreach ($cc_pis as $key=>$value) :
        $grupo = substr($key, 0, 4);
        ?>
        <tr bgcolor="<?=($key == $ind_pis || $pis_proporcional) ? 'yellow' : 'white'?>">
            <td><?=substr($key, 0, 1) . '.' . substr($key, 1, 3) . '.' . substr($key, 4, 2)?></td>
            <td style="text-align: right"><?=number_format($value, 2, ',', '.')?></td>
            <td><?=($grupoant != $grupo) ? substr($grupo, 0, 1) . '.' . substr($grupo, 1, 3) : ''?></td>
            <td style="text-align: right"><?=($grupoant != $grupo) ? number_format($gr_pis[$grupo], 2, ',', '.') : ''?></td>
        </tr>
        <?php
        $grupoant = $grupo;
        endforeach;
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td>Soma PIS</td>
            <td style="text-align: right"><?=number_format($soma_pis, 2, ',', '.')?></td>
        </tr>
    </tfoot>
</table><br>
<a href="<?=base_url()?>encargos/cadastrar/<?=$mes?>">Cadastrar/alterar valores</a>