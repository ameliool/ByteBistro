<?php
// Inclui o arquivo de conexão
include 'conexao.php';

// Excluir pedido quando o botão for clicado
if (isset($_POST['excluir'])) {
    $id_comanda = $_POST['id_comanda'];
    $delete_sql = "DELETE FROM pedidos_finalizados WHERE id_comanda = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $id_comanda);
    $stmt->execute();
    $stmt->close();

}

// Consulta SQL para listar produtos por comanda e mostrar o número da mesa
$sql = "SELECT id_comanda, numero_mesa,
               GROUP_CONCAT(CONCAT(quantidade, 'x ', nome_produto) SEPARATOR '\n') AS produtos, 
               SUM(valor_total) AS total_preco
        FROM pedidos_finalizados
        GROUP BY id_comanda, numero_mesa";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stylePedidos2.css">
    <title>Byte Bistro - Pedidos</title>
</head>
<body>
<div class="fonts">
        <a href="admin.php"><button id="btn-pedidos">Admin</button></a>
        <a href="todos_pedidos.php"><button id="btn-cliente">Todos Pedidos</button></a>
    </div>
    <br>

    <?php
    if ($result->num_rows > 0) {
    // Contêiner flexível para pedidos lado a lado
    echo '<div class="pedido-wrapper">';
    while($row = $result->fetch_assoc()) {
        ?>
        <div class="pedido-container">
            <b><h2>Comanda: <?php echo $row["id_comanda"]; ?> | Mesa: <?php echo $row["numero_mesa"]; ?></h2></b>
        
            <div class="produtos">
                <h3>Produtos:</h3>
                <?php echo nl2br($row["produtos"]); ?>
            </div>

            <p class="preco-total"><strong>Total do Pedido: R$</strong><?php echo $row["total_preco"]; ?></p>

            <!-- Formulário com botão "Pedido feito" para excluir o pedido -->
            <form method="post" action="">
                <input type="hidden" name="id_comanda" value="<?php echo $row["id_comanda"]; ?>">
                <button id="bot" type="submit" name="excluir">Pedido feito</button>
            </form>
        </div>
        <?php
    }
    echo '</div>'; // Fechar o contêiner flexível
} else {
    echo "Nenhuma pedido encontrado.";
}
$conn->close();
?>




</body>
</html>

