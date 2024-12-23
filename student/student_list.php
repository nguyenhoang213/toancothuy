<?php
include("../connection.php"); // Kết nối CSDL
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!$_SESSION['uname'])
    header('Location: https://vatlytruongnghiem.edu.vn/');

include("../side_nav.php"); // Thanh điều hướng

// Lấy ID lớp từ URL
$maLop = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Kiểm tra xem ID lớp có hợp lệ không
if ($maLop <= 0) {
    echo "Lớp không tồn tại!";
    exit;
}

// Tìm kiếm học sinh trong lớp
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = "WHERE pl.MaLop = '$maLop'";
$sort = "ORDER BY Ten, Ho ASC"; // Mặc định sắp xếp
$tinhtrang = "1";

$sort = $_GET['sort'] ?? "";
$school = $_GET['School'] ?? "";
$tinhtrang = $_GET['tinhtrang'] ?? "";

if ($school) {
    $where .= " AND Truong = '$school'";
}
if ($tinhtrang === "all") {
    // Tất cả học sinh trong lớp
    $where .= " AND pl.MaLop = '$maLop'";
} elseif ($tinhtrang === "0") {
    // Học sinh đã nghỉ
    $where .= " AND TinhTrang = 0";
} elseif ($tinhtrang === "1") {
    // Học sinh đang học
    $where .= " AND TinhTrang = 1";
} elseif ($tinhtrang === "2") {
    // Học sinh mới
} elseif ($tinhtrang === "3") {
    // Học sinh chưa có ảnh
    $where .= " AND TinhTrang = 1 AND hs.Anh IS NULL";
}

// Truy vấn dữ liệu học sinh
$sql = "SELECT hs.MaHS, Ho, Ten, Lop, Truong, NgaySinh, Phone, Anh, pl.MaPhanLop FROM hocsinh hs join phanlop pl ON hs.MaHS = pl.MaHS ";
if (!empty($search_query)) {
    $sql .= " AND CONCAT(Ho, ' ', Ten) LIKE '%$search_query%' $where $sort";
} else {
    $sql .= "$where $sort";
}

$result = $conn->query($sql);

// Lấy thông tin lớp học
$class_info = $conn->query("SELECT * FROM lop WHERE MaLop = $maLop")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <link rel="icon" type="image/x-icon" href="../assets/image/logo.png">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý học sinh <?php echo htmlspecialchars($class_info['TenLop']); ?></title>
    <link rel="stylesheet" href="../assets/css/admin-statistical.css">
    <link rel="stylesheet" href="../assets/css/admin-navigation.css">
    <link rel="stylesheet" href="../assets/font/themify-icons/themify-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        table th,
        table td {
            padding: 8px;
            border: 1px solid #0d0d0d;
        }

        table th {
            background-color: #f2f2f2;
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
            border-color: #007fd5;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #004ed5;
            border-color: #004ed5;
        }

        @media screen and (min-width: 600px) {
            .add_button {
                margin: 20px;
                font-size: 24px
            }

            .class_list th,
            tr,
            td {
                font-size: 20px;
            }
        }

        @media screen and (max-width: 600px) {
            h1 {
                margin-top: 10px;
                font-size: 20px
            }

            .add_button {
                margin: 10px;

                font-size: 12px;
            }

            .class_list th,
            tr,
            td {
                font-size: 12px;
            }
        }

        .fixed-button {
            font-size: 25px;
            position: fixed;
            top: 20px;
            /* Cách đáy 20px */
            right: 20px;
            /* Cách phải 20px */
            background-color: #007bff;
            /* Màu nền */
            color: #fff;
            /* Màu chữ */
            border: none;
            border-radius: 50%;
            /* Bo tròn */
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            /* Hiệu ứng đổ bóng */
            cursor: pointer;
            z-index: 1000;
            /* Luôn hiển thị trên cùng */
        }

        .fixed-button:hover {
            background-color: #0056b3;
            /* Màu khi hover */
        }
    </style>
</head>

<body>
    <div class="content">
        <h1 style="padding-bottom: 10px">Quản lý học sinh <?php echo htmlspecialchars($class_info['TenLop']); ?></h1>
        <!-- Nút thêm học sinh mới -->
        <a href="../student/add_student.php?MaLop=<?php echo $maLop; ?>" class="add_button">Thêm học sinh mới</a> <br>

        <!-- Fixed Button -->
        <button class="fixed-button" onclick="avatarStudent()">
            <i class="fa-solid fa-grip"></i>
        </button>

        <script>
            function avatarStudent() {
                window.location.href = "avatar_student.php?id=<?php echo $maLop ?>";
            }
        </script>

        <!-- Tìm kiếm -->
        <form method="GET" action="" style="margin: 20px 0;">
            <input type="hidden" name="id" value="<?php echo $maLop; ?>">
            <label for="">Tìm kiếm</label>
            <input type="text" name="search" placeholder="Nhập tên học sinh"
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                style="padding: 8px; width: 250px; font-size: 16px">
            <select name="School" style="padding: 8px; width: 250px; font-size: 16px">
                <option value="" <?php echo $school === '' ? 'selected' : ''; ?>>Tất cả trường</option>
                <?php
                $sqltruong = "
                    SELECT DISTINCT Truong 
                    FROM hocsinh 
                    JOIN phanlop ON hocsinh.MaHS = phanlop.MaHS 
                    WHERE phanlop.MaLop = '$maLop' AND Truong != '' 
                    ORDER BY Truong ASC
                ";
                $resulttruong = $conn->query($sqltruong);
                while ($row = $resulttruong->fetch_assoc()) {
                    $selected = (isset($school) && $school === $row['Truong']) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($row['Truong']) . "' $selected>" . htmlspecialchars($row['Truong']) . "</option>";
                }
                ?>
            </select>

            <select name="tinhtrang" style="padding: 8px; width: 250px; font-size: 16px">
                <option value="" <?php echo $tinhtrang === '' ? 'selected' : ''; ?>>Tất cả tình trạng</option>
                <option value="1" <?php echo $tinhtrang === '1' ? 'selected' : ''; ?>>Đang học</option>
                <option value="0" <?php echo $tinhtrang === '0' ? 'selected' : ''; ?>>Đã nghỉ</option>
            </select>

            <select name="sort" style="padding: 8px; width: 250px; font-size: 16px">
                <option value="ORDER BY Ten, Ho ASC" <?php echo ($sort === "ORDER BY Ten, Ho ASC") ? "selected" : ""; ?>>
                    Sắp xếp theo Tên</option>
                <option value="ORDER BY Truong, Lop ASC, Ten, Ho ASC" <?php echo ($sort === "ORDER BY Truong, Lop ASC, Ten, Ho ASC") ? "selected" : ""; ?>> Sắp xếp theo Trường/Lớp</option>
            </select>

            <button type="submit" class="submit-btn" style="padding: 8px; font-size: 16px">Tìm kiếm</button>
        </form>

        <!-- Bảng danh sách học sinh -->
        <table class="class_list" style="width: 100%; margin-top: 10px">
            <tr>
                <th>Mã Học Sinh</th>
                <th>SĐT</th>
                <th>Họ và Tên</th>
                <th>Ngày Sinh</th>
                <th>Lớp</th>
                <th>Trường</th>
                <th>Chỉnh sửa</th>
                <th>Xóa</th>
            </tr>

            <?php
            // Kiểm tra xem có dữ liệu học sinh nào không
            if ($result->num_rows > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<td onclick="redirectToStudent(\'' . $row['MaHS'] . '\', \'' . $row['MaPhanLop'] . '\')">' . $row['MaHS'] . '</td>';
                    echo '<td onclick="redirectToStudent(\'' . $row['MaHS'] . '\', \'' . $row['MaPhanLop'] . '\')">' . $row['Phone'] . '</td>';
                    echo '<td style="text-align: left" onclick="redirectToStudent(\'' . $row['MaHS'] . '\', \'' . $row['MaPhanLop'] . '\')">' . $row['Ho'] . ' ' . $row['Ten'] . '</td>';
                    echo '<td>' . $row['NgaySinh'] . '</td>';
                    echo '<td>' . $row['Lop'] . '</td>';
                    echo '<td>' . $row['Truong'] . '</td>';
                    echo '</div>';
                    // Tùy chọn chỉnh sửa học sinh
                    echo "<td><a href='../student/alter_student.php?MaHS=" . $row['MaHS'] . "&MaLop=" . $maLop . "'>Chỉnh sửa</a></td>";

                    // Tùy chọn xóa học sinh
                    echo "<td><a href='#' onclick=\"confirmDelete('" . $row['MaHS'] . "', '" . $maLop . "'); return false;\">Xóa</a></td>";
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="9">Không có học sinh nào</td></tr>';
            }
            ?>

        </table>
    </div>

    <script>
        function redirectToStudent(maHS, maPL) {
            window.location.href = `../student/student.php?MaHS=${maHS}&MaPL=${maPL}`;
        }
    </script>

    <!-- Script để xác nhận trước khi xóa -->
    <script>
        function confirmDelete(maHS, maLop) {
            if (confirm("Bạn muốn xóa học sinh khỏi hệ thống? \n(Nhấn 'OK' để xóa khỏi hệ thống, 'Cancel' để xóa khỏi lớp)")) {
                // Xóa học sinh khỏi hệ thống
                const adminPassword = prompt("Vui lòng nhập mật khẩu admin để xóa học sinh khỏi hệ thống:");
                if (adminPassword !== null) {
                    // Gửi yêu cầu xóa qua POST bằng fetch API
                    fetch('../student/delete_student.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ MaHS: maHS, password: adminPassword, action: 'delete_from_system' })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert("Học sinh đã được xóa khỏi hệ thống thành công!");
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
            } else {
                // Xóa học sinh khỏi lớp
                const adminPassword = prompt("Vui lòng nhập mật khẩu admin để xóa học sinh khỏi lớp <?php echo $class_info['TenLop']; ?>:");
                if (adminPassword !== null) {
                    // Gửi yêu cầu xóa qua POST bằng fetch API
                    fetch('../student/delete_student.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ MaLop: maLop, MaHS: maHS, password: adminPassword, action: 'delete_from_class' })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert("Học sinh đã được xóa khỏi lớp thành công!");
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
        }
    </script>
</body>

</html>