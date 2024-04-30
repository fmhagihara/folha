<?php
$competencias = array();

for ($i = 0; $i < 6; $i++) {
    $data = date('Y-m-01', strtotime("-$i months"));
    $mes_ano = date('m/Y', strtotime("-$i months"));
    $competencias[$data] = $mes_ano;
}


echo '<h3>Listas</h3>';

echo '<p>Agrupado</p>';
echo '<ul>';
foreach ($competencias as $key=>$value) {
    echo '<li>' . anchor('lista/agrupado/' . $key, $value) . '</li>';
}
echo '</ul>';


echo anchor('', 'Importação - início');
