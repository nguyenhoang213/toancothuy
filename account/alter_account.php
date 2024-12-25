<?php
include("../connection.php"); // Kết nối CSDL
session_start();
include("../side_nav.php");

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!$_SESSION['uname'])
    echo '
    <script>
        window.location.href="../index.php";
    </script>';

// Lấy ID tài khoản từ URL để xác định lớp cần chỉnh sửa
if (isset($_GET['MaAdmin'])) {
    $maAdmin = $_GET['MaAdmin'];

    // Truy vấn lấy thông tin lớp hiện tại từ CSDL
    $sql = "SELECT * FROM admin WHERE MaAdmin = '$maAdmin'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $class = $result->fetch_assoc();
    } else {
        echo '<script>
        alert("Tài khoản không tồn tại!");
        window.location.href="../account/list_account.php";
        </script>';
        exit();
    }
} else {
    echo '<script>
        alert("Không tìm thấy mã tài khoản để chỉnh sửa!");
        window.location.href="../account/list_account.php";
    </script>';
    exit();
}

// Xử lý dữ liệu khi form được gửi để cập nhật lớp
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['UserName'];
    $ten = $_POST['Ten'];
    $password = $_POST['PassWord'];
    $phanquyen = $_POST['PhanQuyen'];

    if (empty($ten) || empty($username) || empty($password) || empty($phanquyen)) {
        echo '<script>alert("Vui lòng điền đầy đủ thông tin!");</script>';
    } else {
        // Kiểm tra trùng tên tài khoản
        $checkSql = "SELECT * FROM admin WHERE UserName = '$username' and MaAdmin != '$maAdmin'";
        $result = $conn->query($checkSql);

        if ($result->num_rows > 0) {
            echo '<script>
            alert("Tên tài khoản đã tồn tại! Vui lòng chọn tên khác.");
            window.history.back();
            </script>';
        } else {
            // Cập nhật thông tin lớp trong cơ sở dữ liệu
            $sql = "UPDATE admin SET UserName = '$username', Ten = '$ten', PhanQuyen = '$phanquyen', PassWord = '$password' WHERE MaAdmin = '$maAdmin'";
            if ($conn->query($sql) === TRUE) {
                echo '<script>
                alert("Cập nhật lớp thành công!");
                window.location.href="../account/list_account.php";
                </script>';
            } else {
                echo '<script>
                alert("Lỗi: Không thể cập nhật lớp do lỗi hệ thống!");
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
    <title>Chỉnh sửa tài khoản</title>
    <link rel="stylesheet" href="../assets/css/admin-navigation.css">
    <link rel="stylesheet" href="../assets/css/admin-statistical.css">
    <style>
        @media screen and (min-width: 600px) {
            .content {
                margin-left: 240px;
                width: 80%;
                padding: 40px;
            }
        }

        @media screen and (max-width: 600px) {
            .content {
                margin-left: 15px;
                width: 90%;
                padding: 40px;
            }
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
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
        <h1>Chỉnh sửa tài khoản</h1>
        <form action="" method="POST">
            <!-- Tên Lớp -->
            <div class="form-group">
                <label for="UserName">Username:</label>
                <input type="text" id="UserName" name="UserName"
                    value="<?php echo htmlspecialchars($class['UserName']); ?>" required>
            </div>

            <!-- Tên Người dùng -->
            <div class="form-group">
                <label for="Ten">Tên người dùng:</label>
                <input type="text" id="Ten" name="Ten" value="<?php echo htmlspecialchars($class['Ten']); ?>" required>
            </div>

            <!-- Mật khẩu -->
            <div class="form-group">
                <label for="PassWord">Mật khẩu:</label>
                <input type="text" id="PassWord" name="PassWord"
                    value="<?php echo htmlspecialchars($class['PassWord']); ?>" required>
            </div>

            <div class="form-group">
                <label for="PhanQuyen">Khu vực:</label>
                <select id="PhanQuyen" name="PhanQuyen">
                    <option value="Admin" <?php echo $class['PhanQuyen'] == "Admin" ? 'selected' : ''; ?>>Admin</option>
                    <option value="Super Admin" <?php echo $class['PhanQuyen'] == "Super Admin" ? 'selected' : ''; ?>>
                        Super Admin
                    </option>
                    </option>
                </select>
            </div>

            <!-- Nút Cập Nhật -->
            <button type="submit" class="submit-btn">Cập nhật tài khoản</button>
            <a href="../account/list_account.php" class="cancel-btn">Hủy</a>
        </form>
    </div>
</body>

</html>