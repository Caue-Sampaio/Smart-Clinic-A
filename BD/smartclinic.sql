-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19/05/2026 às 14:24
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
-- Banco de dados: `smartclinic`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamento`
--

CREATE TABLE `agendamento` (
  `cod` int(11) NOT NULL,
  `fk_solicitacao_cod` int(11) DEFAULT NULL,
  `data_agendamento` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `consulta`
--

CREATE TABLE `consulta` (
  `cod` int(11) NOT NULL,
  `fk_agendamento_cod` int(11) DEFAULT NULL,
  `data_consulta` datetime DEFAULT NULL,
  `sintese` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `declaracao`
--

CREATE TABLE `declaracao` (
  `cod` int(11) NOT NULL,
  `fk_paciente_cod` int(11) DEFAULT NULL,
  `fk_medico_cod` int(11) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `motivo` text DEFAULT NULL,
  `validade` date DEFAULT NULL,
  `data_hora` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `exame`
--

CREATE TABLE `exame` (
  `cod` int(11) NOT NULL,
  `fk_solicitacao_cod` int(11) DEFAULT NULL,
  `arquivo` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `instituicao`
--

CREATE TABLE `instituicao` (
  `cod` int(11) NOT NULL,
  `cnpj` varchar(20) DEFAULT NULL,
  `logo` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `endereco` varchar(200) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `instituicao`
--

INSERT INTO `instituicao` (`cod`, `cnpj`, `logo`, `email`, `senha`, `nome`, `telefone`, `endereco`, `status`) VALUES
(1, '12345678000100', 'logo.png', 'instituicao@email.com', 'senha123', 'Instituicao Teste', '11999999999', 'Endereco Teste', 'Ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `medicamento`
--

CREATE TABLE `medicamento` (
  `cod` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `dosagem` varchar(50) DEFAULT NULL,
  `forma` varchar(50) DEFAULT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `medico`
--

CREATE TABLE `medico` (
  `cod` int(11) NOT NULL,
  `fk_instituicao_cod` int(11) DEFAULT NULL,
  `cpf` varchar(11) NOT NULL,
  `crm` varchar(20) NOT NULL,
  `rqe` varchar(20) DEFAULT NULL,
  `foto` varchar(200) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `especialidade` varchar(50) DEFAULT NULL,
  `telefone` varchar(20) NOT NULL,
  `endereco` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `medico`
--

INSERT INTO `medico` (`cod`, `fk_instituicao_cod`, `cpf`, `crm`, `rqe`, `foto`, `nome`, `email`, `senha`, `especialidade`, `telefone`, `endereco`) VALUES
(1, 1, '32525324', '432523525352', '52352355', '', 'cauê', 'medico@gmail.com', 'qwer123', 's', '324324324', 'casa');

-- --------------------------------------------------------

--
-- Estrutura para tabela `monitoramento`
--

CREATE TABLE `monitoramento` (
  `cod` int(11) NOT NULL,
  `fk_paciente_cpf` varchar(11) DEFAULT NULL,
  `altura` decimal(5,2) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `imc` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `paciente`
--

CREATE TABLE `paciente` (
  `cod` int(11) NOT NULL,
  `fk_instituicao_cod` int(11) DEFAULT NULL,
  `cpf` varchar(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `data_nascimento` date NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `senha` varchar(100) DEFAULT NULL,
  `endereco` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `paciente`
--

INSERT INTO `paciente` (`cod`, `fk_instituicao_cod`, `cpf`, `nome`, `data_nascimento`, `email`, `senha`, `endereco`) VALUES
(2, 1, '12345678900', 'cauê Sampaio', '2000-12-01', 'paciente@gmail.com', 'qwer123', 'casa'),
(5, 1, '12345678901', 'Mikael', '2000-01-01', 'Mikael.111@gmail.com', 'qwer111', 'prédio');

-- --------------------------------------------------------

--
-- Estrutura para tabela `prescrever`
--

CREATE TABLE `prescrever` (
  `descricao` text DEFAULT NULL,
  `modo_uso` text DEFAULT NULL,
  `fk_receita_cod` int(11) NOT NULL,
  `fk_medicamento_cod` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `prontuario`
--

CREATE TABLE `prontuario` (
  `cod` int(11) NOT NULL,
  `fk_paciente_cpf` varchar(11) DEFAULT NULL,
  `foto` varchar(200) DEFAULT NULL,
  `tipo_sanguineo` varchar(5) DEFAULT NULL,
  `doencas_cronicas` text DEFAULT NULL,
  `doencas_geneticas` text DEFAULT NULL,
  `doencas_autoimunes` text DEFAULT NULL,
  `outros` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `receita`
--

CREATE TABLE `receita` (
  `cod` int(11) NOT NULL,
  `fk_paciente_cod` int(11) DEFAULT NULL,
  `fk_medico_cod` int(11) DEFAULT NULL,
  `data_receita` date DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `solicitacao`
--

CREATE TABLE `solicitacao` (
  `cod` int(11) NOT NULL,
  `fk_paciente_cod` int(11) NOT NULL,
  `fk_medico_cod` int(11) DEFAULT NULL,
  `tipo` varchar(50) NOT NULL,
  `motivo` text NOT NULL,
  `regime` varchar(50) DEFAULT NULL,
  `resposta` text DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamento`
--
ALTER TABLE `agendamento`
  ADD PRIMARY KEY (`cod`),
  ADD KEY `fk_solicitacao_cod` (`fk_solicitacao_cod`);

--
-- Índices de tabela `consulta`
--
ALTER TABLE `consulta`
  ADD PRIMARY KEY (`cod`),
  ADD KEY `fk_agendamento_cod` (`fk_agendamento_cod`);

--
-- Índices de tabela `declaracao`
--
ALTER TABLE `declaracao`
  ADD PRIMARY KEY (`cod`),
  ADD KEY `fk_paciente_cod` (`fk_paciente_cod`),
  ADD KEY `fk_medico_cod` (`fk_medico_cod`);

--
-- Índices de tabela `exame`
--
ALTER TABLE `exame`
  ADD PRIMARY KEY (`cod`),
  ADD KEY `fk_solicitacao_cod` (`fk_solicitacao_cod`);

--
-- Índices de tabela `instituicao`
--
ALTER TABLE `instituicao`
  ADD PRIMARY KEY (`cod`),
  ADD UNIQUE KEY `cnpj` (`cnpj`);

--
-- Índices de tabela `medicamento`
--
ALTER TABLE `medicamento`
  ADD PRIMARY KEY (`cod`);

--
-- Índices de tabela `medico`
--
ALTER TABLE `medico`
  ADD PRIMARY KEY (`cod`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD UNIQUE KEY `crm` (`crm`),
  ADD UNIQUE KEY `rqe` (`rqe`),
  ADD KEY `fk_instituicao_cod` (`fk_instituicao_cod`);

--
-- Índices de tabela `monitoramento`
--
ALTER TABLE `monitoramento`
  ADD PRIMARY KEY (`cod`),
  ADD UNIQUE KEY `fk_paciente_cpf` (`fk_paciente_cpf`);

--
-- Índices de tabela `paciente`
--
ALTER TABLE `paciente`
  ADD PRIMARY KEY (`cod`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `fk_instituicao_cod` (`fk_instituicao_cod`);

--
-- Índices de tabela `prescrever`
--
ALTER TABLE `prescrever`
  ADD PRIMARY KEY (`fk_receita_cod`,`fk_medicamento_cod`),
  ADD KEY `fk_medicamento_cod` (`fk_medicamento_cod`);

--
-- Índices de tabela `prontuario`
--
ALTER TABLE `prontuario`
  ADD PRIMARY KEY (`cod`),
  ADD UNIQUE KEY `fk_paciente_cpf` (`fk_paciente_cpf`);

--
-- Índices de tabela `receita`
--
ALTER TABLE `receita`
  ADD PRIMARY KEY (`cod`),
  ADD KEY `fk_paciente_cod` (`fk_paciente_cod`),
  ADD KEY `fk_medico_cod` (`fk_medico_cod`);

--
-- Índices de tabela `solicitacao`
--
ALTER TABLE `solicitacao`
  ADD PRIMARY KEY (`cod`),
  ADD KEY `fk_paciente_cod` (`fk_paciente_cod`),
  ADD KEY `fk_medico_cod` (`fk_medico_cod`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamento`
--
ALTER TABLE `agendamento`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `consulta`
--
ALTER TABLE `consulta`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `declaracao`
--
ALTER TABLE `declaracao`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `exame`
--
ALTER TABLE `exame`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `instituicao`
--
ALTER TABLE `instituicao`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `medicamento`
--
ALTER TABLE `medicamento`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `medico`
--
ALTER TABLE `medico`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `monitoramento`
--
ALTER TABLE `monitoramento`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `paciente`
--
ALTER TABLE `paciente`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `prontuario`
--
ALTER TABLE `prontuario`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `receita`
--
ALTER TABLE `receita`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `solicitacao`
--
ALTER TABLE `solicitacao`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamento`
--
ALTER TABLE `agendamento`
  ADD CONSTRAINT `agendamento_ibfk_1` FOREIGN KEY (`fk_solicitacao_cod`) REFERENCES `solicitacao` (`cod`);

--
-- Restrições para tabelas `consulta`
--
ALTER TABLE `consulta`
  ADD CONSTRAINT `consulta_ibfk_1` FOREIGN KEY (`fk_agendamento_cod`) REFERENCES `agendamento` (`cod`);

--
-- Restrições para tabelas `declaracao`
--
ALTER TABLE `declaracao`
  ADD CONSTRAINT `declaracao_ibfk_1` FOREIGN KEY (`fk_paciente_cod`) REFERENCES `paciente` (`cod`),
  ADD CONSTRAINT `declaracao_ibfk_2` FOREIGN KEY (`fk_medico_cod`) REFERENCES `medico` (`cod`);

--
-- Restrições para tabelas `exame`
--
ALTER TABLE `exame`
  ADD CONSTRAINT `exame_ibfk_1` FOREIGN KEY (`fk_solicitacao_cod`) REFERENCES `solicitacao` (`cod`);

--
-- Restrições para tabelas `medico`
--
ALTER TABLE `medico`
  ADD CONSTRAINT `medico_ibfk_1` FOREIGN KEY (`fk_instituicao_cod`) REFERENCES `instituicao` (`cod`);

--
-- Restrições para tabelas `monitoramento`
--
ALTER TABLE `monitoramento`
  ADD CONSTRAINT `monitoramento_ibfk_1` FOREIGN KEY (`fk_paciente_cpf`) REFERENCES `paciente` (`cpf`);

--
-- Restrições para tabelas `paciente`
--
ALTER TABLE `paciente`
  ADD CONSTRAINT `paciente_ibfk_1` FOREIGN KEY (`fk_instituicao_cod`) REFERENCES `instituicao` (`cod`);

--
-- Restrições para tabelas `prescrever`
--
ALTER TABLE `prescrever`
  ADD CONSTRAINT `prescrever_ibfk_1` FOREIGN KEY (`fk_receita_cod`) REFERENCES `receita` (`cod`),
  ADD CONSTRAINT `prescrever_ibfk_2` FOREIGN KEY (`fk_medicamento_cod`) REFERENCES `medicamento` (`cod`);

--
-- Restrições para tabelas `prontuario`
--
ALTER TABLE `prontuario`
  ADD CONSTRAINT `prontuario_ibfk_1` FOREIGN KEY (`fk_paciente_cpf`) REFERENCES `paciente` (`cpf`);

--
-- Restrições para tabelas `receita`
--
ALTER TABLE `receita`
  ADD CONSTRAINT `receita_ibfk_1` FOREIGN KEY (`fk_paciente_cod`) REFERENCES `paciente` (`cod`),
  ADD CONSTRAINT `receita_ibfk_2` FOREIGN KEY (`fk_medico_cod`) REFERENCES `medico` (`cod`);

--
-- Restrições para tabelas `solicitacao`
--
ALTER TABLE `solicitacao`
  ADD CONSTRAINT `solicitacao_ibfk_1` FOREIGN KEY (`fk_paciente_cod`) REFERENCES `paciente` (`cod`),
  ADD CONSTRAINT `solicitacao_ibfk_2` FOREIGN KEY (`fk_medico_cod`) REFERENCES `medico` (`cod`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
