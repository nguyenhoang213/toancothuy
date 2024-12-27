<?php
include("connection.php");
session_start();

// Kiểm tra nếu đã đăng nhập, chuyển đến trang admin
if (isset($_SESSION['uname'])) {
    echo '<script>
        window.location.href="./admin/admin.php";
    </script>';
}

// Kiểm tra nếu có cookie đăng nhập
if (isset($_COOKIE['login_token'])) {
    $token = $_COOKIE['login_token'];
    // Truy vấn cơ sở dữ liệu để kiểm tra token hợp lệ
    $sql = "SELECT * FROM login_tokens WHERE token = '$token'";
    $query = mysqli_query($conn, $sql);
    $num_rows = mysqli_num_rows($query);

    if ($num_rows > 0) {
        // Nếu token hợp lệ, đặt session và chuyển hướng
        $row = mysqli_fetch_assoc($query);
        $_SESSION['uname'] = $row['username'];
        echo '<script>
                window.location.href="./admin/admin.php";
            </script>';
        exit();
    }
}

// Kiểm tra khi người dùng nhấn nút đăng nhập
if (isset($_POST['login-button']) || isset($_POST['uname']) || isset($_POST['psw'])) {
    $username = $_POST["uname"];
    $password = $_POST["psw"];

    // Bảo mật chuỗi nhập vào
    $username = strip_tags($username);
    $username = addslashes($username);
    $password = strip_tags($password);
    $password = addslashes($password);

    // Truy vấn kiểm tra username và password
    $sql = "SELECT * FROM admin WHERE Username = '$username' AND Password = '$password'";
    $query = mysqli_query($conn, $sql);
    $num_rows = mysqli_num_rows($query);

    if ($num_rows == 0) {
        // Thông báo nếu sai tên đăng nhập hoặc mật khẩu
        echo '<script>alert("Tên đăng nhập hoặc mật khẩu không đúng ! Vui lòng kiểm tra lại.")</script>';
    } else {
        // Đăng nhập thành công, thiết lập session
        $_SESSION['uname'] = $username;
        $ip = POST_client_ip();
        $date = date('Y-m-d H:i:s');

        // Lưu thông tin đăng nhập vào lịch sử
        $login = "INSERT INTO login_htr (username, ip_address, login_time) VALUES ('$username', '$ip', '$date')";
        $conn->query($login);

        // Kiểm tra nếu người dùng chọn "Lưu Mật Khẩu"
        if (isset($_POST['remember'])) {
            // Tạo token ngẫu nhiên
            $token = bin2hex(random_bytes(16));

            // Lưu token vào cơ sở dữ liệu
            $saveToken = "INSERT INTO login_tokens (username, token) VALUES ('$username', '$token')";
            $conn->query($saveToken);

            // Lưu token vào cookie, hết hạn sau 30 ngày
            setcookie("login_token", $token, time() + (30 * 24 * 60 * 60), "/"); // 30 ngày
        }

        // Chuyển hướng đến trang admin
        echo '<script>
                window.location.href="./admin/admin.php";
            </script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="./assets/image/logoTH.jpg">
    <title>Đăng Nhập</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/login_style.css">
    <link rel="stylesheet" href="./assets/css/header.css">
    <link rel="stylesheet" href="./assets/css/footer.css">
    <link rel="stylesheet" href="./assets/font/themify-icons/themify-icons.css">
    <script src="https://kit.fontawesome.com/8fcd74b091.js" crossorigin="anonymous"></script>
</head>

<body>
    <div id="header">
        <div class="contact_phone">
            <p><i class="fa-solid fa-phone icon"></i> 0918083884</p>
            <p style="font-size: 20px;">|</p>
            <p><i class="ti-email icon"></i> lethuyntt0708@gmail.com</p>
        </div>
        <div class="network_contact">
            <a href="https://www.facebook.com/thuyvytrinhkhue?mibextid=LQQJ4d"><i class="ti-facebook icon"></i></a>
            <a><i class="ti-google icon"></i></a>
            <a><i class="ti-sharethis icon"></i></a>
        </div>
    </div>
    <div id="slider">
        <div class="logo">
            <a href="./index.php">
                <img src="assets/image/logoTH.jpg" alt="logo">
            </a>
            <a href="./index.php">
                <h1>Lê Thị Thanh Thủy</h1>
            </a>
            <button class="menu_button" onclick="toggleMenu()"><i class="ti-view-list icon"></i></button>
        </div>
        <div class="menu" id="menu">
            <a href="./index.php">Trang chủ</a>
            <a href="./index.php#content">Tra cứu</a>
            <a href="./login.php">Quản trị</a>
            <a href="./index.php#footer">Liên hệ</a>
        </div>
    </div>
    <div style="background-color:#b7ede8; position: relative;" id="content">
        <div class="login">
            <h1>ĐĂNG NHẬP</h1>
            <form action="" method="POST">
                <div style="position: absolute; margin-left: 66px;margin-top: 11px;font-size: 18px; border:2px solid black ; border-radius: 50px; height: 26px;
    width: 26px;"><i style="margin-left: 5px; margin-top: 4px" class="fa-solid fa-user"></i>
                </div>
                <input name="uname" type="text" placeholder="TÀI KHOẢN" class="text-input" required> <br>
                <div style="position: absolute; margin-left: 66px;margin-top: 11px;font-size: 18px; border:2px solid black ; border-radius: 50px; height: 26px;
    width: 26px;"><i style="margin-left: 5px; margin-top: 4px" class="fa-solid fa-lock"></i>
                </div>
                <input name="psw" type="password" placeholder="MẬT KHẨU" class="text-input" required> <br>
                <input name="remember" type="checkbox" class="checkbox"> Lưu Mật Khẩu <br>
                <div class="button-list">
                    <button style="background-color: aqua;color: rgb(0, 0, 0);">QUÊN MẬT KHẨU</button>
                    <button name="login-button" style="background-color: rgb(255, 90, 90);color: rgb(0, 0, 0);">ĐĂNG
                        NHẬP</button>
                </div>
            </form>
        </div>
        <?php
        include('footer.php')
            ?>
</body>
<script>
    function toggleMenu() {
        const menu = document.getElementById("menu");
        const logo = document.querySelector(".logo");

        // Chuyển đổi trạng thái hiển thị của menu
        if (menu.style.display === "block") {
            menu.style.display = "none"; // Ẩn menu
            logo.style.display = "flex"; // Hiện lại logo và tên
        } else {
            menu.style.display = "block"; // Hiện menu
            logo.style.display = "none"; // Ẩn logo và tên
        }
    }
</script>

</html>

<?php
function POST_client_ip()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
?>