<?php
include 'conexao.php';

$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'] ?? $_GET['id'] ?? null;
$vendidos = $data['vendidos'] ?? $_GET['vendidos'] ?? null;

if ($id !== null && $vendidos !== null) {
    $sql = "UPDATE veiculos SET vendidos = ? WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("ii", $vendidos, $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true, 'id' => $id, 'vendidos' => $vendidos]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro na preparação SQL']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Parâmetros ausentes']);
}
?>
