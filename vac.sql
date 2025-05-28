-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2025 at 09:55 PM
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
(3, 1, 3, '2025-05-15', '2025-05-21', 'Repouso por recuperação pós-vacina.'),
(4, 1, 2, '2025-05-27', '2025-05-28', 'teste pro medico'),
(5, 2, 2, '2025-05-27', '2025-05-29', 'YAAAMAAAL'),
(6, 2, 3, '2020-05-20', '2021-08-19', 'gaaa'),
(7, 2, 1, '2025-05-27', '2025-05-27', 'fafafafafafafa'),
(8, 2, 1, '2025-05-27', '2025-05-28', 'neymarr'),
(9, 2, 1, '2025-05-27', '2025-05-29', '215151'),
(10, 2, 3, '2025-05-27', '2025-05-29', 'dfghj,'),
(11, 1, 2, '2025-05-27', '2025-08-01', 'agor avai'),
(12, 2, 3, '2025-05-27', '2026-10-10', 'assinatura'),
(13, 1, 3, '2020-02-21', '2021-02-20', 'da'),
(14, 2, 3, '2020-05-20', '2021-08-08', 'dada'),
(15, 2, 1, '2025-02-27', '2025-05-27', 'vai'),
(16, 1, 1, '2025-05-27', '2025-05-29', 'etstta'),
(17, 2, 1, '2025-05-27', '2025-05-29', 'teetetet'),
(18, 1, 1, '2025-05-27', '2025-05-29', 'tetete'),
(19, 2, 3, '2025-05-27', '2025-05-28', 'mmm'),
(20, 2, 3, '2025-05-27', '2025-05-28', 'dddddd'),
(21, 1, 2, '2026-05-30', '2027-05-10', 'dedededededede'),
(22, 2, 1, '2023-08-29', '2029-12-08', 'grande'),
(23, 3, 2, '2025-05-27', '2038-05-25', 'agr sim'),
(24, 3, 1, '2025-05-27', '2026-06-27', 'teste'),
(25, 3, 2, '2025-05-28', '2025-05-30', 'prefeitura'),
(26, 3, 1, '2025-05-30', '2026-05-21', 'test'),
(27, 3, 2, '2025-05-28', '2025-05-28', 'Repouso fundamental após cirurgia no músculo posterior da panturrilha.'),
(28, 3, 1, '2025-05-28', '2026-05-31', 'repouso crucial em razão do esforço físico realizado nos últimos dias'),
(29, 3, 2, '2025-06-08', '2025-07-08', 'repouso extremo devido ao procedimento realizado em seu coração'),
(30, 3, 2, '2025-09-06', '2025-09-07', 'é aniversário dele'),
(31, 3, 2, '2026-09-06', '2026-09-07', 'é aniversário dele de novo'),
(32, 3, 2, '2025-05-28', '2025-09-20', 'comeu demais');

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
(1, 'João Cervi', '12345678909', 'joao.medico@example.com', '11999999999', 'CRM-SP 123456', 'Clínico Geral', '1980-05-10', 11, 'senha123'),
(2, 'Maria Longet', '98765432100', 'maria.medica@example.com', '21988888888', 'CRM-RJ 654321', 'Pediatra', '1985-08-15', 12, 'senha123'),
(3, 'Carlos Pereira', '11144477735', 'carlos.medico@example.com', '31977777777', 'COREN-MG 987654', 'Enfermeiro', '1978-12-20', 13, 'senha123');

-- --------------------------------------------------------

--
-- Table structure for table `posto`
--

CREATE TABLE `posto` (
  `id_posto` int(11) NOT NULL,
  `nome_posto` varchar(100) NOT NULL,
  `cep_posto` int(8) NOT NULL,
  `n_posto` int(10) NOT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `posto`
--

INSERT INTO `posto` (`id_posto`, `nome_posto`, `cep_posto`, `n_posto`, `endereco`, `cidade`) VALUES
(1, 'Posto A', 12345678, 100, NULL, NULL),
(2, 'Posto B', 12345679, 101, NULL, NULL),
(3, 'Posto C', 12345680, 102, NULL, NULL),
(4, 'Posto D', 12345681, 103, NULL, NULL),
(5, 'Posto E', 12345682, 104, NULL, NULL),
(6, 'Posto F', 12345683, 105, NULL, NULL),
(7, 'Posto G', 12345684, 106, NULL, NULL),
(8, 'Posto H', 12345685, 107, NULL, NULL),
(9, 'Posto I', 12345686, 108, NULL, NULL),
(10, 'Posto J', 12345687, 109, NULL, NULL),
(11, 'Posto K', 12345000, 200, NULL, NULL),
(12, 'Posto L', 12345001, 201, NULL, NULL),
(13, 'Posto M', 12345002, 202, NULL, NULL);

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
  `endereco` varchar(255) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome_usuario`, `cpf`, `email_usuario`, `tel_usuario`, `genero_usuario`, `naci_usuario`, `peso_usuario`, `tipo_sang_usuario`, `med_usuario`, `doen_usuario`, `ale_usuario`, `cep_usuario`, `nc_usuario`, `senha`, `endereco`, `cidade`) VALUES
(3, 'Rafael Favetta', '45260925840', 'rafaelfavetta@gmail.com', '19981084437', 'M', '2007-09-06', '77.00', 'A+', '', '', '', '13607030', 231, '$2y$10$zUO4p0W0OoScwzXiiLcrGeUchmtk5Zrlsdry5LWOeD5WJLY02ACsS', 'Rua Professor Vicente Casale Padovani, Jardim Nossa Senhora de Fátima, Araras - SP', 'Araras');

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
  `estoque` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vacina`
--

INSERT INTO `vacina` (`id_vaci`, `nome_vaci`, `fabri_vaci`, `lote_vaci`, `idade_aplica`, `via_adimicao`, `n_dose`, `intervalo_dose`, `estoque`) VALUES
(1, 'Vacina A', 'Fabricante A', 'Lote001', 18, 'Intramuscular', 2, 30, 100),
(2, 'Vacina B', 'Fabricante B', 'Lote002', 12, 'Subcutânea', 1, 0, 50),
(3, 'Vacina C', 'Fabricante C', 'Lote003', 60, 'Oral', 3, 60, 200),
(4, 'Rafafez', 'Rafa', '25', 10, 'Intravenoso', 2, 1, 0),
(5, 'Vacina do Rafa', 'eu mesmo', '44', 10, 'Gotinha', 5, 4, 1885),
(6, 'Favas', 'Favetta Supermercados', '1', 10, 'Injeção', 2, 2, 500);

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
-- Indexes for table `medico`
--
ALTER TABLE `medico`
  ADD PRIMARY KEY (`id_medico`),
  ADD UNIQUE KEY `cpf` (`cpf`),
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
  MODIFY `id_aplica` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2408;

--
-- AUTO_INCREMENT for table `atestado`
--
ALTER TABLE `atestado`
  MODIFY `id_atestado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

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
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vacina`
--
ALTER TABLE `vacina`
  MODIFY `id_vaci` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
