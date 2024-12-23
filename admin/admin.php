<?php
include("../connection.php");
session_start();
if (!$_SESSION['uname'])
    echo '
    <script>
        window.location.href="../login.php";
    </script>';
include("../side_nav.php");

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/x-icon" href="../assets/image/logo.png">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Admin Cô Lê Thị Thanh Thủy</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <link rel="stylesheet" href="../assets/font/themify-icons/themify-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <style>
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
            margin: 25px;
            border-radius: 15px;
        }

        .count_box>p {
            font-size: 24px;
        }
    </style>
</head>

<body>
    <div class="content">
        <h1 style="margin: 10px">TRANG QUẢN TRỊ HỆ THỐNG</h1>
        <div class="count">
            <div class="count_box">
                <?php
                $sql = "SELECT COUNT(distinct hs.MaHS) as SL FROM hocsinh hs JOIN phanlop pl ON hs.MaHS = pl.MaHS WHERE TinhTrang = 1";
                $row = $conn->query($sql)->fetch_assoc();
                $student_count = $row['SL'];
                ?>
                <p>Tổng số học sinh:
                    <?php echo " " . $student_count . "</p>" ?>
            </div>
            <div class="count_box">
                <?php
                $sql = "SELECT COUNT(*) as SL FROM lop WHERE TinhTrang = 1";
                $row = $conn->query($sql)->fetch_assoc();
                $class_count = $row['SL'];
                ?>
                <p>Tổng số lớp:
                    <?php echo " " . $class_count . "</p>" ?>
            </div>
        </div>
        <div class="chart">
            <?php
            // Truy vấn SQL để đếm số học sinh theo lớp
            $sql = "
                SELECT l.TenLop, COUNT(DISTINCT pl.MaHS) AS SoLuongHocSinh FROM lop l
                LEFT JOIN phanlop pl 
                ON l.MaLop = pl.MaLop
                WHERE l.TinhTrang = 1 AND pl.TinhTrang = 1
                GROUP BY l.TenLop
            ";
            $result = $conn->query($sql);

            // Chuẩn bị dữ liệu
            $chart_data = [];
            while ($row = $result->fetch_assoc()) {
                $chart_data[] = [
                    'TenLop' => $row['TenLop'],
                    'SoLuongHocSinh' => $row['SoLuongHocSinh']
                ];
            }

            // Chuyển dữ liệu thành JSON cho JavaScript
            $chart_data = json_encode($chart_data);
            ?>

            <h1>Biểu đồ số lượng học sinh theo lớp</h1>
            <div style="width: 1000px; height: 500px;">
                <canvas id="myChart"></canvas>
            </div>


            <script>
                // Dữ liệu từ PHP
                const data = <?php echo $chart_data; ?>;

                // Cấu hình biểu đồ
                const ctx = document.getElementById('myChart').getContext('2d');
                const myChart = new Chart(ctx, {
                    type: 'bar', // Biểu đồ cột
                    data: {
                        labels: data.map(row => row.TenLop), // Tên lớp
                        datasets: [{
                            label: 'Số lượng học sinh',
                            data: data.map(row => row.SoLuongHocSinh), // Số lượng học sinh
                            borderWidth: 1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false, // Không duy trì tỉ lệ
                        responsive: true, // Biểu đồ tự điều chỉnh kích thước
                        plugins: {
                            datalabels: { // Hiển thị giá trị trên cột
                                anchor: 'end', // Gắn nhãn ở cuối cột
                                align: 'top', // Căn nhãn ở trên cùng của cột
                                formatter: Math.round, // Làm tròn giá trị
                                font: {
                                    weight: 'bold', // In đậm chữ
                                    size: 12 // Cỡ chữ
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true // Đảm bảo trục y bắt đầu từ 0
                            }
                        }
                    }
                });
            </script>

        </div>
    </div>
</body>

</html>