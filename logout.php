<?php
session_start();

// Xóa tất cả các phiên
session_unset();
session_destroy();

// Xóa cookie "login_token" nếu có
if (isset($_COOKIE['login_token'])) {
    setcookie('login_token', '', time() - 3600, '/'); // Hết hạn cookie
}

echo '<script>
        window.location.href="./login.php";
    </script>';
exit();
?>