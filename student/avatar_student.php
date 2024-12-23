<?php
include("../connection.php");
session_start();
include("../side_nav.php");

if (!$_SESSION['uname'])
    echo '
    <script>
        window.location.href="../index.php";
    </script>';

if (isset($_GET['id'])) {
    $maLop = $_GET['id'];
} else {
    echo '<script>
            alert("Không xác định được lớp!");
            window.location.href="../class/class_list.php?";
            </script>';
}

// Lấy thông tin lớp học
$class_info = $conn->query("SELECT * FROM lop WHERE MaLop = '$maLop'")->fetch_assoc();
if (!$class_info) {
    die("Không tìm thấy thông tin lớp học.");
}

// Cập nhật trạng thái học sinh dựa trên điều kiện
{


}

// Điều kiện lọc mặc định
$where = "WHERE pl.MaLop = '$maLop'";
$sort = "ORDER BY Ten, Ho ASC"; // Mặc định sắp xếp
$tinhtrang = "1";

if (isset($_POST['acp'])) {
    $sort = $_POST['sort'];
    $school = $_POST['Truong'] ?? "";
    $tinhtrang = $_POST['tinhtrang'] ?? "";

    // Lọc theo trường học
    if ($school) {
        $where .= " AND Truong = '$school'";
    }

    // Lọc theo tình trạng
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
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/x-icon" href="../assets/image/logo.png">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin học sinh <?php echo htmlspecialchars($class_info["TenLop"]); ?></title>
    <link rel="stylesheet" href="../assets/css/avatar-student.css">
    <link rel="stylesheet" href="../assets/css/admin-navigation.css">
    <style>
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
    </style>
</head>

<body>

    <!-- Fixed Button -->
    <button class="fixed-button" onclick="avatarStudent()">
        <i class="fa-solid fa-table"></i>
    </button>

    <script>
        function avatarStudent() {
            window.location.href = "student_list.php?id=<?php echo $maLop ?>";
        }
    </script>

    <div class="content">
        <h1>Thông Tin Học Sinh <?php echo htmlspecialchars($class_info["TenLop"]); ?></h1>
        <form action="" method="POST">
            <label for="Truong">Trường:</label>
            <select name="Truong" style="padding: 8px; width: 250px; font-size: 16px">
                <option value="">Tất cả</option>
                <?php
                $sql = "
                    SELECT DISTINCT Truong 
                    FROM hocsinh 
                    JOIN phanlop ON hocsinh.MaHS = phanlop.MaHS 
                    WHERE phanlop.MaLop = '$maLop' AND Truong != '' 
                    ORDER BY Truong ASC
                ";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    $selected = (isset($school) && $school === $row['Truong']) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($row['Truong']) . "' $selected>" . htmlspecialchars($row['Truong']) . "</option>";
                }
                ?>
            </select>

            <label for="sort">Sắp xếp:</label>
            <select name="sort" style="padding: 8px; width: 250px; font-size: 16px">
                <option value="ORDER BY Ten, Ho ASC" <?php echo ($sort === "ORDER BY Ten, Ho ASC") ? "selected" : ""; ?>>
                    Theo Tên</option>
                <option value="ORDER BY Truong, Lop ASC, Ten, Ho ASC" <?php echo ($sort === "ORDER BY Truong, Lop ASC, Ten, Ho ASC") ? "selected" : ""; ?>>Theo Trường/Lớp</option>
            </select>

            <label for="tinhtrang">Tình trạng:</label>
            <select name="tinhtrang" style="padding: 8px; width: 250px; font-size: 16px">
                <option value="1" <?php echo ($tinhtrang === "1") ? "selected" : ""; ?>>Đang học</option>
                <option value="all" <?php echo ($tinhtrang === "all") ? "selected" : ""; ?>>Tất cả</option>
                <!-- <option value="2" <?php echo ($tinhtrang === "2") ? "selected" : ""; ?>>Học sinh mới</option> -->
                <option value="0" <?php echo ($tinhtrang === "0") ? "selected" : ""; ?>>Đã nghỉ</option>
                <option value="3" <?php echo ($tinhtrang === "3") ? "selected" : ""; ?>>Chưa có ảnh</option>
            </select>
            <button type="submit" name="acp" class="submit-btn" style="padding: 8px; font-size: 16px">Chọn</button>
        </form>

        <?php
        // Truy vấn danh sách học sinh
        $sql = "
            SELECT DISTINCT pl.MaLop, hs.MaHS, hs.Phone, Ho, Ten, Anh, NgaySinh, Lop, Truong, pl.MaPhanLop
            FROM hocsinh hs
            JOIN phanlop pl ON hs.MaHS = pl.MaHS 
            $where 
            $sort
        ";
        $result = $conn->query($sql);

        // Hiển thị số lượng học sinh
        $count_sql = "SELECT COUNT(*) as SL FROM ($sql) AS CountQuery";
        $count_result = $conn->query($count_sql);
        $count_row = $count_result->fetch_assoc();
        echo "Tổng Số Lượng: " . htmlspecialchars($count_row['SL']) . "<br>";

        echo '<div class="student-div">';
        while ($row = $result->fetch_assoc()) {
            echo '<div class="student" onclick="redirectToStudent(\'' . $row['MaHS'] . '\', \'' . $row['MaPhanLop'] . '\')">';
            $avatar = $row['Anh'] ? "/assets/image/anhhs/" . htmlspecialchars($row['Anh']) : "/assets/image/anhhs/avt.png";
            echo '<img src="' . $avatar . '">';
            echo "<p>" . htmlspecialchars($row['Ho']) . " " . htmlspecialchars($row['Ten']) . "</p>";
            echo "<p>" . htmlspecialchars($row['Phone']) . "</p>";
            echo "<p>" . htmlspecialchars($row['NgaySinh']) . "</p>";
            echo "<p>" . htmlspecialchars($row['Lop']) . "</p>";
            echo "<p>" . htmlspecialchars($row['Truong']) . "</p>";
            echo '</div>';
        }
        echo '</div>';
        ?>
    </div>
</body>

</html>

<script>
    function redirectToStudent(maHS, maPL) {
        window.location.href = `../student/student.php?MaHS=${maHS}&MaPL=${maPL}`;
    }
</script>