<?php
include("../connection.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ yêu cầu
    $data = json_decode(file_get_contents('php://input'), true);
    $maBuoiHoc = $data['maBuoiHoc'];
    $maPhanLop = $data['maPhanLop'];
    $attended = $data['attended'];

    if (!$maPhanLop) {
        echo json_encode(['success' => false, 'message' => 'Mã phân lớp không hợp lệ.']);
        exit;
    }

    if ($attended == 1) {
        $sql = "INSERT INTO diemdanh(MaBH, MaPL) VALUES ($maBuoiHoc, $maPhanLop)";
    } else {
        $sql = "DELETE FROM diemdanh WHERE MaBH = $maBuoiHoc and MaPL = $maPhanLop";
    }

    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật điểm danh.']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ.']);
}
?>