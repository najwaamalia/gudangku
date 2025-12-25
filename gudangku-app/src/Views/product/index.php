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
        --bg:#f6f8fa;
        --card:#ffffff;
        --muted:#6b7280;
        --accent:#6d72df;
        --accent-dark:#5a5fcf;
        --danger:#dc2626;
        --success:#16a34a;
        --radius:18px;
    }

    /* RESET */
    *{box-sizing:border-box}
    body{
        margin:0;
        font-family:'Segoe UI',system-ui,sans-serif;
        background:linear-gradient(135deg,#6b6fdc,#7b5dc6);
        color:#111827;
    }

    /* HEADER */
    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px 48px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    header .logo {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    header .logo-icon {
        width: 48px;
        height: 48px;
        padding: 8px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        transition: transform 0.3s ease;
    }

    header .logo-icon:hover {
        transform: scale(1.05) rotate(5deg);
    }

    header h1 {
        font-size: 22px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 0;
        font-weight: 700;
    }

    /* Header Actions */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
        color: #4a5568;
        font-weight: 600;
        padding: 8px 16px;
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        border-radius: 50px;
    }

    .user-info i {
        font-size: 20px;
        color: #667eea;
    }

    .logout-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        background: linear-gradient(135deg, #f56565 0%, #c53030 100%);
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(245, 101, 101, 0.3);
        transition: all 0.3s ease;
    }

    .logout-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(245, 101, 101, 0.4);
    }

    /* MAIN */
    main{
        padding:32px;
    }
    .card{
        background:#fff;
        border-radius:22px;
        padding:26px;
        box-shadow:0 30px 70px rgba(0,0,0,.18);
    }

    /* TOP BAR */
    .topbar{
        display:flex;
        flex-wrap:wrap;
        gap:12px;
        align-items:center;
        margin-bottom:18px;
    }
    .actions-left{
        display:flex;
        gap:10px;
    }

    /* BUTTON */
    .btn{
        background:linear-gradient(135deg,var(--accent),#8b5cf6);
        color:#fff;
        border:none;
        padding:9px 16px;
        border-radius:12px;
        font-weight:600;
        cursor:pointer;
        transition:.2s;
    }
    .btn:hover{opacity:.9}
    .btn.secondary{
        background:#eef2f7;
        color:#111;
    }
    .btn.small{
        padding:7px 14px;
        font-size:13px;
    }

    /* =========================
    CUSTOM DROPDOWN
    ========================= */
    .dropdown {
        position: relative;
    }

    .dropdown-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;

        background: linear-gradient(135deg, var(--accent), #8b5cf6);
        color: #fff;
        border: none;
        padding: 9px 16px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        min-width: 130px;

        box-shadow: 0 6px 18px rgba(0,0,0,.15);
        transition: all .25s ease;
    }

    .dropdown-btn:hover {
        opacity: .95;
    }

    /* arrow */
    .dropdown-btn .arrow {
        transition: transform .25s ease;
    }

    /* OPEN STATE */
    .dropdown.open .dropdown-btn {
        background: linear-gradient(135deg, #5b5fdc, #7c3aed);
        box-shadow: 0 0 0 3px rgba(124,58,237,.35);
    }

    .dropdown.open .arrow {
        transform: rotate(180deg);
    }

    /* =========================
    DROPDOWN MENU
    ========================= */
    .dropdown-menu {
        position: absolute;
        top: 110%;
        left: 0;
        width: 100%;
        background: #fff;
        border-radius: 14px;
        padding: 6px;
        box-shadow: 0 20px 40px rgba(0,0,0,.2);

        opacity: 0;
        transform: translateY(-10px);
        pointer-events: none;
        transition: all .25s ease;
        z-index: 100;
    }

    .dropdown.open .dropdown-menu {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    /* item */
    .dropdown-item {
        padding: 10px 14px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        color: #111827;
        transition: background .2s ease;
    }

    .dropdown-item:hover {
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
    }


    /* SEARCH */
    .search-form{
        margin-left:auto;
        display:flex;
        gap:8px;
    }
    .search-form input{
        padding:9px 14px;
        border-radius:12px;
        border:1px solid #e5e7eb;
    }
    .search-btn {
        padding: 14px 32px;
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
    }

    .search-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(72, 187, 120, 0.4);
    }

    /* TABLE */
    .table{
        width:100%;
        border-collapse:separate;
        border-spacing:0 14px;
    }
    .table thead th{
        font-size:12px;
        text-transform:uppercase;
        letter-spacing:.6px;
        color:var(--muted);
        padding:10px 14px;
    }
    .table tbody tr{
        background:#fff;
        border-radius:18px;
        box-shadow:0 10px 25px rgba(0,0,0,.1);
        transition:.2s;
    }
    .table tbody tr:hover{
        transform:translateY(-3px);
    }
    .table tbody td{
        padding:14px 16px;
        font-size:14px;
        vertical-align:middle;
    }
    .table tbody td:first-child{border-radius:18px 0 0 18px}
    .table tbody td:last-child{border-radius:0 18px 18px 0}

    /* PRODUCT */
    .product-name{
        font-weight:600;
        color:#4f46e5;
    }

    /* =========================
    TABLE ALIGN CENTER
    ========================= */
    .table thead th,
    .table tbody td{
        text-align:center;
    }

    /* PRODUCT NAME tetap rapi */
    .product-name{
        display:inline-block;
        text-align:center;
    }

    /* =========================
    STOCK COLUMN (PLAIN TEXT)
    ========================= */
    .table tbody td:nth-child(4){
        background:none !important;
        box-shadow:none !important;
        font-weight:600;
        color:#111827;
        pointer-events:auto;
    }

    /* HILANGKAN SEMUA PSEUDO */
    .table tbody td:nth-child(4)::before,
    .table tbody td:nth-child(4)::after{
        content:none !important;
    }

    /* =========================
    ACTION COLUMN SAFE
    ========================= */
    .table tbody td:nth-child(5){
        text-align:center;
        position:relative;
        z-index:10;
    }


    /* ACTION COLUMN FIX */
    .table tbody td:nth-child(5){
        position:relative;
        z-index:5;
    }

    /* EMPTY */
    .no-data{
        padding:40px;
        text-align:center;
        color:var(--muted);
    }

    /* MODAL */
    .modal{
        display:none;
        position:fixed;
        inset:0;
        background:rgba(15,23,42,.6);
        z-index:9999;
        align-items:center;
        justify-content:center;
    }
    .modal-card{
        background:#fff;
        border-radius:22px;
        padding:26px;
        width:100%;
        max-width:520px;
        box-shadow:0 30px 70px rgba(0,0,0,.35);
    }
    .close{
        position:absolute;
        top:14px;
        right:14px;
        border:none;
        background:#edf2f7;
        border-radius:10px;
        padding:6px 10px;
    }

    /* FORM */
    .form-group label{
        font-size:13px;
        margin-bottom:6px;
        display:block;
    }
    .form-group input,
    .form-group textarea{
        width:100%;
        padding:10px 14px;
        border-radius:12px;
        border:1px solid #e5e7eb;
    }

    /* NOTIF */
    .notif{
        position:fixed;
        right:20px;
        bottom:20px;
        padding:14px 22px;
        border-radius:16px;
        color:#fff;
        font-weight:600;
        box-shadow:0 10px 30px rgba(0,0,0,.3);
    }
    .notif.success{background:linear-gradient(135deg,#22c55e,#16a34a)}
    .notif.error{background:linear-gradient(135deg,#ef4444,#b91c1c)}

    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <img class="logo-icon" src="/assets/icon.png" alt="logo" />
                <h1>GudangKu - Inventory Management System</h1>
        </div>
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
                        <div class="dropdown" id="sortDropdown">
                            <button type="button" class="dropdown-btn" onclick="toggleDropdown()">
                                <span id="dropdownLabel">Sort</span>
                                <span class="arrow">▾</span>
                            </button>

                            <div class="dropdown-menu">
                                <div class="dropdown-item" onclick="selectSort('', 'Sort')">Sort</div>
                                <div class="dropdown-item" onclick="selectSort('az', 'A - Z')">A - Z</div>
                            </div>
                        </div>

                        <input type="hidden" name="sort" id="sortInput">
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

        function toggleDropdown() {
            document.getElementById('sortDropdown').classList.toggle('open');
        }

        function selectSort(value, label) {
            document.getElementById('sortInput').value = value;
            document.getElementById('dropdownLabel').innerText = label;
            document.getElementById('sortDropdown').classList.remove('open');
            document.getElementById('sortForm').submit();
        }

        /* close jika klik di luar */
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('sortDropdown');
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });
    </script>


</body>
</html>
