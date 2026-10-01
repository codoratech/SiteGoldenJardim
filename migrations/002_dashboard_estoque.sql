-- Execute uma vez. Nenhum registro antigo recebe vínculo ou mínimo automaticamente.
ALTER TABLE tb_produtos ADD COLUMN Pro_EstoqueMinimo INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE tb_estoque
 ADD COLUMN TB_Produtos_ID_Produto INT NULL DEFAULT NULL,
 ADD INDEX idx_estoque_produto (TB_Produtos_ID_Produto),
 ADD CONSTRAINT fk_estoque_produto FOREIGN KEY (TB_Produtos_ID_Produto) REFERENCES tb_produtos (ID_Produto);
