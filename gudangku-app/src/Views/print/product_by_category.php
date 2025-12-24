<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List by Category - GudangKu</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            padding: 30px;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #333;
        }

        .header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 14px;
            color: #666;
        }

        .print-info {
            text-align: right;
            margin-bottom: 20px;
            font-size: 12px;
            color: #666;
        }

        .category-section {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }

        .category-header {
            background-color: #4CAF50;
            color: white;
            padding: 12px 15px;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .product-table thead {
            background-color: #f5f5f5;
        }

        .product-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #ddd;
            color: #333;
            font-size: 14px;
        }

        .product-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
            color: #555;
        }

        .product-table tbody tr:hover {
            background-color: #f9f9f9;
        }

        .no-column {
            width: 50px;
            text-align: center;
        }

        .code-column {
            width: 120px;
        }

        .stock-column {
            width: 100px;
            text-align: center;
            font-weight: 600;
            color: #333 !important;  /* Paksa warna hitam */
        }

        /* Hapus semua styling warna stock */
        .stock-low,
        .stock-medium,
        .stock-high {
            color: #333 !important;
            background-color: transparent !important;
        }

        /* Print specific styles */
        @media print {
            .stock-column {
                color: #000 !important;
                background-color: white !important;
            }
        }

        .total-products {
            text-align: right;
            font-weight: bold;
            padding: 10px 12px;
            background-color: #f0f0f0;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 13px;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #2196F3;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .print-button:hover {
            background-color: #1976D2;
        }

        @media print {
            body {
                padding: 20px;
            }

            .print-button {
                display: none;
            }

            .category-section {
                page-break-inside: avoid;
            }

            .product-table thead {
                display: table-header-group;
            }
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <button class="print-button" onclick="window.print()">
        🖨️ Print / Download PDF
    </button>

    <!-- Header -->
    <div class="header">
        <h1>📦 GudangKu - Inventory Management System</h1>
        <p>Product List by Category</p>
    </div>

    <!-- Print Info -->
    <div class="print-info">
        Print Date: <?php echo date('d F Y, H:i'); ?>
    </div>

    <!-- Category Sections -->
    <?php if (empty($categoryProducts)): ?>
        <div style="text-align: center; padding: 50px; color: #999;">
            <h3>No products available</h3>
        </div>
    <?php else: ?>
        <?php foreach ($categoryProducts as $categoryData): ?>
            <div class="category-section">
                <!-- Category Header -->
                <div class="category-header">
                    📁 <?php echo htmlspecialchars($categoryData['category']['name_category']); ?>
                </div>

                <!-- Product Table -->
                <table class="product-table">
                    <thead>
                        <tr>
                            <th class="no-column">No</th>
                            <th>Product Name</th>
                            <th class="code-column">Code</th>
                            <th class="stock-column">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($categoryData['products'] as $product): ?>
                            <tr>
                                <td class="no-column"><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($product['name_product']); ?></td>
                                <td class="code-column"><?php echo htmlspecialchars($product['code']); ?></td>
                                <td class="stock-column"><?php echo number_format($product['stock']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Total Products -->
                <div class="total-products">
                    Total Products: <?php echo count($categoryData['products']); ?> items
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Footer -->
    <div class="footer">
        <p><strong>GudangKu</strong> - Inventory Management System</p>
        <p>Generated automatically by the system</p>
    </div>

    <script>
        // Auto print saat halaman load (opsional)
        // window.onload = function() {
        //     window.print();
        // };
    </script>
</body>
</html>