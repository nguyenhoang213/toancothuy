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

// Lấy ID lớp học từ URL để xác định lớp cần chỉnh sửa
if (isset($_GET['MaLop'])) {
    $maLop = $_GET['MaLop'];

    // Truy vấn lấy thông tin lớp hiện tại từ CSDL
    $sql = "SELECT * FROM lop WHERE MaLop = '$maLop'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $class = $result->fetch_assoc();
    } else {
        echo '<script>
        alert("Lớp không tồn tại!");
        window.location.href="../class/class_list.php";
        </script>';
        exit();
    }
} else {
    echo '<script>
    alert("Không tìm thấy mã lớp để chỉnh sửa!");
    window.location.href="../class/class_list.php";
    </script>';
    exit();
}

// Xử lý dữ liệu khi form được gửi để cập nhật lớp
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenLop = $_POST['TenLop'];
    $tinhTrang = $_POST['TinhTrang'];
    $phanlop = $_POST['PhanLop'];
    $tinhphi = $_POST['TinhPhi'];
    $hocphi = str_replace('.', '', $_POST['HocPhi']);
    if ($tinhphi == 0) {
        $hocphi = 0;
    }

    if (empty($tenLop)) {
        echo '<script>alert("Tên lớp không được để trống!");</script>';
    } else {
        // Kiểm tra trùng tên lớp (không xét lớp hiện tại)
        $checkSql = "SELECT * FROM lop WHERE TenLop = '$tenLop' AND MaLop != '$maLop'";
        $result = $conn->query($checkSql);

        if ($result->num_rows > 0) {
            echo '<script>
            alert("Tên lớp đã tồn tại! Vui lòng chọn tên khác.");
            window.history.back();
            </script>';
        } else {
            // Cập nhật thông tin lớp trong cơ sở dữ liệu
            $sql = "UPDATE lop SET TenLop = '$tenLop', TinhTrang = '$tinhTrang', PhanLop = '$phanlop', TinhPhi = '$tinhphi', HocPhi = '$hocphi' WHERE MaLop = '$maLop'";
            if ($conn->query($sql) === TRUE) {
                echo '<script>
                alert("Cập nhật lớp thành công!");
                window.location.href="../class/class_list.php";
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
    <title>Chỉnh sửa Lớp</title>
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
        <h1>Chỉnh sửa Lớp</h1>
        <form action="" method="POST">
            <!-- Tên Lớp -->
            <div class="form-group">
                <label for="TenLop">Tên Lớp:</label>
                <input type="text" id="TenLop" name="TenLop" value="<?php echo htmlspecialchars($class['TenLop']); ?>"
                    required>
            </div>

            <!-- Tình Trạng -->
            <div class="form-group">
                <label for="TinhTrang">Tình Trạng:</label>
                <select id="TinhTrang" name="TinhTrang">
                    <option value="1" <?php echo $class['TinhTrang'] == 1 ? 'selected' : ''; ?>>Đang hoạt động</option>
                    <option value="0" <?php echo $class['TinhTrang'] == 0 ? 'selected' : ''; ?>>Đã dừng</option>
                </select>
            </div>

            <div class="form-group">
                <label for="PhanLop">Khu vực:</label>
                <select id="PhanLop" name="PhanLop">
                    <option value="1" <?php echo $class['PhanLop'] == 1 ? 'selected' : ''; ?>>Nguyễn Tất Thành
                    </option>
                    <option value="2" <?php echo $class['PhanLop'] == 2 ? 'selected' : ''; ?>>An Bình
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="TinhPhi">Thu học phí:</label>
                <select id="TinhPhi" name="TinhPhi" onchange="toggleInputField()">
                    <option value="0" <?php echo $class['TinhPhi'] == 0 ? 'selected' : ''; ?>>Không
                    </option>
                    <option value="1" <?php echo $class['TinhPhi'] == 1 ? 'selected' : ''; ?>>Có
                    </option>
                </select>
            </div>

            <div class="form-group" id="extraInputField" <?php echo $class['TinhPhi'] == 0 ? "style='display: none;'" : "" ?>>
                <label for="HocPhi">Học phí 1 buổi:</label>
                <input type="text" id="HocPhi" name="HocPhi"
                    value="<?php echo number_format($class['HocPhi'], 0, ',', '.'); ?>">
            </div>

            <!-- Nút Cập Nhật -->
            <div style="text-align: center">
                <button type="submit" class="submit-btn">Cập nhật lớp</button>
                <a href="../class/class_list.php" class="cancel-btn">Hủy</a>
            </div>
        </form>
    </div>
</body>

</html>

<script>
    function toggleInputField() {
        const selectField = document.getElementById('TinhPhi');
        const extraInputField = document.getElementById('extraInputField');

        if (selectField.value === "1") {
            extraInputField.style.display = "block"; // Hiển thị input nếu chọn "Có"
        } else {
            extraInputField.style.display = "none"; // Ẩn input nếu chọn "Không"
        }
    }

    document.getElementById('HocPhi').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, ''); // Loại bỏ ký tự không phải số
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Thêm dấu . vào nhóm 3 chữ số
        e.target.value = value;
    });
</script>