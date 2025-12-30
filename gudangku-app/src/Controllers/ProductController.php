<?php
namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;

class ProductController {

    // Menampilkan daftar produk berdasarkan kategori dengan opsi sorting dan pencarian
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $categoryId = $_GET['cat'] ?? null;

        if (!$categoryId) {
            header("Location: /?r=category");
            exit;
        }

        $sort   = $_GET['sort'] ?? '';
        $search = $_GET['q'] ?? '';

        // Sorting
        if ($sort == 'az') {
    $products = Product::sortAZ($categoryId);
} elseif (!empty($search)) {
    $products = Product::search($search, $categoryId);
} else {
    $products = Product::allByCategory($categoryId);
}
        $category = Category::findById((int)$categoryId);

        require __DIR__ . '/../Views/product/index.php';
    }

    // Menyimpan produk baru ke database
    public function save() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    //Ambil data dari form
    $categoryId = $_POST['categoryId'];
    $name       = $_POST['name'];
    $code       = $_POST['code'];
    $stock      = $_POST['stock'];
    $desc       = $_POST['desc'];

    // VALIDASI: Cek kode duplikat
    if (Product::existsCode($code)) {
        $_SESSION['error'] = "The code already exists!";
        header("Location: /?r=product&cat=$categoryId");
        exit;
    }
    // Simpan produk
    $ok = Product::create($name, $categoryId, $code, $stock, $desc, '');

    // Set pesan sesuai hasil penyimpanan
    if ($ok) {
        $_SESSION['success'] = "Product added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add product!";
    }

    // Redirect kembali ke halaman produk
    header("Location: /?r=product&cat=$categoryId");
    exit;
}

    // Memperbarui data produk yang sudah ada
    public function update() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    // Ambil data dari form
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: /?r=category");
        exit;
    }

    $id         = $_POST['id'];
    $categoryId = $_POST['categoryId'];
    $code       = $_POST['code'];
    $name       = $_POST['name'];
    $stock      = $_POST['stock'];
    $desc       = $_POST['desc'];

    // VALIDASI: cek kode duplikat kecuali ID saat ini
    if (Product::existsCodeExcept($code, $id)) {
        $_SESSION['error'] = "Product code already exists!";
        header("Location: /?r=product&cat=$categoryId");
        exit;
    }

    // Update produk ke database
    $ok = Product::update($id, $name, $code, $stock, $desc);

    // Set pesan sesuai hasil update
    if ($ok) {
        $_SESSION['success'] = "Product updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update product!";
    }

    // Redirect kembali ke halaman produk
    header("Location: /?r=product&cat=$categoryId");
    exit;
}

}
