<?php
include 'conexao.php';

// comentario teste deploy ec2
// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $tipo_veiculo = ($_POST['tipo'] == 'carro') ? 1 : 0; // 1 para carro, 0 para moto
    $quantidade_inicial = $_POST['quantidade_inicial'];
    $valor = str_replace(',', '.', str_replace('.', '', $_POST['valor'])); // normaliza valor
    $valor = floatval($valor);
    $estoque_minimo = $_POST['estoque_minimo'];
    $imagem = '';

    // Upload de imagem
    if (!empty($_FILES['imagem']['name'])) {
        $imagem_nome = strtolower(str_replace(" ", "_", $_FILES['imagem']['name']));
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

    // Inserção no banco
    $sql = "INSERT INTO veiculos (nome, tipo_veiculo, quantidade_inicial, valor, estoque_minimo, imagem) 
            VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("siiiss", $nome, $tipo_veiculo, $quantidade_inicial, $valor, $estoque_minimo, $imagem);

        if ($stmt->execute()) {
            $mensagem = "Veículo adicionado com sucesso!";
        } else {
            $mensagem = "Erro ao adicionar o veículo.";
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
<title>Adicionar Veículo</title>
<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="css/style.css">
<style>
    /* ===== Fundo premium motos ===== */
    body.adicionar-page::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('imagens/motos.jpg');
        background-size: cover;
        background-position: center;
        filter: blur(6px) brightness(0.7);
        z-index: 0;
    }


        .container {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 30px;
            margin: 40px auto;
            width: 85%;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

    h1 {
        text-align: center;
        color: #2c3e50;
        font-size: 32px;
        margin-bottom: 25px;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    label {
        font-weight: 600;
        color: #34495e;
    }

    input[type="text"], input[type="number"], input[type="file"], input[type="url"], select {
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #bbb;
        font-size: 15px;
        transition: border-color 0.3s;
    }

    input:focus, select:focus {
        outline: none;
        border-color: #1abc9c;
    }

    button {
        padding: 12px;
        border: none;
        border-radius: 10px;
        background-color: #1abc9c;
        color: #fff;
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s, transform 0.2s;
    }

    button:hover {
        background-color: #16a085;
        transform: translateY(-2px);
    }

    .mensagem {
        text-align: center;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #27ae60;
    }

    .mensagem.erro {
        color: #c0392b;
    }

    p {
        font-size: 14px;
        color: #7f8c8d;
        margin-top: -10px;
        margin-bottom: 10px;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .container {
            width: 95%;
            padding: 20px;
        }
        h1 {
            font-size: 1.8rem;
        }
        input, select, button {
            font-size: 0.95rem;
        }
    }
</style>
</head>
<body class="adicionar-page">

<?php
$pagina = 'Adicionar';
include 'header.php';
?>

<div class="container">
    <h1>Adicionar Novo Veículo</h1>

    <?php if (isset($mensagem)): ?>
        <p class="mensagem <?= isset($erro) ? 'erro' : '' ?>"><?= $mensagem ?></p>
    <?php endif; ?>

    <form action="adicionar.php" method="POST" enctype="multipart/form-data">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" placeholder="Ex: Yamaha MT-09" required>

        <label for="tipo">Tipo:</label>
        <select name="tipo" id="tipo" required>
            <option value="carro">Carro</option>
            <option value="moto">Moto</option>
        </select>

        <label for="quantidade_inicial">Quantidade Inicial:</label>
        <input type="number" name="quantidade_inicial" id="quantidade_inicial" required>

        <label for="estoque_minimo">Estoque Mínimo:</label>
        <input type="number" name="estoque_minimo" id="estoque_minimo" required>

        <label for="valor">Valor (R$):</label>
        <input type="text" name="valor" id="valor" placeholder="Ex: 120000,00" required>

        <label for="imagem">Imagem do Veículo:</label>
        <input type="file" name="imagem" id="imagem" accept="image/*">

        <p>Ou adicione um link para a imagem:</p>
        <input type="url" name="link_imagem" id="link_imagem" placeholder="http://exemplo.com/imagem.jpg">

        <button type="submit">Adicionar Veículo</button>
    </form>
</div>

</body>
</html>
