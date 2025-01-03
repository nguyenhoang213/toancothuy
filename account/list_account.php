<?php
include("../connection.php"); // Kết nối CSDL
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!$_SESSION['uname'])
    echo '
    <script>
        window.location.href="../index.php";
    </script>';

$username = $_SESSION['uname'];
$Admin_info = $conn->query("SELECT * FROM admin WHERE UserName = '$username'")->fetch_assoc();
if ($Admin_info['PhanQuyen'] != "Super Admin") {
    echo '
    <script>
        alert("Bạn không đủ quyền!");
        window.history.back();
    </script>';
}

include("../side_nav.php"); // Thanh điều hướng



// Truy vấn dữ liệu từ bảng admin
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$phanquyen = isset($_GET['phanquyen']) ? $_GET['phanquyen'] : '';

// Xây dựng truy vấn SQL động dựa trên các tham số tìm kiếm
$sql = "SELECT * FROM admin WHERE 1=1";

if (!empty($search_query)) {
    $sql .= " AND UserName LIKE '%$search_query%'";
}

if ($phanquyen !== '') {
    $sql .= " AND PhanQuyen = '$phanquyen'";
}

$sql .= " ORDER BY PhanQuyen DESC, UserName"; // Sắp xếp kết quả

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <link rel="icon" type="image/x-icon" href="../assets/image/logo.png">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài khoản</title>
    <link rel="stylesheet" href="../assets/css/admin-statistical.css">
    <link rel="stylesheet" href="../assets/css/admin-navigation.css">
    <link rel="stylesheet" href="../assets/font/themify-icons/themify-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .submit-btn {
            background-color: #007fd5;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
        }

        .submit-btn:hover {
            background-color: #005bb5;
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

        .add_button:hover {
            background-color: #218838;
        }

        .form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .form input,
        .form select {
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        @media (max-width: 600px) {
            table th,
            table td {
                font-size: 14px;
                padding: 8px;
            }

            .add_button {
                font-size: 14px;
                padding: 8px 16px;
            }

            .form input,
            .form select {
                font-size: 14px;
                width: 100%;
            }

            .submit-btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="content" style="padding: 40px;">
        <h1 style="padding-bottom: 10px">Quản lý tài khoản</h1>
        <!-- Nút thêm tài khoản mới -->
        <a href="../account/add_account.php" class="add_button">Thêm tài khoản mới</a> <br>

        <!-- Tìm kiếm -->
        <form class="form" method="GET" action="" style="margin: 20px 0;">
            <label for="">Tìm kiếm</label>
            <input type="text" name="search" placeholder="Nhập tên tài khoản"
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                style="padding: 8px; width: 250px; font-size: 16px">

            <select name="phanquyen" style="padding: 8px; width: 250px; font-size: 16px">
                <option value="" <?php echo $phanquyen === '' ? 'selected' : ''; ?>>Tất cả quyền</option>
                <option value="Admin" <?php echo $phanquyen === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="Super Admin" <?php echo $phanquyen === 'Super Admin' ? 'selected' : ''; ?>>Super Admin
                </option>
            </select>

            <button type="submit" class="submit-btn" style="padding: 8px; font-size: 16px">Tìm kiếm</button>
        </form>

        <!-- Bảng danh sách các tài khoản -->
        <table class="class_list" style="width: 100%; margin-top: 10px">
            <tr>
                <th>Mã tài khoản</th>
                <th>Tên tài khoản</th>
                <th>Tên người sử dụng</th>
                <th>Mật khẩu</th>
                <th>Phân quyền</th>
                <th>Chỉnh sửa</th>
                <th>Xóa</th>
            </tr>

            <?php
            // Kiểm tra xem có dữ liệu tài khoản nào không
            if ($result->num_rows > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<td>' . $row['MaAdmin'] . '</td>';
                    echo '<td>' . $row['UserName'] . '</td>';
                    echo '<td>' . $row['Ten'] . '</td>';
                    echo '<td>' . $row['PassWord'] . '</td>';
                    echo '<td>' . $row['PhanQuyen'] . '</td>';

                    // Tùy chọn chỉnh sửa tài khoản
                    echo "<td><a href='../account/alter_account.php?MaAdmin=" . $row['MaAdmin'] . "'>Chỉnh sửa</a></td>";

                    // Tùy chọn xóa tài khoản
                    echo "<td><a href='#' onclick=\"confirmDelete('" . $row['MaAdmin'] . "'); return false;\">Xóa</a></td>";

                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="7">Không có tài khoản nào</td></tr>';
            }
            ?>

        </table>
    </div>

    <!-- Script để xác nhận trước khi xóa -->
    <script>
        function confirmDelete(maAdmin) {
            const adminPassword = prompt("Vui lòng nhập mật khẩu admin để xóa tài khoản:");
            if (adminPassword !== null) {
                // Gửi yêu cầu xóa qua POST bằng fetch API
                fetch('../account/delete_account.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ MaAdmin: maAdmin, password: adminPassword })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Tài khoản đã được xóa thành công!");
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
</body>

</html>