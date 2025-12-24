<?php
namespace App\Controllers;
use App\Models\Category;

class CategoryController {

    // Menampilkan daftar kategori atau hasil pencarian
    public function index() {
        // Pastikan session dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Ambil query pencarian dari URL
        $searchQuery = $_GET['q'] ?? '';
        
        // Jika ada query pencarian, cari kategori
        if (!empty($searchQuery)) {
            $categories = Category::search($searchQuery);
            
            // Jika tidak ada hasil, set pesan info
            if (empty($categories)) {
                $_SESSION['info'] = "No categories found for '" . htmlspecialchars($searchQuery) . "'";
            }
        } else {
            // Jika tidak ada pencarian, tampilkan semua kategori
            $categories = Category::all();
        }

        // Menampilkan data kategori dalam layout
        require __DIR__.'/../Views/category/index.php';
    }

    // Menambahkan kategori baru
    public function create() {
        // Pastikan session dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Validasi method request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /?r=category');
            exit;
        }

        // Ambil nama kategori dari form
        $name = trim($_POST['name'] ?? '');

        // Validasi kategori kosong
        if (empty($name)) {
            $_SESSION['error'] = "Category name cannot be empty!";
            header('Location: /?r=category');
            exit;
        }

        // Validasi panjang maksimal
        if (strlen($name) > 100) {
            $_SESSION['error'] = "Category name is too long! Maximum 100 characters.";
            header('Location: /?r=category');
            exit;
        }

        // Periksa apakah kategori sudah ada
        if (Category::existsByName($name)) {
            $_SESSION['error'] = "The category already exists!";
            header('Location: /?r=category');
            exit;
        }

        // Simpan kategori ke database
        $isCreated = Category::create($name);

        // Cek jika data berhasil disimpan
        if ($isCreated) {
            $_SESSION['success'] = "Category added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add category!";
        }
        
        header('Location: /?r=category');
        exit;
    }

    // Mengupdate kategori
    public function update() {
        // Pastikan session dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Validasi method request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /?r=category');
            exit;
        }

        // Validasi ID ada dan valid
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            $_SESSION['error'] = "Invalid category ID!";
            header('Location: /?r=category');
            exit;
        }

        $id = (int)$_POST['id'];
        $name = trim($_POST['name'] ?? '');

        // Validasi kategori kosong
        if (empty($name)) {
            $_SESSION['error'] = "Category name cannot be empty!";
            header('Location: /?r=category');
            exit;
        }

        // Validasi panjang maksimal
        if (strlen($name) > 100) {
            $_SESSION['error'] = "Category name is too long! Maximum 100 characters.";
            header('Location: /?r=category');
            exit;
        }

        // PERBAIKAN: Cek apakah nama kategori sudah digunakan oleh kategori lain
        $existingCategory = Category::findByName($name);
        if ($existingCategory && $existingCategory['id'] != $id) {
            $_SESSION['error'] = "The category name '" . htmlspecialchars($name) . "' already exists!";
            header('Location: /?r=category');
            exit;
        }

        // Update kategori di database
        $isUpdated = Category::update($id, $name);

        // Cek hasil update
        if ($isUpdated) {
            $_SESSION['success'] = "Category updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update category!";
        }

        // Redirect ke halaman kategori setelah berhasil
        header('Location: /?r=category');
        exit;
    }

    // Method untuk menghapus kategori (opsional)
    public function delete() {
        // Pastikan session dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Validasi method request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /?r=category');
            exit;
        }

        // Validasi ID
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            $_SESSION['error'] = "Invalid category ID!";
            header('Location: /?r=category');
            exit;
        }

        $id = (int)$_POST['id'];

        // Hapus kategori
        $isDeleted = Category::delete($id);

        if ($isDeleted) {
            $_SESSION['success'] = "Category deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete category!";
        }

        header('Location: /?r=category');
        exit;
    }
}
?>