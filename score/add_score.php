<?php
include("../connection.php");

header("Content-Type: application/json");

// Lấy dữ liệu từ AJAX
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['Score'], $data['MaPhanLop'], $data['MaBuoiHoc'], $data['MaLop'])) {
    echo json_encode(["success" => false, "message" => "Thiếu thông tin cần thiết."]);
    exit;
}

$score = $data['Score'];
$maPhanLop = $data['MaPhanLop'];
$maBuoiHoc = $data['MaBuoiHoc'];
$maLop = $data['MaLop'];

// Thêm điểm vào cơ sở dữ liệu
$query = "INSERT INTO diemso (Diem, MaPhanLop, MaBuoiHoc) VALUES ('$score', '$maPhanLop', '$maBuoiHoc')";
if ($conn->query($query)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi lưu vào cơ sở dữ liệu: " . $conn->error]);
}
?>