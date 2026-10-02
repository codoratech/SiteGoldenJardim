<?php
// Called with all login records locked to protect the final administrator.
function admin_validate_login_change($records, $target_id, $actor_id, $deleting, $login = '', $role = '') {
    $target = null; $administrators = 0;
    foreach ($records as $record) {
        if ((int)$record['log_codigo'] === $target_id) $target = $record;
        if ($record['log_perfil'] === 'Administrador' && (int)($record['log_ativo'] ?? 1) === 1) $administrators++;
    }
    if (!$target) throw new AdminFormValidation('Registro não encontrado.');
    $protected = strcasecmp(trim($target['log_login']), 'Adm') === 0;
    if ($deleting) {
        if ($protected) throw new AdminFormValidation('O login Adm é protegido e não pode ser excluído.');
        if ($target_id === $actor_id) throw new AdminFormValidation('Não é possível excluir seu próprio acesso enquanto estiver conectado.');
    } else {
        if (!in_array($role, admin_roles(), true)) throw new AdminFormValidation('Selecione um perfil de usuário válido.');
        if ($protected && ($login !== $target['log_login'] || $role !== 'Administrador')) throw new AdminFormValidation('O login Adm é protegido e deve permanecer Administrador.');
        if (strcasecmp(trim($login), 'Adm') === 0 && $role !== 'Administrador') throw new AdminFormValidation('O login Adm deve permanecer Administrador.');
        if ($target_id === $actor_id && $role !== 'Administrador') throw new AdminFormValidation('Você não pode remover seu próprio perfil de Administrador.');
    }
    if ($target['log_perfil'] === 'Administrador' && (int)($target['log_ativo'] ?? 1) === 1 && ($deleting || $role !== 'Administrador') && $administrators <= 1) throw new AdminFormValidation('O sistema precisa manter pelo menos um Administrador ativo.');
}
