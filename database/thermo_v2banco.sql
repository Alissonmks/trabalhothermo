-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23/11/2026 às 22:38
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `thermo_v2banco`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `auditoria`
--

CREATE TABLE `auditoria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `operacao` varchar(50) NOT NULL,
  `entidade` varchar(100) NOT NULL,
  `registro_id` varchar(100) DEFAULT NULL,
  `resultado` enum('SUCESSO','ERRO') NOT NULL DEFAULT 'SUCESSO',
  `resumo` text DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfil_permissoes`
--

CREATE TABLE `perfil_permissoes` (
  `perfil_id` smallint(5) UNSIGNED NOT NULL,
  `permissao_id` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfis`
--

CREATE TABLE `perfis` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `status` enum('ATIVO','INATIVO') NOT NULL DEFAULT 'ATIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `permissoes`
--

CREATE TABLE `permissoes` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `codigo` varchar(100) NOT NULL,
  `descricao` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `categoria` varchar(50) NOT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `unidade_medida` varchar(10) NOT NULL DEFAULT 'UN',
  `preco_custo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `preco_venda` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantidade` int(11) NOT NULL DEFAULT 0,
  `estoque_minimo` int(11) NOT NULL DEFAULT 0,
  `fornecedor` varchar(100) DEFAULT NULL,
  `data_validade` date DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `data_cadastro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perfil_id` smallint(5) UNSIGNED DEFAULT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(191) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `telefone` varchar(25) DEFAULT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `status` enum('ATIVO','INATIVO','BAIXA') NOT NULL DEFAULT 'ATIVO',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `perfil_id`, `nome`, `email`, `cpf`, `telefone`, `senha_hash`, `status`, `criado_em`, `atualizado_em`) VALUES
(1, NULL, 'jose calunia', 'jose23@gmail.com', '12542325323', '81 923435654', '$2y$10$Smfk.eqG74EvgCt1zo.L5eDuhYwMv4pdvSIHoQo9nMnLMjrmr7CXe', 'ATIVO', '2026-11-22 18:02:22', '2026-11-22 18:02:22'),
(3, NULL, 'mario pedro', 'mario23@gmail.com', '12542322322', '81 923432256', '$2y$10$EkwykjV6OaXftgrGzvNRY.ibieEVZETqXnyqSJwEhC4uQGpSAeLsC', 'ATIVO', '2026-11-22 19:10:56', '2026-11-22 19:58:48'),
(5, NULL, 'marco cervero', 'marco34@gmail.com', '125423226544', '81 923433334', '$2y$10$HWcLT5XW0W9fZkh9khsRruGf5kOLTRpW12OvBoXp1xYnCn7tlbCvG', 'ATIVO', '2026-11-23 08:02:52', '2026-11-23 10:07:34');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_auditoria_usuario` (`usuario_id`);

--
-- Índices de tabela `perfil_permissoes`
--
ALTER TABLE `perfil_permissoes`
  ADD PRIMARY KEY (`perfil_id`,`permissao_id`),
  ADD KEY `fk_pp_permissao` (`permissao_id`);

--
-- Índices de tabela `perfis`
--
ALTER TABLE `perfis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Índices de tabela `permissoes`
--
ALTER TABLE `permissoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `idx_usuarios_perfil_status` (`perfil_id`,`status`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `perfis`
--
ALTER TABLE `perfis`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `permissoes`
--
ALTER TABLE `permissoes`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `perfil_permissoes`
--
ALTER TABLE `perfil_permissoes`
  ADD CONSTRAINT `fk_pp_perfil` FOREIGN KEY (`perfil_id`) REFERENCES `perfis` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pp_permissao` FOREIGN KEY (`permissao_id`) REFERENCES `permissoes` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_perfil` FOREIGN KEY (`perfil_id`) REFERENCES `perfis` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
