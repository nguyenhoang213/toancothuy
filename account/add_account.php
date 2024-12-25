<?php
include("../connection.php"); // Kết nối CSDL
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!$_SESSION['uname'])
    echo '
    <script>
        window.location.href="../index.php";
    </script>';

include("../side_nav.php"); // Thanh điều hướng

// Xử lý dữ liệu khi form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nhận dữ liệu từ form
    $username = $_POST['UserName'];
    $name = $_POST['Ten'];
    $password = $_POST['PassWord'];
    $confirmPassword = $_POST['ConfirmPassWord'];
    $role = $_POST['PhanQuyen'];

    // Kiểm tra xem các trường có rỗng không
    if (empty($username) || empty($name) || empty($password) || empty($confirmPassword)) {
        echo '<script>alert("Vui lòng điền đầy đủ thông tin!");</script>';
    } elseif ($password !== $confirmPassword) {
        echo '<script>alert("Mật khẩu và xác nhận mật khẩu không khớp!");</script>';
    } else {
        // Kiểm tra tài khoản có trùng không
        $checkSql = "SELECT * FROM admin WHERE UserName = '$username'";
        $result = $conn->query($checkSql);

        if ($result->num_rows > 0) {
            // Nếu tài khoản với tên này tồn tại, hiển thị thông báo lỗi
            echo '<script>
            alert("Tên tài khoản đã tồn tại! Vui lòng chọn tên khác.");
            window.history.back();
            </script>';
        } else {
            // Nếu tên tài khoản không trùng, thêm tài khoản mới
            $sql = "INSERT INTO admin (UserName, Ten, PassWord, PhanQuyen) VALUES ('$username', '$name', '$password', '$role')";
            if ($conn->query($sql) === TRUE) {
                echo '<script> 
                alert("Thêm tài khoản thành công!");
                window.location.href="../account/list_account.php";
                </script>';
            } else {
                echo '<script>
                alert("Lỗi: Không thể thêm tài khoản do lỗi hệ thống!");
                </script>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <link rel="icon" type="image/x-icon" href="../assets/image/logo.png">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Tài Khoản Mới</title>
    <link rel="stylesheet" href="../assets/css/admin-navigation.css">
    <link rel="stylesheet" href="../assets/css/admin-statistical.css">
    <style>
        @media screen and (min-width: 600px) {
            .content {
                margin-left: 240px;
                width: 80%;
            }
        }

        @media screen and (max-width: 600px) {
            .content {
                margin-left: 15px;
                width: 90%;
            }
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"],
        select {
            font-size: 18px;
            width: 100%;
            padding: 8px;
            margin: 4px 0;
            box-sizing: border-box;
        }

        .submit-btn {
            padding: 10px 20px;
            background-color: #007fd5;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 18px;
        }

        .submit-btn:hover {
            background-color: #004ed5;
        }

        .cancel-btn {
            padding: 10px 20px;
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 18px;
        }

        .cancel-btn:hover {
            background-color: #e53935;
        }
    </style>
</head>

<body>
    <div class="content">
        <h1>Thêm Tài Khoản Mới</h1>
        <form action="" method="POST">
            <div class="form-group">
                <label for="UserName">Tên Tài Khoản:</label>
                <input type="text" id="UserName" name="UserName" required>
            </div>

            <div class="form-group">
                <label for="Ten">Tên Người Sử Dụng:</label>
                <input type="text" id="Ten" name="Ten" required>
            </div>

            <div class="form-group">
                <label for="PassWord">Mật Khẩu:</label>
                <input type="password" id="PassWord" name="PassWord" required>
            </div>

            <div class="form-group">
                <label for="ConfirmPassWord">Xác Nhận Mật Khẩu:</label>
                <input type="password" id="ConfirmPassWord" name="ConfirmPassWord" required>
            </div>

            <div class="form-group">
                <label for="PhanQuyen">Phân Quyền:</label>
                <select id="PhanQuyen" name="PhanQuyen">
                    <option value="Admin">Admin</option>
                    <option value="Super Admin">Super Admin</option>
                </select>
            </div>

            <button type="submit" class="submit-btn">Thêm tài khoản</button>
            <a href="../account/list_account.php" class="cancel-btn">Hủy</a>
        </form>
    </div>
</body>

</html>