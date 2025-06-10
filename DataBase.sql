-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 10/06/2025 às 02:50
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

-- --------------------------------------------------------

--
--


--

CREATE TABLE `campanha` (
  `id_campanha` int(11) NOT NULL,
  `nome_campanha` varchar(255) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `descricao` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `campanha`
--

INSERT INTO `campanha` (`id_campanha`, `nome_campanha`, `data_inicio`, `data_fim`, `imagem`, `descricao`) VALUES
(1, 'Campanha A', '2025-06-05', '2025-06-04', 'uploads/campanhas/campanha_68421c2385cc34.67209433.jpg', 'Campanha de teste para saber se funciona'),
(2, 'Campanha B', '2025-03-13', '2025-06-04', 'uploads/campanhas/campanha_684221f7d0b843.06649431.jpg', 'campanha para não aparecer'),
(3, 'Campanha C', '2025-06-19', '2025-09-18', 'uploads/campanhas/campanha_68422518518599.94111440.jpg', 'sl só teste');

-- --------------------------------------------------------

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
  `endereco` varchar(255) NOT NULL,
  `cidade` varchar(255) NOT NULL,
  `nc_usuario` int(10) NOT NULL,
  `senha` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome_usuario`, `cpf`, `email_usuario`, `tel_usuario`, `genero_usuario`, `naci_usuario`, `peso_usuario`, `tipo_sang_usuario`, `med_usuario`, `doen_usuario`, `ale_usuario`, `cep_usuario`, `endereco`, `cidade`, `nc_usuario`, `senha`) VALUES
(2, 'Miguel Di-Tanno Viganó', '51382943857', 'miguelzin@gmail.com', '19999999999', 'M', '2007-02-20', 80.00, 'O+', '', '', '', '12600074', '', '', 211, '$2y$10$MRpGXV.HXK9B0X17qY6drONp1wnHMGZPJn4mNO8ce0MwB8IssYTS2'),
(3, 'Rafael Favetta', '45260925840', 'rafaelfavetta@gmail.com', '19981084437', 'M', '2007-09-06', 77.00, 'A+', '', '', '', '13607030', 'Rua Professor Vicente Casale Padovani, Jardim Nossa Senhora de Fátima, Araras - SP', 'Araras', 231, '$2y$10$ArQhH63CSZ4IGYqZK9/.bu8fBKVwLYTTWEI9rNQAfue78k7qHHw2q');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vacina`
--

CREATE TABLE `vacina` (
  `id_vaci` int(11) NOT NULL,
  `nome_vaci` varchar(100) NOT NULL,
  `fabri_vaci` varchar(100) NOT NULL,
  `lote_vaci` varchar(50) NOT NULL,
  `via_adimicao` varchar(50) NOT NULL,
  `n_dose` int(1) NOT NULL,
  `intervalo_dose` int(11) NOT NULL,
  `idade_meses_reco` smallint(6) DEFAULT NULL,
  `idade_anos_reco` smallint(6) DEFAULT NULL,
  `estoque` int(11) NOT NULL,
  `sus` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vacina`
--

INSERT INTO `vacina` (`id_vaci`, `nome_vaci`, `fabri_vaci`, `lote_vaci`, `via_adimicao`, `n_dose`, `intervalo_dose`, `idade_meses_reco`, `idade_anos_reco`, `estoque`, `sus`) VALUES
(22, 'BCG', 'Instituto Butantan', 'BCG2025A', 'Intradérmica', 1, 0, 0, 0, 10000, 1),
(24, 'Penta (DTP/Hib/Hepatite B)', 'Fiocruz', 'PENTA2025', 'Intramuscular', 3, 2, 2, 0, 10000, 1),
(25, 'Poliomielite inativada (VIP)', 'Sanofi', 'VIP2025L01', 'Intramuscular', 4, 2, 2, 0, 10000, 1),
(27, 'Pneumocócica 10-valente', 'GSK', 'PN10V2025', 'Intramuscular', 3, 2, 2, 0, 10000, 1),
(28, 'Meningocócica C (conjugada)', 'Fiocruz', 'MENCC2025', 'Intramuscular', 2, 11, 3, 0, 10000, 1),
(29, 'Rotavírus humano', 'GSK', 'RTH2025', 'Oral', 2, 2, 2, 0, 10000, 1),
(30, 'Febre amarela', 'Bio-Manguinhos', 'FA2025', 'Subcutânea', 1, 0, 60, 5, 10000, 1),
(31, 'Tríplice viral (SCR)', 'MSD', 'SCR2025', 'Subcutânea', 2, 3, 12, 1, 10000, 1),
(33, 'Hepatite A', 'Fiocruz', 'HEPA2025', 'Intramuscular', 2, 6, 12, 1, 10000, 1),
(34, 'Varicela', 'MSD', 'VARIC2025', 'Subcutânea', 2, 36, 12, 1, 10000, 1),
(35, 'HPV (quadrivalente/9-valente)', 'MSD', 'HPVQ2025', 'Intramuscular', 2, 6, 108, 9, 10000, 1),
(37, 'Meningocócica ACWY', 'Fiocruz', 'ACWY2025', 'Intramuscular', 3, 2, 3, 0, 10000, 1),
(39, 'Hepatite B (adulto)', 'Fiocruz', 'HEPBAD2025', 'Intramuscular', 3, 2, 216, 18, 10000, 1),
(40, 'Influenza', 'Instituto Butantan', 'INF2025', 'Intramuscular', 1, 0, 108, 9, 10000, 1),
(41, 'Pneumocócica 23-valente', 'MSD', 'PN23V2025', 'Intramuscular', 1, 0, 60, 5, 10000, 1),
(42, 'Hepatite B', 'Fiocruz', 'HEPB2025', 'Intramuscular', 3, 1, 0, 0, 10000, 1),
(44, 'Influenza', 'Instituto Butantan', 'INF2025', 'Intramuscular', 1, 12, 6, 0, 10000, 1),
(49, 'Poliomielite oral bivalente (VOPb)', 'Instituto Butantan', 'VOP2025B', 'Oral', 0, 0, 3, 0, 10000, 1),
(50, 'Tetraviral (SCRV)', 'MSD', 'SCRV2025', 'Subcutânea', 2, 3, 12, 1, 10000, 1),
(51, 'dTpa (adulto/gestante)', 'Sanofi', 'DTPA2025', 'Intramuscular', 1, 0, 216, 18, 10000, 1),
(53, 'HPV (quadrivalente/9-valente)', 'MSD', 'HPVQ2025', 'Intramuscular', 1, 0, 108, 9, 10000, 1),
(55, 'Herpes-zóster (RZV)', 'GSK', 'HZ2025', 'Intramuscular', 2, 2, 600, 50, 10000, 1),
(56, 'Dengue (Qdenga®)', 'Takeda', 'DENG2025', 'Intramuscular', 2, 3, 120, 10, 10000, 0),
(57, 'VSR (Respiratório)', 'Pfizer', 'VSR2025', 'Intramuscular', 1, 0, 216, 18, 10000, 0),
(58, 'Raiva (pré-exposição)', 'Bio-Manguinhos', 'RAIVA2025', 'Intramuscular', 0, 1, NULL, NULL, 10000, 0),
(59, 'Vacinas de viajantes (tifóide, encefalite, etc.)', '—', '—', 'Variável', 0, 0, NULL, NULL, 10000, 0),
(63, 'Meningocócica C (conjugada)', 'Fiocruz', 'MENCC2025', 'Intramuscular', 2, 2, 3, 0, 10000, 1),
(64, 'dT (adulto)', 'Fiocruz', 'DT2025F', 'Intramuscular', 10, 120, 84, 7, 10000, 1);

-- --------------------------------------------------------


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
  MODIFY `id_atestado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;--

--
-- AUTO_INCREMENT de tabela `campanha`
--
ALTER TABLE `campanha`
  MODIFY `id_campanha` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--


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
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `vacina`
--
ALTER TABLE `vacina`
  MODIFY `id_vaci` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;


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

--
-- Restrições para tabelas `medico`
--
ALTER TABLE `medico`
  ADD CONSTRAINT `medico_ibfk_1` FOREIGN KEY (`id_posto`) REFERENCES `posto` (`id_posto`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- Não crie as tabelas calendario_vacinal ou grupo_especial