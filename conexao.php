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


$mysqli = new mysqli($host, $port, $usuario, $senha, $banco);
if($mysqli->connect_errno){
    echo"Falha ao conectar: (" . $mysqli->connect_errno . " ) " . $mysqli->connect_errno;
}

/*
$host = 'localhost';
$db = 'bytebistro';
$user = 'root';
$pass = '';*/


$host = 'junction.proxy.rlwy.net';
$port = "54476";
$db = 'railway';
$user = 'root';
$pass = 'tdziMesQcdvYEpyRffJAwjgsRFEWGLqT';

$conn = new mysqli($host, $port, $user, $pass, $db);
if($conn->connect_errno){
    echo"Falha ao conectar: (" . $conn->connect_errno . " ) " . $conn->connect_errno;
}

?>