-- Se a consulta retornar linhas, resolva os logins duplicados no painel
-- antes de executar o ALTER TABLE. Nenhum registro é removido.
SELECT log_login, COUNT(*) AS total FROM tb_login GROUP BY log_login HAVING COUNT(*) > 1;
ALTER TABLE tb_login MODIFY log_senha VARCHAR(255) NOT NULL;
ALTER TABLE tb_login ADD UNIQUE KEY uq_log_login (log_login);
