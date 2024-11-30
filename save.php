<?php  
include_once('conexao.php');

if(isset($_FILES['foto'])){
  $id = $_POST['id'];     
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
      $result = mysqli_query($mysqli, "UPDATE itens SET nome='$nome', valor='$valor', descricao='$descricao', categoria='$categoria', foto='$path' WHERE id='$id' ");
      header('Location: admin.php');
      exit;
    }
  else {
  echo "falha ao enviar arquivo";
  }

}

/*
if(isset($_POST['update'])){
    $id = $_POST['id'];
   
    $nome = $_POST['nome'];
    $valor = $_POST['valor'];
    $descricao = $_POST['descricao'];
    $categoria = $_POST['id_categoria'];

    $mysqlUp = "UPDATE itens SET nome='$nome', valor='$valor', descricao='$descricao', categoria='$categoria' WHERE id='$id'";

    $result = $mysqli->query($mysqlUp);
    
}

  header('Location: admin.php');
  exit;
?>

*/
