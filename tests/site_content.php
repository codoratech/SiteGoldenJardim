<?php
require __DIR__.'/../includes/site_content.php';
require __DIR__.'/../includes/site_sections.php';
function site_check($condition, $message) { if (!$condition) throw new RuntimeException($message); }
function site_test_sql($connection, $sql) {
    if (!mysqli_multi_query($connection, $sql)) throw new RuntimeException(mysqli_error($connection));
    do {
        if ($result = mysqli_store_result($connection)) mysqli_free_result($result);
        if (!mysqli_more_results($connection)) break;
        if (!mysqli_next_result($connection)) throw new RuntimeException(mysqli_error($connection));
    } while (true);
}
// Dedicated local database; production configuration is never used here.
$con = mysqli_connect('127.0.0.1', 'root', '', '', 3308);
mysqli_set_charset($con, 'utf8mb4');
mysqli_query($con, 'CREATE DATABASE IF NOT EXISTS gj_site_tests CHARACTER SET utf8mb4');
mysqli_select_db($con, 'gj_site_tests');
$migration = file_get_contents(__DIR__.'/../migrations/003_site_conteudo.sql');
$rafael_migration = file_get_contents(__DIR__.'/../migrations/004_site_conteudo_rafael.sql');
site_test_sql($con, $migration);
foreach (['tb_site_secoes','tb_site_servicos','tb_site_projetos'] as $table) mysqli_query($con, 'DELETE FROM '.$table);
site_test_sql($con, $migration);
site_test_sql($con, $rafael_migration);
$defaults = site_content_load(false);
site_check(site_content_load($con) === $defaults, 'O seed sincronizado deve reproduzir o conteúdo do Rafael.');
mysqli_query($con, "UPDATE tb_site_servicos SET titulo_pt='Conteúdo salvo' WHERE ordem=1");
site_test_sql($con, $migration);
site_test_sql($con, $rafael_migration);
$data = site_content_load($con);
site_check(count($data['services']) === 6 && count($data['projects']) === 4 && $data['services'][0]['pt'][0] === 'Conteúdo salvo', 'Reimportação duplicou ou sobrescreveu conteúdo.');
mysqli_begin_transaction($con);
try {
    mysqli_query($con, "UPDATE tb_site_servicos SET titulo_pt='<script>alert(1)</script>',titulo_en='',icone='javascript:alert(1)',imagem_path='../conexao/config.local.php',ordem=0 WHERE ordem=1");
    mysqli_query($con, 'UPDATE tb_site_servicos SET ativo=0 WHERE ordem=2');
    mysqli_query($con, "UPDATE tb_site_projetos SET titulo_en='',descricao_pt='Teste de descrição',descricao_en='',imagem_alt_pt='',imagem_alt_en='' WHERE ordem=1");
    mysqli_query($con, "UPDATE tb_site_secoes SET titulo_pt='Título salvo',titulo_en='' WHERE chave='services'");
    $site_content = site_content_load($con);
    site_check(count($site_content['services']) === 5 && $site_content['services'][0]['img'] === null, 'Filtro, ordem ou caminho de imagem incorreto.');
    site_check($site_content['services'][0]['en'][0] === '<script>alert(1)</script>' && $site_content['sections']['services']['en']['title'] === 'Título salvo', 'Fallback EN incorreto.');
    site_check($site_content['projects'][0]['en']['description'] === 'Teste de descrição' && $site_content['projects'][0]['en']['alt'] === 'Jardim Frontal', 'Fallback do projeto incorreto.');
    ob_start(); site_render_services($site_content['services']); $html = ob_get_clean();
    site_check(str_contains($html, '01 / 05') && !str_contains($html, '<script>') && str_contains($html, '&lt;script&gt;'), 'HTML ou numeração inseguros.');
    ob_start(); require __DIR__.'/../templates/public-page.php'; $page = ob_get_clean();
    site_check(!str_contains($page, '<script>alert(1)</script>') && str_contains($page, '\\u003Cscript\\u003E'), 'Bootstrap JSON inseguro.');
    mysqli_query($con, 'UPDATE tb_site_servicos SET ativo=0');
    mysqli_query($con, 'UPDATE tb_site_projetos SET ativo=0');
    $data = site_content_load($con);
    site_check($data['services'] === [] && $data['projects'] === [], 'Itens inativos reapareceram.');
    mysqli_query($con, 'DELETE FROM tb_site_servicos'); mysqli_query($con, 'DELETE FROM tb_site_projetos');
    $data = site_content_load($con);
    site_check($data['services'] === $defaults['services'] && $data['projects'] === $defaults['projects'], 'Tabelas vazias perderam o fallback.');
} finally { mysqli_rollback($con); }
mysqli_query($con, "UPDATE tb_site_servicos SET titulo_pt='Paisagismo' WHERE ordem=1");
mysqli_select_db($con, 'gj_dashboard_tests');
site_check(site_content_load($con) === $defaults, 'Tabelas ausentes não usaram o fallback.');
mysqli_close($con);
$original = file_get_contents(__DIR__.'/../index.html');
$template = file_get_contents(__DIR__.'/../templates/public-page.php');
preg_match_all('~<section\b.*?</section>~s', $original, $sections);
foreach ($sections[0] as $section) {
    if (preg_match('/id="(?:services|gallery)"/', $section)) continue;
    site_check(str_contains($template, $section), 'Outra seção do site foi alterada.');
}
preg_match('~<style>(.*?)</style>~s', $original, $styles);
site_check(file_get_contents(__DIR__.'/../assets/css/public.css') === $styles[1], 'O CSS original mudou.');
echo "Site: seed idempotente, leitura real, PT/EN, ordenação, inativos, numeração, HTML/JSON seguro, imagens, tabelas vazias/ausentes e demais seções aprovados.\n";
