<?php
$competencias = array();

if (date('m') >= 11) {
    $decimo_terceiro = date('Y-12-15');
    $competencias[$decimo_terceiro] = '13/' . date('Y');
}

for ($i = 0; $i < 6; $i++) {
    $data = date('Y-m-01', strtotime("first day of -$i months"));
    $mes_ano = date('m/Y', strtotime("first day of -$i months"));
    $competencias[$data] = $mes_ano;
}


echo '<h3>Listas</h3>';

echo '<p>Agrupado</p>';
echo '<ul>';
foreach ($competencias as $key=>$value) {
    echo '<li>' . anchor('lista/agrupado/' . $key, $value) . '</li>';
}
echo '</ul>';


echo anchor('/importacao', 'Importação - início');
