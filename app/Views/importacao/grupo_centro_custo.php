<style>
table, th, td {
  border: 1px solid;
  border-collapse: collapse
}
</style>
<table>
    <thead>
        <tr>
            <th>Grupo</th>
            <th>Conta despesa</th>
            <th>Tipo</th>
            <th>Centro de custos</th>
            <th>Soma</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($agrupado as $ag) : ?><tr>
            <td><?= $ag['nome_grupo'] ?></td>
            <td><?= $ag['conta_despesa'] ?></td>
            <td><?= $ag['tipo'] ?></td>
            <td><?= $ag['centrodecusto'] ?></td>
            <td style="text-align: right"><?= number_format($ag['soma'], 2, ',', '.') ?></td>
        </tr><?php endforeach;?>
    </tbody>
</table>