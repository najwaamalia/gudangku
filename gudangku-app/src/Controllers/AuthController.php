<?php
namespace App\Controllers;

use App\Core\Database;

class AuthController
{
    public function __construct()
    {
        // Pastikan session aktif
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Pastikan akun admin ada di database
        $this->validate();
    }

    private function validate()
    {
        $db = Database::conn();

        $stmt = $db->prepare("SELECT * FROM admin WHERE username = 'admin' LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch();

        if (!$result) {
            $hashedPassword = password_hash('admin', PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO admin (username, password_hash, created_at)
                                  VALUES ('admin', ?, NOW())");
            $stmt->execute([$hashedPassword]);
        }
    }

    public function showLogin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $u = $_POST['username'] ?? '';
        $p = $_POST['password'] ?? '';

        $db = Database::conn();
        $stmt = $db->prepare("SELECT * FROM admin WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $u]);
        $user = $stmt->fetch();

        if ($user && password_verify($p, $user['password_hash'])) {
            $_SESSION['auth'] = true;
            header('Location: /?r=category');
            exit;
        }

        $_SESSION['error'] = 'Incorrect Username or Password!';
        header('Location: /?r=login');
        exit;
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();
        header('Location: /?r=login');
        exit;
    }
}
