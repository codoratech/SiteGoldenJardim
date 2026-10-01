# Como atualizar o site Golden Jardim

Este guia explica como mudar os textos, serviços e projetos que aparecem no site. Você pode fazer isso pelo navegador, sem precisar do XAMPP.

## 1. Entrar no painel

1. Acesse https://goldenjardim.site.je/admin/ e entre com seu login e senha atuais.
2. Abra **Site** no menu lateral.
3. Escolha **Textos das seções**, **Serviços do site** ou **Projetos**.

No celular, toque no botão de três linhas para abrir o menu. O botão de tema no topo alterna entre claro e escuro e mantém sua escolha ao voltar ao painel.

**Serviços do site** são os cards da página pública. A opção **Serviços**, na área Gerenciamento, cuida dos serviços usados nas ordens de serviço. São cadastros separados.

## 2. Mudar os textos das seções

Abra **Site → Textos das seções**. Há um quadro para **Nossos Serviços** e outro para **Projetos assinados**.

| Campo | Onde aparece |
| --- | --- |
| Selo | Texto pequeno acima do título. Deixe o português vazio para ocultá-lo; o inglês pode ter um selo próprio. |
| Título | Texto principal da seção, antes dos cards. O título em português é obrigatório. |
| Subtítulo | Frase de apresentação ao lado ou abaixo do título. |

1. Preencha os campos em português e, se desejar, em inglês.
2. Se um campo em inglês ficar vazio, o site usará o texto do campo correspondente em português.
3. Clique em **Salvar textos de Nossos Serviços** ou **Salvar textos de Projetos assinados**. Cada quadro é salvo separadamente; salvar um não salva o outro.
4. Aguarde o aviso de sucesso e clique em **Ver no site** para conferir.

Os contadores indicam quanto ainda cabe em cada campo. Os limites são 80 caracteres para o selo, 160 para o título e 500 para o subtítulo. Os campos aceitam texto; códigos HTML não são interpretados.

**Descartar alterações** recarrega o conteúdo salvo e perde as mudanças ainda não salvas nos dois quadros.

## 3. Cadastrar ou editar um serviço

1. Abra **Site → Serviços do site** e clique em **Novo serviço**.
2. Preencha o título e a descrição em português. Acrescente os textos em inglês se quiser uma versão diferente.
3. Escolha o ícone e, se desejar, envie uma imagem.
4. Defina a ordem e mantenha **Ativo** marcado para o card aparecer no site.
5. Clique em **Salvar serviço** e confira o aviso de sucesso.

Para mudar um card existente, use **Editar** na linha desse serviço. Salvar sem escolher uma nova imagem mantém a imagem atual.

## 4. Cadastrar ou editar um projeto

1. Abra **Site → Projetos** e clique em **Novo projeto**.
2. Preencha título, descrição e categoria, em português e opcionalmente em inglês.
3. Informe a localização, se for útil ao visitante.
4. Escolha a imagem e escreva o **texto alternativo**: uma descrição curta do que a foto mostra, como “Jardim com palmeiras e caminho de pedras”. Esse texto ajuda quem usa leitor de tela.
5. Defina ordem e visibilidade, clique em **Salvar projeto** e confira o resultado.

Para editar, use **Editar** na linha do projeto. Campos em inglês vazios usam os respectivos textos em português.

## 5. Escolher ou trocar imagens

- Formatos aceitos: **JPG, PNG e WebP**, com até **5 MB por arquivo**.
- O sistema ajusta automaticamente a imagem para até 1600 pixels no maior lado, mantendo a proporção.
- Prefira uma foto nítida, com o assunto centralizado: a miniatura do card pode recortar as bordas.
- Para trocar uma foto, abra **Editar**, escolha a nova imagem e salve. A foto enviada anteriormente é removida quando não está sendo usada por outro card. Imagens originais do site podem ser compartilhadas e são preservadas.
- Se houver erro no envio, confira formato e tamanho. Depois de corrigir, talvez seja necessário selecionar o arquivo novamente.

## 6. Mudar a ordem, ocultar ou excluir

Nas listas de Serviços e Projetos:

- Use as setas **Mover para cima** e **Mover para baixo** para mudar a posição do card. O resultado já é salvo ao clicar.
- Use o controle **Ativo/Inativo** para ocultar um card sem perder seus textos ou imagem. Clique novamente para reativá-lo.
- Use **Excluir** para remover um cadastro. O painel abre uma confirmação com o nome do item; confira antes de clicar em **Sim, excluir**. Use **Cancelar** ou Escape para desistir. A exclusão também remove a imagem enviada quando ela não é compartilhada. Não há botão de desfazer: para retirar algo temporariamente, prefira desativar.

No celular, deslize a tabela para os lados dentro do quadro para acessar as ações. A página inteira não precisa rolar lateralmente.

## 7. Conferir a publicação em português e inglês

1. Depois do aviso de sucesso, clique em **Ver no site** ou abra https://goldenjardim.site.je/.
2. Atualize a página pública caso ela já estivesse aberta.
3. Confira a seção modificada e alterne entre **PT** e **EN**.

Alterações feitas nessas telas do painel já são gravadas na hospedagem. Não é necessário reenviar os arquivos do site para mudar esse conteúdo. Novas páginas ou mudanças no código HTML/PHP continuam precisando de publicação dos arquivos.

## Se algo não funcionar

- **Campo destacado em vermelho:** leia a mensagem, corrija o campo e salve novamente. O painel mantém os textos digitados.
- **Nenhum aviso de sucesso:** confira os erros antes de considerar a mudança salva.
- **Alteração não aparece:** atualize o site, confira se o card está ativo e veja se está no idioma esperado.
- **Sessão encerrada:** entre novamente no painel.
- **Falha temporária do banco:** tente novamente mais tarde. O site público usa o conteúdo padrão para continuar apresentando as seções; esse fallback não apaga os dados salvos.

Use **Sair do Sistema** ao terminar em um computador compartilhado.
