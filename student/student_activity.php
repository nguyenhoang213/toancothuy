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

// Truy vấn lấy danh sách hoạt động của học sinh
$sql = "
    SELECT DATE(hd.ThoiGian) AS Ngay, hd.MaHD, hd.MaHS, hs.Ho, hs.Ten, hs.Lop, hs.Truong, hd.IP, hd.ThietBi, hd.ThoiGian 
    FROM hoatdonghs hd 
    JOIN hocsinh hs ON hd.MaHS = hs.MaHS 
    WHERE hd.MaLop = $maLop 
    ORDER BY hd.ThoiGian DESC
";
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
    <title>Hoạt động học sinh <?php echo htmlspecialchars($class_info['TenLop']); ?></title>
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
    </style>
</head>

<body>
    <div class="content">
        <h1 style="padding-bottom: 10px">Hoạt động học sinh <?php echo htmlspecialchars($class_info['TenLop']); ?></h1>

        <?php
        if ($result->num_rows > 0) {
            // Lưu trữ dữ liệu theo ngày
            $activities_by_date = [];
            while ($row = $result->fetch_assoc()) {
                $date = $row['Ngay'];
                if (!isset($activities_by_date[$date])) {
                    $activities_by_date[$date] = [];
                }
                $activities_by_date[$date][] = $row;
            }

            // Tạo bảng cho từng ngày
            foreach ($activities_by_date as $date => $activities) {
                echo "<h3>Hoạt động ngày: " . htmlspecialchars($date) . "</h3>";
                echo "<table style='width: 100%; margin-top: 10px'>";
                echo "<tr>
                        <th>ID</th>
                        <th>Mã Học Sinh</th>
                        <th>Họ Tên</th>
                        <th>Lớp</th>
                        <th>Trường</th>
                        <th>Địa chỉ IP</th>
                        <th>Thiết Bị</th>
                        <th>Thời Gian</th>
                      </tr>";
                foreach ($activities as $activity) {
                    $maHS = $activity['MaHS'];
                    $phanlop_info = $conn->query("SELECT * FROM phanlop WHERE MaLop = $maLop and MaHS = $maHS")->fetch_assoc();
                    echo "<tr>";
                    echo "<td>" . $activity['MaHD'] . "</td>";
                    echo '<td onclick="redirectToStudent(\'' . $maHS . '\', \'' . $phanlop_info['MaPhanLop'] . '\')">' . htmlspecialchars($activity['MaHS']) . "</td>";
                    echo '<td style="text-align: left" onclick="redirectToStudent(\'' . $maHS . '\', \'' . $phanlop_info['MaPhanLop'] . '\')">' . htmlspecialchars($activity['Ho']) . " " . htmlspecialchars($activity['Ten']) . "</td>";
                    echo "<td>" . htmlspecialchars($activity['Lop']) . "</td>";
                    echo "<td>" . htmlspecialchars($activity['Truong']) . "</td>";
                    echo "<td>" . htmlspecialchars($activity['IP']) . "</td>";
                    echo "<td>" . htmlspecialchars($activity['ThietBi']) . "</td>";
                    echo "<td>" . htmlspecialchars($activity['ThoiGian']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        } else {
            echo "<p>Không có hoạt động nào</p>";
        }
        ?>
    </div>
</body>

</html>

<script>
    function redirectToStudent(maHS, maPL) {
        window.location.href = `../student/student.php?MaHS=${maHS}&MaPL=${maPL}`;
    }
</script>