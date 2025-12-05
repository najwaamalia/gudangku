<?php
namespace App\Models;
use App\Core\Database;
use PDO;

class Category {

    // Method untuk mengambil semua kategori
    public static function all(): array {
        return Database::conn()->query("SELECT * FROM category ORDER BY name_category")->fetchAll();
    }

    // Method untuk mencari kategori berdasarkan nama
    public static function search(string $searchQuery): array {
        // Jika tidak ada pencarian, tampilkan semua kategori
        if (empty($searchQuery)) {
            return self::all();  // Semua kategori jika tidak ada pencarian
        }

        // Query pencarian dengan LIKE, menggunakan wildcard % untuk pencocokan yang lebih fleksibel
        $stmt = Database::conn()->prepare("SELECT * FROM category WHERE name_category LIKE ?");
        $stmt->execute(["%$searchQuery%"]);  // Menambahkan wildcard agar pencarian lebih fleksibel
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Jika hasil kosong, tampilkan kategori "not found"
        return $results;
    }


    // Method untuk menambahkan kategori baru
    public static function create(string $name): bool {
        // Query untuk memasukkan kategori baru ke database
        $stmt = Database::conn()->prepare("INSERT INTO category(name_category) VALUES(?)");
        return $stmt->execute([$name]);  // Mengembalikan true jika berhasil
    }


    // Method untuk mengupdate kategori
    public static function update(int $id, string $name): bool {
        // Query untuk mengupdate kategori berdasarkan ID
        $stmt = Database::conn()->prepare("UPDATE category SET name_category = ? WHERE id = ?");
        return $stmt->execute([$name, $id]);  // Eksekusi query untuk mengupdate data
    }

    // Method untuk memeriksa apakah kategori sudah ada berdasarkan nama
    public static function existsByName(string $name): bool {
        $stmt = Database::conn()->prepare("SELECT COUNT(*) FROM category WHERE name_category = ?");
        $stmt->execute([$name]);
        return $stmt->fetchColumn() > 0;  // Mengembalikan true jika kategori sudah ada
    }

    public static function find(int $id): array|null {
    $stmt = Database::conn()->prepare("SELECT * FROM category WHERE id = ?");
    $stmt->execute([$id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


}
