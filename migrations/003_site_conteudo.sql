-- Golden Jardim: conteúdo público. Não modifica as tabelas operacionais.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS tb_site_secoes (
 chave ENUM('services','gallery') NOT NULL PRIMARY KEY,
 titulo_pt VARCHAR(160) NOT NULL,
 titulo_en VARCHAR(160) NOT NULL DEFAULT '',
 subtitulo_pt VARCHAR(500) NOT NULL DEFAULT '',
 subtitulo_en VARCHAR(500) NOT NULL DEFAULT '',
 selo_pt VARCHAR(80) NOT NULL DEFAULT '',
 selo_en VARCHAR(80) NOT NULL DEFAULT '',
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tb_site_servicos (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 titulo_pt VARCHAR(120) NOT NULL,
 titulo_en VARCHAR(120) NOT NULL DEFAULT '',
 descricao_pt VARCHAR(600) NOT NULL DEFAULT '',
 descricao_en VARCHAR(600) NOT NULL DEFAULT '',
 imagem_path VARCHAR(255) NULL,
 icone VARCHAR(24) NOT NULL DEFAULT 'sprout',
 ordem INT UNSIGNED NOT NULL DEFAULT 0,
 ativo TINYINT UNSIGNED NOT NULL DEFAULT 1,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_site_servicos_exibicao (ativo,ordem,id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tb_site_projetos (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 titulo_pt VARCHAR(160) NOT NULL,
 titulo_en VARCHAR(160) NOT NULL DEFAULT '',
 descricao_pt VARCHAR(600) NOT NULL DEFAULT '',
 descricao_en VARCHAR(600) NOT NULL DEFAULT '',
 categoria_pt VARCHAR(80) NOT NULL DEFAULT '',
 categoria_en VARCHAR(80) NOT NULL DEFAULT '',
 local VARCHAR(180) NOT NULL DEFAULT '',
 imagem_path VARCHAR(255) NULL,
 imagem_alt_pt VARCHAR(180) NOT NULL DEFAULT '',
 imagem_alt_en VARCHAR(180) NOT NULL DEFAULT '',
 ordem INT UNSIGNED NOT NULL DEFAULT 0,
 ativo TINYINT UNSIGNED NOT NULL DEFAULT 1,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_site_projetos_exibicao (ativo,ordem,id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

START TRANSACTION;
SET @seed_servicos = (SELECT COUNT(*) = 0 FROM tb_site_servicos);
SET @seed_projetos = (SELECT COUNT(*) = 0 FROM tb_site_projetos);
INSERT INTO tb_site_secoes (chave,titulo_pt,titulo_en,subtitulo_pt,subtitulo_en,selo_pt,selo_en) SELECT 'services','NOSSOS
SERVIÇOS','OUR
SERVICES','Especialidades conduzidas por Rodrigo & Fernanda Câmara','Specialty services led by Rodrigo & Fernanda Câmara','','' WHERE NOT EXISTS (SELECT 1 FROM tb_site_secoes WHERE chave = 'services');
INSERT INTO tb_site_secoes (chave,titulo_pt,titulo_en,subtitulo_pt,subtitulo_en,selo_pt,selo_en) SELECT 'gallery','Projetos assinados','Signature projects','Uma seleção de transformações entregues em Americana e região.','Selected transformations delivered across Americana region.','Portfólio','Portfolio' WHERE NOT EXISTS (SELECT 1 FROM tb_site_secoes WHERE chave = 'gallery');
INSERT INTO tb_site_servicos (titulo_pt,titulo_en,descricao_pt,descricao_en,imagem_path,icone,ordem,ativo) SELECT 'Paisagismo','Landscaping','Projetos autorais de paisagismo, da concepção 3D à execução final.','Signature landscape design — from 3D vision to flawless delivery.','assets/service-landscaping.jpg','sprout',1,1 WHERE @seed_servicos = 1;
INSERT INTO tb_site_servicos (titulo_pt,titulo_en,descricao_pt,descricao_en,imagem_path,icone,ordem,ativo) SELECT 'Manutenção','Maintenance','Contratos mensais para condomínios e residências de alto padrão.','Monthly contracts for premium condominiums and residences.',NULL,'droplet',2,1 WHERE @seed_servicos = 1;
INSERT INTO tb_site_servicos (titulo_pt,titulo_en,descricao_pt,descricao_en,imagem_path,icone,ordem,ativo) SELECT 'Poda Técnica','Expert Pruning','Poda de árvores e jardins com técnicas avançadas de condução.','Tree and garden pruning with advanced shaping techniques.','assets/service-pruning.jpg','pruning',3,1 WHERE @seed_servicos = 1;
INSERT INTO tb_site_servicos (titulo_pt,titulo_en,descricao_pt,descricao_en,imagem_path,icone,ordem,ativo) SELECT 'Jardinagem','Gardening','Cuidado contínuo, adubação inteligente e saúde vegetal completa.','Ongoing care, smart fertilization and full plant health.',NULL,'tree',4,1 WHERE @seed_servicos = 1;
INSERT INTO tb_site_servicos (titulo_pt,titulo_en,descricao_pt,descricao_en,imagem_path,icone,ordem,ativo) SELECT 'Comercial','Commercial','Soluções escaláveis para empresas, lobbies e áreas corporativas.','Scalable solutions for offices, lobbies and corporate sites.','assets/service-commercial.jpg','building',5,1 WHERE @seed_servicos = 1;
INSERT INTO tb_site_servicos (titulo_pt,titulo_en,descricao_pt,descricao_en,imagem_path,icone,ordem,ativo) SELECT 'Consultoria','Consulting','Diagnóstico, plano de manejo e curadoria botânica especializada.','Diagnostics, management plans and curated botanical advisory.',NULL,'compass',6,1 WHERE @seed_servicos = 1;
INSERT INTO tb_site_projetos (titulo_pt,titulo_en,descricao_pt,descricao_en,categoria_pt,categoria_en,local,imagem_path,imagem_alt_pt,imagem_alt_en,ordem,ativo) SELECT 'Residência Vista Mar','','','','Residencial','','','assets/gallery-1.jpg','Residência Vista Mar','',1,1 WHERE @seed_projetos = 1;
INSERT INTO tb_site_projetos (titulo_pt,titulo_en,descricao_pt,descricao_en,categoria_pt,categoria_en,local,imagem_path,imagem_alt_pt,imagem_alt_en,ordem,ativo) SELECT 'Sede Corporativa Atrium','','','','Comercial','','','assets/gallery-2.jpg','Sede Corporativa Atrium','',2,1 WHERE @seed_projetos = 1;
INSERT INTO tb_site_projetos (titulo_pt,titulo_en,descricao_pt,descricao_en,categoria_pt,categoria_en,local,imagem_path,imagem_alt_pt,imagem_alt_en,ordem,ativo) SELECT 'Caminho das Palmeiras','','','','Paisagismo','','','assets/gallery-3.jpg','Caminho das Palmeiras','',3,1 WHERE @seed_projetos = 1;
INSERT INTO tb_site_projetos (titulo_pt,titulo_en,descricao_pt,descricao_en,categoria_pt,categoria_en,local,imagem_path,imagem_alt_pt,imagem_alt_en,ordem,ativo) SELECT 'Condomínio Belvedere','','','','Condomínio','','','assets/gallery-4.jpg','Condomínio Belvedere','',4,1 WHERE @seed_projetos = 1;
COMMIT;
