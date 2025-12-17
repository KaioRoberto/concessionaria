<?php
$hostname = 'mysql-app';
$usuario  = 'user';
$senha    = 'password';
$database = 'concessionaria';

$mysqli = new mysqli($hostname, $usuario, $senha, $database);

if ($mysqli->connect_errno) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

$mysqli->query("SET time_zone = '-03:00'");
?>