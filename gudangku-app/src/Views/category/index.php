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
                    <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                </span>
                <button class="logout-btn" onclick="openLogoutModal()">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            <?php endif; ?>
        </div>
    </header>


    <main>
        <div class="dashboard-container">
            <h2>Category Product</h2>


            <!-- Action Buttons -->
            <div class="actions">
                <!-- Tombol Print dengan ikon -->
                <a href="/?r=print">
                    <button class="btn print-category">
                        <i class="fas fa-print"></i> Print
                    </button>
                </a>

                <!-- Tombol Add Category dengan ikon -->
                <button class="btn add-category" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Add Category
                </button>
            </div>

            <!-- Search Form Styling  -->
            <form method="get" class="search-form" action="/">
                <!-- PENTING: Hidden input untuk route -->
                <input type="hidden" name="r" value="category">
                
                <!-- Search input -->
                <input 
                    type="text" 
                    name="q" 
                    value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" 
                    placeholder="Search category..."
                    id="searchInput"
                >
                
                <!-- Search button -->
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> Search
                </button>

                <!-- Clear button (jika ada search query) -->
                <?php if (isset($_GET['q']) && !empty($_GET['q'])): ?>
                    <a href="/?r=category" class="btn-clear">
                        <i class="fas fa-times"></i> Clear
                    </a>
                <?php endif; ?>
            </form>

            <!-- Modal Edit Category -->
            <div id="editCategoryModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeModal()">&times;</span>
                    <h3>Edit Category</h3>
                    <form id="editCategoryForm" method="post" action="/?r=catUpdate">
                        <input type="hidden" name="id" id="categoryId">
                        <div class="form-group">
                            <label for="editCategoryName">Name Category</label>
                            <input type="text" name="name" id="editCategoryName" required placeholder="Enter category name">
                        </div>
                        <div class="modal-buttons">
                            <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                            <button type="submit" class="update-btn">Update</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Add Category -->
            <div id="addCategoryModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeAddModal()">&times;</span>
                    <h3>Add New Category</h3>
                    <form id="addCategoryForm" method="post" action="/?r=catCreate">
                        <div class="form-group">
                            <label for="addCategoryName">Category Name</label>
                            <input type="text" name="name" id="addCategoryName" required placeholder="Enter new category name">
                        </div>
                        <div class="modal-buttons">
                            <button type="button" class="cancel-btn" onclick="closeAddModal()">Cancel</button>
                            <button type="submit" class="add-btn">Add Category</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- LOGOUT MODAL -->
            <div id="logoutModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeLogoutModal()">&times;</span>
                    <h3><i class="fas fa-sign-out-alt logout-icon"></i> Confirm Logout</h3>
                    <p class="logout-text">Are you sure you want to logout?</p>
                    <div class="modal-buttons">
                        <button type="button" class="cancel-btn" onclick="closeLogoutModal()">Batal</button>
                        <button type="button" class="logout-confirm" id="logoutBtn" onclick="performLogout()">OK</button>
                    </div>
                </div>
            </div>

            <!-- Category List -->
            <?php if (empty($categories)): ?>
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>No categories found</h3>
                    <p>Try different keywords or <a href="/?r=category">view all categories</a></p>
                </div>
            <?php else: ?>
                <ul class="category-list">
                    <?php foreach ($categories as $category): ?>
                        <li class="category-item">
                            <!-- Link ke product berdasarkan category_id -->
                            <a href="/?r=product&cat=<?= $category['id'] ?>" class="category-name">
                                <?= htmlspecialchars($category['name_category']) ?>
                            </a>
                            <div class="category-actions">
                                <a href="javascript:void(0);" class="edit-category" onclick="openModal(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name_category']); ?>')">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </main>


    <script>

    function openModal(id, name) {
        document.getElementById('editCategoryModal').classList.add('show');
        document.getElementById('categoryId').value = id;
        document.getElementById('editCategoryName').value = name;
    }

    function closeModal() {
        document.getElementById('editCategoryModal').classList.remove('show');
    }

    function openAddModal() {
        document.getElementById('addCategoryModal').classList.add('show');
    }

    function closeAddModal() {
        document.getElementById('addCategoryModal').classList.remove('show');
    }

    function openLogoutModal() {
        document.getElementById('logoutModal').classList.add('show');
    }

    function closeLogoutModal() {
        document.getElementById('logoutModal').classList.remove('show');
    }

    function performLogout() {
        const btn = document.getElementById('logoutBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging out...';
        btn.disabled = true;
        btn.style.opacity = '0.7';
        setTimeout(() => {
            window.location.href = '/?r=logout';
        }, 1000);
    }

    // Keyboard support
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
            closeAddModal();
            closeLogoutModal();
        }
    });
    </script>


</body>
</html>
