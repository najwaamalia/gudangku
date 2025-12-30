<?php
/**
 * View untuk halaman login GudangKu
 * Menampilkan form login dengan validasi dan notifikasi error
 */

// Jangan session_start() di view, sudah dimulai di index.php
if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']); // Hapus pesan setelah ditampilkan
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/assets/login.css"> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <img src="/assets/icon.png" alt="Icon" class="login-icon">
            <h1>GUDANGKU</h1>
            <p class="welcome-text">Welcome! Enter your email/username and password below to sign in.</p>

            <!-- Notifikasi Error -->
            <div class="error-notification" id="error-notification">
                <?php if (isset($error_message)): ?>
                    <?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?>
                <?php endif; ?>
            </div>

            <form method="POST" action="?r=login">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Enter your email/username" required>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            <?php if (isset($error_message)): ?>
                // Tampilkan notifikasi error jika ada
                $('#error-notification').addClass('show');
                setTimeout(function() {
                    $('#error-notification').removeClass('show'); // Hilangkan setelah 3 detik
                }, 3000);
            <?php endif; ?>
        });
    </script>
</body>
</html>