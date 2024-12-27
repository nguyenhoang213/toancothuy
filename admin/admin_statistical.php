<?php
include("../connection.php");
session_start();

// Redirect to login if not logged in
if (!$_SESSION['uname']) {
    echo '
    <script>
        window.location.href="../login.php";
    </script>';
}

include("../side_nav.php");

// Fetch class ID from URL
if (isset($_GET['id'])) {
    $maLop = $_GET['id'];
} else {
    echo '
    <script>
        alert("Không tìm thấy mã lớp");
        window.location.href = "../admin/admin.php";
    </script>';
}

// Get class info
$class_info = $conn->query("SELECT * FROM lop WHERE MaLop = $maLop")->fetch_assoc();

// Update student status based on attendance
$update = "UPDATE phanlop SET TinhTrang='0' 
WHERE MaPhanLop NOT IN (SELECT DISTINCT MaPhanLop FROM diemso ds JOIN buoihoc bh ON ds.MaBuoiHoc = bh.MaBuoiHoc WHERE Ngay > DATE_SUB(CURDATE(), INTERVAL 21 DAY)) AND MaLop = '$maLop'";
$conn->query($update);

$update = "UPDATE phanlop SET TinhTrang='1' 
WHERE MaPhanLop IN (SELECT DISTINCT MaPhanLop FROM diemso ds JOIN buoihoc bh ON ds.MaBuoiHoc = bh.MaBuoiHoc WHERE Ngay > DATE_SUB(CURDATE(), INTERVAL 21 DAY)) AND MaLop = '$maLop'";
$conn->query($update);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê lớp</title>
    <link rel="icon" type="image/x-icon" href="../assets/image/logo.png">
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <link rel="stylesheet" href="../assets/css/admin-statistical.css">
    <link rel="stylesheet" href="../assets/css/admin-navigation.css">
    <link rel="stylesheet" href="../assets/font/themify-icons/themify-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
</head>

<body>
    <div class="content">
        <h1>THỐNG KÊ <?php echo $class_info['TenLop']; ?> </h1>
        
        <!-- Statistics Display -->
        <div class="count">
            <div class="count_student">
                <div class="count_box">
                    <?php
                    $hsdanghoc = $conn->query("SELECT COUNT(DISTINCT hs.MaHS) as SL FROM hocsinh hs JOIN phanlop pl ON hs.MaHS = pl.MaHS WHERE TinhTrang = 1 AND MaLop = $maLop")->fetch_assoc();
                    $hsdanghoc = $hsdanghoc['SL'];
                    $hstong = $conn->query("SELECT COUNT(DISTINCT hs.MaHS) as SL FROM hocsinh hs JOIN phanlop pl ON hs.MaHS = pl.MaHS WHERE MaLop = $maLop")->fetch_assoc();
                    $hstong = $hstong['SL'];
                    ?>
                    <p>Tổng số học sinh: <?php echo $hsdanghoc; ?></p>
                </div>
                <div class="count_box">
                    <?php
                    $sql = "SELECT COUNT(*) as SL FROM buoihoc WHERE MaLop = $maLop";
                    $row = $conn->query($sql)->fetch_assoc();
                    $class_count = $row['SL'];
                    ?>
                    <p>Tổng số buổi: <?php echo $class_count; ?></p>
                </div>
            </div>

            <!-- Charts -->
            <div class="chart">
                <h2>Biểu đồ điểm trung bình học sinh theo ngày</h2>
                <canvas id="avgScoreByDayChart"></canvas>
            </div>

            <div class="chart">
                <h2>Biểu đồ số lượng học sinh theo ngày</h2>
                <canvas id="studentChart"></canvas>
            </div>

            <div class="chart">
                <h2>Biểu đồ trạng thái học sinh</h2>
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Data for Charts -->
        <?php
        // Fetch average score by day
        $sqlTBL = "
            SELECT bh.Ngay, ROUND(AVG(ds.Diem), 2) as DiemTB
            FROM buoihoc bh
            JOIN diemso ds ON bh.MaBuoiHoc = ds.MaBuoiHoc
            WHERE bh.MaLop = $maLop
            AND Diem >= 0 AND Diem <= 10
            GROUP BY bh.Ngay
            ORDER BY bh.Ngay ASC";
        $tbl = $conn->query($sqlTBL);
        $dates = [];
        $scores = [];
        while ($row = $tbl->fetch_assoc()) {
            $dates[] = $row['Ngay'];
            $scores[] = $row['DiemTB'];
        }
        ?>

        <script>
            const dates = <?php echo json_encode($dates); ?>;
            const scores = <?php echo json_encode($scores); ?>;

            // Average score chart
            const ctx2 = document.getElementById('avgScoreByDayChart').getContext('2d');
            const avgScoreByDayChart = new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Điểm trung bình',
                        data: scores,
                        backgroundColor: '#0066FF',
                        borderColor: '#3399FF',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true
                }
            });
        </script>

        <?php
        // Fetch student count by day
        $sql = "SELECT Ngay, COUNT(MaDiemSo) as SL FROM diemso ds JOIN buoihoc bh ON ds.MaBuoiHoc = bh.MaBuoiHoc WHERE bh.MaLop = $maLop GROUP BY Ngay";
        $result = $conn->query($sql);
        $dates2 = [];
        $studentCounts = [];
        while ($row = $result->fetch_assoc()) {
            $dates2[] = $row['Ngay'];
            $studentCounts[] = $row['SL'];
        }
        ?>

        <script>
            const dates2 = <?php echo json_encode($dates2); ?>;
            const studentCounts = <?php echo json_encode($studentCounts); ?>;

            // Student count chart
            const ctx3 = document.getElementById('studentChart').getContext('2d');
            const studentChart = new Chart(ctx3, {
                type: 'line',
                data: {
                    labels: dates2,
                    datasets: [{
                        label: 'Số lượng học sinh',
                        data: studentCounts,
                        backgroundColor: '#73bee4',
                        borderColor: '#73bee4',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true
                }
            });
        </script>

        <?php
        // Fetch student status
        $sqlStatus = "SELECT COUNT(distinct hs.MaHS) as SL FROM hocsinh hs JOIN phanlop pl ON hs.MaHS = pl.MaHS WHERE TinhTrang = 1 and MaLop = $maLop";
        $hsdanghoc = $conn->query($sqlStatus)->fetch_assoc()['SL'];
        ?>

        <script>
            const ctx1 = document.getElementById('statusChart').getContext('2d');
            const statusChart = new Chart(ctx1, {
                type: 'pie',
                data: {
                    labels: ['Đang học', 'Nghỉ học'],
                    datasets: [{
                        label: 'Số học sinh',
                        data: [<?php echo $hsdanghoc; ?>, <?php echo $hstong - $hsdanghoc; ?>],
                        backgroundColor: ['#3399FF', '#FF4040'],
                        borderColor: ['#6699CC', '#CC6666'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });
        </script>

        <!-- Class info table -->
        <h2 style="text-align: center;">Thông tin buổi học</h2>
        <?php
        $sql = "SELECT Ngay, bh.MaBuoiHoc, TenBai, COUNT(MaDiemSo) as SL, 
                ROUND(MAX(CAST(Diem AS float)), 2) as Max, 
                ROUND(MIN(CAST(Diem AS float)), 2) as Min, 
                AVG(CAST(Diem AS float)) as TB
                FROM diemso ds 
                JOIN buoihoc bh ON ds.MaBuoiHoc = bh.MaBuoiHoc 
                WHERE bh.MaLop = $maLop 
                AND Diem >= 0 AND Diem <= 10 
                GROUP BY Ngay 
                ORDER BY Ngay DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table>
                <tr>
                    <th>Ngày</th>
                    <th>Tên bài</th>
                    <th>Số lượng bài kiểm tra</th>
                    <th>Điểm cao nhất</th>
                    <th>Điểm thấp nhất</th>
                    <th>Điểm trung bình</th>
                </tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td onclick=\"redirectToScore('{$row['MaBuoiHoc']}', '$maLop')\">{$row['Ngay']}</td>
                    <td style=\"text-align: left;\" onclick=\"redirectToScore('{$row['MaBuoiHoc']}', '$maLop')\">{$row['TenBai']}</td>
                    <td>{$row['SL']}</td>
                    <td>{$row['Max']}</td>
                    <td>{$row['Min']}</td>
                    <td>" . number_format($row['TB'], 2) . "</td>
                </tr>";
            }

            echo "</table>";
        } else {
            echo "<p>Không có dữ liệu.</p>";
        }
        ?>

    </div>

    <script>
        function redirectToScore(maBuoiHoc, maLop) {
            window.location.href = `../score/score_statistical.php?MaBuoiHoc=${maBuoiHoc}&MaLop=${maLop}`;
        }
    </script>

</body>

</html>
