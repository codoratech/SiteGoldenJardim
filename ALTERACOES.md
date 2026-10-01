# Melhorias aplicadas

Nenhum arquivo ou registro foi excluído. O README com alterações locais foi preservado.

## Segurança do painel

- Todas as páginas administrativas verificam a sessão antes das operações no banco.
- Formulários, exclusões e saída usam POST com token CSRF. Links GET não excluem registros.
- Novas senhas usam `password_hash`. Senhas antigas em MD5 são atualizadas automaticamente após um login válido, sem mudar a senha do usuário.
- Novas senhas devem ter ao menos 8 caracteres. Logins duplicados são recusados no painel. Não há criação automática de contas com senhas públicas.
- O usuário conectado não pode excluir o próprio acesso. A proteção do último usuário foi mantida.
- A sessão usa cookie HttpOnly, SameSite e Secure em HTTPS; seu identificador é renovado no login.
- Campos obrigatórios, e-mail, datas, identificadores, valores, quantidades e opções de status recebem validação no servidor.
- Erros de banco são registrados no log do PHP e não exibem detalhes técnicos nas telas.

## Banco e instalação

As configurações desta instalação foram preservadas em `conexao/config.local.php`, ignorado pelo Git. Em outra instalação, copie `conexao/config.example.php` para esse nome e configure o banco. As variáveis `GJ_DB_HOST`, `GJ_DB_USER`, `GJ_DB_PASSWORD`, `GJ_DB_NAME` e `GJ_DB_PORT` também são aceitas e têm precedência. O código deixa de trocar silenciosamente de banco depois da conexão.

O arquivo `banco.sql` cria instalações novas sem senha pública. Para criar o primeiro administrador, execute `php tools/criar_admin.php` no terminal. O programa só funciona quando não há usuários.

Para instalações existentes, `migrations/001_login_unico.sql` contém a verificação de duplicatas, a ampliação da coluna de senha para 255 caracteres e a restrição de login único. Resolva duplicatas pelo painel antes de aplicar a restrição. Não execute novamente a criação de uma chave que já existe. A migração não foi aplicada ao banco real porque ele não estava acessível durante a revisão.

O modelo de estoque e as tabelas de compras foram preservados. Vincular estoque a produtos ou criar telas de compras exige definir o fluxo operacional e migrar dados existentes; não foram inventados vínculos para os registros atuais.

## Site público

- WhatsApp: +55 19 98986 2859, nos três pontos de contato.
- Menu para celular com controle por teclado e fechamento ao selecionar um link ou pressionar Escape.
- Seletor de idioma disponível também em telas pequenas.
- Título de serviços ajustado em português e inglês.
- Comparação antes/depois acessível por setas, Home e End, com posição anunciada para leitores de tela.
- Indicador de foco visível e descrições mais claras das imagens de comparação.

Os números “120+ projetos”, “15+ anos”, nomes de projetos, depoimentos e dados de atendimento foram preservados e ainda precisam de confirmação da empresa.

## Verificação

Sintaxe PHP e JavaScript verificada. Todos os formulários POST conferidos quanto ao token CSRF. Testes HTTP confirmaram redirecionamento de 21 páginas protegidas, rejeição de POST sem token e disponibilidade do site com o novo contato. Menu em 390 px e comparação por teclado verificados no navegador.

Execute `node tests/seguranca.cjs` para verificar os casos de validação de requisições. O teste usa o PHP local, sem conectar ao banco. Defina `PHP_BINARY` se o PHP estiver em outro caminho.

Pendência de ambiente: validar login existente, migração automática de senha, cadastros, edições e exclusões com um banco de teste acessível antes da publicação. Nenhuma alteração foi publicada ou enviada ao GitHub.

## Testes com a exportação da escola

As 12 tabelas fornecidas foram importadas em uma instância local isolada do MariaDB do XAMPP, com o banco de teste gj_codex_test. Os dumps e dados locais ficam em .local-tests, ignorado pelo Git. Os arquivos originais e o servidor da escola não foram alterados.

A configuração local do projeto foi corrigida para o banco 240224_web, mantendo o host da escola. Os nomes das tabelas nas consultas e na instalação foram alinhados aos nomes minúsculos da exportação, evitando diferenças entre servidores Windows e Linux.

A migração de login único foi aplicada e verificada somente na cópia. Os testes de requisições PHP via CGI confirmaram: 19 telas autenticadas, 9 cadastros, 9 edições e 9 exclusões de registros fictícios; preservação de acentos nos dados novos; bloqueio de exclusões via GET, sem CSRF e com dependências; saída e novo login. Um acesso fictício em MD5 foi atualizado para password_hash e autenticou novamente com a mesma senha. Nenhuma senha original precisou ser utilizada nos testes.

A escola usa MySQL 5.6.21, conforme a exportação. Os testes locais foram feitos em MariaDB 10.4.32 e PHP 8.2.12: confirmam o funcionamento com a estrutura exportada, mas não verificam a rede, as permissões ou a configuração exata do servidor da escola. A migração no servidor real continua pendente.
