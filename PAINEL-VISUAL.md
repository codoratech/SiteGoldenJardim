# Padrão visual do painel

As 18 telas de cadastro, consulta e edição usam os tokens do dashboard aprovado, os mesmos ícones SVG, tipografia Inter, superfícies, botões e temas. O dashboard e seus gráficos permanecem com os arquivos originais.

## Arquivos criados

- `admin/ui_config.php`: campos, rótulos, seções, colunas e filtros das nove áreas.
- `admin/ui_helpers.php`: consultas preparadas, paginação, escape, badges, normalização visual do tipo de cliente e retenção de campos em erros.
- `admin/partials/manage_page.php`: cabeçalho, breadcrumb, feedback, filtros, tabela, ações, estado vazio e paginação.
- `admin/partials/manage_form.php`: card de formulário, seções, campos associados a labels e ações.
- `assets/css/admin-manage.css`: apresentação usando as variáveis já existentes de `admin.css`.
- `assets/js/admin-manage.js`: máscaras, validação em português e modal nativo acessível de exclusão.

## Arquivos alterados

- `admin/header.php`, `admin/footer.php`, `admin/icons.php`: breadcrumb por tela, assets das telas e modal compartilhado.
- `admin/bootstrap.php`: validação mantém o status HTTP 400 e permite renderizar o formulário com feedback; CSRF inválido continua encerrando a requisição com 403.
- Cadastros: `cadastrar_cliente.php`, `cadastrar_produto.php`, `cadastrar_servico.php`, `cadastrar_fornecedor.php`, `cadastrar_ordem.php`, `cadastrar_agendamento.php`, `cadastrar_estoque.php`, `cadastrar_conta.php`, `cadastrar_login.php`.
- Consultas/edições: `clientes.php`, `produtos.php`, `servicos.php`, `fornecedores.php`, `ordens.php`, `agendamentos.php`, `estoque.php`, `financeiro.php`, `logins.php`.

Os handlers das rotas existentes continuam responsáveis por salvar/editar/excluir. As consultas antigas com interpolação foram convertidas em prepared statements; campos, tabelas, relações, redirecionamentos de sucesso e regras foram preservados. Exclusão já era POST com CSRF; agora usa modal com foco, Escape e retorno ao botão de origem. O login Adm continua protegido.

## Banco

Não há migração SQL para esta alteração. A conexão existente já usa `mysqli_set_charset($con, 'utf8mb4')`. Valores legados de tipo começando por `Condom` aparecem como `Condomínio`, e o filtro inclui essas variações. Não foi executado UPDATE ou ALTER TABLE. Ao editar um cliente, o select usa as opções válidas já existentes.

Estoque mostra os mesmos saldos e alertas do dashboard. Contas pendentes com vencimento anterior ao dia atual de Brasília aparecem como Vencido, sem alterar o status salvo.

## Testar

1. Abra Clientes e pesquise por nome, e-mail ou telefone; aplique o filtro de tipo. Ordens, agenda, estoque e financeiro têm filtros de status/tipo próprios.
2. Em Ordens ou Financeiro, avance à segunda página (10 registros por página); a busca e o filtro são preservados.
3. Abra um cadastro/edição. Envie com campo obrigatório vazio, e-mail incorreto, telefone incompleto ou estoque mínimo fracionário. O erro aparece junto ao campo. Erros do servidor mantêm os dados digitados; senhas não são repostas.
4. Abra Excluir e use Cancelar/Escape para conferir o modal. Não é necessário excluir um registro real para testar o visual.
5. Alterne o tema, recarregue e abra as telas em celular. Tabelas têm rolagem horizontal dentro do card; a página não deve rolar lateralmente. Sidebar, submenu ativo e badge de estoque continuam funcionando.
6. Confira Estoque e o filtro Vencido no Financeiro; volte à Visão Geral e confira os gráficos.

Validação funcional local: nove cadastros, nove edições e nove exclusões de fixtures isoladas; proteção de vínculos, autenticação, logout e 14 casos CSRF/validação. Testes de interface cobrem filtros, paginação, acentos, busca com conteúdo SQL, escape, retenção em erros e os contratos das 18 telas. A alteração não adicionou registros na hospedagem.

Publicação concluída no InfinityFree com verificação por hash dos 28 arquivos enviados. Conferidas as 18 telas hospedadas, temas claro/escuro, campo obrigatório e máscara de telefone, filtro de clientes, modal com cancelamento, paginação de contas, badge de contas vencidas, quatro alertas de estoque, proteção do Adm, celular de 390px e tablet de 820px. Após estabilizar o menu, o celular teve largura de documento igual à largura disponível (375px, com barra de rolagem), sem transbordamento da página. O dashboard continua com quatro gráficos e seus estilos originais.

Capturas de validação: `.deploy/infinityfree-2026-09-30/clientes-padrao-dashboard.png`, `cadastro-cliente-novo.png` e `estoque-mobile-novo.png`.
