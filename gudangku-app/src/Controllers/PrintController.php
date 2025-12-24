<?php
namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use Mpdf\Mpdf;

class PrintController {

    // Method untuk generate PDF list produk berdasarkan kategori
    public static function printProductsByCategory() {
        // Pastikan session dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Ambil semua kategori
        $categories = Category::all();
        
        // Array untuk menyimpan produk per kategori
        $categoryProducts = [];
        
        foreach ($categories as $category) {
            // Ambil produk untuk setiap kategori
            $products = Product::getByCategoryId($category['id']);
            
            // Simpan jika kategori memiliki produk
            if (!empty($products)) {
                $categoryProducts[] = [
                    'category' => $category,
                    'products' => $products
                ];
            }
        }

        // Generate HTML content
        $html = self::generatePDFContent($categoryProducts);

        try {
            // Inisialisasi mPDF
            $mpdf = new Mpdf();

            // Set document properties
            $mpdf->SetTitle('GudangKu - Product List');
            $mpdf->SetAuthor('GudangKu System');

            // Write HTML to PDF
            $mpdf->WriteHTML($html);

            // Output PDF sebagai download
            $filename = 'GudangKu_Products_' . date('Y-m-d_His') . '.pdf';
            $mpdf->Output($filename, 'D'); // 'D' untuk download
            
        } catch (\Mpdf\MpdfException $e) {
            $_SESSION['error'] = "Failed to generate PDF: " . $e->getMessage();
            header('Location: /?r=category');
            exit;
        }
    }

    // Method untuk generate HTML content
    private static function generatePDFContent($categoryProducts) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                @page {
                    margin: 20mm;
                }
                
                body {
                    font-family: Arial, sans-serif;
                    font-size: 11pt;
                    color: #333;
                }
                
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 3px solid #0f172a;
                    padding-bottom: 15px;
                }
                
                .header h1 {
                    color: #0f172a;
                    margin: 0 0 5px 0;
                    font-size: 24pt;
                }
                
                .header p {
                    color: #666;
                    margin: 0;
                    font-size: 10pt;
                }
                
                .category-section {
                    margin-bottom: 30px;
                    page-break-inside: avoid;
                }
                
                .category-title {
                    background: #0f172a;
                    color: white;
                    padding: 10px 15px;
                    margin-bottom: 15px;
                    font-size: 14pt;
                    font-weight: bold;
                    border-radius: 4px;
                }
                
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                
                table thead {
                    background: #f1f5f9;
                }
                
                table th {
                    padding: 10px;
                    text-align: left;
                    font-weight: bold;
                    color: #0f172a;
                    border-bottom: 2px solid #cbd5e1;
                }
                
                table td {
                    padding: 8px 10px;
                    border-bottom: 1px solid #e2e8f0;
                }
                
                table tbody tr:hover {
                    background: #f8fafc;
                }
                
                .no-col { width: 8%; text-align: center; }
                .code-col { width: 20%; }
                .name-col { width: 35%; }
                .stock-col { width: 12%; text-align: center; }
                .desc-col { width: 25%; }
                
                .stock-badge {
                    display: inline-block;
                    padding: 3px 8px;
                    border-radius: 4px;
                    font-weight: bold;
                    font-size: 9pt;
                }
                
                .stock-low,
                .stock-medium,
                .stock-high {
                    color: #333 !important;
                    background-color: transparent !important;
                }
                
                .footer {
                    margin-top: 40px;
                    padding-top: 15px;
                    border-top: 1px solid #cbd5e1;
                    text-align: center;
                    font-size: 9pt;
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>GudangKu</h1>
                <p>Inventory Management System - Product List Report</p>
                <p>Generated on: ' . date('d F Y, H:i') . '</p>
            </div>
        ';

        // Loop setiap kategori
        foreach ($categoryProducts as $item) {
            $category = $item['category'];
            $products = $item['products'];
            
            $html .= '<div class="category-section">';
            $html .= '<div class="category-title">' . htmlspecialchars($category['name_category']) . '</div>';
            
            $html .= '<table>
                <thead>
                    <tr>
                        <th class="no-col">No</th>
                        <th class="code-col">Product Code</th>
                        <th class="name-col">Product Name</th>
                        <th class="stock-col">Stock</th>
                        <th class="desc-col">Description</th>
                    </tr>
                </thead>
                <tbody>';
            
            $no = 1;
            foreach ($products as $product) {
                $stock = (int)$product['stock'];
                $stockClass = $stock < 10 ? 'stock-low' : 'stock-normal';
                
                $html .= '<tr>';
                $html .= '<td class="no-col">' . $no++ . '</td>';
                $html .= '<td class="code-col">' . htmlspecialchars($product['code']) . '</td>';
                $html .= '<td class="name-col">' . htmlspecialchars($product['product_name']) . '</td>';
                $html .= '<td class="stock-col"><span class="stock-badge ' . $stockClass . '">' . $stock . '</span></td>';
                $html .= '<td class="desc-col">' . htmlspecialchars($product['description'] ?: '-') . '</td>';
                $html .= '</tr>';
            }
            
            $html .= '</tbody></table>';
            $html .= '</div>';
        }

        $html .= '
            <div class="footer">
                <p>This document was generated automatically by GudangKu System</p>
                <p>&copy; ' . date('Y') . ' GudangKu - All Rights Reserved</p>
            </div>
        </body>
        </html>';

        return $html;
    }
}