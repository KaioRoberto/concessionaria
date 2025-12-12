<?php
include 'conexao.php';

// Consulta para buscar todos os produtos
$sql = "SELECT * FROM veiculos";
$result = $mysqli->query($sql);

// Separar produtos por tipo (motos e carros)
$motos = [];
$carros = [];
while ($produto = $result->fetch_assoc()) {
    if ($produto['tipo_veiculo'] == 1) $carros[] = $produto;
    else $motos[] = $produto;
}

$pagina = 'Produtos';
include 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Produtos</title>
<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="css/style.css">
<style>
/* Mantive o mesmo CSS do seu antigo produtos.php */
body.produtos-page::before {
    content: '';
    position: fixed;
    top:0; left:0; width:100%; height:100%;
    background-image: url('imagens/carros.jpg');
    background-size: cover;
    background-position: center;
    filter: blur(6px) brightness(0.7);
    z-index:0;
}
.container { position: relative; z-index: 2; background: rgba(255,255,255,0.95); border-radius: 12px; padding: 30px; margin: 40px auto; width: 85%; box-shadow:0 10px 25px rgba(0,0,0,0.15);}
.cards-produtos { display:grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap:30px; justify-items:center; margin-top:20px; padding:0 20px; }
.produto { background: rgba(255,255,255,0.95); border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.15); width:100%; max-width:300px; padding:20px; text-align:center; position: relative; transition: transform 0.3s, box-shadow 0.3s;}
.produto:hover { transform: scale(1.05); box-shadow:0 12px 25px rgba(0,0,0,0.25);}
.produto-imagem img { width:100%; height:auto; border-radius:10px; margin-bottom:15px; border:2px solid #ddd;}
.produto h3 { font-size:1.4rem; font-weight:bold; margin-bottom:10px; color:#34495e;}
.produto p { font-size:1rem; color:#666; margin-bottom:8px;}
.contador-vendido { display:flex; align-items:center; justify-content:center; gap:15px; margin-top:15px;}
.contador-vendido button { background-color:#1abc9c; color:#fff; border:none; padding:8px 14px; font-size:1rem; cursor:pointer; border-radius:8px; transition: background 0.3s, transform 0.2s;}
.contador-vendido button:hover { background-color:#16a085; transform: translateY(-2px);}
.contador { font-size:1.3rem; font-weight:bold; min-width:30px; color:#2c3e50; }
.contador-temp { position:absolute; top:10px; right:10px; background-color:#f1c40f; color:#fff; padding:5px 10px; border-radius:10px; font-weight:bold; font-size:0.9rem; animation: fadeOut 5s forwards;}
@keyframes fadeOut { 0%{opacity:1} 80%{opacity:1} 100%{opacity:0} }
</style>
</head>
<body class="produtos-page">
<div class="container">
<h1>Produtos</h1>

<h2>Motos</h2>
<div class="cards-produtos" id="motos-container">
    <?php foreach($motos as $produto): ?>
    <div class="produto" data-id="<?= $produto['id'] ?>">
        <div class="produto-imagem">
            <img src="<?= $produto['imagem'] ?: 'imagens/default.png' ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
        </div>
        <h3><?= htmlspecialchars($produto['nome']) ?></h3>
        <p>Quantidade Inicial: <?= $produto['quantidade_inicial'] ?></p>
        <p>Valor: R$ <?= number_format($produto['valor'], 2, ',', '.') ?></p>
        <div class="contador-vendido">
            <button onclick="alterarQuantidade(<?= $produto['id'] ?>,-1)">-</button>
            <span class="contador"><?= $produto['vendidos'] ?></span>
            <button onclick="alterarQuantidade(<?= $produto['id'] ?>,1)">+</button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<h2>Carros</h2>
<div class="cards-produtos" id="carros-container">
    <?php foreach($carros as $produto): ?>
    <div class="produto" data-id="<?= $produto['id'] ?>">
        <div class="produto-imagem">
            <img src="<?= $produto['imagem'] ?: 'imagens/default.png' ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
        </div>
        <h3><?= htmlspecialchars($produto['nome']) ?></h3>
        <p>Quantidade Inicial: <?= $produto['quantidade_inicial'] ?></p>
        <p>Valor: R$ <?= number_format($produto['valor'], 2, ',', '.') ?></p>
        <div class="contador-vendido">
            <button onclick="alterarQuantidade(<?= $produto['id'] ?>,-1)">-</button>
            <span class="contador"><?= $produto['vendidos'] ?></span>
            <button onclick="alterarQuantidade(<?= $produto['id'] ?>,1)">+</button>
        </div>
    </div>
    <?php endforeach; ?>
</div>
</div>

<script>
let contadores = {};
let produtosAlterados = {};

function alterarQuantidade(id, valor){
    const produto = document.querySelector(`.produto[data-id="${id}"]`);
    const contador = produto.querySelector('.contador');
    let quantidadeVendida = parseInt(contador.textContent);
    quantidadeVendida += valor;
    if(quantidadeVendida < 0) quantidadeVendida = 0;
    contador.textContent = quantidadeVendida;

    produtosAlterados[id] = quantidadeVendida;

    fetch('alterar_quantidade.php', {
        method: 'POST',
        headers: { 'Content-Type':'application/json' },
        body: JSON.stringify({id:id, vendidos:quantidadeVendida})
    }).then(res => res.json())
      .then(data => console.log(data));

    if(!contadores[id]) contadores[id]={quantidade:0, timer:null, elemento:null};
    contadores[id].quantidade += valor;

    if(!contadores[id].elemento){
        const temp = document.createElement('div');
        temp.className='contador-temp';
        produto.appendChild(temp);
        contadores[id].elemento = temp;
    }
    contadores[id].elemento.textContent = `+${contadores[id].quantidade}x`;

    if(contadores[id].timer) clearTimeout(contadores[id].timer);
    contadores[id].timer = setTimeout(()=>{
        contadores[id].elemento.remove();
        delete contadores[id];
    },5000);
}

function registrarVendaAutomatica(){
    for(let id in produtosAlterados){
        let vendidos = produtosAlterados[id];
        fetch('registrar_venda.php',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body: JSON.stringify({produto_id:id, quantidade:vendidos})
        }).then(res=>res.json())
          .then(data=>console.log(data));
    }
    produtosAlterados = {};
}

setInterval(registrarVendaAutomatica,3000);
</script>

</body>
</html>
