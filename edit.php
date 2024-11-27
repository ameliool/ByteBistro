<?php
// Verificar se o parâmetro 'id' está presente na URL
if(!empty($_GET['id'])) {
    include_once('conexao.php');

    $id = $_GET['id'];

    // Consulta para pegar os dados do produto baseado no id
    $mysql = "SELECT * FROM itens WHERE id=$id";
    $result = $mysqli->query($mysql);

    if($result->num_rows > 0){
        while($user_data = mysqli_fetch_assoc($result)){
            $nome = $user_data['nome'];
            $valor = $user_data['valor'];
            $descricao = $user_data['descricao'];
            $id_categoria = $user_data['categoria'];  // Categoria do produto
        }
    } else {
        header('Location: admin.php'); // Redirecionar se não encontrar o produto
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="styleEdit22.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <title>Editar Produto</title>
</head>
<body id="body">
    <div id="modal-add-produto">
        <h2>Editar Produto</h2>
        <br><br>
        <form enctype="multipart/form-data" action="save.php" method="POST">

            <div class="form-group">
                <label for=""><b>Nome: </b></label><br>
                <input class="form-control" type="text" name="nome" value="<?php echo $nome ?>">
            </div>

            <div class="form-group">
                <label for="categorias"><b>Categoria</b></label>
                <select class="form-control" id="id_categoria" name="id_categoria">
                    <!-- Categoria registrada aparece como valor, mas não será visível nas opções -->
                    <option value="<?php echo $id_categoria ?>" selected hidden><?php echo ucfirst($id_categoria); ?></option>

                    <!-- Exibindo as outras opções -->
                    <?php
                    $categorias = ['bebidas', 'pratos', 'sobremesas'];
                    foreach ($categorias as $categoria) {
                        if ($categoria != $id_categoria) {
                            echo "<option value=\"$categoria\">".ucfirst($categoria)."</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for=""><b>Valor: </b></label><br>
                <input class="form-control" type="number" step="0.010" name="valor" value="<?php echo $valor ?>">
            </div>

            <div class="form-group">
                <label for="descricao"><b>Descrição: </b></label> <br> 
                <textarea class="form-control" name="descricao" id="descricao" cols="40" rows="5"><?php echo $descricao ?></textarea>
            </div>

            <div class="form-group">
                <label for="foto"><b>Foto: </b></label>
                <input type="file" class="form-control-file" id="foto" name="foto">
            </div>

            <input type="hidden" name="id" value="<?php echo $id ?>">
            <input type="submit" name="update" id="update">
        </form>

        <a href="admin.php"><button id="btn-fechar2">Fechar</button></a>
    </div>          
</body>
</html>
