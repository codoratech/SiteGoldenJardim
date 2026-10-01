-- Dados fictícios. Use apenas em banco de testes, após a migração 002.
-- A aplicação não insere estes dados automaticamente.
INSERT INTO tb_clientes (cli_Nome,cli_DataCadastro) VALUES ('Cliente de demonstração',NOW());
SET @cliente_demo = LAST_INSERT_ID();
INSERT INTO tb_servicos (Ser_Nome,Ser_Preco) VALUES ('Poda demonstrativa',150);
SET @servico_demo = LAST_INSERT_ID();
INSERT INTO tb_produtos (Pro_Nome,Pro_Preco,Pro_EstoqueMinimo) VALUES ('Mudas · crítico',25,10);
SET @produto_critico = LAST_INSERT_ID();
INSERT INTO tb_produtos (Pro_Nome,Pro_Preco,Pro_EstoqueMinimo) VALUES ('Adubo · baixo',30,10);
SET @produto_baixo = LAST_INSERT_ID();
INSERT INTO tb_produtos (Pro_Nome,Pro_Preco,Pro_EstoqueMinimo) VALUES ('Substrato · suficiente',20,10);
SET @produto_ok = LAST_INSERT_ID();
INSERT INTO tb_estoque (Est_Tipo,Est_Quantidade,TB_Produtos_ID_Produto) VALUES
 ('Entrada',10,@produto_critico),('Saida',9,@produto_critico),
 ('Entrada',8,@produto_baixo),('Entrada',25,@produto_ok);
INSERT INTO tb_ordensservico (TB_Clientes_ID_Cliente,Or_DataServico,Or_Status,Ord_ValorTotal) VALUES
 (@cliente_demo,DATE_SUB(NOW(),INTERVAL 5 MONTH),'Pendente',150),
 (@cliente_demo,DATE_SUB(NOW(),INTERVAL 4 MONTH),'Em Andamento',150),
 (@cliente_demo,DATE_SUB(NOW(),INTERVAL 3 MONTH),'Cancelado',150),
 (@cliente_demo,DATE_SUB(NOW(),INTERVAL 2 MONTH),'Concluído',300),
 (@cliente_demo,DATE_SUB(NOW(),INTERVAL 1 MONTH),'Concluído',300),
 (@cliente_demo,NOW(),'Concluído',300);
SET @ordem_demo = LAST_INSERT_ID() + 5;
INSERT INTO tb_itensordemservico (TB_Servicos_ID_Servico,TB_OrdensServico_ID_Ordem,Ite_Quantidade,Ite_PrecoUnitario,Ite_Subtotal) VALUES (@servico_demo,@ordem_demo,2,150,300);
INSERT INTO tb_agendamentos (TB_Clientes_ID_Cliente,TB_OrdensServico_ID_Ordem,Age_DataAgendada,Age_Status) VALUES (@cliente_demo,@ordem_demo,DATE_ADD(NOW(),INTERVAL 1 DAY),'Agendado');
INSERT INTO tb_contasreceber (TB_Clientes_ID_Cliente,Con_Valor,Con_DataVencimento,Con_Status) VALUES
 (@cliente_demo,300,CURRENT_DATE(),'Pago'),
 (@cliente_demo,150,CURRENT_DATE(),'Pendente'),
 (@cliente_demo,50,DATE_SUB(CURRENT_DATE(),INTERVAL 1 DAY),'Pendente'),
 (@cliente_demo,900,CURRENT_DATE(),'Cancelado'),
 (@cliente_demo,200,DATE_SUB(CURRENT_DATE(),INTERVAL 1 MONTH),'Pago');
