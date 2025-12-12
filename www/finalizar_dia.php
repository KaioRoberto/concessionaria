<?php
include 'conexao.php';

$sql = "UPDATE veiculos SET quantidade_inicial = quantidade_inicial - vendidos, vendidos = 0";

if ($mysqli->query($sql)) {
    echo json_encode(['success' => true, 'message' => 'Dia finalizado']);
} else {
    echo json_encode(['success' => false, 'error' => $mysqli->error]);
}
?>
