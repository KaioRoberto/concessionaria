<?php

if (!isset($pagina)) {
    $pagina = 'Início';
}
?>

<header>
    <div class="container">
        <h1></h1>
        <nav>
            <ul>
                <li><a href="index.php" class="<?= ($pagina == 'Início') ? 'active' : '' ?>">Início</a></li>
                <li><a href="produtos.php" class="<?= ($pagina == 'Produtos') ? 'active' : '' ?>">Veiculos</a></li>
                <li><a href="adicionar.php" class="<?= ($pagina == 'Adicionar') ? 'active' : '' ?>">Adicionar Veiculo</a></li>
                <li><a href="relatorio.php" class="<?= ($pagina == 'Relatório') ? 'active' : '' ?>">Relatório</a></li>
            </ul>
        </nav>
    </div>
</header>
