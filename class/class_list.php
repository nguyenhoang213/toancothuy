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


// Truy vấn dữ liệu từ bảng lop
// $sql = "SELECT * FROM lop ORDER BY NgayTao desc, TinhTrang desc, TenLop";

$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$phanlop = isset($_GET['phanlop']) ? $_GET['phanlop'] : '';
$tinhtrang = isset($_GET['tinhtrang']) ? $_GET['tinhtrang'] : '';

// Xây dựng truy vấn SQL động dựa trên các tham số tìm kiếm
$sql = "SELECT * FROM lop WHERE 1=1"; // Khởi tạo với điều kiện luôn đúng

if (!empty($search_query)) {
    $sql .= " AND TenLop LIKE '%$search_query%'";
}

if ($phanlop !== '') {
    $sql .= " AND PhanLop = '$phanlop'";
}

if ($tinhtrang !== '') {
    $sql .= " AND TinhTrang = '$tinhtrang'";
}

$sql .= " ORDER BY TinhTrang DESC, TenLop"; // Sắp xếp kết quả

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <link rel="icon" type="image/x-icon" href="../assets/image/logo.png">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý lớp</title>
    <link rel="stylesheet" href="../assets/css/admin-statistical.css">
    <link rel="stylesheet" href="../assets/css/admin-navigation.css">
    <link rel="stylesheet" href="../assets/font/themify-icons/themify-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
        }

        .form label {
            flex: 1 1 100%;
            font-size: 16px;
        }

        .form input,
        .form select,
        .form button {
            flex: 1 1 calc(33% - 10px);
            padding: 8px;
            font-size: 16px;
            margin: 5px 0;
        }

        .form button {
            background-color: #007fd5;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form button:hover {
            background-color: #004ed5;
        }

        .add_button {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 16px;
            border-radius: 4px;
            margin: 20px 0;
        }

        /* Mobile View */
        @media screen and (max-width: 600px) {

            .form input,
            .form select,
            .form button {
                flex: 1 1 100%;
            }

            .add_button {
                display: inline-block;
                background-color: #28a745;
                color: white;
                padding: 10px 20px;
                text-decoration: none;
                font-size: 16px;
                border-radius: 4px;
                margin: 20px 0;
            }
        }
    </style>
</head>

<body>
    <div class="content">
        <h1 style="padding-top: 30px">Quản lý lớp</h1>
        <!-- Nút thêm lớp mới -->
        <a href="../class/add_class.php" class="add_button">Thêm lớp mới</a> <br>

        <!-- Tìm kiếm -->
        <form class="form" method="GET" action="" style="margin: 20px 0;">
            <label for="">Tìm kiếm</label>
            <input type="text" name="search" placeholder="Nhập tên lớp"
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                style="padding: 8px; width: 250px; font-size: 16px">

            <select name="phanlop" style="padding: 8px; width: 250px; font-size: 16px">
                <option value="" <?php echo $phanlop === '' ? 'selected' : ''; ?>>Tất cả khu vực</option>
                <option value="1" <?php echo $phanlop === '1' ? 'selected' : ''; ?>>Nguyễn Tất Thành</option>
                <option value="2" <?php echo $phanlop === '2' ? 'selected' : ''; ?>>An Bình</option>
            </select>

            <select name="tinhtrang" style="padding: 8px; width: 250px; font-size: 16px">
                <option value="" <?php echo $tinhtrang === '' ? 'selected' : ''; ?>>Tất cả tình trạng</option>
                <option value="1" <?php echo $tinhtrang === '1' ? 'selected' : ''; ?>>Đang học</option>
                <option value="0" <?php echo $tinhtrang === '0' ? 'selected' : ''; ?>>Đã nghỉ</option>
            </select>

            <button type="submit" class="submit-btn" style="padding: 8px; font-size: 16px">Tìm kiếm</button>
        </form>

        <!-- Bảng danh sách các lớp -->
        <table class="class_list" style="width: 100%; margin: 10px 0">
            <tr>
                <th>Mã Lớp</th>
                <th>Tên Lớp</th>
                <th>Ngày Tạo</th>
                <th>Khu vực</th>
                <th>Học phí</th>
                <th>Tình Trạng</th>
                <th>Chỉnh sửa</th>
                <th>Xóa</th>
            </tr>

            <?php
            // Kiểm tra xem có dữ liệu lớp nào không
            if ($result->num_rows > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<td>' . $row['MaLop'] . '</td>';
                    echo '<td style="text-align: left; padding-left: 2%"><a href="../student/student_list.php?id=' . $row['MaLop'] . '">' . $row['TenLop'] . '</a> </td>';


                    if ($row['NgayTao']) {
                        echo '<td>' . $row['NgayTao'] . '</td>';
                    } else {
                        echo '<td> Không xác định </td>';
                    }

                    if ($row['PhanLop'] == 0) {
                        echo '<td> Đông Anh </td>';
                    } else if ($row['PhanLop'] == 1) {
                        echo '<td> Nguyễn Tất Thành </td>';
                    } else if ($row['PhanLop'] == 2) {
                        echo '<td> An Bình </td>';
                    } else {
                        echo '<td> Không xác định </td>';
                    }

                    if ($row['TinhPhi'] == 1) {
                        echo '<td>' . number_format($row['HocPhi'], 0, ',', '.') . ' VNĐ</td>';
                    } else {
                        echo '<td>Không</td>';
                    }

                    // Kiểm tra tình trạng của lớp (0: Đã dừng, 1: Đang hoạt động)
                    if ($row['TinhTrang'] == 0) {
                        echo '<td>Đã nghỉ</td>';
                    } else {
                        echo '<td>Đang học</td>';
                    }

                    // Tùy chọn chỉnh sửa lớp
                    echo "<td><a href='../class/alter_class.php?MaLop=" . $row['MaLop'] . "'>Chỉnh sửa</a></td>";

                    $username = $_SESSION['uname'];
                    $Admin_info = $conn->query("SELECT * FROM admin WHERE UserName = '$username'")->fetch_assoc();
                    if ($Admin_info['PhanQuyen'] == "Super Admin") {
                        echo "<td><a href='#' onclick=\"confirmDelete('" . $row['MaLop'] . "'); return false;\">Xóa</a></td>";
                    } else {
                        echo "<td><a href='#' onclick='cantDelete()'>Xóa</a></td>";
                    }

                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="7">Không có lớp nào</td></tr>';
            }
            ?>

        </table>
    </div>

    <!-- Script để xác nhận trước khi xóa -->
    <script>
        function confirmDelete() {
            return confirm("Bạn có chắc chắn muốn xóa lớp này?");
        }

        function cantDelete() {
            alert("Bạn không đủ quyền thực hiện chức năng này!");
        }
    </script>
</body>

</html>

<script>
    function confirmDelete(maLop) {
        const adminPassword = prompt("Vui lòng nhập mật khẩu admin để xóa lớp:");
        if (adminPassword !== null) {
            // Gửi yêu cầu xóa qua POST bằng fetch API
            fetch('../class/delete_class.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ MaLop: maLop, password: adminPassword })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Lớp đã được xóa thành công!");
                        location.reload(); // Reload lại trang
                    } else {
                        alert(data.message || "Mật khẩu không đúng hoặc có lỗi xảy ra.");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Đã xảy ra lỗi khi thực hiện xóa.");
                });
        }
    }
</script>