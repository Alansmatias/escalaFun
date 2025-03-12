INSERT INTO `pvrhdb`.`setors` (`nome`, `ativo`) 
VALUES
('Almoxarifado', '1'),
('Assadeiras', '1'),
('Atendimento', '1'),
('Balcão', '1'),
('Caixa', '1'),
('Confeitaria', '1'),
('Cozinha', '1'),
('Embalagem', '1'),
('Entrega', '1'),
('Escritório', '1'),
('Forno', '1'),
('Lanchonete', '1'),
('Monitoria', '1'),
('Padaria', '1'),
('Padaria Auxiliar', '1'),
('Reposição', '1'),
('Restaurante', '1'),
('Rota', '1'),
('Serviços Gerais', '1'),
('Supervisor Buffet', '1'),
('Venda Externa', '1');

INSERT INTO `pvrhdb`.`turnos` (`nome`, `ativo`) 
VALUES
('Manha', '1'),
('Tarde', '1');

INSERT INTO `pvrhdb`.`periodos` (`id`, `dataIni`, `dataFim`) VALUES ('2', '2025-03-01', '2025-03-31');