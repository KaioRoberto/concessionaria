<?php
include 'conexao.php';

$pagina = 'Relatório';
include 'header.php';

$sqlTable = "
    SELECT 
        nome, 
        tipo_veiculo, 
        vendidos AS quantidade_vendida, 
        valor,
        (vendidos * valor) AS total_item
    FROM veiculos p
    ORDER BY tipo_veiculo ASC, nome ASC
";
$resultTable = $mysqli->query($sqlTable);

$veiculos_vendidos = [];
$total_vendas = 0;
while ($row = $resultTable->fetch_assoc()) {
    $veiculos_vendidos[] = $row;
    $total_vendas += $row['total_item'];
}

$sqlGraph = "
    SELECT 
        CONCAT(
            DATE_FORMAT(DATE_SUB(rv.horario, INTERVAL MINUTE(rv.horario) % 30 MINUTE), '%Y-%m-%d %H:'),
            LPAD(FLOOR(MINUTE(rv.horario) / 30) * 30, 2, '0')
        ) AS intervalo,
        p.tipo_veiculo,
        rv.quantidade AS quantidade_vendida
    FROM registro_vendas rv
    JOIN veiculos p ON rv.produto_id = p.id
    WHERE DATE(rv.horario) = CURDATE()
    ORDER BY rv.horario ASC;
";
$resultGraph = $mysqli->query($sqlGraph);

$dados_carro = [];
$dados_moto = [];
while ($row = $resultGraph->fetch_assoc()) {
    $intervalo = $row['intervalo'];
    if ($row['tipo_veiculo'] == 1) {
        $dados_carro[$intervalo] = (int)$row['quantidade_vendida'];
    } else {
        $dados_moto[$intervalo] = (int)$row['quantidade_vendida'];
    }
}

$all_intervals = array_unique(array_merge(array_keys($dados_carro), array_keys($dados_moto)));
sort($all_intervals);

$incrementos_carro = [];
$incrementos_moto = [];
$prev_carro = 0;
$prev_moto = 0;
foreach ($all_intervals as $intv) {
    $current_carro = $dados_carro[$intv] ?? $prev_carro;
    $incrementos_carro[] = $current_carro - $prev_carro;
    $prev_carro = $current_carro;

    $current_moto = $dados_moto[$intv] ?? $prev_moto;
    $incrementos_moto[] = $current_moto - $prev_moto;
    $prev_moto = $current_moto;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Relatório de Vendas</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>

  body.relatorio-page::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('imagens/relatorio.jpg');
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
      max-width: 1200px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    h1, h2, h3 {
      text-align: center;
      color: #2c3e50;
    }

    h1 { font-size: 36px; margin-bottom: 30px; }
    h2 { font-size: 30px; margin-top: 40px; margin-bottom: 20px; }
    h3 { margin-top: 20px; margin-bottom: 20px; }

    .table th, .table td {
      text-align: center;
    }

    .table th {
      background-color: #2c3e50;
      color: white;
    }

    .table tbody tr:nth-child(odd) {
      background-color: #f9f9f9;
    }

    .table tbody tr:nth-child(even) {
      background-color: #f1f1f1;
    }

    .finalizar-dia-btn {
      background-color: #27ae60;
      color: white;
      border: none;
      padding: 10px 20px;
      font-size: 16px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .finalizar-dia-btn:hover {
      background-color: #2ecc71;
    }

    .finalizar-dia-btn:active {
      transform: translateY(2px);
    }

    canvas {
      max-width: 100%;
      height: auto;
    }

    @media (max-width: 768px) {
      .container {
        width: 95%;
        padding: 20px;
      }
      h1 { font-size: 1.8rem; }
      h2 { font-size: 1.5rem; }
      h3 { font-size: 1.2rem; }
    }
  </style>
</head>
<body class="relatorio-page">
  <div class="container my-4">
    <h1>Relatório de Vendas</h1>

    <h2>Tabela de Vendas</h2>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Tipo</th>
          <th>Quantidade Vendida</th>
          <th>Valor Unitário (R$)</th>
          <th>Total do Item (R$)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($veiculos_vendidos as $prod): ?>
        <tr>
          <td><?= $prod['nome'] ?></td>
          <td><?= $prod['tipo_veiculo'] == 1 ? 'Carro' : 'Moto' ?></td>
          <td><?= $prod['quantidade_vendida'] ?></td>
          <td>R$ <?= number_format($prod['valor'], 2, ',', '.') ?></td>
          <td>R$ <?= number_format($prod['total_item'], 2, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="mb-3">
      <h3>Total de Vendas: R$ <?= number_format($total_vendas, 2, ',', '.') ?></h3>
    </div>

    <div style="margin: 20px 0; text-align: right;">
        <form action="finalizar_dia.php" method="post" onsubmit="return confirm('Deseja realmente finalizar o dia?');">
            <button type="submit" class="finalizar-dia-btn">
                Finalizar Dia
            </button>
        </form>
    </div>

    <h2>Gráfico de Vendas Incrementais (30 minutos)</h2>
    <canvas id="graficoVendasIntervalos"></canvas>
    <script>
      var ctx = document.getElementById('graficoVendasIntervalos').getContext('2d');
      var grafico = new Chart(ctx, {
          type: 'line',
          data: {
              labels: <?= json_encode($all_intervals); ?>,
              datasets: [
                  {
                      label: 'Vendas de Carros (30 min)',
                      data: <?= json_encode($incrementos_carro); ?>,
                      borderColor: 'rgba(255, 99, 132, 1)',
                      backgroundColor: 'rgba(255, 99, 132, 0.2)',
                      borderWidth: 2,
                      tension: 0.1
                  },
                  {
                      label: 'Vendas de Motos (30 min)',
                      data: <?= json_encode($incrementos_moto); ?>,
                      borderColor: 'rgba(54, 162, 235, 1)',
                      backgroundColor: 'rgba(54, 162, 235, 0.2)',
                      borderWidth: 2,
                      tension: 0.1
                  }
              ]
          },
          options: {
              responsive: true,
              scales: {
                  x: { title: { display: true, text: 'Intervalo (30 minutos)' } },
                  y: { beginAtZero: true, title: { display: true, text: 'Vendas Incrementais' } }
              }
          }
      });
    </script>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>