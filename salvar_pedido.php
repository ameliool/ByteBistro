<?php
// Conexão com o banco de dados
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'salvar_pedido') {
    $mesa = $_POST['mesa'];
    $itens = $_POST['itens']; // Array com os itens do carrinho
    $valor_total = $_POST['valor_total'];
    $data_pedido = $_POST['data_pedido'];

    try {
        // Inicia uma transação
        $pdo->beginTransaction();

        // Insere cada item do pedido na tabela `pedidos`
        $stmt = $pdo->prepare("INSERT INTO pedidos (nome_produto, quantidade, preco, valor_total, data_pedido) VALUES (?, ?, ?, ?, ?)");

        foreach ($itens as $item) {
            $nome_produto = $item['name'];
            $quantidade = $item['quantity'];
            $preco = $item['price'];
            
            $stmt->execute([$nome_produto, $quantidade, $preco, $valor_total, $data_pedido]);
        }

        // Confirma a transação
        $pdo->commit();
        
        echo json_encode(['status' => 'sucesso']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'erro', 'message' => $e->getMessage()]);
    }
}
?>
