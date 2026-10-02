<?php
// Add future roles/permissions here; the database stores only the profile name.
function admin_profile_definitions() {
    return [
        'Administrador' => ['descricao'=>'Acesso total ao painel, logins e conteúdo público.', 'permissoes'=>['*','painel_visualizar','gestao_editar','gerenciar_logins','conteudo_publico']],
        'Editor do site' => ['descricao'=>'Consulta o painel e edita os textos, serviços, projetos e imagens do site.', 'permissoes'=>['painel_visualizar','conteudo_publico']],
        'Operacional' => ['descricao'=>'Consulta, cadastra, altera e exclui os dados de gestão, sem acesso ao editor do site ou aos logins.', 'permissoes'=>['painel_visualizar','gestao_editar']],
        'Consulta' => ['descricao'=>'Somente leitura dos dados de gestão.', 'permissoes'=>['painel_visualizar']],
    ];
}
function admin_profile_permissions($profile) {
    return admin_profile_definitions()[$profile]['permissoes'] ?? [];
}
