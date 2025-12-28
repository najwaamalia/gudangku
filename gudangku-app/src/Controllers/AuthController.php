<?php
namespace App\Controllers;

use App\Core\Database;
use PDO;

class AuthController
{
    public function __construct()
    {
        // Pastikan session aktif
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Pastikan akun admin ada di database
        $this->ensureAdminExists();
    }

    private function ensureAdminExists()
    {
        try {
            $db = Database::conn();

            $admins = [
                ['admin', 'admin'],
                ['admin1', '123'],
                ['najwa', 'najwa123'],
                ['user', 'user123']
            ];

            foreach ($admins as [$username, $password]) {

                // Cek apakah username sudah ada
                $stmt = $db->prepare(
                    "SELECT id FROM admin WHERE username = ? LIMIT 1"
                );
                $stmt->execute([$username]);

                if (!$stmt->fetch()) {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    $insert = $db->prepare(
                        "INSERT INTO admin (username, password_hash, created_at)
                        VALUES (?, ?, NOW())"
                    );
                    $insert->execute([$username, $hashedPassword]);
                }
            }

        } catch (\PDOException $e) {
            error_log("Database error in ensureAdminExists: " . $e->getMessage());
        }
    }


    public function showLogin(): void
    {
        // Jika sudah login, redirect ke category
        if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
            header('Location: /?r=category');
            exit;
        }

        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(): void
    {
        // Validasi request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /?r=login');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Validasi input kosong
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Username and password are required!';
            header('Location: /?r=login');
            exit;
        }

        try {
            // Reconnect database untuk menghindari "server gone away"
            $db = Database::conn();
            
            $stmt = $db->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Login berhasil
                $_SESSION['auth'] = true;
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['login_time'] = time();

                // Regenerate session ID untuk keamanan
                session_regenerate_id(true);

                header('Location: /?r=category');
                exit;
            }

            // Login gagal
            $_SESSION['error'] = 'Incorrect username or password!';
            header('Location: /?r=login');
            exit;

        } catch (\PDOException $e) {
            error_log("Database error in login: " . $e->getMessage());
            $_SESSION['error'] = 'Database connection error. Please try again.';
            header('Location: /?r=login');
            exit;
        }
    }

    public function logout(): void
    {
        // Simpan username untuk pesan goodbye
        $username = $_SESSION['username'] ?? 'User';

        // Hapus semua session data
        $_SESSION = [];

        // Hapus session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destroy session
        session_destroy();

        // Start new session untuk pesan
        session_start();
        $_SESSION['success'] = 'Goodbye, ' . htmlspecialchars($username) . '! You have been logged out.';

        header('Location: /?r=login');
        exit;
    }
}
?>