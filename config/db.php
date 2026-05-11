<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'WJ28@krhps');
define('DB_NAME', 'bloodbank');
define('DB_PORT', 3306);

class Database {
    private static ?PDO $instance = null;
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME.";charset=utf8mb4";
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
                // Disable strict GROUP BY mode for this session
                self::$instance->exec("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
            } catch (PDOException $e) {
                die('<div style="font-family:sans-serif;padding:40px;background:#0a0c10;color:#ff6b6b;"><h2>Database Connection Failed</h2><p>'.$e->getMessage().'</p><p style="color:#8a9bb5">Check your password in config/db.php</p></div>');
            }
        }
        return self::$instance;
    }
}
?>
