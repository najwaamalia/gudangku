<?php
/**
 * View untuk halaman produk GudangKu
 * Menampilkan daftar produk dalam kategori tertentu dengan fitur tambah, edit, sorting, dan pencarian
 */
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>GudangKu — Produk: <?= htmlspecialchars($category['name_category'] ?? 'Unknown') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/assets/product.css">
</head>
<body>
    <header>
        <div class="logo">
            <img class="logo-icon" src="/assets/icon.png" alt="logo" />
            <h1>GudangKu - Inventory Management System</h1>
        </div>
        <div class="header-actions">
            <?php if (isset($_SESSION['auth'])): ?>
                <span class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                </span>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <div class="card">
            <div class="topbar">
                <div class="actions-left">
                    <a href="/?r=category" class="btn secondary">← Back to Categories</a>
                    <button class="btn add-product" onclick="openAddModal()">+ Add Product</button>

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
                    Products not found.
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
        <div class="notif success show" id="successNotif">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button class="close-notif" onclick="closeNotif('successNotif')">&times;</button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="notif error show" id="errorNotif">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button class="close-notif" onclick="closeNotif('errorNotif')">&times;</button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <script>
        function openAddModal(){
            document.getElementById("addModal").classList.add("show");
        }
        function closeAddModal(){
            document.getElementById("addModal").classList.remove("show");
        }

        function openEditModal(id, code, name, stock, desc){
            document.getElementById("editModal").classList.add("show");
            document.getElementById("editId").value=id;
            document.getElementById("editCode").value=code;
            document.getElementById("editName").value=name;
            document.getElementById("editStock").value=stock;
            document.getElementById("editDesc").value=desc;
        }
        function closeEditModal(){
            document.getElementById("editModal").classList.remove("show");
        }

        function openLogoutModal(){
            document.getElementById("logoutModal").classList.add("show");
        }

        window.onclick=function(e){
            if(e.target===document.getElementById("addModal")) closeAddModal();
            if(e.target===document.getElementById("editModal")) closeEditModal();
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
            }
        });

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

        function closeNotif(id) {
            const notif = document.getElementById(id);
            if (notif) {
                notif.classList.remove('show');
                setTimeout(() => notif.style.display = 'none', 300);
            }
        }

        // Auto-hide notifications after 4 seconds
        setTimeout(() => {
            const notifs = document.querySelectorAll('.notif.show');
            notifs.forEach(notif => {
                notif.classList.remove('show');
                setTimeout(() => notif.style.display = 'none', 300);
            });
        }, 4000);
    </script>


</body>
</html>
