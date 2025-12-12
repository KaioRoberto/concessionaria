<?php
include 'conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $mysqli->begin_transaction();

    try {
        
        $sql_vendas = "DELETE FROM registro_vendas WHERE produto_id = ?";
        $stmt_vendas = $mysqli->prepare($sql_vendas);
        $stmt_vendas->bind_param("i", $id);
        $stmt_vendas->execute();

        $sql_produto = "DELETE FROM veiculos WHERE id = ?";
        $stmt_produto = $mysqli->prepare($sql_produto);
        $stmt_produto->bind_param("i", $id);
        $stmt_produto->execute();

        $mysqli->commit();

        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        $mysqli->rollback();
        echo "Erro ao excluir o produto: " . $e->getMessage();
    }
}
?>
