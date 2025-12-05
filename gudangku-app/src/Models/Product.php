<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Product {

    // Ambil semua produk sesuai kategori TANPA SORT
    public static function allByCategory(int $categoryId): array {
    $stmt = Database::conn()->prepare("
        SELECT * FROM product 
        WHERE category_id = ?
        ORDER BY id ASC
    ");
    $stmt->execute([$categoryId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



    // Sorting A-Z
    public static function sortAZ(int $categoryId): array {
        $stmt = Database::conn()->prepare("
            SELECT * FROM product 
            WHERE category_id = ?
            ORDER BY product_name ASC
        ");
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Pencarian produk
    public static function search(string $keyword, int $categoryId): array {
        $stmt = Database::conn()->prepare("
            SELECT * FROM product
            WHERE product_name LIKE ? AND category_id = ?
        ");
        $stmt->execute(["%$keyword%", $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cek apakah kode produk sudah ada
public static function existsCode(string $code): bool {
    $stmt = Database::conn()->prepare("
        SELECT COUNT(*) FROM product WHERE code = ?
    ");
    $stmt->execute([$code]);
    return $stmt->fetchColumn() > 0;
}

// Cek kode produk saat edit (abaikan ID sendiri)
public static function existsCodeExcept(string $code, int $excludeId): bool {
    $stmt = Database::conn()->prepare("
        SELECT COUNT(*) FROM product 
        WHERE code = ? AND id != ?
    ");
    $stmt->execute([$code, $excludeId]);
    return $stmt->fetchColumn() > 0;
}


    // Buat produk baru
    public static function create($productName, $categoryId, $code, $stock, $description, $comment = '') {
        $stmt = Database::conn()->prepare("
            INSERT INTO product (product_name, category_id, code, stock, description, comment)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $productName,
            $categoryId,
            $code,
            $stock,
            $description,
            $comment
        ]);
    }

    // Update produk
    public static function update($id, $name, $code, $stock, $description) {
        $stmt = Database::conn()->prepare("
            UPDATE product 
            SET 
                product_name = ?, 
                code = ?, 
                stock = ?, 
                description = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $name,
            $code,
            $stock,
            $description,
            $id
        ]);
    }

    // Ambil produk by id
    public static function findById(int $id): array|null {
        $stmt = Database::conn()->prepare("SELECT * FROM product WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cari produk berdasarkan nama (global, bukan per kategori)
public static function searchGlobal(string $keyword): array {
    $stmt = Database::conn()->prepare("
        SELECT * FROM product 
        WHERE product_name LIKE ?
    ");
    $stmt->execute(["%$keyword%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}
