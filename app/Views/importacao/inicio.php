<?php
$competencias = array(
    '2024-03-01' => '03/2024',
    '2024-02-01' => '02/2024',
    '2024-01-01' => '01/2024',
//    '2023-12-01' => '12/2023',
//    '2023-11-01' => '11/2023',
//    '2023-10-01' => '10/2023',
//    '2023-09-01' => '09/2023',
//    '2023-08-01' => '08/2023',
//    '2023-07-01' => '07/2023',
//    '2023-06-01' => '06/2023',
//    '2023-05-01' => '05/2023',
//    '2023-04-01' => '04/2023',
//    '2023-03-01' => '03/2023',
//    '2023-02-01' => '02/2023',
//    '2023-01-01' => '01/2023'
);
$competencias = array();

for ($i = 0; $i < 6; $i++) {
    $data = date('Y-m-01', strtotime("-$i months"));
    $mes_ano = date('m/Y', strtotime("-$i months"));
    $competencias[$data] = $mes_ano;
}
//echo form_open_multipart('importacao/processar');
echo '<h3>Importação da folha - início</h3>';
echo form_open_multipart('importacao/dat_analitico');
//echo form_open_multipart('importacao/agrupar_descontos');
echo form_label('Arquivo:', 'arquivo');
echo form_upload('arquivo');
echo '<br>';
echo form_label('Competência:');
echo form_dropdown('competencia', $competencias);
echo '<br>';
echo form_submit('','Processar');

echo form_close();

echo '<br>';
echo anchor('lista', 'Listas');

?>