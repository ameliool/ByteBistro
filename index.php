<?php

session_start();

include_once('conexao.php');

// Buscar os itens disponíveis na tabela 'itens'
$sql = "SELECT * FROM itens";
$result = $mysqli->query($sql);
$itens = $result->fetch_all(MYSQLI_ASSOC);

// Adicionar produto ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificando se os dados foram passados corretamente
    if (isset($_POST['nome_produto'], $_POST['quantidade'], $_POST['preco'])) {
        $nome_produto = $_POST['nome_produto'];
        $quantidade = $_POST['quantidade'];
        $preco = $_POST['preco'];
        $valor_total = $quantidade * $preco;
        $id_comanda = $_SESSION['id_comanda'];

        // Recuperar o número da mesa usando MySQLi
        $sql_num = "SELECT * FROM comanda WHERE id_comanda='$id_comanda'";
        $result_num = $mysqli->query($sql_num);
        $numero_mes = $result_num->fetch_assoc()['numero_mesa'];

        // Inserir no banco de dados (tabela 'pedidos') com MySQLi
        $sql = "INSERT INTO pedidos (nome_produto, quantidade, preco, valor_total, id_comanda, numero_mesa) 
                VALUES ('$nome_produto', '$quantidade', '$preco', '$valor_total', '$id_comanda', '$numero_mes')";
        
        if ($mysqli->query($sql)) {
            $status_message = "Item adicionado ao carrinho com sucesso!";
        } else {
            $status_message = "Erro ao adicionar o produto!";
        }
    } else {
        echo "";
    }
}

// Verifique se há uma comanda ativa
$comanda_aberta = false;
if (isset($_SESSION["id_comanda"])) {
    // Verifique se a comanda existe no banco de dados e está ativa
    $id_comanda = $_SESSION["id_comanda"];
    $sql_comanda = "SELECT * FROM comanda WHERE id_comanda = '$id_comanda' AND hora_fechar IS NULL";
    $result_comanda = $mysqli->query($sql_comanda);

    // Se encontrar uma comanda ativa, não exibe o modal
    if ($result_comanda->num_rows > 0) {
        $comanda_aberta = true;
    }
} 

// Verifique se o formulário foi enviado e se o cliente está criando uma nova comanda
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['nomeCliente'])) {
    $nomeCliente = $_POST['nomeCliente'];
    $mesaCliente = $_POST['numero_mesa'];
    $comandaId = rand(1,999);
    $_SESSION['id_comanda'] = $comandaId;

    // Verificar se a mesa existe e está disponível
    $sql_verificar_mesa = "SELECT * FROM mesas WHERE numero_mesa = $mesaCliente AND status = 'Disponível'";
    $result_mesa = $mysqli->query($sql_verificar_mesa);

    if ($result_mesa->num_rows > 0){
        $sql_criar_comanda = "INSERT INTO comanda (id_comanda, nome, numero_mesa, hora_abrir) 
                            VALUES ('$comandaId', '$nomeCliente', '$mesaCliente', NOW())";
    } else {
        header('Location: index.php');
    }

    if ($mysqli->query($sql_criar_comanda) === TRUE) {
        // Salvar o ID da nova comanda na sessão
        $_SESSION["id_comanda"] = $mysqli->insert_id;

        // Atualizar o status da mesa para ocupada
        $sql_atualizar_mesa = "UPDATE mesas SET status = 'Ocupada' WHERE numero_mesa = '$mesaCliente'";
        $mysqli->query($sql_atualizar_mesa);

        // Redirecionar para a página de mesas ou onde o cliente deve ser redirecionado
        header("Location: index.php");
        exit();
    } else {
        echo "Erro ao criar comanda: " . $mysqli->error;
    }
}

// Processamento de envio de arquivo e inserção na tabela 'itens' com MySQLi
if (isset($_FILES['foto'])) {

    $foto = $_FILES['foto'];
    $nome = $_POST['nome'];
    $valor = $_POST['valor'];
    $descricao = $_POST['descricao'];
    if ($foto['error'])
        die('Falha ao enviar arquivo');
    

    $pasta = "arquivos/";
    $nomearq = $foto['name'];
    $novonome = uniqid();
    $extensao = strtolower(pathinfo($nomearq, PATHINFO_EXTENSION));
    if ($extensao != "jpg" && $extensao != "png")
        die("Tipo de arquivo não aceito");
    $path = $pasta . $novonome . "." . $extensao;

    $deucerto = move_uploaded_file($foto["tmp_name"], $path);
    if ($deucerto) {
        $result = $mysqli->query("INSERT INTO itens (nome, valor, descricao, foto) VALUES ('$nome', '$valor', '$descricao', '$path')");
    } else {
        echo "Falha ao enviar arquivo";
    }
}

// Consultas para diferentes categorias de itens com MySQLi
$sql_query = $mysqli->query("SELECT * FROM itens WHERE categoria='pratos'") or die($mysqli->error);
$sql_bebida = $mysqli->query("SELECT * FROM itens WHERE categoria='bebidas'") or die($mysqli->error);
$sql_sobremesa = $mysqli->query("SELECT * FROM itens WHERE categoria='sobremesas'") or die($mysqli->error);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Byte Bistro - O melhor sistema para o seu restaurante</title>
    <link rel="stylesheet" href="styleIndex3.css">
    <link rel="shortcut icon" href="imagens/favicon.ico">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="notify.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <!--INICIO MODAL CLIENTE-->
    <?php if (!$comanda_aberta): ?>
    <form method="post">
        <div class="modal-cliente">
            <div class="bg-white p-3" id="conteudo-modal-cliente">
                <input type="text" id="input-enc" name="nomeCliente" placeholder="Nome do Cliente"><br><br>
                <input type="text" id="input-enc" name="numero_mesa" placeholder="Número da Mesa"><br><br>
                <button type="submit" id="enviar-nome-cliente">Continuar</button>
            </div>
        </div>
    </form>
<?php endif; ?>
    <!--FIM MODAL CLIENTE-->
    

    <!-- INICIO HEADER-->
    <header>
        <div id="banner">
            <div id="logo">
                <img src="imagens/logobytebistro2.png" alt="" id="img-logo">
            </div>
        </div>
    </header>
    <!-- INICIO HEADER-->

    <!-- INICIO NAVBAR-->
    <section class="fonts">
        <nav class="navbar navbar-expand-sm navbar-dark fixed-bottom-sm" id="navbar-top">
            <div class="collapse navbar-collapse" id="nav-principal">
                <ul class="navbar-nav">
                    <li class="nav-item ml-3">
                        <a href="#pratos" class="nav-link active navbar-produtos"><b>Pratos</b></a>
                    </li>
                    <li class="nav-item ml-3">
                        <a href="#bebidas" class="nav-link active"><b>Bebidas</b></a>
                    </li>
                    <li class="nav-item ml-3">
                        <a href="#sobremesas" class="nav-link active"><b>Sobremesas</b></a>
                    </li>
                </ul>
            </div>
            <a href="admin.php">
                <button id="btn-admin">Admin</button>
            </a>
        </nav>
    </section>

    <section class="fonts">
        <nav class="navbar navbar-expand navbar-dark fixed-bottom-sm" id="navbar-bot">
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav">
                    <li class="nav-item ml-3">
                        <a href="#pratos" class="nav-link active navbar-produtos"><b><i class="fa-solid fa-utensils icons-cat"></i></b></a>
                    </li>
                    <li class="nav-item ml-3">
                        <a href="#bebidas" class="nav-link active"><b><i class="fa-solid fa-wine-glass icons-cat"></i></b></a>
                    </li>
                    <li class="nav-item ml-3">
                        <a href="#sobremesas" class="nav-link active"><b><i class="fa-solid fa-ice-cream icons-cat"></i></b></a>
                    </li>
                </ul>
            </div>
            <button type="button" id="btn-cart">
                <a href="visualizar_pedido.php"><i class="fa-solid fa-cart-shopping icon-car"></i></a>
            </button>
        </nav>
    </section>
    <!-- FIM NAVBAR-->
     <br>

    <!-- INICIO SESSÃO PRODUTOS -->
        
        <div class="container-fluid ml-3 mb-5 fonts" id="menu2">
            <h2 id="pratos" class="title-cat">Pratos</h2>
            <br>
    
            <div class="row flex-wrap">
                <br>
                <?php
                while ($foto = $sql_query->fetch_assoc()) {
                ?>
                    <div class="col-md-6 my-2">
                        <div class="row">
                            <img height="150px" width="150px" src="<?php echo $foto['foto']; ?>" alt="" srcset="" class="fotos-produtos">
                            <div class="descricao-produtos">
                               <input type="hidden" name="nome_produto"> <h4><?php echo $foto['nome']; ?> </h4> </input>
                                <p class="p-desc"><?php echo $foto['descricao']; ?></p>
                                <div class="align-div">
                                <p class="p-valor"><b><?php echo $foto['valor']; ?></b></p>
                                <form method="POST" class="form-car">
                                  <input type="hidden" name="nome_produto" value="<?php echo htmlspecialchars($foto['nome']); ?>">
                                  <input type="hidden" name="preco" value="<?php echo htmlspecialchars($foto['valor']); ?>">
                                  <input class="input-qtd" type="number" name="quantidade" value="1" min="1" required>
                                  <button class="add-carrinho" id="btn-add-car" type="submit"><i class="fa-solid fa-cart-shopping"></i></button>
                                </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        
        <br>
        <hr>
        <div class="container-fluid mb-5 ml-3 fonts" id="menu3">
            <h2 id="bebidas" class="title-cat">Bebidas</h2>
            <br>
            <div class="row flex-wrap">
                <br>
                <?php
                while ($bebida = $sql_bebida->fetch_assoc()) {
                ?>
                    <div class="col-md-6 my-2">
                        <div class="row">
                            <img height="150px" width="150px" src="<?php echo $bebida['foto']; ?>" alt="" srcset="" class="fotos-produtos">
                            <div class="descricao-produtos">
                                <h4><?php echo $bebida['nome']; ?> </h4>
                                <p class="p-desc"><?php echo $bebida['descricao']; ?></p>
                                <div class="align-div">
                                <p class="p-valor"><b><?php echo $bebida['valor']; ?></b></p>
                                <form method="POST" class="form-car">
                                  <input type="hidden" name="nome_produto" value="<?php echo htmlspecialchars($bebida['nome']); ?>">
                                  <input type="hidden" name="preco" value="<?php echo htmlspecialchars($bebida['valor']); ?>">
                                  <input class="input-qtd" type="number" name="quantidade" value="1" min="1" required>
                                  <button class="add-carrinho" type="submit"><i class="fa-solid fa-cart-shopping"></i></button>
                                </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        
        <br>
        <hr>
        <div class="container-fluid ml-3 mb-5 fonts" id="menu">
            <h2 id="sobremesas" class="title-cat">Sobremesas</h2>
            <br>
            <div class="row flex-wrap">
                <br>
                <?php
                while ($sobremesa = $sql_sobremesa->fetch_assoc()) {
                ?>
                    <div class="col-md-6 my-2">
                        <div class="row">
                            <img height="150px" width="150px" src="<?php echo $sobremesa['foto']; ?>" alt="" srcset="" class="fotos-produtos">
                            <div class="descricao-produtos">
                                <h4><?php echo $sobremesa['nome']; ?> </h4>
                                <p class="p-desc"><?php echo $sobremesa['descricao']; ?></p>
                                <div class="align-div">
                                <p class="p-valor"><b><?php echo $sobremesa['valor']; ?></b></p>
                                <form method="POST" class="form-car">
                                  <input type="hidden" name="nome_produto" value="<?php echo htmlspecialchars($sobremesa['nome']); ?>">
                                  <input type="hidden" name="preco" value="<?php echo htmlspecialchars($sobremesa['valor']); ?>">
                                  <input class="input-qtd" type="number" name="quantidade" value="1" min="1" required>
                                  <button class="add-carrinho" type="submit"><i class="fa-solid fa-cart-shopping"></i></button>
                                </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <!-- FIM SESSÃO PRODUTOS -->



        <dialog id="modal-pedido-realizado">
            
            <img src="imagens/certo.png" alt="" width="300px" id="img-certo">
            <h2>Pedido enviado com sucesso!</h2>
            
            
            <button type="button" id="btn-fechar-pr">Fechar</button>

        </dialog>

        <!-- INICIO FOOTER-->
        <footer class="w-100 py-2 fixed-bottom" id="footer">
            <button type="button" id="btn-cart2">
                <a href="visualizar_pedido.php"><i class="fa-solid fa-cart-shopping icon-car"></i></a>
            </button>
        </footer>

        <!--FIM FOOTER-->

        

        <script src="index20.js"></script>

        <script>
            // Verificar se a variável PHP $status_message existe
            <?php if (isset($status_message)): ?>
                $.notify("<?php echo $status_message; ?>", {
                    globalPosition: 'top',
                    className: 'success', // ou 'error' se for uma falha
                    style: 'bootstrap'
                });
            <?php endif; ?>
        </script>



</body>

</html>