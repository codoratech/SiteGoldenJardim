<?php
require_once __DIR__.'/permissions.php';
function admin_roles() { return array_keys(admin_profile_definitions()); }
function admin_role() { return $GLOBALS['admin_identity']['log_perfil'] ?? ''; }
function admin_is_administrator() { return admin_role() === 'Administrador' && !empty($GLOBALS['admin_identity']['log_ativo']); }
function usuarioTemPermissao($permissao) {
    $identity=$GLOBALS['admin_identity'] ?? null;
    if (!$identity || empty($identity['log_ativo'])) return false;
    $permissions=$identity['permissoes'] ?? [];
    return in_array('*',$permissions,true) || in_array($permissao,$permissions,true);
}
function admin_can_write() { return usuarioTemPermissao('gestao_editar'); }
function admin_management_routes() {
    return ['clientes.php','produtos.php','servicos.php','fornecedores.php','ordens.php','agendamentos.php','estoque.php','financeiro.php'];
}
function admin_site_pages() { return ['site_servicos.php','site_projetos.php','site_textos.php']; }
function admin_is_site_route($route) { return strpos($route,'site_') === 0 || $route === 'teste_upload.php'; }
function admin_route_allowed($role,$route,$method='GET',$action='') {
    $permissions=admin_profile_permissions($role);
    if (!$permissions) return false;
    if ($role === 'Administrador') return true;
    if (admin_is_site_route($route)) return in_array('conteudo_publico',$permissions,true) && in_array($method,['GET','HEAD','POST'],true);
    if (in_array($route,['perfil.php','logout.php'],true)) return in_array($method,['GET','HEAD','POST'],true);
    if (!in_array('painel_visualizar',$permissions,true)) return false;
    if ($route === 'dashboard.php') return in_array($method,['GET','HEAD'],true);
    $lists=admin_management_routes();
    $creates=['cadastrar_cliente.php','cadastrar_produto.php','cadastrar_servico.php','cadastrar_fornecedor.php','cadastrar_ordem.php','cadastrar_agendamento.php','cadastrar_estoque.php','cadastrar_conta.php'];
    if (in_array('gestao_editar',$permissions,true)) return in_array($route,array_merge($lists,$creates),true) && in_array($method,['GET','HEAD','POST'],true);
    return in_array($route,$lists,true) && in_array($method,['GET','HEAD'],true) && $action === '';
}
function admin_load_identity($con,$id) {
    $stmt=mysqli_prepare($con,'SELECT log_codigo,log_nome,log_login,log_perfil,log_ativo FROM tb_login WHERE log_codigo = ?');
    if (!$stmt) reject_request('Não foi possível verificar seu acesso. Confira a migração de perfis.',503);
    mysqli_stmt_bind_param($stmt,'i',$id);
    if (!mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); reject_request('Não foi possível verificar seu acesso.',503); }
    $identity=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
    if (!$identity || (int)$identity['log_ativo'] !== 1 || !in_array($identity['log_perfil'],admin_roles(),true)) return null;
    $identity['permissoes']=admin_profile_permissions($identity['log_perfil']);
    return $identity;
}
function admin_store_identity($identity) {
    $GLOBALS['admin_identity']=$identity;
    foreach (['log_codigo','log_nome','log_login','log_perfil','log_ativo'] as $key) $_SESSION[$key]=$identity[$key];
    $_SESSION['permissoes']=$identity['permissoes'];
}
function admin_require_identity() {
    if (isset($GLOBALS['admin_identity'])) return;
    if (empty($_SESSION['log_codigo'])) { header('Location: index.php'); exit; }
    require_once __DIR__.'/../conexao/connection.php';
    $access_con=gj_open_connection();
    if (!$access_con) reject_request('Não foi possível verificar seu acesso. Tente novamente.',503);
    $identity=admin_load_identity($access_con,(int)$_SESSION['log_codigo']); mysqli_close($access_con);
    if (!$identity) {
        unset($_SESSION['log_codigo'],$_SESSION['log_perfil'],$_SESSION['permissoes']);
        reject_request('Seu acesso não está mais disponível.',403);
    }
    // Refresh permissions from the database on every request, including open sessions.
    admin_store_identity($identity);
}
function exigirPermissao($permissao) {
    admin_require_identity();
    if (usuarioTemPermissao($permissao)) return;
    $message='Você não tem permissão para acessar esta área';
    $route=basename($_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '');
    if (in_array($route,admin_site_pages(),true) && in_array($_SERVER['REQUEST_METHOD'] ?? 'GET',['GET','HEAD'],true)) {
        $_SESSION['admin_permission_error']=$message;
        header('Location: dashboard.php'); exit;
    }
    reject_request($message,403);
}
function admin_require_access() {
    admin_require_identity();
    $route=basename($_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '');
    if (admin_is_site_route($route)) exigirPermissao('conteudo_publico');
    $action=$_GET['acao'] ?? '';
    if (!is_string($action) || !admin_route_allowed(admin_role(),$route,$_SERVER['REQUEST_METHOD'] ?? 'GET',$action)) reject_request('Seu perfil não tem permissão para acessar esta função.',403);
    if (in_array($route,['logins.php','cadastrar_login.php'],true) && !admin_is_administrator()) reject_request('Apenas Administradores podem gerenciar os perfis dos logins.',403);
}
