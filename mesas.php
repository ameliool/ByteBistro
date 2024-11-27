<?php

session_start();
// Conexão com o banco de dados
include_once('conexao.php');

if(isset($_GET['deletar'])){

    $id = intval($_GET['deletar']);
    $most = $conn->query("SELECT * FROM mesas") or die($conn->error);
    $hoje = $most->fetch_assoc();      

    $deu_certo =  $conn->query("DELETE FROM mesas WHERE numero_mesa='$id'") or die($conn->error);
    if($deu_certo){
        header('Location: mesas.php');
    }
}

/*
if (isset($_POST['numeroMesaOcupada'])) {
    $numero_mesa = $_POST['numeroMesaOcupada'];

    // Atualizar a hora de fechamento da comanda
    $sql_atualizar_comanda = "UPDATE comanda SET hora_fechar = NOW() WHERE numero_mesa = $numero_mesa AND hora_fechar IS NULL";
    
    if ($conn->query($sql_atualizar_comanda) === TRUE) {
        // Atualizar o status da mesa para "disponível"
        $sql_atualizar_mesa = "UPDATE mesas SET status = 'Disponível' WHERE numero_mesa = $numero_mesa";
        
        if ($conn->query($sql_atualizar_mesa) === TRUE) {
            // Limpa a sessão da comanda
            unset($_SESSION['id_comanda']);  // Limpa a variável de sessão para permitir que o modal apareça novamente
            echo "Comanda fechada com sucesso!";
        } else {
            echo "Erro ao atualizar o status da mesa: " . $conn->error;
        }
    } else {
        echo "Erro ao fechar comanda: " . $conn->error;
    }
}*/

if (isset($_POST['fechar_comanda'])) {
    $id_comanda = $_SESSION['id_comanda']; // Pega o ID da comanda da sessão

    // Fechar a comanda no banco de dados
    $sql_fechar_comanda = "UPDATE comanda SET hora_fechar = NOW() WHERE id_comanda = '$id_comanda'";
    if ($conn->query($sql_fechar_comanda) === TRUE) {
        // Liberar a mesa
        $numero_mesa = $_POST['numeroMesaOcupada'];
        $sql_libera_mesa = "UPDATE mesas SET status = 'Disponível' WHERE numero_mesa = '$numero_mesa'";
        $conn->query($sql_libera_mesa);

        // Limpar a variável de sessão para permitir a exibição do modal de cliente novamente
        unset($_SESSION["id_comanda"]);

        // Redirecionar ou exibir uma mensagem de sucesso
        echo "";
    } else {
        echo "Erro ao fechar comanda: " . $conn->error;
    }
}


if (isset($_POST['numeroMesa']) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $numero = $_POST['numeroMesa'];
    $status = 'Disponível'; // Status padrão

    // Verifica a conexão
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    // Insere os dados na tabela mesas
    $sql = "INSERT INTO mesas (numero_mesa, status) VALUES (?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $numero, $status);

    if ($stmt->execute()) {
        header("Location: mesas.php"); // Redireciona para o formulário
        exit;
    } else {
        echo "Erro ao cadastrar mesa: " . $stmt->error;
    }

    $stmt->close();
}

$exib = $conn->query("SELECT * FROM mesas") or die($conn->error);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleMesas44.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <title>Byte Bistro - Mesas</title>
</head>
<body>
<div class="fonts">
        <a href="admin.php"><button id="btn-pedidos">Admin</button></a>
    </div>
    <header id="logo">
        <img src="imagens/logobb.png" alt="" width="500px">
    </header>

    <h1>Lista de Mesas</h1>
    <div id="div-add-mesa">
        <button id="btn-add-mesa">Adicionar Mesa</button>
    </div>

    <dialog id="add-mesa">
        <form class="form-inline" enctype="multipart/form-data" action="" method="post">
            <label class="" for="numeroMesa">Número da Mesa: </label>
            <input type="number" id="numeroMesa" name="numeroMesa" class="form-control ml-3 mr-3">
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
    </dialog>

    <div class="container-fluid mt-3" id="menu-mesas">
        <div class="row">
            <br>
            <?php
            while ($agora = $exib->fetch_assoc()) {
                $numero_mesa = $agora['numero_mesa'];
                $status = $agora['status'];
                if ($status == 'Ocupada') {
                    $cor_fundo = 'background-color: rgb(201, 13, 0); color: black;';
                } else {
                    $cor_fundo = 'background-color: rgb(23, 201, 0); color: black;';
                }
        
            ?>
                <div class="col-md-3 my-2 mr-1 mb-3" id="card-mesa" style="<?php echo $cor_fundo; ?>">
                    <div class="row">
                        <div id="descricao-card">
                            <h3>Mesa <?php echo $agora['numero_mesa']; ?></h3>
                            <br>
                            <div>
                                <p id="status-mesa"><b><?php echo $agora['status']; ?></b> </p>
                                <br>
                                <?php
                                if ($status == 'Ocupada') {
                                    echo "<form action='' method='POST'>
                                    <input type='hidden' name='numeroMesaOcupada' value='$numero_mesa'>
                                    <button id='btn-fechar-comanda' name='fechar_comanda' type='submit'><b>Fechar Comanda</b></button>
                                    </form>";
                                }
                                ?>
                                <button id="deletar-mesa" type="submit" class="mt-2"> <a id="link-del" href="mesas.php?deletar=<?php echo $agora['numero_mesa']; ?>"> <b>Deletar Mesa</b> </a></button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>

    <script src="mesas.js"></script>
</body>
</html>
