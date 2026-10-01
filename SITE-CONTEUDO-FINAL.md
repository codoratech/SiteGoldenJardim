# Conteúdo público — entrega final (etapa 3)

Publicado em 01/10/2026 em `https://goldenjardim.site.je/`, conta `if0_43054548`. O módulo está concluído: Textos das seções, Serviços do site e Projetos. Guia de operação sem detalhes técnicos: [GUIA-ADMIN-SITE.md](GUIA-ADMIN-SITE.md).

## Implementação

`admin/site_textos.php` edita os seis campos PT/EN de cada seção: selo, título e subtítulo. As chaves permitidas são `services` e `gallery`. Cada formulário salva uma seção separadamente, com sessão administrativa, CSRF, validação de UTF-8/tamanho, consultas preparadas e escape de saída. Após salvar, há redirecionamento e toast; campos inválidos permanecem preenchidos.

Cada campo informa sua posição no site; as seções têm links diretos “Ver no site”. O submenu Site inclui a nova tela e destaca a página atual. O título PT é obrigatório; selo e subtítulo podem ficar vazios. EN vazio usa o respectivo PT. Selos vazios ficam ocultos, inclusive ao alternar de idioma. O carregamento público conserva o fallback padrão existente se o banco ou as tabelas de conteúdo estiverem indisponíveis.

As três telas do módulo têm somente o breadcrumb do corpo; o breadcrumb do header foi suprimido apenas nessas rotas. O espaçamento superior é uniforme: 32 px em desktop, 24 px em tablet e 20 px em celular. Formulários passam a uma coluna e as tabelas rolam dentro do card, sem alargar a página. Os componentes, tokens e temas seguem `PAINEL-VISUAL.md`.

## Banco e acentuação

Não foi necessário SQL novo nem migração. Esta etapa utiliza `tb_site_secoes`: `chave`, `selo_pt`, `selo_en`, `titulo_pt`, `titulo_en`, `subtitulo_pt`, `subtitulo_en`; o banco atualiza `atualizado_em` automaticamente. As telas de cards continuam usando `tb_site_servicos` e `tb_site_projetos`, sem alterar as tabelas operacionais.

A exportação nova do banco e o site renderizado confirmaram **Condomínio Belvedere** já acentuado. Não existe necessidade de corrigir esse registro, portanto nenhum UPDATE de acentuação foi executado.

## Arquivos desta etapa

| Situação | Arquivos |
| --- | --- |
| Criados e publicados | `admin/site_textos.php`, `admin/site_sections_helpers.php`, `admin/partials/site_sections_form.php` |
| Alterados e publicados | `admin/header.php`, `admin/footer.php`, `assets/css/admin-site.css`, `assets/js/admin-site.js` |
| Alterados e publicados | `templates/public-page.php`, `assets/css/site-content.css`, `assets/js/site-content.js`, `assets/js/public.js`, `.htaccess` |
| Somente locais | `.gitignore`, `tests/site_admin.cjs`, este documento, `GUIA-ADMIN-SITE.md` e notas de encerramento nas documentações anteriores |

Os 12 arquivos de produção foram baixados após o envio e conferidos por SHA-256. Utilitários de publicação, testes de layout, relatórios, SQL e imagens de validação permanecem somente nas pastas privadas locais. O código da etapa 2 e sua validação de uploads continuam ativos.

## Backup anterior à publicação

Pasta absoluta: `C:\TCC\cliente\golden-jardim-html\.deploy\backups\2026-10-01-site-etapa3`.

- `banco-antes.sql`: exportação nova pelo phpMyAdmin, com estrutura e dados das 15 tabelas, 30.014 bytes; SHA-256 `f2486750d98924107b2126c153296467c4d590c2fa81c578a4ac476744a97e33`.
- `files/`: os nove arquivos existentes que seriam sobrescritos: `.htaccess`, header, footer, os dois CSS, os três JS e o template público. Os três PHP novos não existiam antes.
- `textos-originais.json`: campos originais das duas seções, usados para restaurar o conteúdo depois dos testes.
- `layout/`: 30 capturas em larguras fixas e `metrics.json`.
- `fixtures-live.json`, `server-inventory-images.json`, `server-inventory-final.json`: conferência das imagens trocadas/excluídas e inventário remoto final.
- `web-audit.json`: respostas HTTPS da auditoria de acesso direto.
- `painel-textos-publicado.png`: captura da nova tela na hospedagem.
- `manifest.json`: tamanhos e hashes dos backups e evidências.

O backup da etapa 2 foi preservado em sua pasta própria. Nenhum backup foi enviado a `htdocs`. Para voltar à interface anterior, restaurar os nove arquivos de `files/` e remover os três PHP novos. O SQL não precisa ser restaurado para reverter somente a interface; uma restauração integral do banco substitui mudanças posteriores à exportação.

## Testes funcionais

Na hospedagem, os seis campos de Serviços foram editados em PT/EN e conferidos nos dois idiomas. Os textos de Projetos também foram editados; deixar os três campos EN vazios confirmou o fallback PT no site em inglês. Ao final, as duas seções foram restauradas aos valores originais pelos próprios formulários.

Um serviço e um projeto temporários foram criados, editados, reordenados, desativados e excluídos pelo modal. Os cards foram conferidos em PT/EN, inclusive sua posição e retirada do site quando inativos. JPG, PNG e WebP foram enviados, e ambos os registros tiveram a imagem substituída. O inventário durante o teste confirmou ausência dos dois arquivos antigos e presença dos dois novos. O inventário depois da exclusão confirmou a remoção dos quatro arquivos. A ordem original dos cards foi restabelecida; restam seis serviços e quatro projetos, sem os registros temporários.

Testes locais em banco isolado:

- `tests/site_admin.cjs`: CRUD das duas telas, CSRF, sessão, GET sem exclusão, uploads inválidos/tamanho, GD horizontal e vertical até 1600 px, transparência, troca/remoção, escape, fallback EN, ordem e visibilidade; validação das duas seções, limites, chave permitida, GET sem gravação, retenção em erro, CSRF, HTML como texto e breadcrumb único. Dashboard e Clientes continuam respondendo.
- `tests/site_content.php`: conteúdo PT/EN, visibilidade, ordenação, seed idempotente, escape e fallback com banco/tabelas indisponíveis.
- `tests/seguranca.cjs`: 14 casos de proteção e validação.
- Sintaxe dos PHP/JS alterados e verificação das diferenças concluídas sem erros.

## Responsividade comprovada

A simulação de viewport do navegador interno não respondeu de forma confiável. Foi utilizada a alternativa autorizada: **Brave headless com Playwright, em perfil separado, banco local isolado e capturas de larguras fixas**. Os arquivos da aplicação são os mesmos conferidos na publicação. Para evitar falhas de rede no carregamento visual, os recursos oficiais de Tailwind e fontes já usados pelo projeto foram obtidos de seus fornecedores, com verificação TLS, e servidos do cache privado ao navegador de teste.

Foram renderizadas cinco rotas: Textos, lista de Serviços, lista de Projetos e os dois formulários de cadastro. Cada uma foi testada em **390, 768 e 1440 px**, nos temas **claro e escuro**: 30 capturas. As medições confirmaram largura do documento igual à largura disponível, um breadcrumb, ausência do breadcrumb duplicado no header e espaçamento superior idêntico nas três telas. Em 390 px as tabelas de 660 px rolam dentro do card. Sidebar móvel abre/fecha; os dois formulários de Textos validam e focam o título obrigatório. Não houve erro de JavaScript. As capturas representativas de celular, tablet e desktop foram inspecionadas visualmente.

## Segurança e limpeza finais

O inventário final de `htdocs` contém 160 arquivos e não contém SQL, Markdown, backups, `.deploy`, previews, scripts de publicação, logs ou `teste_upload.php`. A configuração de conexão necessária ao PHP permanece no servidor, com acesso direto bloqueado; não há arquivo de publicação com credenciais.

A auditoria HTTPS confirmou página pública HTTP 200 e bloqueio de SQL, Markdown, backups, `.deploy`, `dashboard-preview.php`, `teste_upload.php`, logs e `conexao/config.local.php`. O InfinityFree devolve HTTP 302 para sua página de erro 403 nessas tentativas. Acesso direto ao helper novo retorna 404. A verificação do certificado TLS ficou habilitada.

`uploads/site/` terminou somente com seu `.htaccess`; as imagens dos testes foram removidas. A proteção de execução de PHP nessa pasta permanece instalada e havia sido testada com um arquivo inofensivo na etapa 2, depois removido.

`.deploy/`, `.local-tests/`, backups na raiz ou em subpastas e a configuração local estão no `.gitignore`. A conferência de arquivos rastreados não encontrou `.deploy`, backups ou `conexao/config.local.php` no Git. **Nenhum dos arquivos sensíveis especificados está acessível pela web.**
