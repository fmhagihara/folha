<?php
$competencias = array();

if (date('m') >= 11) {
    $decimo_terceiro = date('Y-12-15');
    $competencias[$decimo_terceiro] = '13/' . date('Y');
}
for ($i = 0; $i < 6; $i++) {
    $data = date('Y-m-01', strtotime("-$i months"));
    $mes_ano = date('m/Y', strtotime("-$i months"));
    $competencias[$data] = $mes_ano;
}
?>

<h3>Importação da folha - início</h3>
<h5>Usuário: <?=$usuario?></h5>
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
<p><?= anchor('logout', 'Sair do sistema') ?></p>