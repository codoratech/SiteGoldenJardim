<?php
// Dedicated local databases; never loads the hosted connection configuration.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$con = new mysqli('127.0.0.1','root','','',3310);
$con->set_charset('utf8mb4');
function migration_run($con) {
    $con->multi_query(file_get_contents(__DIR__.'/../migrations/006_permissoes_conteudo_publico.sql'));
    do {
        if ($result=$con->store_result()) $result->free();
        if (!$con->more_results()) break;
        $con->next_result();
    } while (true);
}
function migration_check($condition,$message) { if (!$condition) throw new RuntimeException($message); }
foreach (['missing','enum','varchar'] as $variant) {
    $database='gj_permissions_migration_'.$variant;
    $con->query('CREATE DATABASE IF NOT EXISTS '.$database.' CHARACTER SET utf8mb4');
    $con->select_db($database);
    $profile=$variant==='missing' ? '' : ($variant==='enum' ? ", log_perfil ENUM('Administrador','Consulta','Operador') NOT NULL DEFAULT 'Administrador'" : ", log_perfil VARCHAR(80) NOT NULL DEFAULT 'Operacional', log_ativo TINYINT(1) NOT NULL DEFAULT 1");
    $con->query('CREATE TABLE IF NOT EXISTS tb_login (log_codigo INT AUTO_INCREMENT PRIMARY KEY,log_nome VARCHAR(120),log_login VARCHAR(50) UNIQUE,log_senha VARCHAR(255)'.$profile.') ENGINE=InnoDB');
    $con->query("DELETE FROM tb_login WHERE log_login IN ('Adm','migration_operador','migration_consulta')");
    $hash=password_hash('LocalMigration-2026!',PASSWORD_DEFAULT);
    $stmt=$con->prepare('INSERT INTO tb_login(log_nome,log_login,log_senha) VALUES (?,?,?)');
    foreach (['Adm','migration_operador','migration_consulta'] as $login) { $name='Teste '.$login; $stmt->bind_param('sss',$name,$login,$hash); $stmt->execute(); }
    $hasProfile=$con->query("SHOW COLUMNS FROM tb_login LIKE 'log_perfil'")->num_rows > 0;
    if ($hasProfile) {
        $con->query("UPDATE tb_login SET log_perfil='Consulta' WHERE log_login IN ('Adm','migration_consulta')");
        if ($variant==='enum') $con->query("UPDATE tb_login SET log_perfil='Operador' WHERE log_login='migration_operador'");
    }
    if ($variant==='varchar') $con->query("UPDATE tb_login SET log_ativo=0 WHERE log_login='Adm'");
    migration_run($con);
    $rows=$con->query('SELECT * FROM tb_login ORDER BY log_codigo')->fetch_all(MYSQLI_ASSOC);
    foreach ($rows as $row) {
        migration_check($row['log_senha']===$hash,'A migração alterou uma senha.');
        migration_check($row['log_nome']==='Teste '.$row['log_login'],'A migração alterou um nome.');
        if ($row['log_login']==='Adm') migration_check($row['log_perfil']==='Administrador' && (int)$row['log_ativo']===1,'Adm perdeu acesso.');
        if ($row['log_login']==='migration_operador') migration_check($row['log_perfil']==='Operacional','Operador não foi convertido.');
        if ($row['log_login']==='migration_consulta' && $hasProfile) migration_check($row['log_perfil']==='Consulta','Consulta perdeu o perfil.');
    }
    $con->query("UPDATE tb_login SET log_perfil='Editor do site' WHERE log_login='migration_operador'");
    $snapshot=$con->query('SELECT * FROM tb_login ORDER BY log_codigo')->fetch_all(MYSQLI_ASSOC);
    migration_run($con); migration_run($con);
    migration_check($con->query('SELECT * FROM tb_login ORDER BY log_codigo')->fetch_all(MYSQLI_ASSOC)===$snapshot,'A repetição não foi idempotente.');
}
echo "Migração aprovada: campo ausente, ENUM legado e VARCHAR; repetição sem perda de dados, senhas ou perfis.\n";
