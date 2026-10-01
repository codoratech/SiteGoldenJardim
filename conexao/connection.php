<?php
function gj_open_connection() {
    $connection = null;
    try {
        mysqli_report(MYSQLI_REPORT_OFF);
        $local = __DIR__ . '/config.local.php';
        $config = is_file($local) ? require $local : [];
        $password = getenv('GJ_DB_PASSWORD');
        $connection = mysqli_init();
        mysqli_options($connection, MYSQLI_OPT_CONNECT_TIMEOUT, 3);
        $connected = @mysqli_real_connect($connection,
            getenv('GJ_DB_HOST') ?: ($config['host'] ?? 'localhost'),
            getenv('GJ_DB_USER') ?: ($config['user'] ?? 'root'),
            $password === false ? ($config['password'] ?? '') : $password,
            getenv('GJ_DB_NAME') ?: ($config['database'] ?? 'golden_jardim_db'),
            (int) (getenv('GJ_DB_PORT') ?: ($config['port'] ?? 3306)));
        if (!$connected || !mysqli_set_charset($connection, 'utf8mb4')) {
            throw new RuntimeException('Conexão indisponível.');
        }
        return $connection;
    } catch (Throwable $error) {
        if ($connection) { try { mysqli_close($connection); } catch (Throwable $ignored) {} }
        error_log('Golden Jardim: falha de conexão com o banco de dados.');
        return null;
    }
}
