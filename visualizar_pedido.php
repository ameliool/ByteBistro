<?php
session_start();

// Incluir a conexão com o banco de dados
include('conexao.php');

// Remover item do carrinho
if (isset($_GET['remover'])) {
    $id = $_GET['remover'];
    $sql = "DELETE FROM pedidos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id); // 'i' indica um inteiro
    $stmt->execute();
    echo "";
}

// Finalizar pedido (mover os itens para pedidos_finalizados)
if (isset($_POST['finalizar_pedido'])) {
    // Transação para mover os itens para pedidos_finalizados
    try {
        $pdo->beginTransaction();

        // Buscar os produtos do carrinho
        $sql = "SELECT * FROM pedidos";
        $stmt = $pdo->query($sql);
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mover os produtos para pedidos_finalizados
        foreach ($pedidos as $pedido) {
            $sql = "INSERT INTO pedidos_finalizados (nome_produto, quantidade, preco, valor_total, id_comanda,numero_mesa) 
                    VALUES (:nome_produto, :quantidade, :preco, :valor_total, :id_comanda, :numero_mesa)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome_produto', $pedido['nome_produto']);
            $stmt->bindParam(':quantidade', $pedido['quantidade']);
            $stmt->bindParam(':preco', $pedido['preco']);
            $stmt->bindParam(':valor_total', $pedido['valor_total']);
            $stmt->bindParam(':id_comanda', $pedido['id_comanda']);
            $stmt->bindParam(':numero_mesa', $pedido['numero_mesa']);
            $stmt->execute();
        }

        foreach ($pedidos as $pedido) {
            $sql = "INSERT INTO todos_pedidos (nome_produto, quantidade, preco, valor_total, id_comanda,numero_mesa) 
                    VALUES (:nome_produto, :quantidade, :preco, :valor_total, :id_comanda, :numero_mesa)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome_produto', $pedido['nome_produto']);
            $stmt->bindParam(':quantidade', $pedido['quantidade']);
            $stmt->bindParam(':preco', $pedido['preco']);
            $stmt->bindParam(':valor_total', $pedido['valor_total']);
            $stmt->bindParam(':id_comanda', $pedido['id_comanda']);
            $stmt->bindParam(':numero_mesa', $pedido['numero_mesa']);
            $stmt->execute();
        }

        // Limpar o carrinho (remover todos os itens da tabela pedidos)
        $sql = "DELETE FROM pedidos";
        $pdo->exec($sql);

        $pdo->commit();
        $pedido_realizado = true; // Indica que o pedido foi realizado

        //header('Location: index.php');
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Erro ao finalizar o pedido: " . $e->getMessage();
    }
}

$id = $_SESSION['id_comanda'];

print_r($id);

$sql = "SELECT * FROM pedidos WHERE id_comanda='$id' ORDER BY data_pedido DESC";
$stmt = $pdo->query($sql);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Calcular o valor total
$total_compra = 0;
foreach ($pedidos as $pedido) {
    $total_compra += $pedido['valor_total'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleVP2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Carrinho</title>
</head>
<body>
    
    <div class="cart-card">
    <div class="cart">
        <a id="voltar-cardapio" href="index.php"><i class="fa-solid fa-arrow-left"></i></a>
    </div>
    <h2>Carrinho</h2>
    <?php if ($pedidos): ?>
        <?php foreach ($pedidos as $pedido): ?>
        <ul class="cart-items">
            <li class="cart-item">
                <div id="nome-item"><span class="item-name"><?php echo htmlspecialchars($pedido['nome_produto']); ?></span></div>
                <div><span class="item-price">(<?php echo htmlspecialchars($pedido['quantidade']); ?>)</span></div>
                <div><span class="item-price">R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></span></div>
                <button class="remove-item"><a href="?remover=<?php echo $pedido['id']; ?>">Remover</a></button>
            </li>
        </ul>
        <?php endforeach; ?>
        <div class="total">
            <span>Total:</span>
            <span class="total-price">R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></span>
        </div>
        <form method="POST">
            <input class="checkout-btn" type="submit" name="finalizar_pedido" value="Finalizar Pedido">
        </form>
    <?php else: ?>
        <p>Nenhum produto foi adicionado ao carrinho.</p>
    <?php endif; ?>
  </div>

  <!-- Modal de Sucesso -->
  <?php if (isset($pedido_realizado) && $pedido_realizado): ?>
    <div class="modal" id="successModal">
        <div class="modal-content">
            <span class="close-btn" id="closeBtn">&times;</span>
            <img src="imagens/certo2.png" alt="" width="300px" id="img-certo">
            <h3>Pedido realizado com sucesso!</h3>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // Código JavaScript para abrir e fechar o modal
        document.addEventListener('DOMContentLoaded', function () {
            // Abre o modal se o pedido foi realizado com sucesso
            if (document.getElementById('successModal')) {
                const modal = document.getElementById('successModal');
                const closeBtn = document.getElementById('closeBtn');
                
                modal.style.display = "block"; // Exibe o modal

                // Quando o usuário clica no botão de fechar (X), o modal é fechado
                closeBtn.addEventListener('click', function () {
                    modal.style.display = "none";
                    // Redireciona para index.php após fechar o modal
                    window.location.href = 'index.php';
                });

                // Fecha o modal se o usuário clicar fora da área do modal
                window.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        modal.style.display = "none";
                        // Redireciona para index.php após fechar o modal
                        window.location.href = 'index.php';
                    }
                });
            }
        });
    </script>
    
</body>
</html>
