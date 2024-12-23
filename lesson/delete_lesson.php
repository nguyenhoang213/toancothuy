<?php
include("../connection.php"); // Kết nối CSDL
session_start();

// Kiểm tra yêu cầu POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ JSON
    $data = json_decode(file_get_contents("php://input"), true);

    // Kiểm tra dữ liệu
    if (!isset($data['MaBuoiHoc'], $data['password'])) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không đầy đủ.']);
        exit;
    }

    $maBuoiHoc = $data['MaBuoiHoc'];
    $password = $data['password'];

    // Xác thực admin
    if (!isset($_SESSION['uname'])) {
        echo json_encode(['success' => false, 'message' => 'Người dùng chưa đăng nhập.']);
        exit;
    }

    $username = $_SESSION['uname'];
    $sql = "SELECT PassWord FROM admin WHERE UserName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Tài khoản admin không tồn tại.']);
        exit;
    }

    $row = $result->fetch_assoc();
    $adminPassword = $row['PassWord'];

    // Kiểm tra mật khẩu
    if ($password !== $adminPassword) {
        echo json_encode(['success' => false, 'message' => 'Mật khẩu không đúng.']);
        exit;
    }

    // Xóa buổi học
    $stmt = $conn->prepare("DELETE FROM buoihoc WHERE MaBuoiHoc = ?");
    $stmt->bind_param("s", $maBuoiHoc);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Buổi học đã được xóa thành công.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể xóa buổi học.']);
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>