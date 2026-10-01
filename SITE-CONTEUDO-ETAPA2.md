# Conteúdo público — etapa 2

Publicado em 01/10/2026, em `goldenjardim.site.je`, conta `if0_43054548`. Os arquivos de produção foram baixados após o envio e conferidos por SHA-256. Não foi necessário SQL novo: esta etapa utiliza as tabelas já instaladas na etapa 1.

## Telas e comportamento

- `admin/site_servicos.php`: cadastro e edição PT/EN, descrição, ícone, imagem, ordem e visibilidade.
- `admin/site_projetos.php`: cadastro e edição PT/EN, descrição, categoria, localização, imagem, texto alternativo, ordem e visibilidade.
- Menu `Site` na sidebar; breadcrumbs `Painel / Site / Serviços ou Projetos / Consultar, Cadastrar ou Editar`.
- Componentes e variáveis de `PAINEL-VISUAL.md`, temas claro/escuro, tabela com miniaturas, mensagens de sucesso, validação junto ao campo e modal de confirmação de exclusão existente.
- As setas reordenam os cards; o controle de visibilidade oculta ou reativa o registro no site. Todos os comandos de gravação, reordenação, visibilidade e exclusão usam POST com CSRF.
- Após salvar, atualizar a página pública mostra o novo conteúdo. EN vazio utiliza o respectivo texto PT. Serviços operacionais e ordens de serviço continuam separados do conteúdo público.

## Banco utilizado

`tb_site_servicos`: `id`, `titulo_pt`, `titulo_en`, `descricao_pt`, `descricao_en`, `imagem_path`, `icone`, `ordem`, `ativo`, `criado_em`, `atualizado_em`.

`tb_site_projetos`: os campos equivalentes de título, descrição, imagem, ordem, visibilidade e datas, mais `categoria_pt`, `categoria_en`, `local`, `imagem_alt_pt`, `imagem_alt_en`.

Não houve ALTER TABLE, importação ou alteração das tabelas operacionais. `tb_site_secoes` continua sendo lida pelo site; a tela de edição dos cabeçalhos fica para a etapa 3.

## Upload e segurança

JPG, PNG e WebP são validados pelo tamanho real, `is_uploaded_file`, `finfo` e `getimagesize`, depois decodificados e regravados pelo GD. Limite de entrada de 5 MB, saída com até 1600 px no maior lado, mantendo proporção e transparência. Há também limite de resolução e estimativa de memória para evitar imagens desproporcionais à capacidade do PHP.

O nome usa 16 bytes aleatórios, sem incorporar o nome enviado. Imagens ficam em `htdocs/uploads/site/`. A regra local permite somente os nomes de imagens gerados pelo sistema, bloqueando PHP e outros tipos de arquivo. O upload é recusado se essa proteção não estiver instalada.

Troca e exclusão removem o upload anterior após a confirmação da transação, desde que nenhuma das duas tabelas ainda o referencie. Falha na gravação remove o arquivo novo. Imagens originais em `assets/` são preservadas porque pertencem ao conteúdo de fallback e podem ser compartilhadas. O sistema somente remove arquivos próprios dentro da pasta de uploads.

As páginas exigem sessão administrativa antes de consultar ou produzir saída. SQL usa os helpers de prepared statements já existentes; nomes de tabelas e campos vêm de configurações internas permitidas. Saídas HTML são escapadas com `htmlspecialchars`; textos não são renderizados como HTML. Transações e bloqueios de linhas protegem a reordenação e a substituição de imagens. Falhas de limpeza geram aviso, sem informar detalhes internos ao visitante.

## Arquivos criados e alterados

| Tipo | Arquivos |
| --- | --- |
| Criados, publicados | `admin/site_config.php`, `admin/site_uploads.php`, `admin/site_helpers.php`, `admin/site_controller.php`, `admin/site_servicos.php`, `admin/site_projetos.php` |
| Criados, publicados | `admin/partials/site_page.php`, `admin/partials/site_form.php`, `assets/css/admin-site.css`, `assets/js/admin-site.js`, `uploads/site/.htaccess` |
| Alterados, publicados | `admin/header.php`, `admin/footer.php`, `admin/icons.php`, `.htaccess` |
| Criados/alterados, somente locais | `tests/site_admin.cjs`, `.gitignore`, `SITE-CONTEUDO.md`, este documento |

Os utilitários privados de publicação, inventário e auditoria permanecem em `.deploy/` local. Testes, documentos, backups e credenciais de publicação não foram enviados à hospedagem.

## Backup anterior à publicação

Pasta local absoluta: `C:\TCC\cliente\golden-jardim-html\.deploy\backups\2026-10-01-site-etapa2`.

- `banco-antes.sql`: exportação phpMyAdmin com estrutura e dados das 15 tabelas, 30.442 bytes. SHA-256 `921B4E87702566272911D9F4A8D41F2438B6894DCDE0B1F043D100C57D73FA35`.
- `files/.htaccess`, `files/admin/header.php`, `files/admin/footer.php`, `files/admin/icons.php`: cópias dos arquivos hospedados antes da primeira publicação desta etapa.
- `files/admin/site_uploads.php`: cópia adicional antes do ajuste final para limitar também a altura das fotos verticais.
- `manifest.json`: tamanhos e hashes dos arquivos de backup e evidências.
- `server-inventory.json` e `server-inventory-final.json`: inventários antes e depois da publicação.
- `web-audit.json`: respostas da auditoria HTTPS. `painel-projetos.png` e `painel-projetos-escuro.png`: imagens da entrega.

O backup fica fora de `htdocs` e não é acessível pela web. Para restaurar a etapa anterior, devolver os quatro arquivos iniciais de `files/` às suas posições e remover os arquivos novos deste módulo. O SQL só deve ser restaurado se também for necessário desfazer mudanças de conteúdo feitas desde a exportação; restaurá-lo substitui dados posteriores.

## Auditoria da hospedagem

O inventário final contém 157 arquivos. Não há `migrations/*.sql`, `tests/*.sql`, `*.md`, logs, arquivos de publicação ou backups no servidor, nem `dashboard-preview.php` ou `teste_upload.php`. A configuração PHP necessária ao funcionamento do banco permanece instalada e tem acesso direto bloqueado.

As requisições HTTPS de teste para SQL, Markdown, os dois diagnósticos, `.deploy/.../ACESSO.txt`, `error.log` e `conexao/config.local.php` foram bloqueadas: a hospedagem respondeu HTTP 302 para sua página de erro 403. A página pública respondeu HTTP 200 normalmente. A validação do certificado TLS permaneceu ativada.

Um PHP inofensivo temporário dentro de `uploads/site/` foi bloqueado com 403 enquanto existia. Depois foi copiado para o backup e removido do servidor. Os registros de teste e todas as suas imagens foram removidos; ao final, `uploads/site/` contém somente seu `.htaccess`.

Resultado da auditoria: nenhum dos arquivos sensíveis especificados ficou acessível pela web em `htdocs`.

## Testes realizados

Na hospedagem, foram criados um serviço e um projeto temporários, editados seus textos, trocadas as imagens, alterada a ordem, desativados, reativados e excluídos pelo modal. PT e EN foram conferidos no site, incluindo fallback de texto EN vazio, categoria e texto alternativo. JPG, PNG e WebP foram aceitos; a foto horizontal de 2400 × 1200 foi entregue em 1600 × 800. As imagens substituídas e excluídas desapareceram do inventário remoto. Restaram os seis serviços e os quatro projetos originais.

`tests/site_admin.cjs`, usando somente banco local isolado, passou para ambas as telas: sessão, CSRF inválido, título obrigatório com preservação dos campos, arquivo PHP disfarçado de JPG, rejeição acima de 5 MB, imagens horizontais e verticais, transparência PNG/WebP, substituição e remoção, salvar sem trocar a imagem, escape de HTML, ordem, visibilidade, GET sem exclusão, POST de exclusão e leitura pública PT/EN. O ajuste final de fotos verticais foi validado com 1200 × 2400 → 800 × 1600 e publicado com conferência por hash.

Também passaram `tests/site_content.php`, `tests/seguranca.cjs`, `tests/dashboard.php`, verificação de sintaxe PHP/JS e revisão de diferenças. Dashboard e Clientes continuam funcionando nos testes de integração. Temas claro e escuro foram conferidos visualmente no painel hospedado. O CSS inclui quebra do formulário em uma coluna e rolagem horizontal da tabela; a simulação de viewport do navegador não respondeu, portanto a conferência visual em celular permanece para o acabamento da etapa 3.

## Como testar pelo painel

1. Entrar no admin e abrir `Site → Serviços do site` ou `Site → Projetos`.
2. Usar `Novo serviço/projeto`, preencher título PT e EN, selecionar uma imagem JPG/PNG/WebP até 5 MB e salvar.
3. Usar `Ver no site` e alternar PT/EN. Atualizar a página pública depois de cada alteração.
4. Editar o registro e escolher outra imagem; usar as setas para mudar sua posição e o controle de visibilidade para ocultá-lo e reativá-lo.
5. Excluir apenas o registro criado para teste, confirmando o modal; conferir que ele desapareceu do site.

As páginas estão em `https://goldenjardim.site.je/admin/site_servicos.php` e `https://goldenjardim.site.je/admin/site_projetos.php`. A etapa 3 foi concluída em 01/10/2026: textos dos cabeçalhos, acabamento responsivo e auditoria final estão documentados em `SITE-CONTEUDO-FINAL.md`; o guia de uso está em `GUIA-ADMIN-SITE.md`.
