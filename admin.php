<?php

    session_start();

    if(!isset($_SESSION['usuario'])){
        header('Location: login.php?erro=true');
        exit;
    }

    include ('verificarLogin.php');

    include_once('conexao.php');



    if(isset($_GET['deletar'])){

        $id = intval($_GET['deletar']);
        $sql_query = $mysqli->query("SELECT * FROM itens WHERE id='$id'") or die($mysqli->error);
        $foto = $sql_query->fetch_assoc();


        if(unlink($foto['foto'])){
         $deu_certo =  $mysqli->query("DELETE FROM itens WHERE id='$id'") or die($mysqli->error);
         if($deu_certo){
            echo "<p> arquivo excluido com sucesso!</p>";   
            header('Location: admin.php');
            exit;
         }
        }

        

        
    }
    

    if(isset($_FILES['foto'])){
        
        $foto = $_FILES['foto'];
        $nome = $_POST['nome'];
        $categoria = $_POST['id_categoria'];
        $valor = $_POST['valor'];
        $descricao = $_POST['descricao'];
    

        if($foto['error'])
        die('erro ao enviar arquivo');

        $pasta = "arquivos/";
        $nomearq = $foto['name'];
        $novonome = uniqid();
        $extensao = strtolower(pathinfo($nomearq, PATHINFO_EXTENSION));
        if($extensao != "jpg" && $extensao != "png")
            die("Tipo de arquivo não aceito");
        $path = $pasta . $novonome . "." . $extensao;
        
        $deucerto = move_uploaded_file($foto["tmp_name"], $path);
        if($deucerto){
            $result = mysqli_query($mysqli, "INSERT INTO itens (nome,valor,descricao,categoria,foto) VALUES ('$nome', '$valor',  '$descricao','$categoria', '$path') ");
        }
        else {
        echo "falha ao enviar arquivo";
        }

       
    }



    /* 
    include_once('conexao.php');
    $nome = $_POST['nome'];
     $valor = $_POST['valor'];
     $descricao = $_POST['descricao'];
     $foto = $_FILES['foto'];

     $result = mysqli_query($mysqli, "INSERT INTO itens (nome,valor,descrição,foto) VALUES ('$nome', '$valor', '$descricao', '$foto')"); */
    $sql_query = $mysqli->query("SELECT * FROM itens") or die($mysqli->error);

    $categoriasId = $mysqli->query("SELECT *FROM itens where categoria = 'bebida'");
?>
    
    



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Byte Bistro - Administrador</title>
    <link rel="stylesheet" href="styleAdm2.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

    <!-- INICIO HEADER-->
    <div class="fonts">
        <a href="index.php"><button id="btn-cliente">Página Cliente</button></a>
        <a href="pedidos.php"><button id="btn-pedidos">Pedidos</button></a>
        <a href="mesas.php"><button id="btn-mesas">Mesas</button></a>
        <a href="logout.php"><button id="btn-sair">Sair</button></a>        
    </div>
    <header id="logo">
        <img src="imagens/logobb.png" alt="" width="500px">
    </header>

    <div id="adicionar-produto" class="fonts">
        <button id="btn-add-produto">
            Adicionar produto
        </button>
    </div>

    <!-- FIM HEADER-->


    <div id="secao-principal" class="fonts">

        <br><br>
           
            <dialog id="modal-add-produto">

            
                <h2>Adicionar Produto</h2>
                <br>
                <form enctype="multipart/form-data" action="" method="POST">
                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <input type="text" class="form-control" id="nome" placeholder="Nome do produto" name="nome">
                    </div>
                    <div class="form-group">
                        <label for="id_categoria">Categoria</label>
                        <select name="id_categoria" id="id_categoria" class="form-control">
                            <option value="">...</option>
                            <option value="pratos">Pratos</option>
                            <option value="bebidas">Bebidas</option>
                            <option value="sobremesas">Sobremesa</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="valor">Valor</label>
                        <input type="number" step="0.010" class="form-control" id="valor" placeholder="Valor do produto" name="valor">
                    </div>
                    <div class="form-group">
                        <label for="descricao">Descrição</label><br>
                        <textarea name="descricao" id="descricao" cols="40" rows="5" class="form-group"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="foto">Foto: </label>
                        <input type="file" class="form-control-file" id="foto" name="foto">
                    </div>
                    <button type="submit" name="submit" id="submit" class="btn btn-white border-secondary">Enviar</button>
                    <button type="submit" id="btn-fechar" class="btn btn-white border-secondary">Fechar</button>
                </form> 
                
      
            </dialog>

    <!-- EDITAR MODAL -->

            
            <dialog id="modal-edt-produto" class="fonts">
            
                <h2>Editar Produto</h2>
                <br><br>
                <form enctype="multipart/form-data" action="" method="POST">

                    <label for=""><b>Nome: </b></label><br>
                    <input type="text" name="nome" value="<?php echo $foto['nome']; ?>"> <br><br>

                    <label for=""><b>Valor: </b></label><br>
                    <input type="number" step="0.010" name="valor" value="<?php echo $foto['valor']; ?>"><br><br>

                    <label for="descricao"><b>Descrição: </b></label> <br> 
                    <textarea name="descricao" id="descricao" cols="40" rows="5" value="<?php echo $foto['descricao']; ?>"></textarea><br><br>

                    <label for=""><b>Foto: </b></label><br>
                    <input type="file" name="foto" value="<?php echo $foto['foto']; ?>"><br>

                    <br>

                <input type="submit" name="submit" id="submit" class="fonts">
                </form>
    
                
                <button id="btn-fechar2">Fechar</button>
            </dialog>
            

            
            <table class="table table-hover fonts">
                <thead id="thead">
                    <th class="titulo">#</th>
                    <th class="titulo">FOTO</th>
                    <th class="titulo">NOME</th>
                    <th class="titulo">CATEGORIA</th>
                    <th class="titulo">DESCRIÇÃO</th>
                    <th class="titulo">VALOR</th>
                    <th class="titulo">EDITAR</th>
                    <th class="titulo">EXCLUIR</th>
                </thead>
                <tbody>

                    <?php
                    while ($foto = $sql_query->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $foto['id']; ?></td>
                        <td><img height="150px" width="150px" src="<?php echo $foto['foto']; ?>" alt="" srcset=""></td>
                        <td><?php echo $foto['nome']; ?> </td>
                        <td><?php echo $foto['categoria']; ?> </td>
                        <td id="descricao-th"><?php echo $foto['descricao']; ?></td>
                        <td><?php echo $foto['valor']; ?></td>
                        <td><a href="edit.php?id=<?php echo $foto['id']; ?>" ><button class="icons"><i class="fa-solid fa-pencil"></i></a></td>
                        <td><a href="admin.php?deletar=<?php echo $foto['id']; ?>" ><button class="icons"><i class="fa-solid fa-trash"></i></button></a></td>
                    </tr>

                    <?php
                    }
                    ?>
                </tbody>

            </table> 

            <br><br><br>
        
        </div>
    </div>

   

    <br><br>
    
    
    <script src="admin2.js"></script> 


</body>
</html>