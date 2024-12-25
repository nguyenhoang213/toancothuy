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

// Lấy ID lớp từ URL
$maLop = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Kiểm tra xem ID lớp có hợp lệ không
if ($maLop <= 0) {
    echo "Lớp không tồn tại!";
    exit;
}

// Tìm kiếm bài học trong lớp
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Truy vấn dữ liệu bài học
$sql = "SELECT bh.MaBuoiHoc, MaLop, Ngay, TenBai, DapAn FROM buoihoc bh WHERE bh.MaLop = $maLop";
if (!empty($search_query)) {
    $sql .= " AND TenBai LIKE '%$search_query%'";
}
$sql .= " ORDER BY Ngay DESC"; // Sắp xếp kết quả

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
    <title>Quản lý bài học <?php echo htmlspecialchars($class_info['TenLop']); ?></title>
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

            .lesson_list th,
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

            .lesson_list th,
            tr,
            td {
                font-size: 12px;
            }
        }

        .fixed-button {
            font-size: 25px;
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            z-index: 1000;
        }

        .fixed-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="content">
        <h1 style="padding-top: 30px">Quản lý bài học <?php echo htmlspecialchars($class_info['TenLop']); ?></h1>
        <!-- Nút thêm bài học mới -->
        <a href="../lesson/add_lesson.php?MaLop=<?php echo $maLop; ?>" class="add_button">Thêm bài học mới</a> <br>

        <!-- Tìm kiếm -->
        <form method="GET" action="" style="margin: 20px 0;">
            <input type="hidden" name="id" value="<?php echo $maLop; ?>">
            <label for="">Tìm kiếm</label>
            <input type="text" name="search" placeholder="Nhập tên bài học"
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                style="padding: 8px; width: 250px; font-size: 16px">
            <button type="submit" class="submit-btn" style="padding: 8px; font-size: 16px">Tìm kiếm</button>
        </form>

        <!-- Bảng danh sách bài học -->
        <table class="lesson_list" style="width: 100%; margin-top: 10px">
            <tr>
                <th>Mã Buổi Học</th>
                <th>Tên Bài Học</th>
                <th>Ngày </th>
                <th>Đáp án</th>
                <th>Chỉnh sửa</th>
                <th>Xóa</th>
            </tr>

            <?php
            // Kiểm tra xem có dữ liệu bài học nào không
            if ($result->num_rows > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<td onclick="redirectToScore(\'' . $row['MaBuoiHoc'] . '\', \'' . $maLop . '\')">' . $row['MaBuoiHoc'] . '</td>';
                    echo '<td onclick="redirectToScore(\'' . $row['MaBuoiHoc'] . '\', \'' . $maLop . '\')" style="text-align: left">' . htmlspecialchars($row['TenBai']) . '</td>';
                    echo '<td onclick="redirectToScore(\'' . $row['MaBuoiHoc'] . '\', \'' . $maLop . '\')">' . $row['Ngay'] . '</td>';
                    if ($row['DapAn']) {
                        echo '<td> <a href = "' . $row['DapAn'] . '" style ="color: #3300ff; text-decoration: underline"> Xem đáp án</a> </td>';
                    } else {
                        echo '<td> Chưa có đáp án </td>';
                    }
                    // Tùy chọn chỉnh sửa bài học
                    echo "<td><a href='../lesson/alter_lesson.php?MaBuoiHoc=" . $row['MaBuoiHoc'] . "&MaLop=" . $maLop . "'>Chỉnh sửa</a></td>";

                    // Tùy chọn xóa bài học
                    echo "<td><a href='#' onclick=\"confirmDelete('" . $row['MaBuoiHoc'] . "'); return false;\">Xóa</a></td>";
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="7">Không có bài học nào</td></tr>';
            }
            ?>

            <script>
                function redirectToScore(maBuoiHoc, maLop) {
                    window.location.href = `../score/score_statistical.php?MaBuoiHoc=${maBuoiHoc}&MaLop=${maLop}`;
                }
            </script>


        </table>
    </div>

    <!-- Script để xác nhận trước khi xóa -->
    <script>
        function confirmDelete(maBuoiHoc) {
            if (confirm("Bạn muốn xóa bài học khỏi hệ thống?")) {
                const adminPassword = prompt("Vui lòng nhập mật khẩu admin để xóa bài học:");
                if (adminPassword !== null) {
                    fetch('../lesson/delete_lesson.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ MaBuoiHoc: maBuoiHoc, password: adminPassword })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert("Bài học đã được xóa thành công!");
                                location.reload();
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