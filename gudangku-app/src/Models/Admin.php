<?php
namespace App\Models;
use App\Core\Database;
use PDO;

class Admin {

  //verifikasi username dan password saat login
  public static function verify(string $username, string $password): bool {
    // Ambil hash password berdasarkan username dari database
    $stmt = Database::conn()->prepare("SELECT password_hash FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $row = $stmt->fetch();

    // bandingkan password yang diinput dengan hash dari database
    return $row && password_verify($password, $row['password_hash']);
  }

  // Mengambil data admin berdasarkan username
  // menyimpan data admin di session saat login
  public static function findByUsername(string $username): array|null {
    $stmt = Database::conn()->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result !== false ? $result : null;
  }

  // Memeriksa apakah admin dengan username tertentu sudah ada
  // dipakai saat inisialisasi akun admin di AuthController
  public static function exists(string $username): bool {
    $stmt = Database::conn()->prepare("SELECT id FROM admin WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    return $stmt->fetch() !== false;
  }


  // Membuat akun admin baru
  public static function create(string $username, string $passwordHash): bool {
    $stmt = Database::conn()->prepare("INSERT INTO admin (username, password_hash, created_at) VALUES (?, ?, NOW())");
    return $stmt->execute([$username, $passwordHash]);
  }
}
