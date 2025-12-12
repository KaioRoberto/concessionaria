SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `veiculos` (
  `id` int(3) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `tipo_veiculo` tinyint(1) NOT NULL,
  `quantidade_inicial` int(11) NOT NULL,
  `vendidos` int(11) NOT NULL DEFAULT 0,
  `valor` double NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `estoque_minimo` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `registro_vendas` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `horario` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `registro_vendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

ALTER TABLE `produtos`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT;


ALTER TABLE `registro_vendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `registro_vendas`
  ADD CONSTRAINT `registro_vendas_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);
COMMIT;