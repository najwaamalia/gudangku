<?php
namespace App\Models;
use App\Core\Database;
use PDO;

class Category {

    // Method untuk mengambil semua kategori
    public static function all(): array {
        return Database::conn()->query("SELECT * FROM category ORDER BY name_category")->fetchAll();
    }

    // Method untuk mencari kategori berdasarkan ID
    public static function findById(int $id): ?array {
        $stmt = Database::conn()->prepare("SELECT * FROM category WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // Method untuk mencari kategori berdasarkan nama (SEARCH)
    public static function search(string $searchQuery): array {
        // Jika tidak ada pencarian, tampilkan semua kategori
        if (empty($searchQuery)) {
            return self::all();
        }

        // Query pencarian dengan LIKE - case insensitive
        $stmt = Database::conn()->prepare("SELECT * FROM category 
                                          WHERE LOWER(name_category) LIKE LOWER(?) 
                                          ORDER BY name_category");
        $stmt->execute(["%$searchQuery%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    // Method untuk menghapus kategori
    public static function delete(int $id): bool {
        $stmt = Database::conn()->prepare("DELETE FROM category WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Method untuk memeriksa apakah kategori sudah ada berdasarkan nama
    public static function existsByName(string $name): bool {
        $stmt = Database::conn()->prepare("SELECT COUNT(*) FROM category WHERE LOWER(name_category) = LOWER(?)");
        $stmt->execute([$name]);
        return $stmt->fetchColumn() > 0;
    }

    public static function findByName(string $name): ?array {
    $stmt = Database::conn()->prepare("SELECT * FROM category WHERE LOWER(name_category) = LOWER(?)");
    $stmt->execute([$name]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}
}