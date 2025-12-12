<?php
namespace App\Models;
use App\Core\Database;
use PDO;

class Product {

    // Method untuk mengambil semua produk
    public static function all(): array {
        return Database::conn()->query("SELECT p.*, c.name_category 
                                        FROM product p 
                                        LEFT JOIN category c ON p.category_id = c.id 
                                        ORDER BY p.name_product")->fetchAll();
    }

    // Method untuk mengambil produk berdasarkan category_id
    public static function getByCategoryId(int $categoryId): array {
        $stmt = Database::conn()->prepare("SELECT p.*, c.name_category 
                                          FROM product p 
                                          LEFT JOIN category c ON p.category_id = c.id 
                                          WHERE p.category_id = ? 
                                          ORDER BY p.name_product");
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method untuk mengambil semua produk dikelompokkan per kategori
    public static function getAllGroupedByCategory(): array {
        $query = "SELECT p.*, c.name_category, c.id as category_id
                  FROM product p 
                  LEFT JOIN category c ON p.category_id = c.id 
                  ORDER BY c.name_category, p.name_product";
        
        $results = Database::conn()->query($query)->fetchAll(PDO::FETCH_ASSOC);
        
        // Group products by category
        $grouped = [];
        foreach ($results as $product) {
            $categoryId = $product['category_id'];
            if (!isset($grouped[$categoryId])) {
                $grouped[$categoryId] = [
                    'category_name' => $product['name_category'],
                    'products' => []
                ];
            }
            $grouped[$categoryId]['products'][] = $product;
        }
        
        return $grouped;
    }

    // Method untuk mencari produk berdasarkan nama
    public static function search(string $searchQuery): array {
        if (empty($searchQuery)) {
            return self::all();
        }

        $stmt = Database::conn()->prepare("SELECT p.*, c.name_category 
                                          FROM product p 
                                          LEFT JOIN category c ON p.category_id = c.id 
                                          WHERE p.name_product LIKE ? OR p.code LIKE ? 
                                          ORDER BY p.name_product");
        $stmt->execute(["%$searchQuery%", "%$searchQuery%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method untuk menambahkan produk baru
    public static function create(array $data): bool {
        $stmt = Database::conn()->prepare("INSERT INTO product(name_product, code, stock, category_id) 
                                          VALUES(?, ?, ?, ?)");
        return $stmt->execute([
            $data['name_product'], 
            $data['code'], 
            $data['stock'], 
            $data['category_id']
        ]);
    }

    // Method untuk mengupdate produk
    public static function update(int $id, array $data): bool {
        $stmt = Database::conn()->prepare("UPDATE product 
                                          SET name_product = ?, code = ?, stock = ?, category_id = ? 
                                          WHERE id = ?");
        return $stmt->execute([
            $data['name_product'], 
            $data['code'], 
            $data['stock'], 
            $data['category_id'], 
            $id
        ]);
    }

    // Method untuk menghapus produk
    public static function delete(int $id): bool {
        $stmt = Database::conn()->prepare("DELETE FROM product WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Method untuk mengecek apakah produk dengan code tertentu sudah ada
    public static function existsByCode(string $code): bool {
        $stmt = Database::conn()->prepare("SELECT COUNT(*) FROM product WHERE code = ?");
        $stmt->execute([$code]);
        return $stmt->fetchColumn() > 0;
    }

    // Method untuk mendapatkan detail produk berdasarkan ID
    public static function findById(int $id): ?array {
        $stmt = Database::conn()->prepare("SELECT p.*, c.name_category 
                                          FROM product p 
                                          LEFT JOIN category c ON p.category_id = c.id 
                                          WHERE p.id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}
?>