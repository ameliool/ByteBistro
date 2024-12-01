<?php
session_start();

include('conexao.php');

if(!isset($_SESSION['usuario'])){
    header('Location: login.php?erro=true');
    exit;
}

include ('verificarLogin.php');

// Variáveis para os filtros
$data_inicial = $_POST['data_inicial'] ?? '';
$data_final = $_POST['data_final'] ?? '';

// Construção da consulta com filtros dinâmicos
$sql = "SELECT * FROM todos_pedidos WHERE 1=1";

if (!empty($data_inicial)) {
    $sql .= " AND data_pedido >= '$data_inicial'";
}
if (!empty($data_final)) {
    $sql .= " AND data_pedido <= '$data_final'";
}

// Executar a consulta
$result = $mysqli->query($sql);

// Agrupar pedidos por ID Comanda e Número da Mesa
$pedidos_agrupados = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $key = $row['id_comanda'] . '-' . $row['numero_mesa'];
        if (!isset($pedidos_agrupados[$key])) {
            $pedidos_agrupados[$key] = [
                'id_comanda' => $row['id_comanda'],
                'numero_mesa' => $row['numero_mesa'],
                'data_pedido' => $row['data_pedido'],
                'produtos' => []
            ];
        }
        $pedidos_agrupados[$key]['produtos'][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos os Pedidos</title>
    <style>
       
       @font-face {
            font-family: "Archivo";
            src: url(fonts/Archivo-VariableFont_wdth\,wght.ttf);
        }

        .fonts {
            font-family: "Archivo";
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            font-family: "Archivo", sans-serif;
        }

        #btn-pedidos{
            margin-right: 5px;
            margin-top: 5px;
            background-color:rgba(255, 255, 255, 0.699);
            border-radius: 10px;
            cursor: pointer;
            padding: 10px;
            border: 1px solid black;
            font-size: 17px;
        }

        #btn-pedidos:hover{
            background-color: rgb(170, 170, 170);
        }

        .top-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #f4f4f4;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
      
        .filter-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .filter-form label {
            font-weight: bold;
        }
        .filter-form input[type="date"] {
            padding: 5px 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fff;
        }
        .filter-form button {
            padding: 5px 15px;
            font-size: 14px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }
        .filter-form button:hover {
            background-color: #0056b3;
        }
        .card-container {
            margin: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .card {
            border: 2px solid #000;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.02);
        }
        .card h2 {
            margin: 0 0 10px;
            font-size: 18px;
            color: #333;
        }
        .card p {
            margin: 5px 0;
            color: #555;
        }
        .card .total {
            font-weight: bold;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="top-section">
        <a href="pedidos.php"><button id="btn-pedidos">Pedidos</button></a>
        <div class="filter-form">
            <form method="POST" action="">
                <label>
                    Data Inicial:
                    <input type="date" name="data_inicial" value="<?= htmlspecialchars($data_inicial) ?>">
                </label>
                <label>
                    Data Final:
                    <input type="date" name="data_final" value="<?= htmlspecialchars($data_final) ?>">
                </label>
                <button type="submit">Filtrar</button>
            </form>
        </div>
    </div>

    <div class="card-container">
        <?php if (!empty($pedidos_agrupados)): ?>
            <?php foreach ($pedidos_agrupados as $pedido): ?>
                <div class="card">
                    <h2>Comanda: <?= htmlspecialchars($pedido['id_comanda']) ?> | Mesa: <?= htmlspecialchars($pedido['numero_mesa']) ?></h2>
                    <p><strong>Data do Pedido:</strong> <?= htmlspecialchars($pedido['data_pedido']) ?></p>
                    <h3>Produtos:</h3>
                        <?php foreach ($pedido['produtos'] as $produto): ?>
                                <?= htmlspecialchars($produto['quantidade']) ?>x <?= htmlspecialchars($produto['nome_produto']) ?>
                                - R$ <?= htmlspecialchars(number_format($produto['preco'], 2, ',', '.')) ?><br>
                        <?php endforeach; ?><br>
                    <p class="total"><strong>Total do Pedido:</strong> R$ 
                        <?= htmlspecialchars(number_format(array_sum(array_column($pedido['produtos'], 'valor_total')), 2, ',', '.')) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nenhum pedido encontrado.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Fechar a conexão com o banco de dados
$mysqli->close();
?>
