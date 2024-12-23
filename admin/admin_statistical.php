<?php
include("../connection.php");
session_start();
if (!$_SESSION['uname'])
    echo '
    <script>
        window.location.href="../login.php";
    </script>';

include("../side_nav.php");

if (isset($_GET['id'])) {
    $maLop = $_GET['id'];
} else {
    echo '
    <script>
        alert("Không tìm thấy mã lớp");
        window.location.href = `../admin/admin.php`;
    </script>';
}

$class_info = $conn->query("SELECT * FROM lop WHERE MaLop = $maLop")->fetch_assoc();

$update = "UPDATE phanlop SET TinhTrang='0' 
WHERE MaPhanLop not in (SELECT DISTINCT MaPhanLop FROM diemso ds JOIN buoihoc bh ON ds.MaBuoiHoc = bh.MaBuoiHoc WHERE Ngay > DATE_SUB(CURDATE(), INTERVAL 21 DAY)) and MaLop = '$maLop'";
$conn->query($update);

$update = "UPDATE phanlop SET TinhTrang='1' 
WHERE MaPhanLop in (SELECT DISTINCT MaPhanLop FROM diemso ds JOIN buoihoc bh ON ds.MaBuoiHoc = bh.MaBuoiHoc WHERE Ngay > DATE_SUB(CURDATE(), INTERVAL 21 DAY)) and MaLop = '$maLop'";
$conn->query($update);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/x-icon" href="../assets/image/logo.png">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Admin Vật Lý Trường Nghiêm</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <link rel="stylesheet" href="../assets/css/admin-statistical.css">
    <link rel="stylesheet" href="../assets/css/admin-navigation.css">
    <link rel="stylesheet" href="../assets/font/themify-icons/themify-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <style>
        .content {
            padding-left: 50px;

        }

        .count {
            display: flex;
            justify-content: start;
        }

        .count_box {
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            background-color: #2cdaff99;
            width: 350px;
            height: 100px;
            margin: 25px 50px 25px 0;
            border-radius: 15px;
        }

        .count_box>p {
            font-size: 24px;
        }

        .chart {
            text-align: center;
        }

        table th,
        table td {
            padding: 8px;
            font-size: 16px;
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

        table {
            width: 80vw;
        }
    </style>
</head>

<body>
    <h1 style="margin: 15px 0 0 240px">THỐNG KÊ LỚP <?php echo $class_info['TenLop'] ?> </h1>
    <div class="content">
        <div class="count">
            <div>
                <div class="count">
                    <div class="count_box">
                        <?php
                        $hsdanghoc = $conn->query("SELECT COUNT(distinct hs.MaHS) as SL FROM hocsinh hs JOIN phanlop pl ON hs.MaHS = pl.MaHS WHERE TinhTrang = 1 and MaLop = $maLop")->fetch_assoc();
                        $hsdanghoc = $hsdanghoc['SL'];
                        $hstong = $conn->query("SELECT COUNT(distinct hs.MaHS) as SL FROM hocsinh hs JOIN phanlop pl ON hs.MaHS = pl.MaHS WHERE MaLop = $maLop")->fetch_assoc();
                        $hstong = $hstong['SL'];
                        ?>
                        <p>Tổng số học sinh: <?php echo " " . $hsdanghoc . "</p>" ?>
                    </div>
                    <div class="count_box">
                        <?php
                        $sql = "SELECT COUNT(*) as SL FROM buoihoc WHERE MaLop = $maLop";
                        $row = $conn->query($sql)->fetch_assoc();
                        $class_count = $row['SL'];
                        ?>
                        <p>Tổng số buổi:
                            <?php echo " " . $class_count . "</p>" ?>
                    </div>
                </div>
                <div class="chart" style="width: 800px; height: 420px;">
                    <h2 style="margin-top: 10px">Biểu đồ điểm trung bình học sinh theo ngày</h2>
                    <canvas id="avgScoreByDayChart"></canvas>
                </div>
                <br>
                <div class="chart" style="width: 800px; height: 420px;">
                    <h2 style="margin-top: 10px">Biểu đồ số lượng học sinh theo ngày</h2>
                    <canvas id="studentChart"></canvas>
                </div>
            </div>
            <div class="chart" style="margin-left: 30px">
                <div style="width: 300px; height: 300px;">
                    <h2 style="margin-top: 10px">Biểu đồ trạng thái học sinh</h2>
                    <canvas id="statusChart"></canvas>
                </div>
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
                                },
                            }
                        }
                    });
                </script>
            </div>
        </div>
        <div>
            <?php
            $sqlTBL = "
                SELECT bh.Ngay, ROUND(AVG(ds.Diem),2) as DiemTB
                FROM buoihoc bh
                JOIN diemso ds ON bh.MaBuoiHoc = ds.MaBuoiHoc
                WHERE bh.MaLop = $maLop
                AND Diem >= 0 AND Diem <= 10  AND NOT ((Diem > 'a*' AND Diem < 'z*') OR (Diem > 'A*' AND Diem < 'Z*'))
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

                const ctx2 = document.getElementById('avgScoreByDayChart').getContext('2d');
                const avgScoreByDayChart = new Chart(ctx2, {
                    type: 'line',
                    data: {
                        labels: dates, // Danh sách ngày học
                        datasets: [{
                            label: 'Điểm trung bình',
                            data: scores, // Điểm trung bình tương ứng
                            backgroundColor: '#0066FF',
                            borderColor: '#3399FF',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            datalabels: { // This code is used to display data values
                                anchor: 'end',
                                align: 'top',
                                color: 'black',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                },
                            }
                        }
                    }
                });
            </script>

            <?php
            $sql = "SELECT Ngay, COUNT(MaDiemSo) as SL FROM diemso ds JOIN buoihoc bh ON ds.MaBuoiHoc = bh.MaBuoiHoc WHERE bh.MaLop = $maLop GROUP BY Ngay";
            $result = $conn->query($sql);

            // Tạo mảng dữ liệu cho biểu đồ
            $dates2 = [];
            $studentCounts = [];
            while ($row = $result->fetch_assoc()) {
                $dates2[] = $row['Ngay'];
                $studentCounts[] = $row['SL'];
            }
            ?>

            <script>
                // Dữ liệu từ PHP
                const dates2 = <?php echo json_encode($dates2); ?>;
                const studentCounts = <?php echo json_encode($studentCounts); ?>;

                // Tạo biểu đồ cột
                const ctx3 = document.getElementById('studentChart').getContext('2d');
                const studentChart = new Chart(ctx3, {
                    type: 'line',
                    data: {
                        labels: dates, // Các ngày học
                        datasets: [{
                            label: 'Số lượng học sinh',
                            data: studentCounts, // Số lượng học sinh
                            backgroundColor: '#0066FF', // Màu nền
                            borderColor: '#3399FF', // Màu viền
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                        },
                    }
                });
            </script>
        </div>
        <h2 style="margin: 20px 0; text-align: center">Thông tin buổi học</h2>
        <?php
        $sql = "SELECT Ngay, bh.MaBuoiHoc , TenBai,COUNT(MaDiemSo) as SL, ROUND(Max(CAST(Diem as float)),2) as Max, ROUND(Min(CAST(Diem as float)),2) as Min, AVG(CAST(Diem as float)) as TB FROM diemso ds JOIN buoihoc bh ON ds.MaBuoiHoc = bh.MaBuoiHoc 
        WHERE bh.MaLop = $maLop AND Diem >= 0 AND Diem <= 10  AND NOT ((Diem > 'a*' AND Diem < 'z*') OR (Diem > 'A*' AND Diem < 'Z*'))
        GROUP BY Ngay ORDER BY Ngay DESC";
        // Thực thi truy vấn
        $result = $conn->query($sql);

        // Kiểm tra và hiển thị kết quả
        if ($result->num_rows > 0) {
            // Mở bảng HTML để hiển thị kết quả
            echo "<table border='1'>
            <tr>
                <th>Ngày</th>
                <th>Tên bài</th>
                <th>Số lượng bài kiểm tra</th>
                <th>Điểm cao nhất</th>
                <th>Điểm thấp nhất</th>
                <th>Điểm trung bình</th>
            </tr>";

            // Lặp qua kết quả và hiển thị dữ liệu
            while ($row = $result->fetch_assoc()) {
                echo '<tr>
                <td onclick="redirectToScore(\'' . $row['MaBuoiHoc'] . '\', \'' . $maLop . '\')">' . $row["Ngay"] . '</td>
                <td style="text-align: left" onclick="redirectToScore(\'' . $row['MaBuoiHoc'] . '\', \'' . $maLop . '\')">' . $row["TenBai"] . "</td>
                <td>" . $row["SL"] . "</td>
                <td>" . $row["Max"] . "</td>
                <td>" . $row["Min"] . "</td>
                <td>" . number_format($row["TB"], 2) . "</td>
              </tr>";
            }

            echo "</table>";
        } else {
            echo "Không có dữ liệu.";
        }
        ?>
    </div>
</body>

<script>
    function redirectToScore(maBuoiHoc, maLop) {
        window.location.href = `../score/score_statistical.php?MaBuoiHoc=${maBuoiHoc}&MaLop=${maLop}`;
    }
</script>

</html>