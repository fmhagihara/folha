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
<table border="1">
    <thead>
        <tr>
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
        <tr>
            <td><?=form_dropdown('tipo', $tipos, $editar['tipo'])?></td>
            <td><?=form_input('historico', $editar['historico'], 'size="50"')?></td>
            <td><?=form_input('conta_despesa', $editar['conta_despesa'])?></td>
            <td><?=form_input('conta_liquidacao', $editar['conta_liquidacao'])?></td>
            <td><?=form_input('conta_banco', $editar['conta_banco'])?></td>
            <td><?=form_dropdown('exportar_xml', $simnao, $editar['exportar_xml'])?></td>
            <td><?=$editar['id'] ? form_submit('', 'Alterar') . ' ' . anchor('grupos', 'Novo') : form_submit('', 'Cadastrar')?></td>
        </tr>
        <?php foreach ($grupos as $gr) :?>
        <tr class="<?=($editar['id'] && $editar['id'] == $gr['id'])? 'odd' : ''?>">
            <td><?=$gr['tipo']?></td>
            <td><?=$gr['historico']?></td>
            <td><?=$gr['conta_despesa']?></td>
            <td><?=$gr['conta_liquidacao']?></td>
            <td><?=$gr['conta_banco']?></td>
            <td><?=$simnao[$gr['exportar_xml']]?></td>
            <td><?=anchor('grupos/' . $gr['id'], 'Editar')?> /
            <?= anchor('excluir_grupo/' . $gr['id'], 'Excluir', [
            'onclick' => "return confirm('Você tem certeza que deseja excluir esse grupo?');"
            ]) ?></td>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>