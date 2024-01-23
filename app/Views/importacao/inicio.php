<?php
$competencias = array(
    '2024-01-01' => '01/2024',
    '2023-10-01' => '10/2023',
    '2023-09-01' => '09/2023'
);
//echo form_open_multipart('importacao/processar');
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


?>