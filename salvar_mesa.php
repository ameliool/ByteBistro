<?php
// Incluir a conexão com o banco de dados
include('conexao.php');

// Verificar se os dados foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numero = $_POST['numeroMesa'];

    // Preparar o SQL para inserir a nova mesa
    $sql = "INSERT INTO mesas (numero, status) VALUES (?, 'disponível')";

    // Preparar a declaração
    if ($stmt = $mysqli->prepare($sql)) {
        // Bind dos parâmetros (número da mesa)
        $stmt->bind_param("i", $numero);

        // Executar a consulta
        if ($stmt->execute()) {
            echo "Mesa cadastrada com sucesso!";
        } else {
            echo "Erro ao cadastrar mesa: " . $stmt->error;
        }

        // Fechar a declaração
        $stmt->close();
    } else {
        echo "Erro na preparação da consulta: " . $mysqli->error;
    }
}

// Fechar a conexão
$mysqli->close();
?>
