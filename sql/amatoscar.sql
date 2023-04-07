-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 07-Abr-2023 às 16:28
-- Versão do servidor: 8.0.32
-- versão do PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `amatoscar`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `participantes`
--

CREATE TABLE `participantes` (
  `id` int NOT NULL,
  `id_reuniao` int NOT NULL,
  `nome_participante` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `participantes`
--

INSERT INTO `participantes` (`id`, `id_reuniao`, `nome_participante`) VALUES
(4, 2, 'David'),
(5, 2, 'Eve'),
(6, 2, 'Frank'),
(10, 4, 'Jack'),
(11, 4, 'Karen'),
(12, 4, 'Leo'),
(17, 10, 'André Antunes'),
(18, 10, 'António Farragola Santos'),
(19, 10, 'António Gomes'),
(20, 10, 'António Mendonça'),
(21, 11, 'André Antunes'),
(22, 11, 'António Farragola Santos'),
(23, 11, 'António Mendonça'),
(24, 11, 'António Ramalho'),
(25, 12, 'André Antunes'),
(26, 12, 'André Madeira'),
(27, 12, 'António Gomes'),
(28, 12, 'António Ramalho'),
(29, 13, 'André Antunes'),
(30, 13, 'António Mendonça'),
(31, 13, 'António Ramalho'),
(32, 13, 'Luis Craveiro'),
(38, 16, 'André Madeira'),
(39, 16, 'António Mendonça'),
(40, 16, 'António Ramalho'),
(57, 20, 'André Antunes'),
(58, 20, 'António Farragola Santos'),
(59, 20, 'António Mendonça'),
(60, 20, 'António Ramalho'),
(61, 21, 'André Antunes'),
(62, 21, 'André Madeira'),
(63, 21, 'António Gomes'),
(64, 21, 'António Ramalho');

-- --------------------------------------------------------

--
-- Estrutura da tabela `reunioes`
--

CREATE TABLE `reunioes` (
  `id` int NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `data` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  `organizador` varchar(255) NOT NULL,
  `id_sala` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `reunioes`
--

INSERT INTO `reunioes` (`id`, `motivo`, `data`, `hora_inicio`, `hora_fim`, `organizador`, `id_sala`) VALUES
(2, 'Design Review', '2023-04-02', '14:00:00', '15:30:00', 'João Carlos', 2),
(4, 'Team Building', '2023-04-04', '16:00:00', '17:00:00', 'João Carlos', 4),
(9, 'Team Building', '2023-04-07', '16:00:00', '17:00:00', 'Manuel Carreiras', 4),
(10, 'Teste desta merda', '2023-04-07', '15:00:00', '16:30:00', 'Manuel Carreiras', 1),
(11, 'Teste desta merda', '2023-04-07', '15:00:00', '18:00:00', 'Manuel Carreiras', 1),
(12, 'Testar esta cena', '2023-04-08', '15:00:00', '14:30:00', 'Manuel Carreiras', 1),
(13, 'Teste desta bosta', '2023-04-07', '15:00:00', '16:00:00', 'Manuel Carreiras', 4),
(14, 'Teste desta reunião grfgfgfgfgf', '2023-04-12', '15:30:00', '20:30:00', 'Manuel Carreiras', 2),
(16, 'vfvfvfvf', '2023-04-08', '15:30:00', '19:00:00', 'Manuel Carreiras', 4),
(20, 'Reunia ode teste', '2023-04-14', '17:30:00', '19:00:00', 'Manuel Carreiras', 1),
(21, 'dvsdvsdvsdvsdv', '2023-04-14', '18:00:00', '17:30:00', 'Manuel Carreiras', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `salas`
--

CREATE TABLE `salas` (
  `id` int NOT NULL,
  `nome` varchar(255) NOT NULL,
  `url_imagem` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `salas`
--

INSERT INTO `salas` (`id`, `nome`, `url_imagem`) VALUES
(1, 'Sala Xutos e Pontapés', ' assets/img/xutos.webp                      '),
(2, 'Sala Variações', 'assets/img/variacoes.webp'),
(3, 'Sala dos Campeões', 'assets/img/campeoes.webp'),
(4, 'Escritório', 'assets/img/imagem.webp');

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `NAME` varchar(24) DEFAULT NULL,
  `CONCESSAO` varchar(18) DEFAULT NULL,
  `FUNCAO` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`NAME`, `CONCESSAO`, `FUNCAO`) VALUES
('Hugo Leal', 'Fundao VK', 'Bate Chapas'),
('José Grou', 'Evora Colisao', 'Bate Chapas'),
('Célio Brás', 'Portalegre Oficina', 'Bate Chapas'),
('Raul Abreu', 'Evora Colisao', 'Bate Chapas'),
('Nuno Santos', 'Castelo Branco OI', 'Bate Chapas'),
('Paulo Silva', 'Evora Colisao', 'Bate Chapas'),
('Pedro Quito', 'Evora Colisao', 'Bate Chapas'),
('Joaquim Rego', 'Castelo Branco OI', 'Bate Chapas'),
('Manuel Quito', 'Evora Colisao', 'Bate Chapas'),
('Ricardo Relvas', 'Portalegre Oficina', 'Bate Chapas'),
('Tiago Vaz', 'Fundao VK', 'Chefe de Oficina'),
('João Magro', 'Castelo Branco OI', 'Chefe de Oficina'),
('Luis Rocha', 'Beja OC', 'Chefe de Oficina'),
('Vitor Cabo', 'Evora B', 'Chefe de Oficina'),
('João Mendes Costa', 'Castelo Branco OI', 'Chefe de Oficina'),
('Diogo Garcia', 'Evora F', 'Chefe de Oficina'),
('João Canelas', 'Evora K', 'Chefe de Oficina'),
('João Casinha', 'Evora V', 'Chefe de Oficina'),
('Luís Pateiro', 'Evora S', 'Chefe de Oficina'),
('Nuno Crispim', 'Beja IIKH', 'Chefe de Oficina'),
('Pedro Torrão', 'Evora Colisao', 'Chefe de Oficina'),
('André Antunes', 'Castelo Branco VW', 'Chefe de Oficina'),
('Carlos Pelado', 'Evora OI', 'Chefe de Oficina'),
('Michael Silva', 'Castelo Branco CV', 'Chefe de Oficina'),
('Ricardo Afonso', 'Portalegre Oficina', 'Chefe de Oficina'),
('António Ramalho', 'Evora C', 'Chefe de Oficina'),
('Hugo Lança', 'Beja OC', 'Lavador'),
('Joao Roupa', 'Evora C', 'Lavador'),
('Jose Pedro', 'Evora OI', 'Lavador'),
('João Santos', 'Portalegre Oficina', 'Lavador'),
('Jose Coelho', 'Evora F', 'Lavador'),
('José Pegado', 'Castelo Branco OI', 'Lavador'),
('Marco Cunha', 'Guarda CFO', 'Lavador'),
('Diogo Marques', 'Portalegre Oficina', 'Lavador'),
('Luis Cabeçana', 'Evora F', 'Lavador'),
('Luis Craveiro', 'Fundao VK', 'Lavador'),
('Tiago Aldeano', 'Evora OI', 'Lavador'),
('Bruno Martinho', 'Beja IIKH', 'Lavador'),
('Ricardo Cabana', 'Portalegre Oficina', 'Lavador'),
('Wilson Pereira', 'Evora OI', 'Lavador'),
('Joaquim Miguens', 'Castelo Branco CV', 'Lavador'),
('Albertino Vilela', 'Castelo Branco OI', 'Lavador'),
('António Mendonça', 'Castelo Branco VW', 'Lavador'),
('Manuel Carapinha', 'Evora Colisao', 'Lavador'),
('Herlander Pereira', 'Evora OI', 'Lavador'),
('Joao Lobo', 'Evora C', 'Mecânico'),
('João Costa', 'Portalegre Oficina', 'Mecânico'),
('José Graça', 'Beja OC', 'Mecânico'),
('José Ramos', 'Beja OC', 'Mecânico'),
('Luis Bento', 'Evora V', 'Mecânico'),
('Vitor Maia', 'Evora C', 'Mecânico'),
('Fábio Silva', 'Castelo Branco CV', 'Mecânico'),
('João Filipe', 'Evora OI', 'Mecânico'),
('João Rufino', 'Portalegre Oficina', 'Mecânico'),
('José Pimpão', 'Evora V', 'Mecânico'),
('Luis Calado', 'Beja IIKH', 'Mecânico'),
('Nelson Bilé', 'Portalegre Oficina', 'Mecânico'),
('Vitor Sousa', 'Beja IIKH', 'Mecânico'),
('Daniel Russo', 'Portalegre Oficina', 'Mecânico'),
('Davide Pedro', 'Castelo Branco VW', 'Mecânico'),
('Enilson Baia', 'Evora S', 'Mecânico'),
('Igor Bezerra', 'Guarda CFO', 'Mecânico'),
('João Navalha', 'Portalegre Oficina', 'Mecânico'),
('José Flamino', 'Evora B', 'Mecânico'),
('Nuno Marques', 'Evora OI', 'Mecânico'),
('Pedro Coelho', 'Guarda CFO', 'Mecânico'),
('Rodrigo Mota', 'Castelo Branco VW', 'Mecânico'),
('Rúben Pratas', 'Evora OI', 'Mecânico'),
('Tiago Sobral', 'Evora B', 'Mecânico'),
('André Madeira', 'Portalegre Oficina', 'Mecânico'),
('António Gomes', 'Beja IIKH', 'Mecânico'),
('Ivo Rodrigues', 'Evora S', 'Mecânico'),
('João Pedro Almeida', 'Evora S', 'Mecânico'),
('Sergio Mendes', 'Evora B', 'Mecânico'),
('Sérgio Santos', 'Castelo Branco OI', 'Mecânico'),
('António Farragola Santos', 'Portalegre Oficina', 'Mecânico'),
('César Sequeira', 'Evora S', 'Mecânico'),
('David Salvador', 'Evora H', 'Mecânico'),
('Fernando Félix', 'Castelo Branco OI', 'Mecânico'),
('João Domingues', 'Evora OI', 'Mecânico'),
('Manuel Pereira Martins', 'Guarda CFO', 'Mecânico'),
('Nuno Conceição', 'Elvas', 'Mecânico'),
('Paulo Oliveira Santos', 'Castelo Branco CV', 'Mecânico'),
('Romulo Pereira', 'Fundao VK', 'Mecânico'),
('Tiago Lourenço', 'Castelo Branco CV', 'Mecânico'),
('Flavio Monteiro', 'Guarda CFO', 'Mecânico'),
('Joaquim Barroso', 'Guarda CFO', 'Mecânico'),
('Joaquim Estróia', 'Evora B', 'Mecânico'),
('Joaquim Vicente', 'Castelo Branco OI', 'Mecânico'),
('Jorge Gonçalves', 'Evora S', 'Mecânico'),
('Jorge Rodrigues', 'Castelo Branco OI', 'Mecânico'),
('Miguel Guerreiro', 'Evora OI', 'Mecânico'),
('Sérgio Fernandes', 'Guarda CFO', 'Mecânico'),
('Vítor Galambinha', 'Beja OC', 'Mecânico'),
('Francisco Clemente', 'Evora S', 'Mecânico'),
('José Costa', 'Portalegre Oficina', 'Pintor'),
('Luis Prata', 'Castelo Branco OI', 'Pintor'),
('Lucio Alves', 'Fundao VK', 'Pintor'),
('João Paulino', 'Evora Colisao', 'Pintor'),
('Carlos Leitão', 'Castelo Branco OI', 'Pintor'),
('João Monteiro', 'Portalegre Oficina', 'Pintor'),
('Filipe Miguel Vicente', 'Evora Colisao', 'Pintor'),
('Nelson Andrade', 'Guarda CFO', 'Pintor'),
('Pedro Gordinho', 'Evora Colisao', 'Pintor'),
('Sérgio Galhano', 'Evora Colisao', 'Pintor'),
('Joaquim Antunes', 'Guarda CFO', 'Pintor'),
('Claudio Batanete', 'Evora Colisao', 'Pintor');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `participantes`
--
ALTER TABLE `participantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_reuniao` (`id_reuniao`);

--
-- Índices para tabela `reunioes`
--
ALTER TABLE `reunioes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_sala` (`id_sala`);

--
-- Índices para tabela `salas`
--
ALTER TABLE `salas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `participantes`
--
ALTER TABLE `participantes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de tabela `reunioes`
--
ALTER TABLE `reunioes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `salas`
--
ALTER TABLE `salas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `participantes`
--
ALTER TABLE `participantes`
  ADD CONSTRAINT `participantes_ibfk_1` FOREIGN KEY (`id_reuniao`) REFERENCES `reunioes` (`id`);

--
-- Limitadores para a tabela `reunioes`
--
ALTER TABLE `reunioes`
  ADD CONSTRAINT `reunioes_ibfk_1` FOREIGN KEY (`id_sala`) REFERENCES `salas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
