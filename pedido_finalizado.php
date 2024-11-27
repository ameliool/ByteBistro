<?php
// Incluir a conexão
include('conexao.php');

// Buscar os pedidos finalizados
$sql = "SELECT * FROM pedidos_finalizados ORDER BY data_pedido DESC";
$result = $mysqli->query($sql);

// Verificar se a consulta retornou resultados
if ($result) {
    $pedidos_finalizados = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Erro na consulta: " . $mysqli->error;
}

// Calcular o valor total
$total_compra = 0;
foreach ($pedidos_finalizados as $pedido) {
    $total_compra += $pedido['valor_total'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Finalizado</title>
</head>
<body>
    <h1>Pedido Finalizado</h1>

    <?php if ($pedidos_finalizados): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Número Comanda</th>
                    <th>Nome do Produto</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Valor Total</th>
                    <th>Data do Pedido</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos_finalizados as $pedido): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pedido['id_comanda']); ?></td>
                        <td><?php echo htmlspecialchars($pedido['nome_produto']); ?></td>
                        <td><?php echo htmlspecialchars($pedido['quantidade']); ?></td>
                        <td>R$ <?php echo number_format($pedido['preco'], 2, ',', '.'); ?></td>
                        <td>R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></td>
                        <td><?php echo $pedido['data_pedido']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><strong>Valor Total: R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></strong></p>
    <?php else: ?>
        <p>Não há pedidos finalizados.</p>
    <?php endif; ?>

    <a href="index.php">Voltar ao Carrinho</a>
</body>
</html>
