<?php
namespace App\Models;
use App\Core\Database;
use PDO;

class Admin {
  public static function verify(string $username, string $password): bool {
    $stmt = Database::conn()->prepare("SELECT password_hash FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    return $row && password_verify($password, $row['password_hash']);
  }

  public static function findByUsername(string $username): array|null {
    $stmt = Database::conn()->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result !== false ? $result : null;
  }

  public static function exists(string $username): bool {
    $stmt = Database::conn()->prepare("SELECT id FROM admin WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    return $stmt->fetch() !== false;
  }

  public static function create(string $username, string $passwordHash): bool {
    $stmt = Database::conn()->prepare("INSERT INTO admin (username, password_hash, created_at) VALUES (?, ?, NOW())");
    return $stmt->execute([$username, $passwordHash]);
  }
}
