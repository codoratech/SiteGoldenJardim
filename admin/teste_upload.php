<?php
require_once __DIR__.'/bootstrap.php';
require_login();
exigirPermissao('conteudo_publico');
header('Content-Type: text/plain; charset=utf-8');
$dir = __DIR__ . '/uploads/site';
if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
echo "Pasta existe: " . (is_dir($dir) ? 'sim' : 'não') . "\n";
echo "Pasta gravável: " . (is_writable($dir) ? 'sim' : 'não') . "\n";
$ok = @file_put_contents($dir . '/_teste.txt', 'ok');
echo "Consegue gravar arquivo: " . ($ok !== false ? 'sim' : 'não') . "\n";
@unlink($dir . '/_teste.txt');
echo "GD (redimensionar imagem): " . (extension_loaded('gd') ? 'sim' : 'não') . "\n";
echo "fileinfo (validar tipo): " . (extension_loaded('fileinfo') ? 'sim' : 'não') . "\n";
echo "file_uploads: " . ini_get('file_uploads') . "\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "PHP: " . PHP_VERSION . "\n";
