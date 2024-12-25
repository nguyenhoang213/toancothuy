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

// Kiểm tra mã lớp
if ($_GET['MaLop']) {
    $maLop = $_GET['MaLop'];
} else {
    echo '<script>
    alert("Không tìm thấy mã lớp để chỉnh sửa!");
    window.location.href="../class/class_list.php";
    </script>';
}

// Lấy thông tin buổi học cần chỉnh sửa
if (isset($_GET['MaBuoiHoc'])) {
    $maBuoiHoc = $_GET['MaBuoiHoc'];

    $sql = "SELECT * FROM buoihoc WHERE MaBuoiHoc = '$maBuoiHoc'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $lesson = $result->fetch_assoc();
    } else {
        echo '<script>
        alert("Buổi học không tồn tại!");
        window.location.href="../lesson/lesson_list.php?id=' . $maLop . '";
        </script>';
        exit();
    }
} else {
    echo '<script>
    alert("Không tìm thấy mã buổi học để chỉnh sửa!");
    window.location.href="../lesson/lesson_list.php?id=' . $maLop . '";
    </script>';
    exit();
}

// Xử lý khi form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ngay = $_POST['Ngay'];
    $tenBai = $_POST['TenBai'];
    $dapAn = $_POST['DapAn'];

    if (empty($ngay) || empty($tenBai)) {
        echo '<script>alert("Ngày và Tên bài không được để trống!");</script>';
    } else {
        $sql = "UPDATE buoihoc SET Ngay = '$ngay', TenBai = '$tenBai', DapAn = '$dapAn' WHERE MaBuoiHoc = '$maBuoiHoc'";
        if ($conn->query($sql) === TRUE) {
            echo '<script>
            alert("Cập nhật buổi học thành công!");
            window.location.href="../lesson/lesson_list.php?id=' . $maLop . '";
            </script>';
        } else {
            echo '<script>
            alert("Lỗi: Không thể cập nhật buổi học do lỗi hệ thống!");
            </script>';
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
    <title>Chỉnh sửa Buổi học</title>
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
        input[type="date"] {
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
        <h1>Chỉnh sửa Buổi học</h1>
        <form action="" method="POST">
            <div class="form-group">
                <label for="Ngay">Ngày:</label>
                <input type="date" id="Ngay" name="Ngay" value="<?php echo htmlspecialchars($lesson['Ngay']); ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="TenBai">Tên Bài:</label>
                <input type="text" id="TenBai" name="TenBai" value="<?php echo htmlspecialchars($lesson['TenBai']); ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="DapAn">Đáp Án:</label>
                <input type="text" id="DapAn" name="DapAn" value="<?php echo htmlspecialchars($lesson['DapAn']); ?>">
            </div>

            <button type="submit" class="submit-btn">Cập nhật buổi học</button>
            <a href="../lesson/lesson_list.php?id=<?php echo $maLop ?>" class="cancel-btn">Hủy</a>
        </form>
    </div>
</body>

</html>