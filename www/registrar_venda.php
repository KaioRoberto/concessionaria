<?php
include 'conexao.php';

$data = json_decode(file_get_contents('php://input'), true);

$produto_id = $data['produto_id'] ?? $_POST['produto_id'] ?? $_GET['produto_id'] ?? null;
$quantidade = $data['quantidade'] ?? $_POST['quantidade'] ?? $_GET['quantidade'] ?? null;

if (!$produto_id || !$quantidade) {
    echo json_encode(['success' => false, 'error' => 'Parâmetros ausentes']);
    exit;
}

// Verificar a ultima venda
$sql = "SELECT horario FROM registro_vendas WHERE produto_id = ? ORDER BY horario DESC LIMIT 1";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $produto_id);
$stmt->execute();
$result = $stmt->get_result();
$ultima_venda = $result->fetch_assoc();
$stmt->close();

$registrar = false;

if (!$ultima_venda) {
    $registrar = true;
} else {
    $ultima_hora = strtotime($ultima_venda['horario']);
    $agora = time();
    if (($agora - $ultima_hora) >= 300) $registrar = true;
}

if ($registrar) {
    $sql = "INSERT INTO registro_vendas (produto_id, quantidade) VALUES (?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $produto_id, $quantidade);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Venda registrada']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao registrar venda']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Menos de 10 minutos desde a última venda']);
}

$mysqli->close();
?>
