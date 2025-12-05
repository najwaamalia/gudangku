<?php
namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;

class CategoryController {

    // Menampilkan daftar kategori atau hasil pencarian
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $searchQuery = trim($_GET['q'] ?? '');  // input pencarian dari form

        // Jika ada pencarian
        if (!empty($searchQuery)) {
            // Cari produk secara global berdasarkan nama
            $foundProducts = Product::searchGlobal($searchQuery);

            if (!empty($foundProducts)) {
                // Ambil category id dari produk pertama
                $firstProduct = $foundProducts[0];
                $catId = (int)$firstProduct['category_id'];

                // Ambil detail kategori
                $category = \App\Models\Category::find($catId);

                // Filter produk dalam kategori pertama agar sesuai query
                $productsInCategory = array_filter($foundProducts, function($p) use ($catId) {
                    return (int)$p['category_id'] === $catId;
                });

                $products = empty($productsInCategory) ? $foundProducts : $productsInCategory;
                // Jika kosong fallback ambil semua produk kategori
                
                // Render view product
                require __DIR__ . '/../Views/product/index.php';
                return;
            } else {
                // Tidak ditemukan produk
                $_SESSION['error'] = "Product not found.";
                // tetap render category page di bawah
            }
        }

        // Jika bukan pencarian produk, atau tidak ditemukan
        $categories = \App\Models\Category::search($searchQuery);

        require __DIR__ . '/../Views/category/index.php';
    }

    // Tambah kategori
    public static function create() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /?r=category');
            exit;
        }

        $name = trim($_POST['name'] ?? '');

        if (empty($name)) {
            $_SESSION['error'] = "Category name cannot be empty!";
            header('Location: /?r=category');
            exit;
        }

        if (strlen($name) > 100) {
            $_SESSION['error'] = "Category name is too long! Maximum 100 characters.";
            header('Location: /?r=category');
            exit;
        }

        if (Category::existsByName($name)) {
            $_SESSION['error'] = "The category has already been added!";
            header('Location: /?r=category');
            exit;
        }

        $isCreated = Category::create($name);

        if ($isCreated) {
            $_SESSION['success'] = "Category added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add category!";
        }

        header('Location: /?r=category');
        exit;
    }

    // Update kategori
    public static function update() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /?r=category');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');

        if ($id <= 0 || empty($name)) {
            $_SESSION['error'] = "Invalid category data!";
            header('Location: /?r=category');
            exit;
        }

        if (strlen($name) > 100) {
            $_SESSION['error'] = "Category name is too long! Maximum 100 characters.";
            header('Location: /?r=category');
            exit;
        }

        if (Category::existsByName($name)) {
            $_SESSION['error'] = "Category name already exists!";
            header('Location: /?r=category');
            exit;
        }

        $isUpdated = Category::update($id, $name);

        $_SESSION['success'] = $isUpdated ? "Category updated successfully!" : "Failed to update category!";

        header('Location: /?r=category');
        exit;
    }
}
