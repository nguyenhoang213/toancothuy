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

if (isset($_GET['MaHS']) && isset($_GET['MaLop'])) {
    $maHS = $_GET['MaHS'];  // Mã học sinh
    $maLop = $_GET['MaLop'];  // Mã lớp

    $sql = "INSERT INTO phanlop(MaLop, MaHS, TinhTrang) VALUES ('$maLop', '$maHS', 1)";

    if ($conn->query($sql) === TRUE) {
        // Thêm thành công
        echo '<script>
            alert("Thêm học sinh vào lớp thành công!");
            window.location.href = "/student/student_list.php?id=' . $maLop . '"; 
            </script>';
    } else {
        // Lỗi khi thêm
        echo '<script>
            alert("Lỗi: Không thể thêm học sinh vào lớp.");
            window.location.href = "/class/class_list.php?id=' . $maLop . '";
            </script>';
    }
}
