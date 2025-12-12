<?php
include 'conexao.php';

$pagina = 'Início';
include 'header.php';

// Consulta para buscar os veículos
$sql = "SELECT * FROM veiculos";
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Veículos</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <style>

        body.home-page {
            position: relative;
        }

        body.home-page::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('imagens/concessionaria.jpg');
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
            font-size: 32px;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        thead {
            background-color: #34495e;
            color: #fff;
        }

        th, td {
            padding: 15px 10px;
            text-align: center;
            font-size: 15px;
        }

        tbody tr {
            background-color: rgba(255, 255, 255, 0.9);
            transition: transform 0.2s, background 0.3s;
        }

        tbody tr:hover {
            transform: scale(1.01);
            background-color: rgba(240,240,240,0.95);
        }

        /* Destaque de estoque baixo */
        .alerta-estoque {
            background-color: #fdecea !important;
            color: #c0392b;
            font-weight: bold;
        }

        /* Botões */
        .btn-editar, .btn-excluir {
            padding: 6px 12px;
            border-radius: 8px;
            color: #fff;
            font-weight: bold;
            text-decoration: none;
            transition: transform 0.2s, background 0.3s;
        }

        .btn-editar {
            background-color: #1abc9c;
        }

        .btn-editar:hover {
            background-color: #16a085;
            transform: translateY(-2px);
        }

        .btn-excluir {
            background-color: #e74c3c;
        }

        .btn-excluir:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            thead tr {
                display: none;
            }
            tbody tr {
                margin-bottom: 20px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                padding: 15px;
                border-radius: 12px;
            }
            tbody td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            tbody td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                font-weight: bold;
                color: #34495e;
            }
        }
    </style>
</head>
<body class="home-page">

<div class="container">
    <h1>Lista de Veículos</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Quantidade Inicial</th>
                <th>Vendidos</th>
                <th>Estoque Atual</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($produto = $result->fetch_assoc()) : 
                $estoque_atual = $produto['quantidade_inicial'] - $produto['vendidos'];
                $classe = ($estoque_atual <= $produto['estoque_minimo']) ? 'alerta-estoque' : '';
            ?>
            <tr class="<?= $classe ?>">
                <td data-label="ID"><?= $produto['id'] ?></td>
                <td data-label="Nome"><?= htmlspecialchars($produto['nome']) ?></td>
                <td data-label="Tipo"><?= $produto['tipo_veiculo'] == 1 ? 'Carro' : 'Moto' ?></td>
                <td data-label="Quantidade Inicial"><?= $produto['quantidade_inicial'] ?></td>
                <td data-label="Vendidos"><?= $produto['vendidos'] ?></td>
                <td data-label="Estoque Atual"><?= $estoque_atual ?></td>
                <td data-label="Ações">
                    <a href="editar.php?id=<?= $produto['id'] ?>" class="btn-editar">Editar</a>
                    <a href="excluir.php?id=<?= $produto['id'] ?>" class="btn-excluir" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>