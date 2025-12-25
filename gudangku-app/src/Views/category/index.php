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
                <a href="/?r=logout" class="logout-btn" onclick="return confirm('Are you sure you want to logout?')">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- ================= NOTIFICATION ================= -->

    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-notification" id="errorNotification">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button class="notification-close"
                    onclick="closeNotification('errorNotification')">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['info'])): ?>
        <div class="info-notification" id="infoNotification">
            <?= htmlspecialchars($_SESSION['info']) ?>
            <button class="notification-close"
                    onclick="closeNotification('infoNotification')">&times;</button>
        </div>
    <?php endif; ?>

    <main>
        <div class="dashboard-container">
            <h2>Category Product</h2>

            <!-- Search Info -->
            <?php if (isset($_GET['q']) && !empty($_GET['q'])): ?>
                <div class="search-info">
                    <p>
                        Search results for: <strong>"<?php echo htmlspecialchars($_GET['q']); ?>"</strong>
                        <a href="/?r=category" class="clear-search">
                            <i class="fas fa-times"></i> Clear Search
                        </a>
                    </p>
                    <p class="result-count">Found <?php echo count($categories); ?> category(ies)</p>
                </div>
            <?php endif; ?>

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

    <?php
    
    $hasError   = isset($_SESSION['error']);
    $hasInfo    = isset($_SESSION['info']);
    ?>

    <script>
    $(function () {
        if (<?= json_encode($hasError) ?>) {
            $('#errorNotification').addClass('show');
            setTimeout(() => $('#errorNotification').removeClass('show'), 4000);
        }
        if (<?= json_encode($hasInfo) ?>) {
            $('#infoNotification').addClass('show');
            setTimeout(() => $('#infoNotification').removeClass('show'), 4000);
        }
    });

    function closeNotification(id) {
        document.getElementById(id)?.classList.remove('show');
    }

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
    </script>


</body>
</html>
<?php
unset($_SESSION['error'], $_SESSION['info']);
?>
