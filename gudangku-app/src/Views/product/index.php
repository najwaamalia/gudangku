<?php
// src/Views/product/index.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>GudangKu — Produk: <?= htmlspecialchars($category['name_category'] ?? 'Unknown') ?></title>

    <style>
        :root{
            --bg:#f6f8fa; --card:#ffffff; --muted:#6b7280; --accent:#2563eb; --danger:#dc2626; --success:#16a34a;
            --radius:10px; --pad:14px;
        }
        *{box-sizing:border-box}
        body{
            margin:0; font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            background:var(--bg); color:#111827;
        }
        header{background:#0f172a;color:white;padding:16px 24px;display:flex;align-items:center;gap:12px}
        header .logo-icon{width:36px;height:36px;border-radius:6px;background:white;padding:6px;object-fit:cover}
        header h1{font-size:18px;margin:0}
        main{padding:20px; max-width:1100px;margin:18px auto}
        .card{background:var(--card); border-radius:var(--radius); padding:20px; box-shadow:0 6px 18px rgba(15,23,42,0.06);}
        .topbar{display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:16px;}
        .btn{background:var(--accent); color:white; border:none; padding:8px 12px; border-radius:8px; cursor:pointer; display:inline-flex; gap:8px; align-items:center;}
        .btn.secondary{background:#e6e9ef;color:#111827}
        .btn.danger{background:var(--danger)}
        .btn.small{padding:6px 8px;font-size:14px}
        .actions-left{display:flex;gap:8px;align-items:center}
        .search-form{display:flex; gap:8px; align-items:center; margin-left:auto;}
        .search-form input[type="text"]{padding:8px 10px;border-radius:8px;border:1px solid #e6e9ef}
        .select{padding:8px;border-radius:8px;border:1px solid #e6e9ef}
        .table{width:100%; border-collapse:collapse; margin-top:12px}
        table thead th{ text-align:left; padding:12px 10px; border-bottom:1px solid #eef2f7; font-weight:600; color:var(--muted)}
        table tbody td{padding:12px 10px; border-bottom:1px solid #f3f4f6; vertical-align:middle}
        .product-name{font-weight:600}
        .muted{color:var(--muted); font-size:13px}
        .no-data{padding:40px;text-align:center;color:var(--muted)}
        .flex-row{display:flex;gap:8px;align-items:center}

        /* Modal */
        .modal{display:none; position:fixed; inset:0; background:rgba(2,6,23,0.45); z-index:9999; align-items:center; justify-content:center; padding:20px}
        .modal .modal-card{width:100%; max-width:520px; background:white; border-radius:12px; padding:18px; box-shadow:0 12px 40px rgba(2,6,23,0.2); position:relative}
        .modal .modal-card h3{margin:0 0 12px 0}
        .modal .close{position:absolute;right:12px;top:12px;background:#f3f4f6;border:none;border-radius:8px;padding:6px;cursor:pointer}
        .form-group{margin-bottom:12px}
        .form-group label{display:block;font-size:13px;margin-bottom:6px}
        .form-group input[type="text"], .form-group input[type="number"], .form-group textarea{width:100%; padding:10px;border:1px solid #e6e9ef;border-radius:8px}
        .textarea{min-height:100px;resize:vertical}

        /* notif */
        .notif{position:fixed;right:20px;bottom:20px;padding:12px 16px;border-radius:10px;color:white;z-index:20000;box-shadow:0 8px 30px rgba(2,6,23,0.15)}
        .notif.success{background:var(--success)}
        .notif.error{background:var(--danger)}
        .hide{opacity:0;transform:translateY(8px);transition:all .28s}
        .show{opacity:1;transform:none;transition:all .28s}
    </style>
</head>
<body>
    <header>
        <img class="logo-icon" src="/assets/icon.png" alt="logo" />
        <h1>GudangKu — Produk</h1>
    </header>

    <main>
        <div class="card">
            <div class="topbar">
                <div class="actions-left">
                    <a href="/?r=category" class="btn secondary">← Back to Categories</a>
                    <button class="btn" onclick="openAddModal()">+ Add Product</button>

                    <form method="get" id="sortForm" style="display:inline;">
                        <input type="hidden" name="r" value="product">
                        <input type="hidden" name="cat" value="<?= htmlspecialchars($category['id'] ?? '') ?>">
                        <select name="sort" class="select" onchange="document.getElementById('sortForm').submit()">
                            <option value="">Sort</option>
                            <option value="az" <?= (isset($_GET['sort']) && $_GET['sort']=='az') ? 'selected' : '' ?>>A – Z</option>
                        </select>
                    </form>
                </div>

                <form method="get" class="search-form" action="/?">
                    <input type="hidden" name="r" value="product">
                    <input type="hidden" name="cat" value="<?= htmlspecialchars($category['id'] ?? '') ?>">
                    <input type="text" name="q" placeholder="Search product..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    <button class="btn small" type="submit">Search</button>
                </form>
            </div>

            <h2 style="margin:0 0 12px 0"><?= htmlspecialchars($category['name_category'] ?? 'Category') ?></h2>

            <?php if (empty($products)): ?>
                <div class="no-data card" style="margin-top:10px">
                    No products found.
                </div>
            <?php else: ?>

                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Product</th>
                            <th>Code</th>
                            <th>Stock</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
<?php $no=1; foreach ($products as $p): ?>
<tr>
    <td><?= $no++ ?></td>

    <td>
        <div class="product-name"><?= htmlspecialchars($p['product_name']) ?></div>
    </td>

    <td><?= htmlspecialchars($p['code']) ?></td>

    <td><?= (int)$p['stock'] ?></td>

    <td>
        <div class="flex-row">
            <button class="btn small"
                onclick="openEditModal(
                    <?= (int)$p['id'] ?>,
                    '<?= htmlspecialchars(addslashes($p['code'])) ?>',
                    '<?= htmlspecialchars(addslashes($p['product_name'])) ?>',
                    <?= (int)$p['stock'] ?>,
                    '<?= htmlspecialchars(addslashes($p['description'])) ?>'
                )">
                Edit
            </button>
        </div>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
                </table>

            <?php endif; ?>
        </div>
    </main>

    <!-- ADD MODAL -->
    <div id="addModal" class="modal">
        <div class="modal-card">
            <button class="close" onclick="closeAddModal()">✕</button>
            <h3>Add New Product</h3>

            <form method="post" action="/?r=prodSave">
                <input type="hidden" name="categoryId" value="<?= htmlspecialchars($category['id'] ?? '') ?>">

                <div class="form-group">
                    <label>Product Code</label>
                    <input name="code" type="text" required>
                </div>

                <div class="form-group">
                    <label>Product Name</label>
                    <input name="name" type="text" required>
                </div>

                <div class="form-group">
                    <label>Stock</label>
                    <input name="stock" type="number" min="0" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="desc" class="textarea"></textarea>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="btn secondary" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="btn">Add Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal" class="modal">
        <div class="modal-card">
            <button class="close" onclick="closeEditModal()">✕</button>
            <h3>Edit Product</h3>

            <form method="post" action="/?r=prodUpdate">
                <input type="hidden" name="id" id="editId">
                <input type="hidden" name="categoryId" value="<?= htmlspecialchars($category['id'] ?? '') ?>">

                <div class="form-group">
                    <label>Product Code</label>
                    <input name="code" id="editCode" required>
                </div>

                <div class="form-group">
                    <label>Product Name</label>
                    <input name="name" id="editName" required>
                </div>

                <div class="form-group">
                    <label>Stock</label>
                    <input name="stock" id="editStock" type="number" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="desc" id="editDesc" class="textarea"></textarea>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="btn secondary" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn">Update Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notification -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="notif success show"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="notif error show"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <script>
        function openAddModal(){
            document.getElementById("addModal").style.display="flex";
        }
        function closeAddModal(){
            document.getElementById("addModal").style.display="none";
        }

        function openEditModal(id, code, name, stock, desc){
            document.getElementById("editModal").style.display="flex";
            document.getElementById("editId").value=id;
            document.getElementById("editCode").value=code;
            document.getElementById("editName").value=name;
            document.getElementById("editStock").value=stock;
            document.getElementById("editDesc").value=desc;
        }
        function closeEditModal(){
            document.getElementById("editModal").style.display="none";
        }

        window.onclick=function(e){
            if(e.target===document.getElementById("addModal")) closeAddModal();
            if(e.target===document.getElementById("editModal")) closeEditModal();
        }
    </script>

</body>
</html>
