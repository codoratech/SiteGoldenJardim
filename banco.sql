-- Banco de Dados para o Sistema Admin - Golden Jardim
CREATE DATABASE IF NOT EXISTS golden_jardim_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE golden_jardim_db;

CREATE TABLE IF NOT EXISTS tb_login (
    log_codigo INT(11) NOT NULL AUTO_INCREMENT,
    log_nome VARCHAR(120) NOT NULL,
    log_login VARCHAR(50) NOT NULL,
    log_senha VARCHAR(255) NOT NULL,
    log_perfil VARCHAR(80) NOT NULL DEFAULT 'Operacional',
    log_ativo TINYINT(1) NOT NULL DEFAULT 1,
    log_data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (log_codigo),
    UNIQUE KEY uq_log_login (log_login)
);

-- Crie o primeiro administrador com: php tools/criar_admin.php
-- Não são instaladas contas com senhas públicas.

CREATE TABLE IF NOT EXISTS tb_fornecedores (
    ID_Fornecedor INT(11) NOT NULL AUTO_INCREMENT,
    For_Nome VARCHAR(100) NOT NULL,
    For_Telefone VARCHAR(20),
    For_Endereco VARCHAR(150),
    For_DataCadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ID_Fornecedor)
);

CREATE TABLE IF NOT EXISTS tb_produtos (
    ID_Produto INT(11) NOT NULL AUTO_INCREMENT,
    Pro_Nome VARCHAR(100) NOT NULL,
    Pro_Descricao VARCHAR(255),
    Pro_Preco DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (ID_Produto)
);

CREATE TABLE IF NOT EXISTS tb_compras (
    ID_Compra INT(11) NOT NULL AUTO_INCREMENT,
    TB_Fornecedores_ID_Fornecedor INT(11) NOT NULL,
    Com_DataCompra DATETIME DEFAULT CURRENT_TIMESTAMP,
    Com_ValorTotal DECIMAL(10,2) NOT NULL,
    Com_Status VARCHAR(20) DEFAULT 'Pendente',
    PRIMARY KEY (ID_Compra),
    CONSTRAINT fk_compras_fornecedor FOREIGN KEY (TB_Fornecedores_ID_Fornecedor) REFERENCES tb_fornecedores (ID_Fornecedor)
);

CREATE TABLE IF NOT EXISTS tb_itenscompra (
    ID_ItemCompra INT(11) NOT NULL AUTO_INCREMENT,
    TB_Compras_ID_Compra INT(11) NOT NULL,
    TB_Produtos_ID_Produto INT(11) NOT NULL,
    IteCom_Quantidade INT(11) NOT NULL,
    IteCom_PrecoUnitario DECIMAL(10,2) NOT NULL,
    IteCom_Subtotal DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (ID_ItemCompra),
    CONSTRAINT fk_itenscompra_compra FOREIGN KEY (TB_Compras_ID_Compra) REFERENCES tb_compras (ID_Compra),
    CONSTRAINT fk_itenscompra_produto FOREIGN KEY (TB_Produtos_ID_Produto) REFERENCES tb_produtos (ID_Produto)
);

CREATE TABLE IF NOT EXISTS tb_clientes (
    ID_Cliente INT(11) NOT NULL AUTO_INCREMENT,
    cli_Nome VARCHAR(150) NOT NULL,
    cli_Tipo VARCHAR(30) DEFAULT 'Residencial',
    cli_Telefone VARCHAR(20),
    cli_Email VARCHAR(100),
    cli_Endereco VARCHAR(200),
    cli_DataCadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ID_Cliente)
);

CREATE TABLE IF NOT EXISTS tb_servicos (
    ID_Servico INT(11) NOT NULL AUTO_INCREMENT,
    Ser_Nome VARCHAR(100) NOT NULL,
    Ser_Descricao VARCHAR(255),
    Ser_Preco DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (ID_Servico)
);

CREATE TABLE IF NOT EXISTS tb_estoque (
    ID_Estoque INT(11) NOT NULL AUTO_INCREMENT,
    Est_Tipo VARCHAR(20) NOT NULL,
    Est_Quantidade INT(11) NOT NULL,
    Est_DataMovimentacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ID_Estoque)
);

CREATE TABLE IF NOT EXISTS tb_ordensservico (
    ID_Ordem INT(11) NOT NULL AUTO_INCREMENT,
    TB_Clientes_ID_Cliente INT(11) NOT NULL,
    Or_DataServico DATETIME DEFAULT CURRENT_TIMESTAMP,
    Or_Status VARCHAR(30) DEFAULT 'Em Andamento',
    Ord_ValorTotal DECIMAL(10,2) NOT NULL,
    Ord_Observacoes VARCHAR(500),
    PRIMARY KEY (ID_Ordem),
    CONSTRAINT fk_os_cliente FOREIGN KEY (TB_Clientes_ID_Cliente) REFERENCES tb_clientes (ID_Cliente)
);

CREATE TABLE IF NOT EXISTS tb_itensordemservico (
    ID_Item INT(11) NOT NULL AUTO_INCREMENT,
    TB_Servicos_ID_Servico INT(11) NOT NULL,
    TB_OrdensServico_ID_Ordem INT(11) NOT NULL,
    Ite_Quantidade INT(11) NOT NULL,
    Ite_PrecoUnitario DECIMAL(10,2) NOT NULL,
    Ite_Subtotal DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (ID_Item),
    CONSTRAINT fk_itensos_servico FOREIGN KEY (TB_Servicos_ID_Servico) REFERENCES tb_servicos (ID_Servico),
    CONSTRAINT fk_itensos_os FOREIGN KEY (TB_OrdensServico_ID_Ordem) REFERENCES tb_ordensservico (ID_Ordem)
);

CREATE TABLE IF NOT EXISTS tb_contasreceber (
    ID_ContaReceber INT(11) NOT NULL AUTO_INCREMENT,
    TB_OrdensServico_ID_Ordem INT(11) DEFAULT NULL,
    TB_Clientes_ID_Cliente INT(11) NOT NULL,
    Con_Valor DECIMAL(10,2) NOT NULL,
    Con_DataVencimento DATE NOT NULL,
    Con_Status VARCHAR(20) DEFAULT 'Pendente',
    Con_FormaPagamento VARCHAR(50) DEFAULT 'Pix',
    PRIMARY KEY (ID_ContaReceber),
    CONSTRAINT fk_contasreceber_os FOREIGN KEY (TB_OrdensServico_ID_Ordem) REFERENCES tb_ordensservico (ID_Ordem),
    CONSTRAINT fk_contasreceber_cliente FOREIGN KEY (TB_Clientes_ID_Cliente) REFERENCES tb_clientes (ID_Cliente)
);

CREATE TABLE IF NOT EXISTS tb_agendamentos (
    ID_Agendamento INT(11) NOT NULL AUTO_INCREMENT,
    TB_OrdensServico_ID_Ordem INT(11) DEFAULT NULL,
    TB_Clientes_ID_Cliente INT(11) NOT NULL,
    Age_DataAgendada DATETIME NOT NULL,
    Age_Status VARCHAR(30) DEFAULT 'Agendado',
    Age_Observacoes VARCHAR(255),
    PRIMARY KEY (ID_Agendamento),
    CONSTRAINT fk_agendamentos_os FOREIGN KEY (TB_OrdensServico_ID_Ordem) REFERENCES tb_ordensservico (ID_Ordem),
    CONSTRAINT fk_agendamentos_cliente FOREIGN KEY (TB_Clientes_ID_Cliente) REFERENCES tb_clientes (ID_Cliente)
);
