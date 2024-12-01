<?php

/*
$host = "localhost";
$banco = "bytebistro";
$usuario = "root";
$senha = "";*/


$host = "junction.proxy.rlwy.net";
$port = "54476";
$banco = "railway";
$usuario = "root";
$senha = "tdziMesQcdvYEpyRffJAwjgsRFEWGLqT";


$mysqli = new mysqli($host, $usuario, $senha, $banco, $port);
if($mysqli->connect_errno){
    echo"Falha ao conectar: (" . $mysqli->connect_errno . " ) " . $mysqli->connect_errno;
}

$conn = $mysqli;
?>