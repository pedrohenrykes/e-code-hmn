-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Tempo de geração: 30/07/2017 às 18:46
-- Versão do servidor: 5.7.19-0ubuntu0.16.04.1
-- Versão do PHP: 7.0.18-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `adianti_template`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `acessos`
--

CREATE TABLE `acessos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sessionid` varchar(100) DEFAULT NULL,
  `login` varchar(100) DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos`
--

CREATE TABLE `documentos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `system_user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `submission_date` date DEFAULT NULL,
  `archive_date` date DEFAULT NULL,
  `filename` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos_categorias`
--

CREATE TABLE `documentos_categorias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos_grupos`
--

CREATE TABLE `documentos_grupos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `document_id` bigint(20) UNSIGNED NOT NULL,
  `system_group_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos_usuarios`
--

CREATE TABLE `documentos_usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `document_id` bigint(20) UNSIGNED NOT NULL,
  `system_user_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupos`
--

CREATE TABLE `grupos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Fazendo dump de dados para tabela `grupos`
--

INSERT INTO `grupos` (`id`, `name`) VALUES
(1, 'Default'),
(2, 'Developers'),
(3, 'Administrators');

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupos_programas`
--

CREATE TABLE `grupos_programas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `system_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `system_program_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Fazendo dump de dados para tabela `grupos_programas`
--

INSERT INTO `grupos_programas` (`id`, `system_group_id`, `system_program_id`) VALUES
(1, 2, 1),
(2, 2, 2),
(3, 2, 3),
(4, 2, 4),
(5, 2, 5),
(6, 2, 6),
(7, 2, 7),
(8, 2, 8),
(9, 2, 9),
(10, 2, 10),
(11, 2, 11),
(12, 2, 12),
(13, 2, 13),
(14, 2, 14),
(15, 2, 15),
(16, 2, 16),
(17, 1, 1),
(18, 1, 17),
(19, 1, 18),
(20, 1, 19),
(21, 1, 20),
(22, 1, 21),
(23, 1, 22),
(24, 1, 23),
(25, 1, 24),
(26, 1, 25),
(27, 1, 26),
(28, 1, 27),
(29, 1, 28),
(30, 1, 29);

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `system_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `system_user_to_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `message` varchar(500) DEFAULT NULL,
  `dt_message` datetime DEFAULT NULL,
  `checked` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `system_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `system_user_to_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `message` varchar(500) DEFAULT NULL,
  `dt_message` datetime DEFAULT NULL,
  `action_url` varchar(200) DEFAULT NULL,
  `action_label` varchar(200) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `checked` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `preferencias`
--

CREATE TABLE `preferencias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `value` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `programas`
--

CREATE TABLE `programas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `controller` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Fazendo dump de dados para tabela `programas`
--

INSERT INTO `programas` (`id`, `name`, `controller`) VALUES
(1, 'Welcome', 'WelcomeView'),
(2, 'System Group Form', 'SystemGroupForm'),
(3, 'System Group List', 'SystemGroupList'),
(4, 'System Program Form', 'SystemProgramForm'),
(5, 'System Program List', 'SystemProgramList'),
(6, 'System User Form', 'SystemUserForm'),
(7, 'System User List', 'SystemUserList'),
(8, 'System Unit Form', 'SystemUnitForm'),
(9, 'System Unit List', 'SystemUnitList'),
(10, 'System SMTP Preference', 'SystemPreferenceForm'),
(11, 'System Change Log', 'SystemChangeLogView'),
(12, 'System Access Log', 'SystemAccessLogList'),
(13, 'System SQL Log', 'SystemSqlLogList'),
(14, 'System Access stats', 'SystemAccessLogStats'),
(15, 'System SQL Panel', 'SystemSQLPanel'),
(16, 'System PHP Info', 'SystemPHPInfoView'),
(17, 'System Profile View', 'SystemProfileView'),
(18, 'System Profile Form', 'SystemProfileForm'),
(19, 'System Message Form', 'SystemMessageForm'),
(20, 'System Message List', 'SystemMessageList'),
(21, 'System Message Form View', 'SystemMessageFormView'),
(22, 'System Notification List', 'SystemNotificationList'),
(23, 'System Notification Form View', 'SystemNotificationFormView'),
(24, 'System Support form', 'SystemSupportForm'),
(25, 'System Document Category', 'SystemDocumentCategoryFormList'),
(26, 'System Document Form', 'SystemDocumentForm'),
(27, 'System Document List', 'SystemDocumentList'),
(28, 'System Document Upload', 'SystemDocumentUploadForm'),
(29, 'System Shared Document', 'SystemSharedDocumentList');

-- --------------------------------------------------------

--
-- Estrutura para tabela `unidades`
--

CREATE TABLE `unidades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `login` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `frontpage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `system_unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `active` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Fazendo dump de dados para tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `name`, `login`, `password`, `email`, `frontpage_id`, `system_unit_id`, `active`) VALUES
(1, 'Developer', 'dev', 'e77989ed21758e78331b20e477fc5582', NULL, 1, NULL, 'Y'),
(2, 'Administrator', 'admin', '21232f297a57a5a743894a0e4a801fc3', NULL, 1, NULL, 'Y');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios_grupos`
--

CREATE TABLE `usuarios_grupos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `system_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `system_group_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Fazendo dump de dados para tabela `usuarios_grupos`
--

INSERT INTO `usuarios_grupos` (`id`, `system_user_id`, `system_group_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 1),
(4, 2, 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios_programas`
--

CREATE TABLE `usuarios_programas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `system_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `system_program_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices de tabelas apagadas
--

--
-- Índices de tabela `acessos`
--
ALTER TABLE `acessos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_system_document_with_system_user_id` (`system_user_id`) USING BTREE,
  ADD KEY `idx_system_document_with_category_id` (`category_id`) USING BTREE;

--
-- Índices de tabela `documentos_categorias`
--
ALTER TABLE `documentos_categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `documentos_grupos`
--
ALTER TABLE `documentos_grupos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_system_document_group_with_document_id` (`document_id`) USING BTREE,
  ADD KEY `idx_system_document_group_with_system_group_id` (`system_group_id`) USING BTREE;

--
-- Índices de tabela `documentos_usuarios`
--
ALTER TABLE `documentos_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_system_document_user_with_document_id` (`document_id`) USING BTREE,
  ADD KEY `idx_system_document_user_with_system_user_id` (`system_user_id`) USING BTREE;

--
-- Índices de tabela `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `grupos_programas`
--
ALTER TABLE `grupos_programas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_system_group_program_with_system_program_id` (`system_program_id`) USING BTREE,
  ADD KEY `idx_system_group_program_with_system_group_id` (`system_group_id`) USING BTREE;

--
-- Índices de tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_system_message_with_system_user_id` (`system_user_id`) USING BTREE,
  ADD KEY `idx_system_message_with_system_user_to_id` (`system_user_to_id`) USING BTREE;

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_system_notification_with_system_user_id` (`system_user_id`) USING BTREE,
  ADD KEY `idx_system_notification_with_system_user_to_id` (`system_user_to_id`) USING BTREE;

--
-- Índices de tabela `preferencias`
--
ALTER TABLE `preferencias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `programas`
--
ALTER TABLE `programas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `unidades`
--
ALTER TABLE `unidades`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_system_user_with_frontpage_id` (`frontpage_id`) USING BTREE,
  ADD KEY `idx_system_user_with_system_unit_id` (`system_unit_id`) USING BTREE;

--
-- Índices de tabela `usuarios_grupos`
--
ALTER TABLE `usuarios_grupos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_system_user_group_with_system_group_id` (`system_group_id`) USING BTREE,
  ADD KEY `idx_system_user_group_with_system_user_id` (`system_user_id`) USING BTREE;

--
-- Índices de tabela `usuarios_programas`
--
ALTER TABLE `usuarios_programas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_system_user_program_with_system_program_id` (`system_program_id`) USING BTREE,
  ADD KEY `idx_system_user_program_with_system_user_id` (`system_user_id`) USING BTREE;

--
-- AUTO_INCREMENT de tabelas apagadas
--

--
-- AUTO_INCREMENT de tabela `acessos`
--
ALTER TABLE `acessos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de tabela `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `documentos_categorias`
--
ALTER TABLE `documentos_categorias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `documentos_grupos`
--
ALTER TABLE `documentos_grupos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `documentos_usuarios`
--
ALTER TABLE `documentos_usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de tabela `grupos_programas`
--
ALTER TABLE `grupos_programas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `preferencias`
--
ALTER TABLE `preferencias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `programas`
--
ALTER TABLE `programas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT de tabela `unidades`
--
ALTER TABLE `unidades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de tabela `usuarios_grupos`
--
ALTER TABLE `usuarios_grupos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de tabela `usuarios_programas`
--
ALTER TABLE `usuarios_programas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Restrições para dumps de tabelas
--

--
-- Restrições para tabelas `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `fk_category_id_on_system_document` FOREIGN KEY (`category_id`) REFERENCES `documentos_categorias` (`id`),
  ADD CONSTRAINT `fk_system_user_id_on_system_document` FOREIGN KEY (`system_user_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `documentos_grupos`
--
ALTER TABLE `documentos_grupos`
  ADD CONSTRAINT `fk_document_id_on_system_document_group` FOREIGN KEY (`document_id`) REFERENCES `documentos` (`id`),
  ADD CONSTRAINT `fk_system_user_id_on_system_document_group` FOREIGN KEY (`system_group_id`) REFERENCES `grupos` (`id`);

--
-- Restrições para tabelas `documentos_usuarios`
--
ALTER TABLE `documentos_usuarios`
  ADD CONSTRAINT `fk_document_id_on_system_document_user` FOREIGN KEY (`document_id`) REFERENCES `documentos` (`id`),
  ADD CONSTRAINT `fk_system_user_id_on_system_document_user` FOREIGN KEY (`system_user_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `grupos_programas`
--
ALTER TABLE `grupos_programas`
  ADD CONSTRAINT `fk_system_group_id_on_system_group_program` FOREIGN KEY (`system_group_id`) REFERENCES `grupos` (`id`),
  ADD CONSTRAINT `fk_system_program_id_on_system_group_program` FOREIGN KEY (`system_program_id`) REFERENCES `programas` (`id`);

--
-- Restrições para tabelas `mensagens`
--
ALTER TABLE `mensagens`
  ADD CONSTRAINT `fk_system_user_id_on_system_message` FOREIGN KEY (`system_user_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_system_user_to_id_system_message` FOREIGN KEY (`system_user_to_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `fk_system_user_id_on_system_notification` FOREIGN KEY (`system_user_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_system_user_to_id_on_system_notification` FOREIGN KEY (`system_user_to_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_frontpage_id_on_system_user` FOREIGN KEY (`frontpage_id`) REFERENCES `programas` (`id`),
  ADD CONSTRAINT `fk_system_unit_id_on_system_user` FOREIGN KEY (`system_unit_id`) REFERENCES `unidades` (`id`);

--
-- Restrições para tabelas `usuarios_grupos`
--
ALTER TABLE `usuarios_grupos`
  ADD CONSTRAINT `fk_system_group_id_on_system_user_group` FOREIGN KEY (`system_group_id`) REFERENCES `grupos` (`id`),
  ADD CONSTRAINT `fk_system_user_id_on_system_user_group` FOREIGN KEY (`system_user_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `usuarios_programas`
--
ALTER TABLE `usuarios_programas`
  ADD CONSTRAINT `fk_system_program_id_on_system_user_program` FOREIGN KEY (`system_program_id`) REFERENCES `programas` (`id`),
  ADD CONSTRAINT `fk_system_user_id_on_system_user_program` FOREIGN KEY (`system_user_id`) REFERENCES `usuarios` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
