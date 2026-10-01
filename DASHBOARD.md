# Visão Geral — Golden Jardim

O painel mantém PHP/mysqli, autenticação, proteção CSRF e as rotas atuais. Gráficos usam Chart.js 4.5.1 local, SVGs sem dependência externa e Inter. Não foram inseridos dados de demonstração na hospedagem.

## Arquivos

Alterados: `admin/dashboard.php`, `admin/header.php`, `admin/footer.php`, `admin/produtos.php`, `admin/cadastrar_produto.php`, `admin/estoque.php`, `admin/cadastrar_estoque.php`.

Criados: `admin/dashboard_data.php`, `admin/icons.php`, `admin/inventory_helpers.php`, `admin/stock_fields.php`, `assets/css/admin.css`, `assets/css/dashboard.css`, `assets/js/admin-theme.js`, `assets/js/admin-layout.js`, `assets/js/admin-forms.js`, `assets/js/dashboard.js`, `assets/vendor/chart.umd.min.js`, `migrations/002_dashboard_estoque.sql`, `tests/dashboard.php`, `tests/dashboard-exemplos.sql` e este documento.

## Dados e regras

| Origem | Colunas utilizadas |
| --- | --- |
| tb_clientes | ID_Cliente, cli_Nome, cli_DataCadastro |
| tb_produtos | ID_Produto, Pro_Nome, Pro_EstoqueMinimo |
| tb_servicos | ID_Servico, Ser_Nome |
| tb_ordensservico | ID_Ordem, TB_Clientes_ID_Cliente, Or_DataServico, Or_Status, Ord_ValorTotal |
| tb_itensordemservico | TB_Servicos_ID_Servico, TB_OrdensServico_ID_Ordem, Ite_Quantidade |
| tb_agendamentos | ID_Agendamento, TB_Clientes_ID_Cliente, TB_OrdensServico_ID_Ordem, Age_DataAgendada, Age_Status |
| tb_contasreceber | Con_Valor, Con_DataVencimento, Con_Status |
| tb_estoque | Est_Tipo, Est_Quantidade, TB_Produtos_ID_Produto |

Os seis KPIs mostram totais reais. Clientes, ordens e agendamentos comparam o mês atual com o anterior pela data disponível; sem base anterior não há percentual. O mês atual pode estar incompleto. Produtos e serviços não têm data de cadastro no schema, portanto não recebem variação fictícia.

Ordens mensais usam a data do serviço. A rosca inclui todo o histórico; status desconhecidos aparecem em Outros. Top serviços soma quantidades vinculadas a ordens concluídas, sem inferir serviços por texto. Agendamentos sem itens vinculados mostram “Serviço ainda não vinculado”.

O financeiro compara contas Pago/Pendente pelo **vencimento**, pois não existe data de pagamento. Canceladas ficam fora do comparativo. Não representa fluxo de caixa pela data efetiva de recebimento.

Estoque: saldo = entradas − saídas vinculadas ao produto. Registros antigos ou avulsos, sem vínculo, não são convertidos nem somados. Mínimo NULL desativa monitoramento. Saldo <= mínimo gera alerta; saldo zerado, negativo ou <= 25% do mínimo é crítico. Estoque negativo é visível para permitir detectar inconsistências. Registre o saldo inicial como Entrada, depois suas movimentações; compras e ordens não alteram o estoque automaticamente.

## Migração

Aplicada na conta if0_43054548 após autorização. Execute uma única vez em outras instalações:

```sql
ALTER TABLE tb_produtos ADD COLUMN Pro_EstoqueMinimo INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE tb_estoque
 ADD COLUMN TB_Produtos_ID_Produto INT NULL DEFAULT NULL,
 ADD INDEX idx_estoque_produto (TB_Produtos_ID_Produto),
 ADD CONSTRAINT fk_estoque_produto FOREIGN KEY (TB_Produtos_ID_Produto)
 REFERENCES tb_produtos (ID_Produto);
```

## Testar

1. Abra `admin/dashboard.php`: KPIs, ordens, agenda e financeiro devem refletir o banco.
2. Troque o tema e recarregue. Gráficos e todas as superfícies acompanham a escolha, salva em localStorage.
3. Em tela pequena, abra/feche o menu; use Escape e Tab. Teste os submenus, busca de telas, notificações e menu do usuário.
4. Cadastre um produto com mínimo 10. Registre Entrada 8 vinculada: alerta Baixo. Registre Saída 7: saldo 1 e alerta Crítico, badge na sidebar e aviso no topo. Remova o mínimo: alerta desaparece.
5. Sem registros, cada seção deve mostrar seu estado vazio. Abra “Ver dados em texto” para ler os valores dos gráficos sem depender do canvas.

`tests/dashboard-exemplos.sql` fornece INSERTs fictícios para seis meses de ordens, serviços, agenda, contas e três níveis de estoque. Use somente num banco de testes após a migração. Os exemplos não foram aplicados no site público.

Testes automatizados: `php tests/dashboard.php` usa exclusivamente MySQL local na porta 3308, banco isolado gj_dashboard_tests, e desfaz os exemplos por rollback. `node tests/seguranca.cjs` verifica CSRF/validações. A integração local também verifica cadastros, edições, relações e logout.

Chart.js: https://www.chartjs.org/docs/latest/getting-started/ (licença MIT preservada no arquivo distribuído).

## Demonstração na hospedagem

Em 30/09/2026, com autorização do usuário, foi importado o lote `.deploy/infinityfree-2026-09-30/dados-demonstracao.sql` no banco `if0_43054548_goldenjardim`. Foram adicionados 8 clientes, 5 serviços, 6 produtos, 12 movimentações de estoque, 28 ordens e seus itens, 34 contas, 5 próximos agendamentos e 2 fornecedores. Os nomes usam `[DEMO]`, os e-mails usam `example.invalid` e os dados anteriores foram preservados. O lote tem transação e proteção contra importação duplicada.

Verificado no dashboard hospedado: quatro gráficos preenchidos para seis meses, cinco próximos agendamentos e quatro alertas de estoque (dois críticos e dois baixos), incluindo badge da sidebar e aviso no topo.

Validação concluída: consultas com fixtures locais e rollback; integração de 19 telas, 9 cadastros e 9 edições, CSRF e logout; tema claro/escuro e persistência; celular com 390px sem rolagem lateral; menu móvel, busca, notificações e formulários publicados. Migração aplicada e todos os arquivos publicados foram comparados por hash.
