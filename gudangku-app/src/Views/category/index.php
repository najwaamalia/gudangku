<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GudangKu - Inventory Management System</title>
    <link rel="stylesheet" href="/assets/category.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="/assets/icon.png" alt="GudangKu Icon" class="logo-icon">
            <h1>GudangKu - Inventory Management System</h1>
        </div>
        <!-- Logout Button di Header -->
        <div class="header-actions">
            <?php if (isset($_SESSION['auth'])): ?>
                <span class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <?php echo htmlspecialchars($_SESSION['auth']['username'] ?? 'User'); ?>
                </span>
                <a href="/?r=logout" class="logout-btn" onclick="return confirm('Are you sure you want to logout?')">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <div class="dashboard-container">
            <h2>Category Product</h2>

            <!-- Action Buttons -->
            <div class="actions">
                <a href="/?r=print">
                    <button class="btn print-category">
                        <i class="fas fa-print"></i> Print
                    </button>
                </a>

                <button class="btn add-category" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Add Category
                </button>
            </div>

            <!-- Search Form -->
            <form method="get" class="search-form" action="/?r=category">
    <input type="text" name="q" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" placeholder="Search product...">
    <button type="submit" class="search-btn"><i class="fas fa-search"></i> Search</button>
</form>


            <!-- Modal Edit Category -->
            <div id="editCategoryModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeModal()">&times;</span>
                    <h3>Edit Category</h3>
                    <form id="editCategoryForm" method="post" action="/?r=catUpdate">
                        <input type="hidden" name="id" id="categoryId">
                        <label for="editCategoryName">Name Category</label>
                        <input type="text" name="name" id="editCategoryName" required placeholder="Enter new category name">
                        <button type="submit" class="update-btn">Update</button>
                        <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                    </form>
                </div>
            </div>

            <!-- Modal Add Category -->
            <div id="addCategoryModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeAddModal()">&times;</span>
                    <h3>Add New Category</h3>
                    <form id="addCategoryForm" method="post" action="/?r=catCreate">
                        <label for="addCategoryName">Category Name</label>
                        <input type="text" name="name" id="addCategoryName" required placeholder="Enter new category name">
                        <button type="submit" class="add-btn">Add Category</button>
                        <button type="button" class="cancel-btn" onclick="closeAddModal()">Cancel</button>
                    </form>
                </div>
            </div>

            <!-- Category List -->
            <ul class="category-list">
                <?php foreach ($categories as $category): ?>
                    <li class="category-item">

                        <!-- Link menuju Product Page -->
                        <a href="/?r=product&cat=<?= $category['id'] ?>" class="category-name">
                            <?= htmlspecialchars($category['name_category']) ?>
                        </a>

                        <!-- Tombol Edit -->
                        <a href="javascript:void(0);" class="edit-category"
                           onclick="openModal(<?= $category['id']; ?>, '<?= htmlspecialchars($category['name_category']); ?>')">
                            <i class="fas fa-pencil-alt"></i>
                        </a>

                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </main>

    <!-- Error Notification -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-notification">
            <?php echo $_SESSION['error']; ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <script>
        function openModal(id, name) {
            document.getElementById('editCategoryModal').style.display = 'block';
            document.getElementById('categoryId').value = id;
            document.getElementById('editCategoryName').value = name;
        }

        function closeModal() {
            document.getElementById('editCategoryModal').style.display = 'none';
        }

        function openAddModal() {
            document.getElementById('addCategoryModal').style.display = 'block';
        }

        function closeAddModal() {
            document.getElementById('addCategoryModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const editModal = document.getElementById('editCategoryModal');
            const addModal = document.getElementById('addCategoryModal');

            if (event.target === editModal) closeModal();
            if (event.target === addModal) closeAddModal();
        }
    </script>
</body>
</html>
