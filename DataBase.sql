-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2025 at 06:40 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vac`
--

-- --------------------------------------------------------

--
-- Table structure for table `aplicacao`
--

CREATE TABLE `aplicacao` (
  `id_aplica` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_posto` int(11) NOT NULL,
  `id_medico` int(11) NOT NULL,
  `id_vaci` int(11) NOT NULL,
  `data_aplica` date NOT NULL,
  `dose_aplicad` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `atestado`
--

CREATE TABLE `atestado` (
  `id_atestado` int(11) NOT NULL,
  `id_paci` int(11) NOT NULL,
  `id_medico` int(11) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `justificativa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `atestado`
--

INSERT INTO `atestado` (`id_atestado`, `id_paci`, `id_medico`, `data_inicio`, `data_fim`, `justificativa`) VALUES
(1, 1, 1, '2025-05-01', '2025-05-07', 'Repouso por febre.'),
(2, 1, 2, '2025-05-08', '2025-05-14', 'Repouso por dor muscular.'),
(3, 1, 3, '2025-05-15', '2025-05-21', 'Repouso por recuperação pós-vacina.');

-- --------------------------------------------------------

--
-- Table structure for table `calendario_vacinal`
--

CREATE TABLE `calendario_vacinal` (
  `id_calendario` int(11) NOT NULL,
  `nome_vacina` varchar(100) NOT NULL,
  `doses_obrigatorias` varchar(255) NOT NULL,
  `doses_recomendadas` varchar(255) DEFAULT NULL,
  `sus` tinyint(1) NOT NULL DEFAULT 0,
  `grupo_indicado` varchar(100) DEFAULT 'Geral'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `calendario_vacinal`
--

INSERT INTO `calendario_vacinal` (`id_calendario`, `nome_vacina`, `doses_obrigatorias`, `doses_recomendadas`, `sus`, `grupo_indicado`) VALUES
(1, 'BCG', '1 dose (ao nascer)', '', 1, 'Geral'),
(2, 'Hepatite B', '3 doses (0-1-6 meses)', '', 1, 'Trabalhador da saúde'),
(3, 'Penta (DTP/Hib/Hepatite B)', '3 doses (2-4-6 meses)', '', 1, 'Geral'),
(4, 'Poliomielite inativada (VIP)', '4 doses (2-4-6 meses + reforço aos 15 meses)', '', 1, 'Geral'),
(5, 'Poliomielite oral bivalente (VOPb)', '— (substituída pela VIP)', '', 0, 'Geral'),
(6, 'Pneumocócica 10-valente', '3 doses (2-4-6 meses)', '4ª dose opcional em 12 meses', 1, 'Geral'),
(7, 'Pneumocócica 23-valente', '1 dose (≥ 60 anos)', '', 1, 'Doença crônica'),
(8, 'Meningocócica C (conjugada)', '2 doses (3–12 meses) + reforço em 5 anos', 'ACWY aos 11–12 anos', 1, 'Geral'),
(9, 'Meningocócica ACWY', '—', '1 dose aos 11–12 anos', 0, 'Geral'),
(10, 'Rotavírus humano', '2 doses (2-4 meses)', '', 1, 'Geral'),
(11, 'Febre amarela', '1 dose (9 meses) + reforço 4 anos em áreas de risco', '', 1, 'Viajante'),
(12, 'Tríplice viral (SCR)', '2 doses (12-15 meses)', 'SCRV como 2ª dose', 1, 'Geral'),
(13, 'Tetraviral (SCRV)', '—', '1 dose aos 15 meses', 1, 'Geral'),
(14, 'Varicela', '1 dose (15 meses)', '2 doses (15 meses + reforço 4 anos)', 1, 'Trabalhador da saúde'),
(15, 'Hepatite A', '1 dose (15 meses)', '2 doses em regiões endêmicas', 1, 'Viajante'),
(16, 'dTpa (adulto/gestante)', '1 dose gestante cada gestação', '3 doses adulto não vacinado (0-1-6 meses)', 1, 'Trabalhador da saúde'),
(17, 'dT (adulto)', 'reforço a cada 10 anos', '', 1, 'Geral'),
(18, 'Hepatite B (adulto)', '3 doses (0-1-6 meses)', '', 1, 'Geral'),
(19, 'HPV (quadrivalente/9-valente)', '1 dose (9-14 anos no PNI)', '2-3 doses conforme idade', 1, 'Geral'),
(20, 'Influenza', '1 dose anual (grupos prioritários)', '1 dose anual ≥6 meses', 1, 'Trabalhador da saúde'),
(21, 'Covid-19', 'varia conforme campanha', 'reforços anuais ou conforme risco', 1, 'Trabalhador da saúde'),
(22, 'Herpes-zóster (RZV)', '—', '2 doses ≥50 anos', 0, 'Idoso'),
(23, 'Dengue (Qdenga®)', '—', '3 doses 9-16 anos em área endêmica', 0, 'Geral'),
(24, 'VSR (Respiratório)', '—', '1 dose sazonal ≥60 anos e gestantes', 0, 'Geral'),
(25, 'Raiva (pré-exposição)', '—', '3 doses pré-exposição', 0, 'Viajante'),
(26, 'Vacinas de viajantes (tifóide, encefalite, etc.)', '—', 'esquemas conforme risco', 0, 'Viajante');

-- --------------------------------------------------------

--
-- Table structure for table `campanha`
--

CREATE TABLE `campanha` (
  `id_campanha` int(11) NOT NULL,
  `nome_campanha` varchar(255) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `descricao` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `campanha`
--

INSERT INTO `campanha` (`id_campanha`, `nome_campanha`, `data_inicio`, `data_fim`, `imagem`, `descricao`) VALUES
(1, 'Campanha A', '2025-06-05', '2025-06-04', 'uploads/campanhas/campanha_68421c2385cc34.67209433.jpg', 'Campanha de teste para saber se funciona'),
(2, 'Campanha B', '2025-03-13', '2025-06-04', 'uploads/campanhas/campanha_684221f7d0b843.06649431.jpg', 'campanha para não aparecer'),
(3, 'Campanha C', '2025-06-19', '2025-09-18', 'uploads/campanhas/campanha_68422518518599.94111440.jpg', 'sl só teste');

-- --------------------------------------------------------

--
-- Table structure for table `grupo_especial`
--

CREATE TABLE `grupo_especial` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `grupo` varchar(50) NOT NULL,
  `data_resposta` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `grupo_especial`
--

INSERT INTO `grupo_especial` (`id`, `id_usuario`, `grupo`, `data_resposta`) VALUES
(1, 1, 'Nenhum', '2025-06-07 01:40:45'),
(2, 1, 'Nenhum', '2025-06-07 01:41:52'),
(3, 1, 'Nenhum', '2025-06-07 01:47:22'),
(4, 1, 'Gestante', '2025-06-07 01:51:04'),
(5, 1, 'Nenhum', '2025-06-07 01:51:17');

--
-- Triggers `grupo_especial`
--
DELIMITER $$
CREATE TRIGGER `trg_update_usuario_grupo_especial` AFTER INSERT ON `grupo_especial` FOR EACH ROW BEGIN
  UPDATE usuario SET grupo_especial = NEW.grupo WHERE id_usuario = NEW.id_usuario;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_usuario_grupo_especial_update` AFTER UPDATE ON `grupo_especial` FOR EACH ROW BEGIN
  UPDATE usuario SET grupo_especial = NEW.grupo WHERE id_usuario = NEW.id_usuario;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `medico`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `medico`
--

INSERT INTO `medico` (`id_medico`, `nome_medico`, `cpf`, `email_medico`, `tel_medico`, `coren_crm`, `tipo_medico`, `naci_medico`, `id_posto`, `senha`) VALUES
(1, 'Dr. João', '12345678909', 'joao.medico@example.com', '11999999999', 'CRM-SP 123456', 'Clínico Geral', '1980-05-10', 11, 'senha123'),
(2, 'Dra. Maria', '98765432100', 'maria.medica@example.com', '21988888888', 'CRM-RJ 654321', 'Pediatra', '1985-08-15', 12, 'senha123'),
(3, 'Dr. Carlos', '11144477735', 'carlos.medico@example.com', '31977777777', 'COREN-MG 987654', 'Enfermeiro', '1978-12-20', 13, 'senha123');

-- --------------------------------------------------------

--
-- Table structure for table `posto`
--

CREATE TABLE `posto` (
  `id_posto` int(11) NOT NULL,
  `nome_posto` varchar(100) NOT NULL,
  `cep_posto` int(8) NOT NULL,
  `n_posto` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `posto`
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
-- Table structure for table `usuario`
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
  `senha` varchar(100) NOT NULL,
  `grupo_especial` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome_usuario`, `cpf`, `email_usuario`, `tel_usuario`, `genero_usuario`, `naci_usuario`, `peso_usuario`, `tipo_sang_usuario`, `med_usuario`, `doen_usuario`, `ale_usuario`, `cep_usuario`, `nc_usuario`, `senha`, `grupo_especial`) VALUES
(1, 'Rafael Favetta', '45260925840', 'rafaelfavetta@gmail.com', '19981084437', 'M', '2007-09-06', '77.00', 'A+', '', '', '', '13607030', 231, '$2y$10$7AgFK/3Cj6LkYeq2sB3OmeWAr0s7uys9zIL5C/kuPtykLhxT2bBXi', 'Nenhum'),
(2, 'Miguel Di-Tanno Viganó', '51382943857', 'miguelzin@gmail.com', '19999999999', 'M', '2007-02-20', '80.00', 'O+', '', '', '', '12600074', 211, '$2y$10$MRpGXV.HXK9B0X17qY6drONp1wnHMGZPJn4mNO8ce0MwB8IssYTS2', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vacina`
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
  `idade_meses_reco` smallint(6) DEFAULT NULL,
  `idade_anos_reco` smallint(6) DEFAULT NULL,
  `estoque` int(11) NOT NULL,
  `id_calendario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vacina`
--

INSERT INTO `vacina` (`id_vaci`, `nome_vaci`, `fabri_vaci`, `lote_vaci`, `idade_aplica`, `via_adimicao`, `n_dose`, `intervalo_dose`, `idade_meses_reco`, `idade_anos_reco`, `estoque`, `id_calendario`) VALUES
(22, 'BCG', 'Instituto Butantan', 'BCG2025A', 0, 'Intradérmica', 1, 0, 0, 0, 10000, 1),
(23, 'Hepatite B', 'Fiocruz', 'HEPB2025', 0, 'Intramuscular', 1, 1, 0, 0, 10000, 2),
(24, 'Penta (DTP/Hib/Hepatite B)', 'Fiocruz', 'PENTA2025', 2, 'Intramuscular', 3, 2, 2, 0, 10000, 3),
(25, 'Poliomielite inativada (VIP)', 'Sanofi', 'VIP2025L01', 2, 'Intramuscular', 3, 2, 2, 0, 10000, 4),
(27, 'Pneumocócica 10-valente', 'GSK', 'PN10V2025', 2, 'Intramuscular', 3, 2, 2, 0, 10000, 6),
(28, 'Meningocócica C (conjugada)', 'Fiocruz', 'MENCC2025', 3, 'Intramuscular', 2, 11, 3, 0, 10000, 8),
(29, 'Rotavírus humano', 'GSK', 'RTH2025', 2, 'Oral', 2, 2, 2, 0, 10000, 10),
(30, 'Febre amarela', 'Bio-Manguinhos', 'FA2025', 9, 'Subcutânea', 1, 0, 9, 0, 10000, 11),
(31, 'Tríplice viral (SCR)', 'MSD', 'SCR2025', 12, 'Subcutânea', 2, 3, 12, 1, 10000, 12),
(33, 'Hepatite A', 'Fiocruz', 'HEPA2025', 15, 'Intramuscular', 1, 0, 15, 1, 10000, 15),
(34, 'Varicela', 'MSD', 'VARIC2025', 15, 'Subcutânea', 1, 0, 15, 1, 10000, 14),
(35, 'HPV (quadrivalente/9-valente)', 'MSD', 'HPVQ2025', 108, 'Intramuscular', 2, 6, 108, 9, 10000, 19),
(36, 'dT (adulto)', 'Fiocruz', 'DT2025F', 120, 'Intramuscular', 3, 12, 120, 10, 10000, 17),
(37, 'Meningocócica ACWY', 'Fiocruz', 'ACWY2025', 132, 'Intramuscular', 1, 0, 132, 11, 10000, 9),
(39, 'Hepatite B (adulto)', 'Fiocruz', 'HEPBAD2025', 240, 'Intramuscular', 3, 1, 216, 18, 10000, 18),
(40, 'Influenza', 'Instituto Butantan', 'INF2025', 720, 'Intramuscular', 1, 12, 6, 0, 10000, 20),
(41, 'Pneumocócica 23-valente', 'MSD', 'PN23V2025', 720, 'Intramuscular', 1, 60, 720, 60, 10000, 7),
(42, 'Hepatite B', 'Fiocruz', 'HEPB2025', 0, 'Intramuscular', 3, 1, 0, 0, 10000, 2),
(43, 'dTpa (adulto/gestante)', 'Sanofi', 'DTPA2025', 0, 'Intramuscular', 1, 120, 216, 18, 10000, 16),
(44, 'Influenza', 'Instituto Butantan', 'INF2025', 0, 'Intramuscular', 1, 12, 6, 0, 10000, 20),
(45, 'Covid-19', 'Pfizer', 'COVID2025', 0, 'Intramuscular', 1, 12, 0, 0, 10000, 21),
(46, 'dTpa (adulto/gestante)', 'Sanofi', 'DTPA2025', 5, 'Intramuscular', 1, 0, 216, 18, 10000, 16),
(48, 'Poliomielite inativada (VIP)', 'Sanofi', 'VIP2025L01', 0, 'Intramuscular', 4, 0, 2, 0, 10000, 4),
(49, 'Poliomielite oral bivalente (VOPb)', 'Instituto Butantan', 'VOP2025B', 0, 'Oral', 0, 0, 3, 0, 10000, 5),
(50, 'Tetraviral (SCRV)', 'MSD', 'SCRV2025', 15, 'Subcutânea', 0, 0, 15, 1, 10000, 13),
(51, 'dTpa (adulto/gestante)', 'Sanofi', 'DTPA2025', 216, 'Intramuscular', 1, 6, 216, 18, 10000, 16),
(52, 'dT (adulto)', 'Fiocruz', 'DT2025F', 0, 'Intramuscular', 0, 0, 120, 10, 10000, 17),
(53, 'HPV (quadrivalente/9-valente)', 'MSD', 'HPVQ2025', 0, 'Intramuscular', 1, 0, 108, 9, 10000, 19),
(54, 'Covid-19', 'Pfizer', 'COVID2025', 0, 'Intramuscular', 0, 12, 0, 0, 10000, 21),
(55, 'Herpes-zóster (RZV)', 'GSK', 'HZ2025', 600, 'Intramuscular', 0, 2, 600, 50, 10000, 22),
(56, 'Dengue (Qdenga®)', 'Takeda', 'DENG2025', 108, 'Intramuscular', 0, 6, 108, 9, 10000, 23),
(57, 'VSR (Respiratório)', 'Pfizer', 'VSR2025', 720, 'Intramuscular', 0, 0, 720, 60, 10000, 24),
(58, 'Raiva (pré-exposição)', 'Bio-Manguinhos', 'RAIVA2025', 0, 'Intramuscular', 0, 1, NULL, NULL, 10000, 25),
(59, 'Vacinas de viajantes (tifóide, encefalite, etc.)', '—', '—', 0, 'Variável', 0, 0, NULL, NULL, 10000, 26);

-- --------------------------------------------------------

--
-- Table structure for table `vacina_contraindicada`
--

CREATE TABLE `vacina_contraindicada` (
  `id` int(11) NOT NULL,
  `grupo_especial` varchar(100) NOT NULL,
  `id_calendario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vacina_contraindicada`
--

INSERT INTO `vacina_contraindicada` (`id`, `grupo_especial`, `id_calendario`) VALUES
(1, 'Imunodeprimido', 11),
(2, 'Gestante', 12),
(3, 'Imunodeprimido', 12),
(4, 'Gestante', 14),
(5, 'Imunodeprimido', 14),
(6, 'Gestante', 13),
(7, 'Imunodeprimido', 13);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aplicacao`
--
ALTER TABLE `aplicacao`
  ADD PRIMARY KEY (`id_aplica`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_posto` (`id_posto`),
  ADD KEY `id_medico` (`id_medico`),
  ADD KEY `id_vaci` (`id_vaci`);

--
-- Indexes for table `atestado`
--
ALTER TABLE `atestado`
  ADD PRIMARY KEY (`id_atestado`),
  ADD KEY `id_paci` (`id_paci`),
  ADD KEY `id_medico` (`id_medico`);

--
-- Indexes for table `calendario_vacinal`
--
ALTER TABLE `calendario_vacinal`
  ADD PRIMARY KEY (`id_calendario`);

--
-- Indexes for table `campanha`
--
ALTER TABLE `campanha`
  ADD PRIMARY KEY (`id_campanha`);

--
-- Indexes for table `grupo_especial`
--
ALTER TABLE `grupo_especial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `medico`
--
ALTER TABLE `medico`
  ADD PRIMARY KEY (`id_medico`),
  ADD KEY `id_posto` (`id_posto`);

--
-- Indexes for table `posto`
--
ALTER TABLE `posto`
  ADD PRIMARY KEY (`id_posto`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Indexes for table `vacina`
--
ALTER TABLE `vacina`
  ADD PRIMARY KEY (`id_vaci`),
  ADD KEY `idx_vaci_calendario` (`id_calendario`);

--
-- Indexes for table `vacina_contraindicada`
--
ALTER TABLE `vacina_contraindicada`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_calendario` (`id_calendario`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aplicacao`
--
ALTER TABLE `aplicacao`
  MODIFY `id_aplica` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2407;

--
-- AUTO_INCREMENT for table `atestado`
--
ALTER TABLE `atestado`
  MODIFY `id_atestado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `calendario_vacinal`
--
ALTER TABLE `calendario_vacinal`
  MODIFY `id_calendario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `campanha`
--
ALTER TABLE `campanha`
  MODIFY `id_campanha` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `grupo_especial`
--
ALTER TABLE `grupo_especial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `medico`
--
ALTER TABLE `medico`
  MODIFY `id_medico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `posto`
--
ALTER TABLE `posto`
  MODIFY `id_posto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vacina`
--
ALTER TABLE `vacina`
  MODIFY `id_vaci` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `vacina_contraindicada`
--
ALTER TABLE `vacina_contraindicada`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aplicacao`
--
ALTER TABLE `aplicacao`
  ADD CONSTRAINT `aplicacao_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `aplicacao_ibfk_2` FOREIGN KEY (`id_posto`) REFERENCES `posto` (`id_posto`),
  ADD CONSTRAINT `aplicacao_ibfk_3` FOREIGN KEY (`id_medico`) REFERENCES `medico` (`id_medico`),
  ADD CONSTRAINT `aplicacao_ibfk_4` FOREIGN KEY (`id_vaci`) REFERENCES `vacina` (`id_vaci`);

--
-- Constraints for table `atestado`
--
ALTER TABLE `atestado`
  ADD CONSTRAINT `atestado_ibfk_1` FOREIGN KEY (`id_paci`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `atestado_ibfk_2` FOREIGN KEY (`id_medico`) REFERENCES `medico` (`id_medico`);

--
-- Constraints for table `grupo_especial`
--
ALTER TABLE `grupo_especial`
  ADD CONSTRAINT `grupo_especial_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Constraints for table `medico`
--
ALTER TABLE `medico`
  ADD CONSTRAINT `medico_ibfk_1` FOREIGN KEY (`id_posto`) REFERENCES `posto` (`id_posto`);

--
-- Constraints for table `vacina`
--
ALTER TABLE `vacina`
  ADD CONSTRAINT `fk_vaci_calendario` FOREIGN KEY (`id_calendario`) REFERENCES `calendario_vacinal` (`id_calendario`);

--
-- Constraints for table `vacina_contraindicada`
--
ALTER TABLE `vacina_contraindicada`
  ADD CONSTRAINT `vacina_contraindicada_ibfk_1` FOREIGN KEY (`id_calendario`) REFERENCES `calendario_vacinal` (`id_calendario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
