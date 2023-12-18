<?php

//echo form_open_multipart('importacao/processar');
echo form_open_multipart('importacao/dat_analitico');
//echo form_open_multipart('importacao/agrupar_descontos');
echo form_label('Arquivo:', 'arquivo');
echo form_upload('arquivo');
echo '<br>';
echo form_submit('','Processar');

echo form_close();


?>