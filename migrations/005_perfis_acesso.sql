-- Execute once before publishing the role-based access checks.
-- Existing logins retain their original full access.
ALTER TABLE tb_login
    ADD COLUMN log_perfil ENUM('Administrador', 'Consulta', 'Operador') NOT NULL DEFAULT 'Administrador';
