<?php 
    include_once('conexao.php');

  
    if(isset($_GET['deletar'])){

        $id = intval($_GET['deletar']);
        $sql_query = $mysqli->query("SELECT * FROM itens WHERE id='$id'") or die($mysqli->error);
        $foto = $sql_query->fetch_assoc();
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>



    <h2>Deseja mesmo Deletar este item ?</h2>

    <?php

   

    $id = intval($_GET['del']);
    $sql_query = $mysqli->query("SELECT * FROM itens WHERE id='$id'") or die($mysqli->error);
    $foto = $sql_query->fetch_assoc();


  
  
    
    ?>

    <img src="<?php echo $foto['foto']; ?>" alt=""> <br>
  <h1><?php echo $foto['nome']; ?> <br> </h1>
 <a href="admin.php?deletar=<?php echo $foto['id']; ?>"> <button>Sim</button></a>
<button>Não</button>

</body>
</html>