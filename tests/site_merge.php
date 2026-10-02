<?php
require __DIR__.'/../includes/site_content.php';
function merge_assert($value,$message) { if (!$value) throw new RuntimeException($message); }
function merge_sql($con,$sql) {
    if (!mysqli_multi_query($con,$sql)) throw new RuntimeException(mysqli_error($con));
    do { if ($result=mysqli_store_result($con)) mysqli_free_result($result); if (!mysqli_more_results($con)) break; if (!mysqli_next_result($con)) throw new RuntimeException(mysqli_error($con)); } while (true);
}
// A dedicated loopback-only test database; never the hosting connection.
$con=mysqli_connect('127.0.0.1','root','','',3308);mysqli_set_charset($con,'utf8mb4');
mysqli_query($con,'CREATE DATABASE IF NOT EXISTS gj_merge_tests CHARACTER SET utf8mb4');mysqli_select_db($con,'gj_merge_tests');
$source=mysqli_query($con,'SHOW TABLES FROM gj_codex_test');
foreach (mysqli_fetch_all($source,MYSQLI_NUM) as [$table]) {
    if (str_starts_with($table,'tb_site_')) continue;
    merge_assert((bool)preg_match('/\Atb_[a-z_]+\z/',$table),'Nome de tabela inesperado.');
    mysqli_query($con,"CREATE TABLE IF NOT EXISTS `$table` LIKE gj_codex_test.`$table`");
    if ((int)mysqli_fetch_row(mysqli_query($con,"SELECT COUNT(*) FROM `$table`"))[0]===0) mysqli_query($con,"INSERT INTO `$table` SELECT * FROM gj_codex_test.`$table`");
}
$original=file_get_contents(__DIR__.'/../migrations/003_site_conteudo.sql');$sync=file_get_contents(__DIR__.'/../migrations/004_site_conteudo_rafael.sql');
function merge_reset($con,$original) { foreach (['tb_site_secoes','tb_site_servicos','tb_site_projetos'] as $table) mysqli_query($con,'DROP TABLE IF EXISTS '.$table);merge_sql($con,$original); }
merge_reset($con,$original);
mysqli_query($con,"UPDATE tb_site_servicos SET descricao_pt='Texto personalizado' WHERE ordem=1");
mysqli_query($con,"UPDATE tb_site_projetos SET titulo_pt='Projeto personalizado' WHERE ordem=2");
mysqli_query($con,"UPDATE tb_site_projetos SET categoria_pt='PAISAGISMO' WHERE ordem=3");
mysqli_query($con,"UPDATE tb_site_secoes SET subtitulo_pt='Subtítulo personalizado' WHERE chave='services'");
mysqli_query($con,'UPDATE tb_site_projetos SET ordem=99,ativo=0 WHERE ordem=4');
merge_sql($con,$sync);
merge_assert(mysqli_fetch_row(mysqli_query($con,'SELECT imagem_path FROM tb_site_servicos WHERE ordem=1'))[0]==='assets/service-landscaping.jpg','Serviço personalizado foi sobrescrito.');
merge_assert(mysqli_fetch_row(mysqli_query($con,'SELECT titulo_pt FROM tb_site_projetos WHERE ordem=2'))[0]==='Projeto personalizado','Projeto personalizado foi sobrescrito.');
merge_assert(mysqli_fetch_row(mysqli_query($con,'SELECT titulo_pt FROM tb_site_projetos WHERE ordem=3'))[0]==='Caminho das Palmeiras','Comparação binária não preservou personalização.');
$row=mysqli_fetch_assoc(mysqli_query($con,'SELECT titulo_pt,ativo,ordem FROM tb_site_projetos WHERE ordem=99'));
merge_assert($row['titulo_pt']==='Entrada · Villagio Azul'&&(int)$row['ativo']===0&&(int)$row['ordem']===99,'Ordem/visibilidade não foram preservadas.');
merge_assert(str_replace("\r",'',mysqli_fetch_row(mysqli_query($con,"SELECT titulo_pt FROM tb_site_secoes WHERE chave='services'"))[0])==="NOSSOS\nSERVIÇOS",'Seção personalizada foi sobrescrita.');
$before=[];foreach (['tb_site_secoes','tb_site_servicos','tb_site_projetos'] as $table) $before[$table]=mysqli_fetch_all(mysqli_query($con,'SELECT * FROM '.$table),MYSQLI_ASSOC);
merge_sql($con,$sync);foreach ($before as $table=>$rows) merge_assert($rows===mysqli_fetch_all(mysqli_query($con,'SELECT * FROM '.$table),MYSQLI_ASSOC),'Sincronização não é idempotente.');
merge_reset($con,$original);merge_sql($con,$sync);
merge_assert(site_content_load($con)===site_content_load(false),'Conteúdo sincronizado difere do fallback do Rafael.');
foreach (site_defaults()['services'] as $item) merge_assert(is_file(__DIR__.'/../'.$item['img']),'Imagem de serviço ausente.');
foreach (site_defaults()['projects'] as $item) merge_assert(is_file(__DIR__.'/../'.$item['img']),'Imagem de projeto ausente.');
mysqli_close($con);
echo "Merge: sincronização idempotente, conteúdo Rafael, imagens, personalizações, ordem e visibilidade preservados. Banco isolado preparado.\n";
