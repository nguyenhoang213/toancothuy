<?php
include("../connection.php");

if (isset($_GET['mapl'])) {
    $maPL = $_GET['mapl'];
    $year = isset($_GET['year']) ? $_GET['year'] : null;
    $month = isset($_GET['month']) ? $_GET['month'] : null;

    // Truy vấn lấy danh sách buổi học
    $query = "
        SELECT bh.Ngay, bh.TenBai 
        FROM buoihoc bh 
        JOIN diemdanh dd ON bh.MaBuoiHoc = dd.MaBH 
        WHERE dd.MaPL = '$maPL'
    ";

    // Nếu có tháng/năm, thêm điều kiện vào truy vấn
    if ($year && $month) {
        $query .= " AND YEAR(bh.Ngay) = '$year' AND MONTH(bh.Ngay) = '$month'";
    }

    $query .= " ORDER BY bh.Ngay";
    $result = $conn->query($query);

    $buoiHoc = [];
    while ($row = $result->fetch_assoc()) {
        $buoiHoc[] = $row;
    }

    echo json_encode($buoiHoc);
}
?>