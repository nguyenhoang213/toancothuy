<?php
include("../connection.php");

// Đặt tiêu đề JSON
header("Content-Type: application/json");

// Đọc dữ liệu từ yêu cầu AJAX
$data = json_decode(file_get_contents("php://input"), true);
$maPhanLop = $data['MaPhanLop'] ?? null;
$newScore = $data['Score'] ?? null;

// Khởi tạo phản hồi mặc định
$response = ['success' => false, 'message' => ''];

// Kiểm tra dữ liệu đầu vào
if (!$maPhanLop) {
    $response['message'] = "Không get được mã phân lớp.";
    echo json_encode($response);
    exit;
}

// Lấy `$maBH` từ dữ liệu đầu vào
$maBH = $data['MaBuoiHoc'] ?? '';

// Nếu `$newScore` rỗng, xóa điểm
if ($newScore === null || $newScore === '') {
    $deleteScoreQuery = "DELETE FROM diemso WHERE MaPhanLop = ? AND MaBuoiHoc = ?";
    $stmt = $conn->prepare($deleteScoreQuery);
    $stmt->bind_param("ss", $maPhanLop, $maBH);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Đã xóa điểm thành công.";
    } else {
        $response['message'] = "Xóa điểm thất bại.";
    }
    $stmt->close();
} else {
    // Cập nhật điểm vào cơ sở dữ liệu
    $updateScoreQuery = "UPDATE diemso SET Diem = ? WHERE MaPhanLop = ? AND MaBuoiHoc = ?";
    $stmt = $conn->prepare($updateScoreQuery);
    $stmt->bind_param("sss", $newScore, $maPhanLop, $maBH);

    if ($stmt->execute()) {
        // Lấy thứ hạng mới
        $rankQuery = "
            SELECT Rank 
            FROM (
                SELECT MaPhanLop, RANK() OVER(ORDER BY CAST(Diem AS FLOAT) DESC) AS Rank
                FROM diemso
                WHERE MaBuoiHoc = ?
            ) AS Ranking 
            WHERE MaPhanLop = ?";
        $rankStmt = $conn->prepare($rankQuery);
        $rankStmt->bind_param("ss", $maBH, $maPhanLop);

        if ($rankStmt->execute()) {
            $rankResult = $rankStmt->get_result();
            if ($rankRow = $rankResult->fetch_assoc()) {
                $response['success'] = true;
                $response['newRank'] = $rankRow['Rank'];
            } else {
                $response['message'] = "Không thể tính toán thứ hạng.";
            }
        } else {
            $response['message'] = "Truy vấn thứ hạng thất bại.";
        }
        $rankStmt->close();
    } else {
        $response['message'] = "Cập nhật điểm thất bại.";
    }
    $stmt->close();
}

// Đảm bảo chỉ trả về JSON
echo json_encode($response);
?>