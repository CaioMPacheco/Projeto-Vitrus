-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 20-Maio-2025 às 00:36
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `assistec`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `carrinhos`
--

CREATE TABLE `carrinhos` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `carrinho_itens`
--

CREATE TABLE `carrinho_itens` (
  `id` int(11) NOT NULL,
  `carrinho_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `estoque` int(11) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `imagem` text NOT NULL,
  `tipo_imagem` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `preco`, `categoria`, `estoque`, `descricao`, `imagem`, `tipo_imagem`) VALUES
(10, 'caderno', 10.00, 'hardware', 25, 'caderno', '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', 'image/jpeg');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(500) NOT NULL,
  `nivel_usuario` varchar(35) NOT NULL,
  `usuario_ativo` varchar(5) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `rua` varchar(100) NOT NULL,
  `numero` varchar(6) NOT NULL,
  `complemento` varchar(45) NOT NULL,
  `bairro` varchar(100) NOT NULL,
  `cidade` varchar(75) NOT NULL,
  `estado` varchar(45) NOT NULL,
  `cep` varchar(10) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nome`, `email`, `senha`, `nivel_usuario`, `usuario_ativo`, `telefone`, `rua`, `numero`, `complemento`, `bairro`, `cidade`, `estado`, `cep`, `foto_perfil`) VALUES
(1, 'Caio', 'caio@gmail.com', '123', 'Administrador', 'Sim', '', '', '', '', '', '', '', '', 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxMTEBUSEhMVFRUVGRgaGBYYGBoYFxsYGBcXGhgYGBgYHSggGholHRoXIzEiJSkrLi4uFx8zODMsNygtLisBCgoKDg0OGxAQGy0mICIrLS01LS0uLS0vLS01NS0tLy0tLS0tLS0tLy0tLS0vLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAOEA4QMBIgACEQEDEQH/');

-- --------------------------------------------------------

--
-- Estrutura da tabela `vendas`
--

CREATE TABLE `vendas` (
  `id` int(11) NOT NULL,
  `idproduto` int(11) NOT NULL,
  `idcategoria` varchar(100) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `data_venda` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `carrinhos`
--
ALTER TABLE `carrinhos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `carrinho_itens`
--
ALTER TABLE `carrinho_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carrinho_id` (`carrinho_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices para tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Índices para tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idproduto` (`idproduto`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `carrinhos`
--
ALTER TABLE `carrinhos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `carrinho_itens`
--
ALTER TABLE `carrinho_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `carrinho_itens`
--
ALTER TABLE `carrinho_itens`
  ADD CONSTRAINT `carrinho_itens_ibfk_1` FOREIGN KEY (`carrinho_id`) REFERENCES `carrinhos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrinho_itens_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);

--
-- Limitadores para a tabela `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`idproduto`) REFERENCES `produtos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
