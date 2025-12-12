<?php
include 'conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM veiculos WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $produto = $result->fetch_assoc();
        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $tipo_veiculo = $_POST['tipo_veiculo'];
    $quantidade_inicial = $_POST['quantidade_inicial'];
    $valor = str_replace(',', '.', str_replace('.', '', $_POST['valor']));
    $valor = floatval($valor);
    $estoque_minimo = $_POST['estoque_minimo'];
    $imagem = $produto['imagem'];

    if (!empty($_FILES['imagem']['name'])) {
        $imagem_nome = $_FILES['imagem']['name'];
        $imagem_nome = strtolower(str_replace(" ", "_", $imagem_nome));
        $diretorio = 'imagens/';
        $caminho_imagem = $diretorio . $nome . '.' . pathinfo($imagem_nome, PATHINFO_EXTENSION);

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_imagem)) {
            $imagem = $caminho_imagem;
        } else {
            $mensagem = "Erro ao fazer upload da imagem.";
        }
    } elseif (!empty($_POST['link_imagem'])) {
        $imagem = $_POST['link_imagem'];
    }

    $sql = "UPDATE veiculos SET nome = ?, tipo_veiculo = ?, quantidade_inicial = ?, valor = ?, estoque_minimo = ?, imagem = ? WHERE id = ?";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("ssiisii", $nome, $tipo_veiculo, $quantidade_inicial, $valor, $estoque_minimo, $imagem, $id);

        if ($stmt->execute()) {
            $mensagem = "Veículo atualizado com sucesso!";
        } else {
            $mensagem = "Erro ao atualizar o veículo.";
        }
        $stmt->close();
    } else {
        $mensagem = "Erro na preparação da consulta.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Veículo</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* ===== Estilo visual de concessionária ===== */
        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 30px;
            background: linear-gradient(145deg, #ffffff, #f9f9f9);
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            font-size: 32px;
            margin-bottom: 25px;
        }

        form label {
            font-weight: 600;
            color: #34495e;
        }

        form input, form select {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #bbb;
            margin-bottom: 15px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        form input:focus, form select:focus {
            outline: none;
            border-color: #1abc9c;
        }

        form button {
            background-color: #1abc9c;
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            padding: 12px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s, transform 0.2s;
        }

        form button:hover {
            background-color: #16a085;
            transform: translateY(-2px);
        }

        .mensagem {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 600;
            color: #27ae60;
        }

        .mensagem.erro {
            color: #c0392b;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h1>Editar Veículo</h1>

    <?php if (isset($mensagem)): ?>
        <p class="mensagem <?= isset($erro) ? 'erro' : '' ?>"><?= $mensagem ?></p>
    <?php endif; ?>

    <form action="editar.php?id=<?= $produto['id'] ?>" method="POST" enctype="multipart/form-data">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($produto['nome']) ?>" placeholder="Ex: Honda Civic" required>

        <label for="tipo_veiculo">Tipo:</label>
        <select name="tipo_veiculo" id="tipo_veiculo" required>
            <option value="1" <?= $produto['tipo_veiculo'] == 1 ? 'selected' : '' ?>>Carro</option>
            <option value="0" <?= $produto['tipo_veiculo'] == 0 ? 'selected' : '' ?>>Moto</option>
        </select>

        <label for="quantidade_inicial">Quantidade Inicial:</label>
        <input type="number" name="quantidade_inicial" id="quantidade_inicial" value="<?= $produto['quantidade_inicial'] ?>" required>

        <label for="valor">Valor (R$):</label>
        <input type="text" name="valor" id="valor" value="<?= $produto['valor'] ?>" placeholder="Ex: 95000,00" required>

        <label for="estoque_minimo">Estoque Mínimo:</label>
        <input type="number" name="estoque_minimo" id="estoque_minimo" value="<?= $produto['estoque_minimo'] ?>" required>

        <button type="submit">Atualizar Veículo</button>
    </form>
</div>

</body>
</html>
