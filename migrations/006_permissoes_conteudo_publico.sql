-- Golden Jardim: select the site's database in phpMyAdmin, then run this file.
-- Idempotent: preserves logins, password hashes, data and current profiles.
-- Does not require migration 005; also handles installations without log_perfil.
SET @gj_schema = DATABASE();
SET @gj_profile_ddl = (
    SELECT CASE
        WHEN COUNT(*) = 0 THEN 'ALTER TABLE tb_login ADD COLUMN log_perfil VARCHAR(80) NOT NULL DEFAULT ''Operacional'''
        WHEN MAX(DATA_TYPE) <> 'varchar' OR MAX(CHARACTER_MAXIMUM_LENGTH) < 80
            THEN 'ALTER TABLE tb_login MODIFY COLUMN log_perfil VARCHAR(80) NOT NULL DEFAULT ''Operacional'''
        ELSE 'SELECT 1'
    END
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @gj_schema AND TABLE_NAME = 'tb_login' AND COLUMN_NAME = 'log_perfil'
);
PREPARE gj_profile_stmt FROM @gj_profile_ddl;
EXECUTE gj_profile_stmt;
DEALLOCATE PREPARE gj_profile_stmt;

SET @gj_active_ddl = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE tb_login ADD COLUMN log_ativo TINYINT(1) NOT NULL DEFAULT 1',
        'SELECT 1')
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @gj_schema AND TABLE_NAME = 'tb_login' AND COLUMN_NAME = 'log_ativo'
);
PREPARE gj_active_stmt FROM @gj_active_ddl;
EXECUTE gj_active_stmt;
DEALLOCATE PREPARE gj_active_stmt;

-- Preserve the previous operator's management permissions with the new name.
UPDATE tb_login SET log_perfil = 'Operacional' WHERE log_perfil = 'Operador';
UPDATE tb_login SET log_perfil = 'Operacional' WHERE log_perfil IS NULL OR TRIM(log_perfil) = '';
-- The protected account must stay active and keep its administrator access.
UPDATE tb_login SET log_perfil = 'Administrador', log_ativo = 1
WHERE LOWER(TRIM(log_login)) = 'adm';

-- Optional verification; no passwords are selected.
SELECT log_codigo, log_nome, log_login, log_perfil, log_ativo FROM tb_login ORDER BY log_codigo;
