-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2025 at 06:57 PM
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

--
-- Dumping data for table `aplicacao`
--

INSERT INTO `aplicacao` (`id_aplica`, `id_usuario`, `id_posto`, `id_medico`, `id_vaci`, `data_aplica`, `dose_aplicad`) VALUES
(2407, 3, 5, 3, 22, '2007-09-06', 1),
(2408, 3, 2, 1, 37, '2007-09-06', 1),
(2409, 3, 3, 3, 37, '2007-11-06', 2),
(2410, 3, 3, 3, 37, '2008-01-06', 3),
(2411, 4, 4, 3, 30, '2025-06-16', 1),
(2412, 4, 3, 3, 56, '2025-06-15', 1),
(2413, 4, 4, 2, 22, '1978-02-02', 1),
(2414, 4, 3, 2, 42, '1978-04-05', 1),
(2415, 4, 7, 2, 42, '1978-05-06', 2),
(2416, 4, 1, 2, 42, '1978-06-09', 3),
(2417, 4, 1, 1, 37, '1976-03-02', 1),
(2418, 4, 6, 2, 37, '1976-03-05', 2),
(2419, 4, 6, 3, 37, '1976-12-09', 3),
(2420, 4, 8, 3, 49, '1976-03-02', 1);

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
(4, 3, 2, '2025-06-15', '2025-06-28', 'Repouso por dores no músculo posterior da coxa'),
(5, 3, 2, '2024-04-12', '2024-05-12', 'O paciente foi submetido à uma cirurgia que requer repouso imediato'),
(6, 4, 3, '2025-06-15', '2025-06-15', 'Tomou a vicina da dengue');

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
(1, 'João Sopara', '12345678909', 'joao.medico@example.com', '11999999999', 'CRM-SP 123456', 'Clínico Geral', '1980-05-10', 11, 'senha123'),
(2, 'Maria Longetti', '98765432100', 'maria.medica@example.com', '21988888888', 'CRM-RJ 654321', 'Pediatra', '1985-08-15', 12, 'senha123'),
(3, 'Carlos Pierote', '11144477735', 'carlos.medico@example.com', '31977777777', 'COREN-MG 987654', 'Enfermeiro', '1978-12-20', 13, 'senha123');

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
  `endereco` varchar(255) NOT NULL,
  `cidade` varchar(255) NOT NULL,
  `nc_usuario` int(10) NOT NULL,
  `senha` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome_usuario`, `cpf`, `email_usuario`, `tel_usuario`, `genero_usuario`, `naci_usuario`, `peso_usuario`, `tipo_sang_usuario`, `med_usuario`, `doen_usuario`, `ale_usuario`, `cep_usuario`, `endereco`, `cidade`, `nc_usuario`, `senha`) VALUES
(2, 'Miguel Di-Tanno Viganó', '51382943857', 'miguelzin@gmail.com', '19999999999', 'M', '2007-02-20', '80.00', 'O+', '', '', '', '12600074', '', '', 211, '$2y$10$MRpGXV.HXK9B0X17qY6drONp1wnHMGZPJn4mNO8ce0MwB8IssYTS2'),
(3, 'Rafael Favetta', '45260925840', 'rafaelfavetta@gmail.com', '19981084437', 'M', '2007-09-06', '77.00', 'A+', '', '', '', '13607030', 'Rua Professor Vicente Casale Padovani, Jardim Nossa Senhora de Fátima, Araras - SP', 'Araras', 231, '$2y$10$ArQhH63CSZ4IGYqZK9/.bu8fBKVwLYTTWEI9rNQAfue78k7qHHw2q'),
(4, 'André Favetta', '16061751818', 'andfavetta@gmail.com', '19981291195', 'M', '1976-03-02', '85.00', 'A+', '', '', '', '13607030', 'Rua Professor Vicente Casale Padovani, Jardim Nossa Senhora de Fátima, Araras - SP', 'Araras', 231, '$2y$10$RFeLbAqEJv7i/m4UDFSODOc8xzyS5Hs/fcr884/lDpVjvZj/AAquy');

-- --------------------------------------------------------

--
-- Table structure for table `vacina`
--

CREATE TABLE `vacina` (
  `id_vaci` int(11) NOT NULL,
  `nome_vaci` varchar(100) NOT NULL,
  `fabri_vaci` varchar(100) NOT NULL,
  `lote_vaci` varchar(50) NOT NULL,
  `via_adimicao` varchar(50) NOT NULL,
  `n_dose` int(1) NOT NULL,
  `intervalo_dose` int(11) NOT NULL,
  `idade_reco` varchar(20) DEFAULT NULL,
  `estoque` int(11) NOT NULL,
  `sus` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vacina`
--

INSERT INTO `vacina` (`id_vaci`, `nome_vaci`, `fabri_vaci`, `lote_vaci`, `via_adimicao`, `n_dose`, `intervalo_dose`, `idade_reco`, `estoque`, `sus`) VALUES
(22, 'BCG', 'Instituto Butantan', 'BCG2025A', 'Intradérmica', 1, 0, '0 meses', 9998, 1),
(24, 'Penta (DTP/Hib/Hepatite B)', 'Fiocruz', 'PENTA2025', 'Intramuscular', 3, 2, '2 meses', 10000, 1),
(25, 'Poliomielite inativada (VIP)', 'Sanofi', 'VIP2025L01', 'Intramuscular', 4, 2, '2 meses', 10000, 1),
(27, 'Pneumocócica 10-valente', 'GSK', 'PN10V2025', 'Intramuscular', 3, 2, '2 meses', 10000, 1),
(29, 'Rotavírus humano', 'GSK', 'RTH2025', 'Oral', 2, 2, '2 meses', 10000, 1),
(30, 'Febre amarela', 'Bio-Manguinhos', 'FA2025', 'Subcutânea', 1, 0, '5 anos', 9999, 1),
(31, 'Tríplice viral (SCR)', 'MSD', 'SCR2025', 'Subcutânea', 2, 3, '1 ano', 10000, 1),
(33, 'Hepatite A', 'Fiocruz', 'HEPA2025', 'Intramuscular', 2, 6, '1 ano', 10000, 1),
(34, 'Varicela', 'MSD', 'VARIC2025', 'Subcutânea', 2, 36, '1 ano', 10000, 1),
(35, 'HPV (quadrivalente/9-valente)', 'MSD', 'HPVQ2025', 'Intramuscular', 2, 6, '9 anos', 10000, 1),
(37, 'Meningocócica ACWY', 'Fiocruz', 'ACWY2025', 'Intramuscular', 3, 2, '0 meses', 9994, 1),
(39, 'Hepatite B (adulto)', 'Fiocruz', 'HEPBAD2025', 'Intramuscular', 3, 2, '18 anos', 10000, 1),
(40, 'Influenza', 'Instituto Butantan', 'INF2025', 'Intramuscular', 1, 0, '9 anos', 10000, 1),
(41, 'Pneumocócica 23-valente', 'MSD', 'PN23V2025', 'Intramuscular', 1, 0, '5 anos', 10000, 1),
(42, 'Hepatite B', 'Fiocruz', 'HEPB2025', 'Intramuscular', 3, 1, '0 meses', 9997, 1),
(49, 'Poliomielite oral bivalente (VOPb)', 'Instituto Butantan', 'VOP2025B', 'Oral', 1, 0, '0 meses', 9999, 1),
(50, 'Tetraviral (SCRV)', 'MSD', 'SCRV2025', 'Subcutânea', 2, 3, '1 ano', 10000, 1),
(51, 'dTpa (adulto/gestante)', 'Sanofi', 'DTPA2025', 'Intramuscular', 1, 0, '18 anos', 10000, 1),
(55, 'Herpes-zóster (RZV)', 'GSK', 'HZ2025', 'Intramuscular', 2, 2, '50 anos', 10000, 1),
(56, 'Dengue (Qdenga®)', 'Takeda', 'DENG2025', 'Intramuscular', 2, 3, '10 anos', 9999, 0),
(57, 'VSR (Respiratório)', 'Pfizer', 'VSR2025', 'Intramuscular', 1, 0, 'A qualquer momento', 10000, 0),
(58, 'Raiva (pré-exposição)', 'Bio-Manguinhos', 'RAIVA2025', 'Intramuscular', 1, 0, 'A qualquer momento', 10000, 0),
(59, 'Vacinas de viajantes (tifóide, encefalite, etc.)', '—', '—', 'Variável', 1, 0, 'A qualquer momento', 10000, 0),
(63, 'Meningocócica C (conjugada)', 'Fiocruz', 'MENCC2025', 'Intramuscular', 2, 2, '3 meses', 10000, 1),
(64, 'dT', 'Fiocruz', 'DT2025F', 'Intramuscular', 10, 120, '7 anos', 10000, 1);

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
-- Indexes for table `campanha`
--
ALTER TABLE `campanha`
  ADD PRIMARY KEY (`id_campanha`);

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
  ADD PRIMARY KEY (`id_vaci`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aplicacao`
--
ALTER TABLE `aplicacao`
  MODIFY `id_aplica` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2421;

--
-- AUTO_INCREMENT for table `atestado`
--
ALTER TABLE `atestado`
  MODIFY `id_atestado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `campanha`
--
ALTER TABLE `campanha`
  MODIFY `id_campanha` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vacina`
--
ALTER TABLE `vacina`
  MODIFY `id_vaci` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

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
-- Constraints for table `medico`
--
ALTER TABLE `medico`
  ADD CONSTRAINT `medico_ibfk_1` FOREIGN KEY (`id_posto`) REFERENCES `posto` (`id_posto`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
