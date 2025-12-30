<?php
namespace App\Core;

class Config 
{
    // Database Configuration
    public const DB_HOST = 'localhost';  // Ganti dari 127.0.0.1 ke localhost
    public const DB_NAME = 'gudangku';
    public const DB_USER = 'root';
    public const DB_PASS = 'admin';       // Password untuk root user
    public const DB_PORT = '3306';
    public const DB_CHARSET = 'utf8mb4';

    // Application Configuration
    public const APP_NAME = 'GudangKu';
    public const APP_VERSION = '1.0.0';
    
    // Session Configuration
    public const SESSION_LIFETIME = 7200; // 2 hours in seconds
    public const SESSION_NAME = 'gudangku_session';
    
    // Timezone
    public const TIMEZONE = 'Asia/Jakarta';
    
    // Environment
    public const ENV = 'development'; // 'development' or 'production'
    
    // Error Reporting
    public const DISPLAY_ERRORS = true;
    public const ERROR_REPORTING = E_ALL;

    /**
     * Initialize application configuration
     */
    public static function init(): void
    {
        // Set timezone
        date_default_timezone_set(self::TIMEZONE);
        
        // Set error reporting based on environment
        if (self::ENV === 'development') {
            ini_set('display_errors', self::DISPLAY_ERRORS ? '1' : '0');
            error_reporting(self::ERROR_REPORTING);
        } else {
            ini_set('display_errors', '0');
            error_reporting(0);
        }
        
        // Configure session
        ini_set('session.cookie_lifetime', (string)self::SESSION_LIFETIME);
        ini_set('session.gc_maxlifetime', (string)self::SESSION_LIFETIME);
        
        // Set session name
        if (session_status() === PHP_SESSION_NONE) {
            session_name(self::SESSION_NAME);
        }
    }

    /**
     * Get database DSN string
     */
    public static function getDSN(): string
    {
        return sprintf(
            "mysql:host=%s;port=%s;dbname=%s;charset=%s",
            self::DB_HOST,
            self::DB_PORT,
            self::DB_NAME,
            self::DB_CHARSET
        );
    }

    /**
     * Check if in development mode
     */
    public static function isDevelopment(): bool
    {
        return self::ENV === 'development';
    }

    /**
     * Check if in production mode
     */
    public static function isProduction(): bool
    {
        return self::ENV === 'production';
    }
}
?>