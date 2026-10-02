<?php
require_once __DIR__.'/bootstrap.php';
require_login();
exigirPermissao('conteudo_publico');
$site_kind='services';
require __DIR__.'/site_controller.php';
