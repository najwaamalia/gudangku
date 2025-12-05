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
}
