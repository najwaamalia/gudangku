<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function conn(): PDO
    {
        // Cek apakah koneksi masih hidup
        if (self::$pdo !== null) {
            try {
                // Test koneksi dengan query sederhana
                self::$pdo->query('SELECT 1');
            } catch (PDOException $e) {
                // Jika koneksi mati, reset untuk reconnect
                self::$pdo = null;
            }
        }

        // Buat koneksi baru jika belum ada atau sudah mati
        if (self::$pdo === null) {
            try {
                $dsn = "mysql:host=" . Config::DB_HOST . ";dbname=" . Config::DB_NAME . ";charset=utf8mb4";
                self::$pdo = new PDO($dsn, Config::DB_USER, Config::DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::ATTR_PERSISTENT         => false, // Non-persistent untuk menghindari "gone away"
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                ]);

                // Set timeout
                self::$pdo->setAttribute(PDO::ATTR_TIMEOUT, 30);

            } catch (PDOException $e) {
                // Error handling yang lebih informatif
                $errorMsg = "<h3>Database Connection Failed</h3>";
                $errorMsg .= "<p><strong>Host:</strong> " . Config::DB_HOST . "</p>";
                $errorMsg .= "<p><strong>Database:</strong> " . Config::DB_NAME . "</p>";
                $errorMsg .= "<p><strong>User:</strong> " . Config::DB_USER . "</p>";
                $errorMsg .= "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
                $errorMsg .= "<p>Please check your database configuration in <code>App/Core/Config.php</code></p>";
                
                error_log("Database Connection Error: " . $e->getMessage());
                die($errorMsg);
            }
        }

        return self::$pdo;
    }

    /**
     * Menutup koneksi database (opsional)
     */
    public static function disconnect(): void
    {
        self::$pdo = null;
    }

    /**
     * Force reconnect - paksa buat koneksi baru
     */
    public static function reconnect(): PDO
    {
        self::$pdo = null;
        return self::conn();
    }
}
?>