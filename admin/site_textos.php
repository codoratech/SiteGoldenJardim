<?php
require_once __DIR__.'/bootstrap.php';
require_login();
require_once __DIR__.'/../conexao/banco.php';
require_once __DIR__.'/site_sections_helpers.php';
$section_labels=['services'=>'Nossos Serviços','gallery'=>'Projetos assinados'];
$section_values=[]; $section_errors=[]; $section_error=''; $section_ready=false; $posted_key='';
try {
    $rows=ui_rows($con,'SELECT chave,selo_pt,selo_en,titulo_pt,titulo_en,subtitulo_pt,subtitulo_en FROM tb_site_secoes');
    foreach ($section_labels as $key=>$label) $section_values[$key]=site_section_defaults($key);
    foreach ($rows as $row) if (isset($section_labels[$row['chave']])) $section_values[$row['chave']]=$row;
    $section_ready=true;
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET')==='POST') {
        $posted_key=$_POST['secao'] ?? '';
        if (($_POST['acao'] ?? '')!=='salvar' || !isset($section_labels[$posted_key])) throw new AdminFormValidation('Seção inválida. Atualize a página e tente novamente.');
        [$values,$errors]=site_section_validate($_POST);
        $section_values[$posted_key]=$values; $section_errors[$posted_key]=$errors;
        if ($errors) throw new AdminFormValidation('Confira os campos destacados.');
        site_section_save($con,$posted_key,$values);
        $_SESSION['site_flash']=['route'=>'site_textos.php','text'=>'Textos de “'.$section_labels[$posted_key].'” salvos com sucesso.','section'=>$posted_key,'warning'=>false];
        header('Location: site_textos.php#'.$posted_key); exit;
    }
} catch (AdminFormValidation $error) { $section_error=$error->getMessage(); http_response_code(400); }
catch (Throwable $error) { error_log('Golden Jardim: textos das seções: '.$error->getMessage()); $section_error='Não foi possível carregar ou salvar os textos. Tente novamente. O site continua protegido pelo conteúdo padrão.'; http_response_code(500); }
$site_flash=$_SESSION['site_flash'] ?? null;
if (($site_flash['route'] ?? '')!=='site_textos.php') $site_flash=null;
else unset($_SESSION['site_flash']);
require __DIR__.'/header.php';
require __DIR__.'/partials/site_sections_form.php';
require __DIR__.'/footer.php';
