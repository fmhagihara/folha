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
?>

<h3>Importação da folha - início</h3>
<?= form_open_multipart('importacao/dat_analitico') ?>
<div class="row">
    <div class="col col-1">
        <?= form_label('Arquivo:', 'arquivo') ?>
    </div>
    <div class="col col-4">
        <?= form_upload('arquivo', '', 'id="arquivo" class="form-control"') ?>
    </div>
</div>
<div class="row">
    <div class="col col-1">
        <?= form_label('Competencia:', 'competencia', ['class' => 'form-label']) ?>
    </div>
    <div class="col col-2">
        <?= form_dropdown('competencia', $competencias, '', 'class="form-select"') ?>
    </div>
    <div class="col col-2">
        <?= form_submit('', 'Processar', 'class="btn btn-success"') ?>
    </div>
</div>
<?= form_close() ?>

<p><?= anchor('lista', 'Listas') ?></p>
<p><?= anchor('grupos', 'Grupos de contabilização') ?></p>