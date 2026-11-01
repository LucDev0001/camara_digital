<?php
/**
 * Câmara Digital - Conexão com o Banco de Dados MySQL
 */

class Database {
    private static ?PDO $instance = null;

    private const DB_HOST = '127.0.0.1';
    private const DB_PORT = '3306';
    private const DB_NAME = 'camara_digital_db';
    private const DB_USER = 'root';
    private const DB_PASS = '';

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . self::DB_HOST . ";port=" . self::DB_PORT . ";dbname=" . self::DB_NAME . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ];
                self::$instance = new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
            } catch (PDOException $e) {
                die("<h3>Erro de Conexão com o Banco de Dados</h3><p>" . htmlspecialchars($e->getMessage()) . "</p><p>Certifique-se de que o MySQL no XAMPP está ativo e o banco <code>camara_digital_db</code> foi criado.</p>");
            }
        }
        return self::$instance;
    }
}
