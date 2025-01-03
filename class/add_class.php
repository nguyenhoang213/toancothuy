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

// Xử lý dữ liệu khi form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nhận dữ liệu từ form
    $tenLop = $_POST['TenLop'];
    $tinhTrang = $_POST['TinhTrang'];
    $phanlop = $_POST['PhanLop'];
    $ngaytao = $Date = date("Y-m-d");
    $tinhphi = $_POST['TinhPhi'];
    $hocphi = str_replace('.', '', $_POST['HocPhi']);
    if ($tinhphi == 0) {
        $hocphi = 0;
    }

    // Kiểm tra xem các trường có rỗng không
    if (empty($tenLop)) {
        echo '<script>alert("Tên lớp không được để trống!");</script>';
    } else {
        // Kiểm tra tên lớp có trùng không
        $checkSql = "SELECT * FROM lop WHERE TenLop like '%$tenLop%'";
        $result = $conn->query($checkSql);

        if ($result->num_rows > 0) {
            // Nếu có lớp với tên này tồn tại, hiển thị thông báo lỗi
            echo '<script>
            alert("Tên lớp đã tồn tại! Vui lòng chọn tên khác.");
            window.history.back();
            </script>';
        } else {
            // Nếu tên lớp không trùng, thêm lớp mới
            $sql = "INSERT INTO lop (TenLop, TinhTrang, NgayTao, PhanLop, TinhPhi, HocPhi) VALUES ('$tenLop', '$tinhTrang','$ngaytao','$phanlop','$tinhphi','$hocphi')";
            if ($conn->query($sql) === TRUE) {
                echo '<script> 
                alert("Thêm lớp thành công!");
                window.location.href="../class/class_list.php";
                </script>';
            } else {
                echo '<script>
                alert("Lỗi: Không thể thêm lớp do lỗi hệ thống!");
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
    <title>Thêm Lớp Mới</title>
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
        <h1>Thêm Lớp Mới</h1>
        <form action="" method="POST">
            <div class="form-group">
                <label for="TenLop">Tên Lớp:</label>
                <input type="text" id="TenLop" name="TenLop" required>
            </div>

            <div class="form-group">
                <label for="TinhTrang">Tình Trạng:</label>
                <select id="TinhTrang" name="TinhTrang">
                    <option value="1">Đang hoạt động</option>
                    <option value="0">Đã dừng</option>
                </select>
            </div>

            <div class="form-group">
                <label for="PhanLop">Khu vực:</label>
                <select id="PhanLop" name="PhanLop">
                    <option value="1">Nguyễn Tất Thành</option>
                    <option value="2">An Bình</option>
                </select>
            </div>

            <div class="form-group">
                <label for="TinhPhi">Thu học phí:</label>
                <select id="TinhPhi" name="TinhPhi" onchange="toggleInputField()">
                    <option value="0">Không </option>
                    <option value="1">Có </option>
                </select>
            </div>

            <div class="form-group" id="extraInputField" style='display: none;'>
                <label for="HocPhi">Học phí 1 buổi:</label>
                <input type="text" id="HocPhi" name="HocPhi">
            </div>

            <div style="text-align: center">
                <button type="submit" class="submit-btn">Thêm lớp</button>
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