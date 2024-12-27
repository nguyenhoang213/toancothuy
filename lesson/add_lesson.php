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

if (isset($_GET['MaLop'])) {
    $maLop = $_GET['MaLop'];
    $class_info = $conn->query("SELECT * FROM lop WHERE MaLop = $maLop")->fetch_assoc();
} else {
    echo '<script>
        alert("Không tìm thấy mã lớp!");
        window.location.href="../class/class_list.php";
        </script>';
    exit();
}

// Xử lý dữ liệu khi form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nhận dữ liệu từ form
    $ngay = $_POST['Ngay'];
    $tenBai = $_POST['TenBai'];
    $dapAn = trim($_POST['DapAn']);

    // Kiểm tra xem các trường có rỗng không
    if (empty($ngay) || empty($tenBai)) {
        echo '<script>alert("Vui lòng điền đầy đủ thông tin buổi học!");</script>';
    } else {
        // Chèn dữ liệu buổi học vào bảng
        $sql = "INSERT INTO buoihoc (MaLop, Ngay, TenBai, DapAn) VALUES ('$maLop', '$ngay', '$tenBai', '$dapAn')";

        if ($conn->query($sql)) {
            echo '<script>
                alert("Thêm buổi học thành công!");
                window.location.href="../lesson/lesson_list.php?id=' . $maLop . '";
                </script>';
        } else {
            echo '<script>alert("Lỗi: Không thể thêm buổi học do lỗi hệ thống!");</script>';
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
    <title>Thêm Buổi Học Mới Lớp <?php echo $class_info['TenLop'] ?></title>
    <link rel="stylesheet" href="../assets/css/admin-navigation.css">
    <link rel="stylesheet" href="../assets/css/admin-statistical.css">

    <style>
        @media screen and (min-width: 600px) {
            .content {
                margin-left: 250px;
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

        .content {
            text-align: left;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="date"],
        textarea {
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
        <h1 style="margin: 20px 0">Thêm Buổi Học Mới <?php echo $class_info['TenLop'] ?></h1>
        <form action="" method="POST">
            <div class="form-group">
                <label for="Ngay">Ngày:</label>
                <input type="date" id="Ngay" name="Ngay" required>

                <label for="TenBai">Tên Bài:</label>
                <input type="text" id="TenBai" name="TenBai">

                <label for="DapAn">Đáp Án (link):</label>
                <textarea id="DapAn" name="DapAn" rows="4"></textarea>
            </div>

            <div style="text-align: center">
                <button type="submit" class="submit-btn">Thêm buổi học</button>
                <a href="../lesson/lesson_list.php?id=<?php echo $maLop ?>" class="cancel-btn">Hủy</a>
            </div>
        </form>
    </div>
</body>

</html>