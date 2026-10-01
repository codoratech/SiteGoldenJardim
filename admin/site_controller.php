<?php
if (!isset($site_kind)) { http_response_code(404); exit; }
require_once __DIR__.'/bootstrap.php';
require_login();
require_once __DIR__.'/../conexao/banco.php';
require_once __DIR__.'/ui_helpers.php';
require_once __DIR__.'/site_helpers.php';
$site_config=site_admin_config($site_kind);
$site_record=null; $site_errors=[]; $site_error=''; $site_rows=[];
$site_action=is_string($_GET['acao'] ?? '') ? ($_GET['acao'] ?? '') : '';
$site_form=in_array($site_action,['novo','editar'],true);
$site_id=0;
try {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET')==='POST') {
        $action=$_POST['acao'] ?? '';
        if (!in_array($action,['salvar','excluir','alternar','subir','descer'],true)) throw new AdminFormValidation('Ação inválida.');
        $site_id=site_admin_id($_POST['registro'] ?? '',$action==='salvar');
        if ($action==='salvar') {
            $site_form=true;
            [$data,$site_errors]=site_admin_validate($site_config,$_POST);
            $image=null;
            try { $image=site_upload_inspect($_FILES['imagem'] ?? null); }
            catch (SiteImageValidation $error) { $site_errors['imagem']=$error->getMessage(); }
            if ($site_errors) throw new AdminFormValidation('Confira os campos destacados. Se houver imagem, selecione-a novamente antes de enviar.');
        } else { $data=[]; $image=null; }
        $warning=site_admin_mutate($con,$site_config,$action,$site_id,$data,$image);
        $messages=['salvar'=>ucfirst($site_config['singular']).' salvo com sucesso.','excluir'=>ucfirst($site_config['singular']).' excluído com sucesso.','alternar'=>'Visibilidade atualizada com sucesso.','subir'=>'Ordem atualizada com sucesso.','descer'=>'Ordem atualizada com sucesso.'];
        $_SESSION['site_flash']=['route'=>$site_config['route'],'text'=>$warning ?: $messages[$action],'warning'=>(bool)$warning];
        header('Location: '.$site_config['route']); exit;
    }
    if ($site_action==='editar') $site_id=site_admin_id($_GET['id'] ?? '');
} catch (SiteImageValidation $error) { $site_errors['imagem']=$error->getMessage(); $site_error='Confira a imagem selecionada.'; http_response_code(400); }
catch (AdminFormValidation $error) { $site_error=$error->getMessage(); http_response_code(400); }
catch (Throwable $error) { error_log('Golden Jardim: módulo Site: '.$error->getMessage()); $site_error='Não foi possível salvar a alteração. Tente novamente.'; http_response_code(500); }
try {
    $site_rows=ui_rows($con,'SELECT * FROM '.$site_config['table'].' ORDER BY ordem,id');
    foreach ($site_rows as $row) if ((int)$row['id']===$site_id) $site_record=$row;
    if ($site_action==='editar' && !$site_record && !$site_error) { $site_form=false; $site_error='Registro não encontrado. Escolha um item da lista.'; http_response_code(404); }
} catch (Throwable $error) { error_log('Golden Jardim: lista Site: '.$error->getMessage()); $site_error='Não foi possível carregar o conteúdo. Tente novamente.'; http_response_code(500); }
$site_values=$site_record ?? ['icone'=>'sprout','ordem'=>count($site_rows)+1,'ativo'=>1];
if (($_SERVER['REQUEST_METHOD'] ?? 'GET')==='POST' && ($_POST['acao'] ?? '')==='salvar') $site_values=array_merge($site_values,$_POST);
$site_flash=$_SESSION['site_flash'] ?? null;
if (($site_flash['route'] ?? '')!==$site_config['route']) $site_flash=null;
else unset($_SESSION['site_flash']);
require __DIR__.'/header.php';
require __DIR__.'/partials/site_page.php';
require __DIR__.'/footer.php';
