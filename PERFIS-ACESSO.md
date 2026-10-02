# Perfis e permissões — Golden Jardim

Foi reaproveitado `tb_login.log_perfil`, agora `VARCHAR(80)`, evitando tabelas extras para os poucos perfis atuais.
O mapa central em `admin/permissions.php` associa perfis a permissões e permite ampliar ambos sem outra alteração de esquema.

| Perfil | Consultar o painel | Alterar dados de gestão | Conteúdo público | Gerenciar logins/perfis |
| --- | --- | --- | --- | --- |
| Administrador | Sim | Sim | Sim | Sim |
| Editor do site | Sim | Não | Sim | Não |
| Operacional | Sim | Sim | Não | Não |
| Consulta (compatibilidade) | Sim | Não | Não | Não |

`conteudo_publico` permite editar textos, serviços, projetos, ordenação, visibilidade e imagens. O cadastro de novos logins seleciona Operacional por padrão. O perfil é exibido no cadastro, edição, listagem e Meu perfil. Meu perfil continua somente leitura, exceto pela alteração da própria senha.

## Migração no InfinityFree

1. No phpMyAdmin, selecione **o banco utilizado pelo site** e faça uma exportação de backup.
2. Abra **Importar**, selecione `migrations/006_permissoes_conteudo_publico.sql` e execute. Também é possível colar o conteúdo na aba SQL.
3. Não é necessário executar a migração 005 antes: a 006 verifica se `log_perfil` existe, ajusta seu tipo se necessário e cria `log_ativo` somente se ausente.
4. Confira o resultado final: Adm deve estar como Administrador, com `log_ativo = 1`. Operador é convertido para Operacional; Consulta e demais perfis preenchidos são preservados. Nomes, logins, códigos e hashes de senha são mantidos.
5. Publique os arquivos PHP listados abaixo, preservando os caminhos relativos. A migração deve preceder os novos arquivos PHP; o SQL não deve ser colocado na pasta pública. O painel usa a nova coluna de atividade para conferir acessos.
6. O SQL pode ser executado novamente. Não apaga tabelas ou registros e não redefine alterações de perfil feitas posteriormente, exceto por manter o login protegido Adm ativo e Administrador.

Os perfis são definidos em PHP; não há tabela de perfis a popular. `banco.sql` contempla a estrutura nova para instalações novas. Para o banco já hospedado, utilize somente a migração 006.

## Funcionamento no servidor

- `usuarioTemPermissao($permissao)` e `exigirPermissao($permissao)` estão centralizadas em `admin/access.php`.
- No login, perfil e permissões são salvos na sessão. Em cada requisição autenticada, a identidade é lida novamente com prepared statement. Alterações de perfil e desativação de contas valem também para sessões abertas; dados enviados pelo formulário ou uma lista antiga de permissões na sessão não concedem acesso.
- O bloco inteiro Conteúdo Público/Site só aparece se houver `conteudo_publico`.
- GET/HEAD sem permissão em `site_servicos.php`, `site_projetos.php` e `site_textos.php` redirecionam para `dashboard.php`, com o aviso “Você não tem permissão para acessar esta área”. O aviso é exibido uma vez.
- POST, upload e acesso direto aos controladores/helpers/partials do editor sem permissão retornam HTTP 403. A autorização ocorre antes de validar conteúdo, gravar no banco ou salvar/remover imagens. O CSRF existente continua obrigatório.
- Somente Administrador acessa Cadastrar Login e Logins Existentes. O login Adm, a própria condição de Administrador e o último Administrador ativo são protegidos. A edição/exclusão trava os registros de logins na transação antes de validar essas proteções.
- `log_ativo = 0` bloqueia novas autenticações e sessões abertas. Não foi adicionada uma tela de desativação de contas.

## Arquivos desta implementação

Criados:

- `admin/permissions.php`
- `migrations/006_permissoes_conteudo_publico.sql`
- `tests/permissions_migration.php`
- `PERFIS-ACESSO.md`

Alterados:

- `admin/access.php`
- `admin/index.php`
- `admin/login_access_helpers.php`
- `admin/logins.php`
- `admin/header.php`
- `admin/dashboard.php`
- `admin/perfil.php`
- `admin/ui_config.php`
- `admin/partials/manage_form.php`
- `admin/site_servicos.php`
- `admin/site_projetos.php`
- `admin/site_textos.php`
- `admin/site_controller.php`
- `admin/site_config.php`
- `admin/site_helpers.php`
- `admin/site_sections_helpers.php`
- `admin/site_uploads.php`
- `admin/teste_upload.php` (diagnóstico legado; permanece bloqueado pelo `.htaccess` hospedado)
- `admin/partials/site_page.php`
- `admin/partials/site_form.php`
- `admin/partials/site_sections_form.php`
- `banco.sql`
- `tools/criar_admin.php`
- `tests/access.php`
- `tests/access_http.cjs`

`admin/cadastrar_login.php`, `admin/ui_helpers.php`, `admin/bootstrap.php` e a coluna Perfil já existiam e reutilizam as funções atualizadas; não precisaram de novas alterações nesta etapa. Nenhum arquivo do site público ou de CSS foi alterado nesta etapa.

No servidor são necessários os arquivos de `admin/` criados/alterados, exceto o diagnóstico `teste_upload.php`, que não precisa ser publicado. Testes, documentação, SQL e ferramentas CLI permanecem locais.

## Roteiro curto de teste

1. Entre como Adm. Confira Conteúdo Público > Site, as três telas do editor e Meu perfil indicando Administrador.
2. Em Cadastrar Login, crie um login próprio para teste com perfil Operacional. Confira a coluna Perfil em Logins Existentes.
3. Entre com esse login em outra janela/perfil de navegador. Confirme que o título Conteúdo Público e o item Site não aparecem e que os cadastros de gestão continuam acessíveis.
4. Digite `/admin/site_servicos.php`, `/admin/site_projetos.php` e `/admin/site_textos.php`: espere redirecionamento para Visão Geral e a mensagem de falta de permissão. Um POST ou upload sem permissão deve receber 403, sem alterar banco ou imagens.
5. Volte ao Administrador e altere esse login para Editor do site. Na sessão de teste, atualize a página: o menu deve aparecer sem novo login. Confira salvar/editar/excluir e upload de uma imagem de teste no editor; cadastros de gestão e Logins não devem permitir alterações por esse perfil.
6. Confira que tentar rebaixar o próprio Administrador é recusado. Em um ambiente de teste, confira também a proteção do último Administrador ativo.

## Validação local realizada

- Sintaxe dos PHP alterados: aprovada.
- Matriz de permissões: 446 verificações, mais proteções de atividade e adulteração de permissões da sessão.
- Rotas PHP reais, em banco MariaDB isolado: 105 grupos de testes, incluindo cadastro/edição de perfil, CRUD de gestão, editor de serviços/projetos/textos, multipart de imagem permitido/bloqueado, remoção de imagens, redirecionamento e aviso único, CSRF, perfil próprio, revogação em sessão aberta e contas inativas.
- Migração: instalações com campo ausente, ENUM legado e VARCHAR; várias execuções sem perda de hashes ou perfis.

Esta etapa foi validada localmente. A migração 006 e os novos arquivos desta etapa ainda não foram aplicados ao InfinityFree.
