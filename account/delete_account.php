<?php
include("../connection.php"); // Kết nối CSDL
session_start();

// Kiểm tra yêu cầu POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ JSON
    $data = json_decode(file_get_contents("php://input"), true);
    $maAdmin = $data['MaAdmin'];
    $password = $data['password'];

    // Mật khẩu admin 
    $username = $_SESSION['uname'];
    $sql = "Select PassWord from admin Where UserName = '$username'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $adminPassword = $row['PassWord'];
    }

    // Kiểm tra mật khẩu
    if ($password !== $adminPassword) {
        echo json_encode(['success' => false, 'message' => 'Mật khẩu không đúng.']);
        exit;
    }

    // Xóa lớp trong CSDL
    $stmt = $conn->prepare("DELETE FROM admin WHERE MaAdmin = ?");
    $stmt->bind_param("s", $maAdmin);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể xóa tài khoản.']);
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>