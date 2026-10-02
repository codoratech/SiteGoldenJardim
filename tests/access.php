<?php
require __DIR__.'/../admin/access.php';
class AdminFormValidation extends RuntimeException {}
require __DIR__.'/../admin/login_access_helpers.php';
function access_check($condition, $message) { if (!$condition) throw new RuntimeException($message); }
$checks = 0;
foreach (admin_roles() as $role) {
    foreach (admin_management_routes() as $route) {
        foreach (['GET','HEAD','POST','PUT'] as $method) {
            foreach (['','editar','excluir'] as $action) {
                $expected = $role === 'Administrador' || ($role === 'Operacional' && $method !== 'PUT') || (in_array($role,['Consulta','Editor do site'],true) && in_array($method,['GET','HEAD'],true) && $action === '');
                access_check(admin_route_allowed($role,$route,$method,$action) === $expected, "$role / $route / $method / $action"); $checks++;
            }
        }
    }
    foreach (['logins.php','cadastrar_login.php','site_servicos.php','site_projetos.php','site_textos.php','teste_upload.php','header.php'] as $route) {
        foreach (['GET','POST'] as $method) {
            access_check(admin_route_allowed($role,$route,$method) === ($role === 'Administrador' || ($role === 'Editor do site' && admin_is_site_route($route))), "$role / $route"); $checks++;
        }
    }
    access_check(admin_route_allowed($role,'perfil.php','POST'), 'Troca da própria senha deve permanecer disponível.');
    access_check(admin_route_allowed($role,'logout.php','POST'), 'Logout deve permanecer disponível.');
}
access_check(!admin_route_allowed('invalido','dashboard.php'), 'Perfil inválido deve ser bloqueado.');
$records = [['log_codigo'=>1,'log_login'=>'Adm','log_perfil'=>'Administrador'],['log_codigo'=>2,'log_login'=>'gestor','log_perfil'=>'Administrador'],['log_codigo'=>3,'log_login'=>'consulta','log_perfil'=>'Consulta']];
admin_validate_login_change($records,3,1,false,'consulta','Operacional');
admin_validate_login_change($records,2,1,false,'gestor','Consulta');
foreach ([[1,2,true,'',''],[1,2,false,'Adm','Consulta'],[2,2,false,'gestor','Consulta'],[2,2,true,'',''],[3,1,false,'consulta','invalido'],[3,1,false,'Adm','Operacional']] as $args) {
    $rejected=false; try { admin_validate_login_change($records,...$args); } catch (AdminFormValidation $error) { $rejected=true; }
    access_check($rejected,'Alteração insegura de acesso aceita.'); $checks++;
}
$rejected=false; try { admin_validate_login_change([['log_codigo'=>2,'log_login'=>'gestor','log_perfil'=>'Administrador']],2,1,false,'gestor','Consulta'); } catch (AdminFormValidation $error) { $rejected=true; }
access_check($rejected,'Último administrador foi rebaixado.');
$rejected=false; try { admin_validate_login_change([['log_codigo'=>2,'log_login'=>'gestor','log_perfil'=>'Administrador','log_ativo'=>1],['log_codigo'=>4,'log_login'=>'inativo','log_perfil'=>'Administrador','log_ativo'=>0]],2,1,true); } catch (AdminFormValidation $error) { $rejected=true; }
access_check($rejected,'Administrador inativo foi contado para permitir excluir o último ativo.');
$GLOBALS['admin_identity']=['log_perfil'=>'Editor do site','log_ativo'=>1,'permissoes'=>admin_profile_permissions('Editor do site')];
access_check(usuarioTemPermissao('conteudo_publico') && !admin_can_write() && !admin_is_administrator(),'Permissões do editor incorretas.');
$_SESSION['permissoes']=['*'];
$GLOBALS['admin_identity']=['log_perfil'=>'Operacional','log_ativo'=>1,'permissoes'=>admin_profile_permissions('Operacional')];
access_check(!usuarioTemPermissao('conteudo_publico') && admin_can_write(),'Permissões adulteradas na sessão foram aceitas.');
$GLOBALS['admin_identity']['log_ativo']=0;
access_check(!usuarioTemPermissao('gestao_editar'),'Usuário inativo recebeu permissão.');
echo "$checks verificações de permissões aprovadas.\n";
