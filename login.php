<!DOCTYPE html>

<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap"
    />
    <link rel="stylesheet" href="styleLogin.css">
    <title>Login</title>
  </head>

  <?php

  if(!empty($_SESSION)){
    session_start();
  }

  if (isset($_POST['usuario'], $_POST['senha'])){
    if ($_POST['usuario'] == 'admin' && $_POST['senha'] == 'admin123') {
      $_SESSION['usuario'] = $_POST['usuario'];
      header('Location: admin.php');
    }
  }

  ?>

  <body id="principal">

    <div id="login">
      <div class="caixa">
       <a href="index.html"><img src="imagens/logo.png" alt="" id="logo"></a>
        <h1>LOGIN</h1>
        
        <form action="" method="post">
          <div class="usuario">
            <input name="usuario" type="name" placeholder="Usuário" />
          </div>

          <div class="senha">
            <input name="senha" type="password" placeholder="Senha" />
          </div>
          <br>
          <div class="entrar">
            <input type="submit" name="login" value="Entrar">
          </div>
        </form>

      </div>
    </div>
  </body>
</html>
