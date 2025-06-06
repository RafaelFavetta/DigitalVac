-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/06/2025 às 00:33
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `vac`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `aplicacao`
--

CREATE TABLE `aplicacao` (
  `id_aplica` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_posto` int(11) NOT NULL,
  `id_medico` int(11) NOT NULL,
  `id_vaci` int(11) NOT NULL,
  `data_aplica` date NOT NULL,
  `dose_aplicad` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `aplicacao`
--

INSERT INTO `aplicacao` (`id_aplica`, `id_usuario`, `id_posto`, `id_medico`, `id_vaci`, `data_aplica`, `dose_aplicad`) VALUES
(1, 1, 11, 1, 1, '2025-05-01', 1);
(2, 1, 11, 2, 2, '2025-05-08', 1),
(3, 1, 11, 3, 3, '2025-05-15', 2),
(2406, 2, 12, 1, 4, '2025-05-01', 1);
-- --------------------------------------------------------

--
-- Estrutura para tabela `atestado`
--

CREATE TABLE `atestado` (
  `id_atestado` int(11) NOT NULL,
  `id_paci` int(11) NOT NULL,
  `id_medico` int(11) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `justificativa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `atestado`
--

INSERT INTO `atestado` (`id_atestado`, `id_paci`, `id_medico`, `data_inicio`, `data_fim`, `justificativa`) VALUES
(1, 1, 1, '2025-05-01', '2025-05-07', 'Repouso por febre.'),
(2, 1, 2, '2025-05-08', '2025-05-14', 'Repouso por dor muscular.'),
(3, 1, 3, '2025-05-15', '2025-05-21', 'Repouso por recuperação pós-vacina.');

-- --------------------------------------------------------

--
-- Estrutura para tabela `campanha`
--

CREATE TABLE `campanha` (
  `id_campanha` int(11) NOT NULL,
  `nome_campanha` varchar(255) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `descricao` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `medico`
--

CREATE TABLE `medico` (
  `id_medico` int(11) NOT NULL,
  `nome_medico` varchar(100) NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `email_medico` varchar(100) NOT NULL,
  `tel_medico` varchar(11) NOT NULL,
  `coren_crm` varchar(20) NOT NULL,
  `tipo_medico` varchar(50) NOT NULL,
  `naci_medico` date NOT NULL,
  `id_posto` int(11) NOT NULL,
  `senha` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `medico`
--

INSERT INTO `medico` (`id_medico`, `nome_medico`, `cpf`, `email_medico`, `tel_medico`, `coren_crm`, `tipo_medico`, `naci_medico`, `id_posto`, `senha`) VALUES
(1, 'Dr. João', '12345678909', 'joao.medico@example.com', '11999999999', 'CRM-SP 123456', 'Clínico Geral', '1980-05-10', 11, 'senha123'),
(2, 'Dra. Maria', '98765432100', 'maria.medica@example.com', '21988888888', 'CRM-RJ 654321', 'Pediatra', '1985-08-15', 12, 'senha123'),
(3, 'Dr. Carlos', '11144477735', 'carlos.medico@example.com', '31977777777', 'COREN-MG 987654', 'Enfermeiro', '1978-12-20', 13, 'senha123');

-- --------------------------------------------------------

--
-- Estrutura para tabela `posto`
--

CREATE TABLE `posto` (
  `id_posto` int(11) NOT NULL,
  `nome_posto` varchar(100) NOT NULL,
  `cep_posto` int(8) NOT NULL,
  `n_posto` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `posto`
--

INSERT INTO `posto` (`id_posto`, `nome_posto`, `cep_posto`, `n_posto`) VALUES
(1, 'Posto A', 12345678, 100),
(2, 'Posto B', 12345679, 101),
(3, 'Posto C', 12345680, 102),
(4, 'Posto D', 12345681, 103),
(5, 'Posto E', 12345682, 104),
(6, 'Posto F', 12345683, 105),
(7, 'Posto G', 12345684, 106),
(8, 'Posto H', 12345685, 107),
(9, 'Posto I', 12345686, 108),
(10, 'Posto J', 12345687, 109),
(11, 'Posto K', 12345000, 200),
(12, 'Posto L', 12345001, 201),
(13, 'Posto M', 12345002, 202);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nome_usuario` varchar(100) NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `email_usuario` varchar(100) NOT NULL,
  `tel_usuario` varchar(11) NOT NULL,
  `genero_usuario` char(1) NOT NULL CHECK (`genero_usuario` in ('M','F','O')),
  `naci_usuario` date NOT NULL,
  `peso_usuario` decimal(5,2) NOT NULL,
  `tipo_sang_usuario` varchar(3) NOT NULL,
  `med_usuario` varchar(100) NOT NULL,
  `doen_usuario` varchar(255) NOT NULL,
  `ale_usuario` varchar(255) NOT NULL,
  `cep_usuario` varchar(8) NOT NULL,
  `nc_usuario` int(10) NOT NULL,
  `senha` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome_usuario`, `cpf`, `email_usuario`, `tel_usuario`, `genero_usuario`, `naci_usuario`, `peso_usuario`, `tipo_sang_usuario`, `med_usuario`, `doen_usuario`, `ale_usuario`, `cep_usuario`, `nc_usuario`, `senha`) VALUES
(1, 'Rafael Favetta', '45260925840', 'rafaelfavetta@gmail.com', '19981084437', 'M', '2007-09-06', 77.00, 'A+', '', '', '', '13607030', 231, '$2y$10$7AgFK/3Cj6LkYeq2sB3OmeWAr0s7uys9zIL5C/kuPtykLhxT2bBXi'),
(2, 'Miguel Di-Tanno Viganó', '51382943857', 'miguelzin@gmail.com', '19999999999', 'M', '2007-02-20', 80.00, 'O+', '', '', '', '12600074', 211, '$2y$10$OFVLttem/NAXkhttj/x9putG92ZeO65A.0bweCIl7ilc4ptFP5xuq');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vacina`
--

CREATE TABLE `vacina` (
  `id_vaci` int(11) NOT NULL,
  `nome_vaci` varchar(100) NOT NULL,
  `fabri_vaci` varchar(100) NOT NULL,
  `lote_vaci` varchar(50) NOT NULL,
  `idade_aplica` int(3) NOT NULL,
  `via_adimicao` varchar(50) NOT NULL,
  `n_dose` int(1) NOT NULL,
  `intervalo_dose` int(11) NOT NULL,
  `estoque` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vacina`
--

INSERT INTO `vacina` (`id_vaci`, `nome_vaci`, `fabri_vaci`, `lote_vaci`, `idade_aplica`, `via_adimicao`, `n_dose`, `intervalo_dose`, `estoque`) VALUES
(1, 'Vacina A', 'Fabricante A', 'Lote001', 18, 'Intramuscular', 2, 30, 100),
(2, 'Vacina B', 'Fabricante B', 'Lote002', 12, 'Subcutânea', 1, 0, 50),
(3, 'Vacina C', 'Fabricante C', 'Lote003', 60, 'Oral', 3, 60, 200),
(4, 'Rafafez', 'Rafa', '25', 10, 'Intravenoso', 2, 1, 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `aplicacao`
--
ALTER TABLE `aplicacao`
  ADD PRIMARY KEY (`id_aplica`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_posto` (`id_posto`),
  ADD KEY `id_medico` (`id_medico`),
  ADD KEY `id_vaci` (`id_vaci`);

--
-- Índices de tabela `atestado`
--
ALTER TABLE `atestado`
  ADD PRIMARY KEY (`id_atestado`),
  ADD KEY `id_paci` (`id_paci`),
  ADD KEY `id_medico` (`id_medico`);

--
-- Índices de tabela `campanha`
--
ALTER TABLE `campanha`
  ADD PRIMARY KEY (`id_campanha`);

--
-- Índices de tabela `medico`
--
ALTER TABLE `medico`
  ADD PRIMARY KEY (`id_medico`),
  ADD KEY `id_posto` (`id_posto`);

--
-- Índices de tabela `posto`
--
ALTER TABLE `posto`
  ADD PRIMARY KEY (`id_posto`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices de tabela `vacina`
--
ALTER TABLE `vacina`
  ADD PRIMARY KEY (`id_vaci`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `aplicacao`
--
ALTER TABLE `aplicacao`
  MODIFY `id_aplica` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2407;

--
-- AUTO_INCREMENT de tabela `atestado`
--
ALTER TABLE `atestado`
  MODIFY `id_atestado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `campanha`
--
ALTER TABLE `campanha`
  MODIFY `id_campanha` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `medico`
--
ALTER TABLE `medico`
  MODIFY `id_medico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `posto`
--
ALTER TABLE `posto`
  MODIFY `id_posto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `vacina`
--
ALTER TABLE `vacina`
  MODIFY `id_vaci` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `aplicacao`
--
ALTER TABLE `aplicacao`
  ADD CONSTRAINT `aplicacao_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `aplicacao_ibfk_2` FOREIGN KEY (`id_posto`) REFERENCES `posto` (`id_posto`),
  ADD CONSTRAINT `aplicacao_ibfk_3` FOREIGN KEY (`id_medico`) REFERENCES `medico` (`id_medico`),
  ADD CONSTRAINT `aplicacao_ibfk_4` FOREIGN KEY (`id_vaci`) REFERENCES `vacina` (`id_vaci`);

--
-- Restrições para tabelas `atestado`
--
ALTER TABLE `atestado`
  ADD CONSTRAINT `atestado_ibfk_1` FOREIGN KEY (`id_paci`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `atestado_ibfk_2` FOREIGN KEY (`id_medico`) REFERENCES `medico` (`id_medico`);

--
-- Restrições para tabelas `medico`
--
ALTER TABLE `medico`
  ADD CONSTRAINT `medico_ibfk_1` FOREIGN KEY (`id_posto`) REFERENCES `posto` (`id_posto`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
