-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 14-Ago-2024 às 19:48
-- Versão do servidor: 5.7.36
-- versão do PHP: 8.1.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `db_salas`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_cargos`
--

CREATE TABLE `tb_cargos` (
  `id_cargo` int(11) NOT NULL,
  `cargo` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tb_cargos`
--

INSERT INTO `tb_cargos` (`id_cargo`, `cargo`) VALUES
(2, 'Professor(a)'),
(3, 'Aluno(a)'),
(5, 'Funcionario(a)'),
(6, 'Aux. Administrativo'),
(9, 'Cooperos'),
(12, 'Aux.limpeza'),
(13, 'ASG'),
(14, 'teste');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_ocupacoes`
--

CREATE TABLE `tb_ocupacoes` (
  `id_ocupacao` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `dh_entrada` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dh_saida` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `observacoes` varchar(200) DEFAULT NULL,
  `devolver_chave` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_reservas`
--

CREATE TABLE `tb_reservas` (
  `id_reserva` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `entrada_previsao` datetime NOT NULL,
  `saida_previsao` datetime NOT NULL,
  `dh_reserva` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `eh_aprovada` tinyint(4) NOT NULL DEFAULT '0',
  `devolver_chave` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tb_reservas`
--

INSERT INTO `tb_reservas` (`id_reserva`, `id_sala`, `id_usuario`, `entrada_previsao`, `saida_previsao`, `dh_reserva`, `eh_aprovada`, `devolver_chave`) VALUES
(29, 22, 12, '2024-07-18 15:33:00', '2024-07-25 15:33:00', '2024-07-11 15:34:02', 1, 1),
(30, 12, 12, '2024-07-11 15:34:00', '2024-07-11 15:34:00', '2024-07-11 15:34:24', 0, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_salas`
--

CREATE TABLE `tb_salas` (
  `id_sala` int(11) NOT NULL,
  `sala` varchar(100) NOT NULL,
  `bloco` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tb_salas`
--

INSERT INTO `tb_salas` (`id_sala`, `sala`, `bloco`) VALUES
(4, 'A1/A2  - laboratorio de dómotica', 'Bloco A'),
(5, 'A3 - sala de aula ', 'Bloco A'),
(7, 'A4 - Sala de aula', 'Bloco A'),
(8, 'A5 - Sala de aula ', 'Bloco A'),
(9, 'A6 - Sala de aula ', 'Bloco A'),
(10, 'A7 - Sala de aula', 'Bloco A'),
(11, 'A8 - Sala de aula', 'Bloco A'),
(12, 'A9 - Sala de aula', 'Bloco A'),
(13, 'A10 - Sala de aula', 'Bloco A'),
(14, 'A11 - Sala de Informática', 'Bloco A'),
(15, 'A12 - Sala de aula', 'Bloco A'),
(16, 'A13 - Laboratório de Automação', 'Bloco A'),
(17, 'A14 - Laboratório de Pneumática', 'Bloco A'),
(18, 'B5 - Sala de aula', 'Bloco B'),
(19, 'B7 - Laboratório de informática 1', 'Bloco B'),
(20, 'B9 - Laboratório de informática 2', 'Bloco B'),
(21, 'B10 - Laboratório de fotovoltaica', 'Bloco B'),
(22, 'C4 - Laboratório de eletrônica', 'Bloco C'),
(23, 'C5 - Sala de aula', 'Bloco C'),
(24, 'C6 - Laboratório de medidas elétricas', 'Bloco C'),
(25, 'C7 - Laboratório de eletricidade predial', 'Bloco C'),
(26, 'D1 - Laboratório de robotica', 'Bloco D'),
(27, 'D2 - Laboratorio de eletricidade industrial ', 'Bloco  D '),
(28, 'E1 - Laboratório de instrumentação', 'Bloco E'),
(29, 'E2 - Laboratório de telecom / redes', 'Bloco E'),
(30, 'E3 - Sala de aula solar', 'Bloco E'),
(31, 'E4 - Sala de aula eólica', 'Bloco E'),
(32, 'E5 - Sala de aula GWO', 'Bloco E'),
(33, 'E6 - Senai lab', 'Bloco E'),
(34, 'E8 - Sala de aula segurança', 'Bloco E'),
(35, 'E9 - Laboratório de metrologia ', 'Bloco E'),
(36, 'F1 - Sala de aula 1 ', 'Bloco F - Show room'),
(37, 'F2 - Sala de aula 2', 'Bloco F - Show room'),
(38, 'F3 - Sala de aula 3', 'Bloco F - Show room'),
(39, 'F4 - Sala de aula 4', 'Bloco F - Show room'),
(40, 'F5 - Sala de aula 5', 'Bloco F - Show room'),
(41, 'F7 - Laboratório de informática mezanino', 'Bloco F - Show room'),
(42, 'F8 - Laboratório de mecânica de manutenção', 'Bloco F - Show room'),
(43, 'G1 - Laboratório de segurança', 'Bloco G'),
(44, 'G2 - Laboratório de usinagem', 'Bloco G'),
(45, 'G1 - Laboratório de soldagem', 'Bloco G'),
(46, 'G1 - Laboratório de refrigeração', 'Bloco G'),
(47, '1 - Automação industrial', 'Unidade móvel'),
(48, '2 - Instalação elétricas 1', 'Bloco móvel'),
(49, '3 - Instalação elétricas 2 - Nova', 'Bloco móvel'),
(50, '4 - Eletroeletrônica ', 'Bloco móvel'),
(51, '5 - Soldagem', 'Bloco móvel'),
(52, '6 - Segurança', 'Bloco móvel '),
(53, '1 - Campo de poste - dry wall', 'Extra'),
(54, '2 - GWO (Antigo multiuso)', 'Extra'),
(55, '3 - Acesso torre nacele', ' Extras'),
(56, '4 - Banheiro feminino', 'Extra'),
(57, '5 - Banheiro masculino ', 'Extra');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_usuarios`
--

CREATE TABLE `tb_usuarios` (
  `id_usuario` int(11) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `nome_usuario` varchar(45) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(16) DEFAULT NULL,
  `eh_admin` tinyint(1) NOT NULL DEFAULT '0',
  `eh_ativo` tinyint(1) NOT NULL DEFAULT '0',
  `id_cargo` int(11) NOT NULL,
  `dh_cadastro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tb_usuarios`
--

INSERT INTO `tb_usuarios` (`id_usuario`, `cpf`, `nome_usuario`, `senha`, `telefone`, `eh_admin`, `eh_ativo`, `id_cargo`, `dh_cadastro`) VALUES
(12, '123.456.789-10', 'Administrador', '$2y$10$2gEGoXrVonXNZXLTJ4Cmc.v.idE0cv5gj0AjbTEkcRo4fh6FrQ27i', '', 1, 1, 6, '2024-06-13 22:32:17'),
(22, '245.181.584-51', 'Alice Lima', '123', '(00) 0 0000-0000', 0, 1, 3, '2024-07-11 15:36:39'),
(23, '115.151.515-25', 'Georgia Ramos', '321', '(15) 4 8252-2625', 0, 1, 13, '2024-07-11 15:39:03');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `tb_cargos`
--
ALTER TABLE `tb_cargos`
  ADD PRIMARY KEY (`id_cargo`);

--
-- Índices para tabela `tb_ocupacoes`
--
ALTER TABLE `tb_ocupacoes`
  ADD PRIMARY KEY (`id_ocupacao`),
  ADD KEY `id_sala` (`id_sala`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices para tabela `tb_reservas`
--
ALTER TABLE `tb_reservas`
  ADD PRIMARY KEY (`id_reserva`),
  ADD KEY `id_sala` (`id_sala`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices para tabela `tb_salas`
--
ALTER TABLE `tb_salas`
  ADD PRIMARY KEY (`id_sala`),
  ADD UNIQUE KEY `descricao_sala_UNIQUE` (`sala`);

--
-- Índices para tabela `tb_usuarios`
--
ALTER TABLE `tb_usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `cpf_UNIQUE` (`cpf`),
  ADD KEY `id_cargo` (`id_cargo`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tb_cargos`
--
ALTER TABLE `tb_cargos`
  MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `tb_ocupacoes`
--
ALTER TABLE `tb_ocupacoes`
  MODIFY `id_ocupacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de tabela `tb_reservas`
--
ALTER TABLE `tb_reservas`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de tabela `tb_salas`
--
ALTER TABLE `tb_salas`
  MODIFY `id_sala` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de tabela `tb_usuarios`
--
ALTER TABLE `tb_usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `tb_ocupacoes`
--
ALTER TABLE `tb_ocupacoes`
  ADD CONSTRAINT `tb_ocupacoes_ibfk_1` FOREIGN KEY (`id_sala`) REFERENCES `tb_salas` (`id_sala`),
  ADD CONSTRAINT `tb_ocupacoes_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuarios` (`id_usuario`);

--
-- Limitadores para a tabela `tb_reservas`
--
ALTER TABLE `tb_reservas`
  ADD CONSTRAINT `tb_reservas_ibfk_1` FOREIGN KEY (`id_sala`) REFERENCES `tb_salas` (`id_sala`),
  ADD CONSTRAINT `tb_reservas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuarios` (`id_usuario`);

--
-- Limitadores para a tabela `tb_usuarios`
--
ALTER TABLE `tb_usuarios`
  ADD CONSTRAINT `tb_usuarios_ibfk_1` FOREIGN KEY (`id_cargo`) REFERENCES `tb_cargos` (`id_cargo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
