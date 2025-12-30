<?php
namespace App\Controllers;

use App\Core\Database;
use App\Models\Admin;
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

        // Cek session timeout berdasarkan waktu idle (15 menit = 900 detik)
        if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
            if (isset($_SESSION['last_activity'])) {
                // Hitung waktu tidak aktif
                $inactive_time = time() - $_SESSION['last_activity'];

                // Jika lebih dari 15 menit tidak aktif
                if ($inactive_time > 900) {
                    // Lakukan logout otomatis karena timeout
                    $_SESSION = [];
                    session_destroy();
                    session_start();
                    $_SESSION['error'] = 'Your session has expired due to inactivity. Please login again.';
                }
            }

            // Update waktu aktivitas terakhir
            $_SESSION['last_activity'] = time();
        }
    }

    // memastikan akun admin sudah ada di database
    // jika belum ada, akun akan dibuat otomatis
    private function ensureAdminExists()
    {
        try {
            // Daftar akun admin default
            $admins = [
                ['admin', 'admin'],
                ['admin1', '123'],
                ['najwa', 'najwa123'],
                ['user', 'user123'],
                ['user@gmail.com', 'ahay']
            ];

            // loop setiap akun admin
            foreach ($admins as [$username, $password]) {
                // Cek apakah akun sudah ada
                if (!Admin::exists($username)) {
                    //password di-hash sebelum disimpan
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    // Buat akun admin baru
                    Admin::create($username, $hashedPassword);
                }
            }

        } catch (\PDOException $e) {
            // Log error jika terjadi masalah database
            error_log("Database error in ensureAdminExists: " . $e->getMessage());
        }
    }


    // Menampilkan halaman login
    public function showLogin(): void
    {
        // Jika sudah login, redirect ke category
        if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
            header('Location: /?r=category');
            exit;
        }

        // Tampilkan halaman login
        require __DIR__ . '/../Views/auth/login.php';
    }

    // Memproses login dari form
    public function login(): void
    {
        // Validasi request method dari form POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /?r=login');
            exit;
        }

        // Ambil data dari form
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Validasi input kosong
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Username and password are required!';
            header('Location: /?r=login');
            exit;
        }

        try {
            // Cari user di database
            $user = Admin::findByUsername($username);

            //jika user tidak ditemukan
            if (!$user) {
                $_SESSION['error'] = 'Username not found in database!';
                header('Location: /?r=login');
                exit;
            }

            // Verifikasi password
            if (!password_verify($password, $user['password_hash'])) {
                $_SESSION['error'] = 'Incorrect password!';
                header('Location: /?r=login');
                exit;
            }

            // jika benar usn dan pass, Login berhasil
            $_SESSION['auth'] = true;

            // Simpan data user di session
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login_time'] = time();
            $_SESSION['last_activity'] = time(); // Set waktu aktivitas terakhir saat login

            // Regenerate session ID untuk keamanan
            session_regenerate_id(true);

            // Redirect ke halaman kategori
            header('Location: /?r=category');
            exit;

        } catch (\PDOException $e) {
            // Log error jika terjadi masalah database
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
        
        header('Location: /?r=login');
        exit;
    }
}
?>