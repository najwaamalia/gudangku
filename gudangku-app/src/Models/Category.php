<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Category
{
    // Method untuk mengambil semua kategori
    public static function all() {
        $pdo = Database::conn();
        
        // Gunakan nama tabel yang benar: 'category' 
        $stmt = $pdo->query("
            SELECT id, name_category 
            FROM category 
            ORDER BY name_category ASC
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method untuk mencari kategori berdasarkan nama
    public static function search(string $searchQuery): array {
        // Jika tidak ada pencarian, tampilkan semua kategori
        if (empty($searchQuery)) {
            return self::all();
        }

        $stmt = Database::conn()->prepare("SELECT * FROM category WHERE name_category LIKE ?");
        $stmt->execute(["%$searchQuery%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }

    // Method untuk menambahkan kategori baru
    public static function create(string $name): bool {
        $stmt = Database::conn()->prepare("INSERT INTO category(name_category) VALUES(?)");
        return $stmt->execute([$name]);
    }

    // Method untuk mengupdate kategori
    public static function update(int $id, string $name): bool {
        $stmt = Database::conn()->prepare("UPDATE category SET name_category = ? WHERE id = ?");
        return $stmt->execute([$name, $id]);
    }

    // Method untuk memeriksa apakah kategori sudah ada berdasarkan nama
    public static function existsByName(string $name): bool {
        $stmt = Database::conn()->prepare("SELECT COUNT(*) FROM category WHERE name_category = ?");
        $stmt->execute([$name]);
        return $stmt->fetchColumn() > 0;
    }

    // Method untuk mencari kategori berdasarkan ID
    public static function find(int $id): array|null {
        $stmt = Database::conn()->prepare("SELECT * FROM category WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}