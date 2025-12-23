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

            $stmt = $db->prepare("SELECT * FROM admin WHERE username = 'admin' LIMIT 1");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                // Buat akun admin default
                $hashedPassword = password_hash('admin', PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO admin (username, password_hash, created_at)
                                      VALUES ('admin', ?, NOW())");
                $stmt->execute([$hashedPassword]);
            }
        } catch (\PDOException $e) {
            // Log error atau handle sesuai kebutuhan
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

                $_SESSION['success'] = 'Welcome back, ' . htmlspecialchars($username) . '!';
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