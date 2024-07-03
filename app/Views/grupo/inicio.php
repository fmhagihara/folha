<head>
<style>
.odd {
    background-color: lightgray;
}
</style>
</head>
<p><?=anchor('lista', 'Tela de Listas') ?></p>
<p><?=anchor('', 'Tela inicial - Importação') ?></p>

<?php
$simnao = [1=>'SIM', 0=>'NÃO'];
$tipos = [
    'A - Despesa' => 'A - Despesa',
    'B - Benefício' => 'B - Benefício',
    'C - Desconto' => 'C - Desconto',
    'F - Estorno' => 'F - Estorno',
]

?>
<h3>Cadastro de grupos de contabilização</h3>
<table class="table table-bordered table-hover table-sm table-striped">
    <thead class="table table-success">
        <tr class="text-center">
            <th>Tipo</th>
            <th>Histórico/descrição</th>
            <th>Conta despesa</th>
            <th>Conta liquidação</th>
            <th>Conta banco</th>
            <th>XML</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody>
        <?=form_open('cadastrar_grupo', '', ['id'=>$editar['id']])?>
        <tr class="text-sm">
            <td><?=form_dropdown('tipo', $tipos, $editar['tipo'], 'class="form-select"')?></td>
            <td><?=form_input('historico', $editar['historico'], 'class="form-control" required')?></td>
            <td><?=form_input('conta_despesa', $editar['conta_despesa'], 'class="form-control"')?></td>
            <td><?=form_input('conta_liquidacao', $editar['conta_liquidacao'], 'class="form-control"')?></td>
            <td><?=form_input('conta_banco', $editar['conta_banco'], 'class="form-control"')?></td>
            <td><?=form_dropdown('exportar_xml', $simnao, $editar['exportar_xml'], 'class="form-select"')?></td>
            <td style="white-space: nowrap; width: 150px;">
                <?=$editar['id'] ? form_submit('', 'Alterar', 'class="btn btn-sm btn-secondary"') .
                anchor('grupos', 'Novo', 'class="btn btn-sm btn-info"') :
                form_submit('', 'Cadastrar', 'class="btn btn-sm btn-success"')?></td>
        </tr>
        <?php foreach ($grupos as $gr) :?>
        <tr class="<?=($editar['id'] && $editar['id'] == $gr['id'])? 'odd' : ''?>">
            <td><?=$gr['tipo']?></td>
            <td><?=$gr['historico']?></td>
            <td><?=$gr['conta_despesa']?></td>
            <td><?=$gr['conta_liquidacao']?></td>
            <td><?=$gr['conta_banco']?></td>
            <td><?=$simnao[$gr['exportar_xml']]?></td>
            <td style="white-space: nowrap; width: 150px;">
                <?=anchor('grupos/' . $gr['id'], 'Editar', 'class="btn btn-sm btn-primary"')?>
            <?= anchor('excluir_grupo/' . $gr['id'], 'Excluir', [
            'onclick' => "return confirm('Você tem certeza que deseja excluir esse grupo?');",
            'class'=>'btn btn-sm btn-danger']) ?></td>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>