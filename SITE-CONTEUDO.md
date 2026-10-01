# Conteúdo das duas seções públicas — etapa 1

Publicado em 01/10/2026 na conta `if0_43054548`. Importação confirmada pelo phpMyAdmin: 20 consultas executadas. Os 12 arquivos enviados foram conferidos por hash após a transferência. No navegador, foram verificados `/` e `/index.html`, bootstrap PHP, seis serviços, quatro projetos, imagens carregadas, PT/EN, tema claro/escuro e acesso autenticado ao dashboard. A revisão específica de celular foi concluída na etapa 3; veja `SITE-CONTEUDO-FINAL.md`.

## Banco

`migrations/003_site_conteudo.sql` cria `tb_site_secoes`, `tb_site_servicos` e `tb_site_projetos`, com utf8mb4. O seed preserva os cabeçalhos, os seis serviços e os quatro projetos originais e suas imagens. Reimportar não sobrescreve registros existentes nem duplica os cards. Nenhuma tabela operacional é modificada.

## Arquivos desta etapa

- Criados: `.htaccess`, `index.php`, `conexao/connection.php`.
- Criados: `includes/site_defaults.php`, `includes/site_content.php`, `includes/site_sections.php`.
- Criado: `templates/public-page.php`, mantendo o markup das outras seções.
- Criados: `assets/css/public.css`, `assets/js/public.js`, extraídos do HTML original.
- Criados: `assets/css/site-content.css`, `assets/js/site-content.js`.
- Criados: `migrations/003_site_conteudo.sql`, `tests/site_content.php`, este documento.
- Alterado: `conexao/banco.php`, utilizando a mesma fábrica de conexão com o comportamento anterior do admin.

O `index.html` original foi preservado. Na hospedagem, `/`, `/index.php` e `/index.html` usam PHP por meio do `.htaccess`, incluindo links existentes com `#services` ou `#gallery`.

## Comportamento

O PHP consulta as três tabelas com prepared statements. O HTML inicial já contém serviços e projetos; o JSON público permite a troca PT/EN sem outra requisição. Texto EN vazio usa PT, inclusive descrição e texto alternativo. A numeração usa somente cards ativos, ordenados por `ordem,id`. HTML é escapado; JavaScript usa `textContent` e elementos DOM. Imagens aceitam apenas os caminhos locais permitidos.

Falha da conexão ou consulta usa o conteúdo original. Tabela de cards fisicamente vazia usa o respectivo conteúdo original. Quando há registros mas todos estão inativos, nenhum card reaparece. Não há cache de conteúdo nesta etapa: uma nova visita lê o banco novamente.

## Como conferir agora

1. Abra `https://goldenjardim.site.je/index.html#services`: devem aparecer seis serviços, numerados de `01 / 06` a `06 / 06`, com o layout original.
2. Use EN: títulos e descrições dos serviços e cabeçalhos das seções devem mudar de idioma. Os projetos mantêm os nomes originais em PT porque o EN ainda está vazio.
3. Abra `https://goldenjardim.site.je/#gallery`: devem aparecer os quatro projetos e suas imagens originais.
4. Confira o login e o dashboard: as telas administrativas atuais continuam funcionando.

## Validação local

`tests/site_content.php` usa exclusivamente o banco local isolado `gj_site_tests` na porta 3308. Verifica seed, reimportação, leitura, ordem, inatividade, PT/EN, numeração, escape HTML/JSON, caminhos de imagem, tabelas vazias/ausentes e preservação das demais seções e CSS. As verificações CGI também simulam o banco indisponível e confirmam HTTP 200 no site e a resposta administrativa 503 anterior.

## Etapas seguintes

Etapa 2 concluída e publicada: telas de serviços e projetos, upload GD, validação de imagens, reordenação e ações POST/CSRF. A entrega, os backups e os testes estão documentados em `SITE-CONTEUDO-ETAPA2.md`.

Etapa 3 concluída: textos das seções, acabamento e revisão responsiva. O grupo Site na sidebar, edição dos cards pelo painel e bloqueio de execução em `uploads/site/` já estão ativos desde a etapa 2. Consulte `SITE-CONTEUDO-FINAL.md` para a entrega e auditoria finais e `GUIA-ADMIN-SITE.md` para o passo a passo de uso.
