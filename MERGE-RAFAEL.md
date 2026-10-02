# Merge Golden Jardim — Rafael + painel

## Resultado

Publicado em `https://goldenjardim.site.je/` em 01/10/2026. O phpMyAdmin confirmou 14 consultas executadas com sucesso: 11 registros originais atualizados, sem alteração do schema. HTML, template PHP, padrões, CSS e JS foram conferidos por hash após publicação. Os registros personalizados são preservados pelas condições do SQL.

A conferência de uma edição no admin hospedado aguarda nova autenticação: a sessão expirou. A integração de edição já passou no teste local com o PHP e os formulários reais do painel.

`index.html` utiliza a estrutura e o design de `indexRafael.html`, com as integrações do painel reaplicadas. A página PHP realmente utilizada na hospedagem também foi atualizada: a regra existente de `.htaccess` encaminha `/index.html` para `index.php`, que carrega o banco e renderiza `templates/public-page.php`.

`indexRafael.html` foi preservado integralmente. SHA-256 antes e depois: `2451b1ff33955722b3a18c0d265da7daca4a759f4a48b78a19eeccb52953743a`.

Não houve mudança no dashboard, nas telas administrativas ou nas tabelas operacionais. O conteúdo público editado no painel continua tendo prioridade; a sincronização aprovada atualiza apenas os registros que ainda correspondem aos originais.

## O que foi mantido de cada versão

**Rafael:** todas as sete seções, estrutura, paleta, estilos, fotos, textos atualizados, contador de 70+ projetos, projetos da galeria e o novo comparador antes/depois. Hero, antes/depois, depoimentos, Sobre e Contato continuam com o conteúdo dele; não receberam uma integração nova inventada.

**Codex:** conteúdo de Serviços e Projetos carregado do banco, textos de seções editáveis, selo opcional, PT/EN com fallback para PT, ordenação, visibilidade, uploads, saída segura, renderização PHP e JSON, fallback se o banco falhar, IDs/seletores, menu móvel e acessibilidade do comparador. A versão anterior já utilizava renderização PHP; não foi criada uma API ou um fetch desnecessário.

Todas as seções têm correspondência. Nenhuma seção foi removida ou descartada; não houve funcionalidade sem lugar no novo layout.

## Conflitos e decisões

| Conflito | Decisão |
| --- | --- |
| “Nossos Serviços” / “Our Services” versus “Sistemas Centrais” / “Core Systems” | Rafael como padrão; título original do banco sincronizado condicionalmente. O painel continua podendo alterá-lo. |
| Quatro projetos antigos versus os quatro novos | Com autorização do Gustavo, os originais foram mapeados por conteúdo completo para Jardim Frontal, Área de Lazer, Entrada · Parque das Oliveiras e Entrada · Villagio Azul. Personalizações são preservadas. |
| Fotos antigas e serviços sem foto versus imagens embutidas do Rafael | As dez fotos dos cards foram extraídas para `assets/rafael-*.jpg` sem recompressão ou alteração de pixels. As três fotos embutidas de hero e antes/depois permanecem no HTML/template. |
| Número provisório do WhatsApp no arquivo do Rafael | Mantido o número real anteriormente informado por Gustavo: +55 19 98986-2859. |
| `logo-golden-jardim.png` ausente | Por autorização do Gustavo, mantido temporariamente o símbolo anterior. Não foi inventada uma logo nem deixado um caminho quebrado. |
| HTML do Rafael informa 08h–18h, mas seu dicionário JS informa 09h–17h | HTML atualizado prevalece; dicionários PT/EN alinhados para 08h–18h / 8am–6pm. |
| Texto de Sobre atualizado no HTML, antigo no dicionário JS | Texto PT do HTML aplicado ao dicionário para não ser substituído durante o carregamento. O título visível “equilíbrio” segue o próprio dicionário do Rafael, que já corrige o “equilíbsrio” do HTML inicial. |
| Renderização estática dos cards versus conteúdo do painel | Renderizadores seguros existentes recebem os dados do banco e preservam as classes do Rafael. Numeração usa a quantidade real de cards, sem `/ 06` fixo. |
| CSS novo do comparador versus lógica antiga que ajustava largura | Mantidos CSS e eventos de ponteiro do Rafael; integração de teclado preservada no elemento com papel de slider, com quatro setas, Home/End e posição zero correta. |
| Menu e idiomas no celular ausentes na versão do Rafael | Reaplicados o botão de menu, ID, atributos ARIA e controles de idioma; desktop conserva a estrutura visual dele. |
| Títulos longos ultrapassavam 390 px com as fontes carregadas | Somente abaixo de 768 px, tamanhos dos títulos foram ajustados para caber na tela, mantendo fontes, pesos e cores. Sem alteração da tipografia desktop. |

## Arquivos

- Mesclado: `index.html`.
- Adaptados para o site dinâmico: `templates/public-page.php`, `includes/site_defaults.php`, `assets/css/public.css`, `assets/js/public.js`.
- Dez fotos novas: `assets/rafael-servico-N-HASH.jpg` (1–6) e `assets/rafael-projeto-N-HASH.jpg` (1–4). Os nomes e hashes exatos estão no `merge-assets.json` do backup. Os arquivos são referenciados no HTML, no fallback PHP e no SQL.
- Sincronização de dados: `migrations/004_site_conteudo_rafael.sql`, somente local, não publicada como arquivo web.
- Testes locais: `tests/site_merge.php`, ajuste de `tests/site_content.php`.
- Este documento e utilitários/evidências de teste são somente locais.

`index.php`, `.htaccess`, renderizadores, helpers de upload e `assets/js/site-content.js` foram reaproveitados sem refatoração. Os parâmetros de versão dos assets foram atualizados no HTML/template para evitar carregar JS/CSS antigos do cache.

## Sincronização aprovada

O SQL foi apresentado ao Gustavo antes de ser executado. Não altera o schema, não apaga registros e não modifica IDs, ordem ou visibilidade. Executa 11 UPDATEs condicionais: uma seção, seis imagens de serviços e quatro projetos. Comparações binárias impedem que diferenças de texto sejam ignoradas pela colação; títulos da seção toleram a diferença técnica entre quebras de linha LF e CRLF.

Cada atualização exige que os campos de conteúdo ainda correspondam ao seed original. Se qualquer campo tiver sido personalizado, o registro inteiro é preservado. Reexecutar não duplica conteúdo nem substitui uma personalização. Inglês vazio continua utilizando o texto PT; traduções de nomes de projetos não fornecidas pelo Rafael não foram inventadas.

## Backups

Pasta: `C:\TCC\cliente\golden-jardim-html\.deploy\backups\2026-10-01-merge-rafael`.

- `index.codex-backup.html`: cópia do HTML anterior, antes do merge.
- Arquivos PHP/CSS/JS originais nas respectivas subpastas: cópias locais antes da alteração.
- `remote-files/`: cinco arquivos baixados da hospedagem antes da publicação.
- `banco-antes.sql`: exportação nova das 15 tabelas pelo phpMyAdmin, antes de sincronizar o conteúdo.
- `merge-assets.json`: hash do arquivo do Rafael e hashes das dez fotos extraídas.
- `browser-merge.json` e 24 PNGs: validação local de HTML e PHP.
- `publicacao-verificada.json`: arquivos enviados e hashes conferidos por download.
- `photos-http.json` e `photos-comparison.json`: hashes das fotos recebidas por HTTPS e comparação com os originais.
- `intermediarias-remotas/`: cópias das quatro imagens intermediárias antes de removê-las da hospedagem.
- `merge-publicado.png`: captura do site hospedado após a sincronização.

Backups, SQL, testes e este documento não devem ser enviados a `htdocs`. As pastas privadas já estão no `.gitignore` e o servidor mantém as regras de bloqueio de acesso direto.

Inventário final: 170 arquivos, sem SQL, Markdown, ZIP de publicação, backups, logs ou arquivos de teste/preview. As quatro fotos intermediárias foram removidas; as dez fotos finais permanecem. A auditoria HTTPS confirmou bloqueio dos 14 endereços sensíveis testados, inclusive o arquivo de configuração local necessário ao PHP. Relatórios: `server-inventory-final.json` e `web-audit.json`. Os pacotes enviados pelo gerenciador foram extraídos sem permanecer na pasta pública.

## Verificação

O teste usa banco local isolado `gj_merge_tests` e Brave headless em perfil separado. Testou o HTML e o PHP em 390, 768 e 1440 px, PT/EN, claro/escuro: 24 combinações, sem transbordamento. As capturas foram feitas depois de carregar as imagens e percorrer a página para ativar as animações de entrada. Recursos oficiais de fontes e bandeiras foram usados do cache privado de teste; não há fontes ou imagens substitutas na aplicação.

Não houve erros de JavaScript, recursos ausentes ou imagens sem decodificação. Foram verificados links de seções, WhatsApp real, seis serviços, quatro projetos, controles de idioma/tema, menu móvel e teclado do comparador. Pelo próprio admin, títulos de seção, serviço e projeto foram alterados, conferidos na página PHP e restaurados.

`tests/site_merge.php` verificou a preservação de registros personalizados, inclusive diferenças somente de maiúsculas, ordem/visibilidade e idempotência do SQL. `tests/site_content.php` confirmou escape HTML/JSON, fallback EN, imagens, inativos e fallback com banco/tabelas indisponíveis. Sintaxe PHP/JS validada.

## Alinhamento restante com Rafael

Na publicação, as cópias recebidas das dez fotos têm recompressão JPEG: mesmas dimensões, erro médio por canal abaixo de 2 em 255 e PSNR entre 39,19 e 46,33 dB. São as mesmas fotografias, com pequena diferença de compressão; os bytes originais permanecem nos arquivos locais e backups. O relatório registra os hashes originais e hospedados separadamente, sem declarar igualdade inexistente. Os cinco arquivos de código correspondem exatamente aos locais.

Fornecer a logo `logo-golden-jardim.png` ou o arquivo equivalente desejado. Enquanto isso, o símbolo temporário aprovado permanece. As futuras mudanças de conteúdo/layout devem partir deste `index.html` mesclado e também ser levadas ao template PHP publicado; para textos de Serviços/Projetos já cadastrados, usar o painel. Alterar apenas os valores padrão do HTML não substitui cadastros personalizados do banco.
