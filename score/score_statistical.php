<?php
include("../connection.php");
session_start();
include("../side_nav.php");

if (!$_SESSION['uname'])
    header('Location: https://vatlytruongnghiem.edu.vn/login.php');

if (isset($_GET['MaLop']) && isset($_GET['MaBuoiHoc'])) {
    $maLop = $_GET['MaLop'];
    $maBH = $_GET['MaBuoiHoc'];
} else if (!isset($_GET['MaLop'])) {
    echo '<script>
    alert("Không tìm thấy mã lớp");
    window.location.href="../class/class_list.php";
    </script>';
} else if (!isset($_GET['MaBuoiHoc'])) {
    echo '<script>
    alert("Không tìm thấy mã buổi học!");
    window.location.href="../lesson/lesson_list.php?id=' . $maLop . '";
    </script>';
}

$update = "UPDATE PhanLop SET TinhTrang='0' 
WHERE MaPhanLop not in (SELECT DISTINCT MaPhanLop FROM diemso ds JOIN buoihoc bh ON ds.MaBuoiHoc = bh.MaBuoiHoc WHERE Ngay > DATE_SUB(CURDATE(), INTERVAL 21 DAY)) and MaLop = '$maLop'";
$conn->query($update);

// Lấy thông tin lớp học
$class_info = $conn->query("SELECT * FROM lop WHERE MaLop = '$maLop'")->fetch_assoc();
$lesson_info = $conn->query("SELECT * FROM buoihoc WHERE MaBuoiHoc = '$maBH'")->fetch_assoc();
$sort = isset($_GET['sort']) ? $_GET['sort'] : 0;
if ($sort == 0) {
    $orderby = "ORDER BY CAST(Diem as float) DESC, Ten, Ho";
} else {
    $orderby = "ORDER BY Ten, Ho";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vật Lý Thầy Phạm Trường Nghiêm</title>
    <link rel="stylesheet" href="../assets/css/admin-statistical.css">
    <link rel="stylesheet" href="../assets/css/admin-navigation.css">
    <link rel="stylesheet" href="../assets/font/themify-icons/themify-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="../assets/image/logo.png">
    <script src="/assets/table2excel.js"></script>
    <style>
        @media screen and (min-width: 600px) {
            #iScore {
                text-align: center;
                padding: 10px 20px;
                font-size: 22px;
                width: 100% !important;
                border: none;
                font-family: Times New Roman;
            }
        }

        @media screen and (max-width: 600px) {
            #iScore {
                text-align: center;
                padding: 5px 20px;
                font-size: 10px;
                width: 100% !important;
                border: none;
                font-family: Times New Roman;
            }
        }

        table th,
        table td {
            padding: 8px;
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

        .fixed-button {
            font-size: 20px;
            position: fixed;
            top: 25px;
            right: 25px;
            background-color: #007bff;
            /* Màu nền */
            color: #fff;
            /* Màu chữ */
            border: none;
            border-radius: 50%;
            /* Bo tròn */
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            /* Hiệu ứng đổ bóng */
            cursor: pointer;
            z-index: 1000;
            /* Luôn hiển thị trên cùng */
        }

        .fixed-button:hover {
            background-color: #0056b3;
            /* Màu khi hover */
        }
    </style>
</head>

<body>
    <div class="content">
        <!-- End: Statistical -->
        <h1 style="margin: 15px 0;" id="Title">PHỔ ĐIỂM
            <?php echo $class_info['TenLop'] ?>
        </h1>
        <h2 style="margin: 10px">Tên bài: <?php echo $lesson_info['TenBai'] . ' - ' . $lesson_info['Ngay'] ?> </h2>

        <form action="/score/score_statistical.php" method="GET" style="margin-bottom: 15px;">
            <input type="hidden" name="MaBuoiHoc" value="<?php echo $maBH; ?>">
            <input type="hidden" name="MaLop" value="<?php echo $maLop; ?>">
            <label for="sort" style="margin-right: 10px;">Sắp xếp:</label>
            <select name="sort" onchange="this.form.submit()" style="padding: 5px; font-size: 16px;">
                <option value="0" <?php echo isset($_GET['sort']) && $_GET['sort'] == '0' ? 'selected' : ''; ?>>Theo điểm
                </option>
                <option value="1" <?php echo isset($_GET['sort']) && $_GET['sort'] == '1' ? 'selected' : ''; ?>>Theo họ
                    tên
                </option>
            </select>
        </form>

        <!-- Fixed Button -->
        <button class="fixed-button" onclick="nhapDiem()">
            <i class="fa-solid fa-pen-to-square"></i>
        </button>

        <script>
            function nhapDiem() {
                window.location.href = "input_score.php?MaBH=<?php echo $maBH ?>&MaLop=<?php echo $maLop ?>";
            }
        </script>

        <div class="chart-barjs">
            <!-- Start: Chart-bar -->
            <div class="chart-bar" style="padding:0px 20px 0 10px">
                <canvas id="myChart"></canvas>
            </div>
            <?php
            // Truy vấn SQL để lấy dữ liệu từ bảng mới
            $query = "SELECT 
                    CAST(Diem as float) as FScore,
                    COUNT(Diem) as SL
                FROM diemso 
                WHERE MaBuoiHoc = '$maBH'
                AND NOT (
                    (Diem > 'a*' AND Diem < 'z*')
                    OR (Diem > 'A*' AND Diem < 'Z*')
                ) 
                GROUP BY Diem 
                ORDER BY CAST(Diem as float) ASC"; // Sắp xếp theo điểm tăng dần
            
            $result = mysqli_query($conn, $query); // Thực thi truy vấn
            $chart_data = '';
            $max = 0;
            while ($row = mysqli_fetch_array($result)) {
                $chart_data .= "{ Điểm:" . $row["FScore"] . ", SL:" . $row["SL"] . "}, "; // Chuỗi dữ liệu biểu đồ
                if ($row["SL"] > $max)
                    $max = $row["SL"]; // Lấy số lượng lớn nhất
            }
            ?>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
            <script
                src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>
            <script>
                Chart.register(ChartDataLabels);

                // Chuyển dữ liệu PHP sang JavaScript
                const data = [
                    <?php echo $chart_data ?>
                ];

                const ctx = document.getElementById('myChart').getContext('2d');
                const myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(row => row.Điểm), // Nhãn là các điểm số
                        datasets: [{
                            label: 'Số lượng', // Ghi chú cho biểu đồ
                            data: data.map(row => row.SL), // Dữ liệu là số lượng
                            backgroundColor: 'rgba(54, 162, 235, 0.8)', // Màu nền
                            borderColor: 'rgba(54, 162, 235, 1)', // Màu viền
                        }]
                    },
                    options: {
                        maintainAspectRatio: false, // Tắt chế độ duy trì tỉ lệ
                        responsive: true, // Biểu đồ tự điều chỉnh kích thước
                        plugins: {
                            datalabels: { // Hiển thị giá trị trên cột
                                anchor: 'end',
                                align: 'top',
                                formatter: Math.round,
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            }
                        },
                        scales: {
                            y: {
                                suggestedMin: 0, // Giá trị nhỏ nhất trục y
                                suggestedMax: <?php echo $max >= 20 ? $max + 5 : $max + 2; ?> // Tự động tăng giá trị lớn nhất
                            }
                        }
                    }
                });
            </script>
            <!-- Kết thúc: Biểu đồ cột -->
            <!-- Start: Statistics-bar -->
            <div class="statistics-bar">
                <?php
                // Lấy điểm trung bình
                $sql = "SELECT ROUND(AVG(Diem), 2) as AVG 
                        FROM diemso 
                        WHERE MaBuoiHoc = '$maBH' AND Diem >= 0 AND Diem <= 10  AND NOT ((Diem > 'a*' AND Diem < 'z*') OR (Diem > 'A*' AND Diem < 'Z*'))";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_array($result)) {
                    echo '<p> Điểm Trung Bình: ' . $row['AVG'] . '</p>';
                }

                // Tổng số bài
                $sql = "SELECT COUNT(Diem) as SL 
                        FROM diemso 
                        WHERE MaBuoiHoc = '$maBH'";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_array($result)) {
                    echo '<p> Tổng Số Bài: ' . $row['SL'] . '</p>';
                }

                // Số điểm 10
                $sql = "SELECT COUNT(Diem) as SL 
                        FROM diemso 
                        WHERE MaBuoiHoc = '$maBH' AND Diem = 10 AND Diem >= 0 AND Diem <= 10";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_array($result)) {
                    echo '<p> Số điểm 10: ' . $row['SL'] . '</p>';
                }

                // Số điểm từ 8 đến dưới 10
                $sql = "SELECT COUNT(Diem) as SL 
                        FROM diemso 
                        WHERE MaBuoiHoc = '$maBH' AND Diem >= 8  AND Diem < 10 AND Diem >= 0 AND Diem <= 10";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_array($result)) {
                    echo '<p> Số điểm từ 8 đến 10: ' . $row['SL'] . '</p>';
                }

                // Số điểm từ 5 đến dưới 8
                $sql = "SELECT COUNT(Diem) as SL  
                        FROM diemso 
                        WHERE MaBuoiHoc = '$maBH' AND Diem >= 5 AND Diem < 8 AND Diem >= 0 AND Diem <= 10";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_array($result)) {
                    echo '<p> Số điểm từ 5 đến 8: ' . $row['SL'] . '</p>';
                }

                // Số điểm nhỏ hơn 5
                $sql = "SELECT  COUNT(Diem) as SL 
                        FROM diemso 
                        WHERE MaBuoiHoc = '$maBH' AND Diem < 5  AND Diem >= 0 AND Diem <= 10";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_array($result)) {
                    echo '<p> Số điểm nhỏ hơn 5: ' . $row['SL'] . '</p>';
                }
                ?>
            </div>
            <!-- End: Statistics-bar -->

        </div>
        <!-- Start: Student-list -->

        <h1 class="tittle">DANH SÁCH HỌC SINH</h1>
        <button id="exportExcel" style="font-size: 20px;float:right">Xuất</button>
        <table class="student-list" data-excel-name="Bảng điểm" style="width: 100%">
            <tr>
                <td>SĐT</td>
                <td>Họ Tên</td>
                <td>Ngày Sinh</td>
                <td>Lớp</td>
                <td>Trường</td>
                <td style="width: 5rem">Điểm</td>
                <td>Xếp Hạng</td>
            </tr>
            <?php
            $query = "SELECT hs.MaHS, Ho, Ten, Lop, Truong, NgaySinh, Phone, Diem, pl.MaPhanLop 
                FROM hocsinh hs JOIN phanlop pl ON hs.MaHS = pl.MaHS JOIN diemso ds ON pl.MaPhanLop = ds.MaPhanLop
                WHERE ds.MaBuoiHoc = '$maBH'
                $orderby";

            $result = $conn->query($query);
            while ($row = mysqli_fetch_array($result)) {
                echo '<tr data-mapl="' . $row['MaPhanLop'] . '">';
                echo '<td onclick="redirectToStudent(\'' . $row['MaHS'] . '\', \'' . $row['MaPhanLop'] . '\')">' . $row['Phone'] . '</td>';
                echo '<td onclick="redirectToStudent(\'' . $row['MaHS'] . '\', \'' . $row['MaPhanLop'] . '\')" style= "text-align:left;">' . $row['Ho'] . ' ' . $row['Ten'] . '</td>';
                echo '<td>' . $row['NgaySinh'] . '</td>';
                echo '<td>' . $row['Lop'] . '</td>';
                echo '<td>' . $row['Truong'] . '</td>';
                // Input chỉnh sửa điểm
                echo '<td style="padding: 0">';
                echo '<input class="score-input" id="iScore" name="AlterScore" type="text" value="' . $row['Diem'] . '" style="width: 50px;" />';
                echo '</td>';

                // Thứ hạng
                if (is_numeric($row['Diem'])) {
                    $maPL = $row['MaPhanLop'];
                    $sqlrank = "SELECT Rank 
                            FROM (SELECT MaPhanLop, RANK() OVER(ORDER BY CAST(Diem AS FLOAT) DESC) AS Rank
                                FROM diemso
                                WHERE MaBuoiHoc = '$maBH') AS Ranking 
                            WHERE MaPhanLop = '$maPL'";
                    $results = mysqli_query($conn, $sqlrank);
                    if ($rankRow = $results->fetch_assoc()) {
                        echo '<td class="rank-cell">' . $rankRow['Rank'] . '</td>';
                    } else {
                        echo '<td class="rank-cell">Không</td>';
                    }
                } else {
                    echo '<td class="rank-cell">Không</td>';
                }


                echo '</tr>';
            }
            ?>
        </table>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const scoreInputs = document.querySelectorAll(".score-input");

                scoreInputs.forEach(input => {
                    input.addEventListener("change", function () {
                        const newScore = this.value;
                        const row = this.closest("tr");
                        const maPhanLop = row.dataset.mapl;
                        const maBuoiHoc = "<?php echo $maBH; ?>"; // Lấy MaBuoiHoc từ PHP

                        // AJAX request
                        fetch("/score/alter_score.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                MaPhanLop: maPhanLop,
                                MaBuoiHoc: maBuoiHoc,
                                Score: newScore
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert("Điểm đã được cập nhật!");
                                    // Cập nhật thứ hạng
                                    row.querySelector(".rank-cell").textContent = data.newRank || "Không";
                                } else {
                                    alert("Lỗi khi cập nhật điểm: " + data.message);
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                alert("Lỗi kết nối!");
                            });
                    });
                });
            });

        </script>

        <!-- End: Student-list -->
        <table class="student-list" data-excel-name="Danh sách học sinh không có điểm">
            <h1>DANH SÁCH HỌC SINH NGHỈ HỌC</h1>
            <tr>
                <td>SĐT</td>
                <td>Họ Tên</td>
                <td>Ngày Sinh</td>
                <td>Lớp</td>
                <td>Trường</td>
            </tr>
            <?php
            $query = "SELECT hs.MaHS, Ho, Ten, Lop, Truong, NgaySinh, Phone, pl.MaPhanLop 
            FROM hocsinh hs JOIN phanlop pl ON hs.MaHS = pl.MaHS 
            WHERE MaPhanLop not in (SELECT MaPhanLop FROM diemso WHERE MaBuoiHoc = '$maBH')
            and pl.MaLop = $maLop 
            and pl.TinhTrang = 1
            ORDER BY Ten, Ho
            ";
            $result = $conn->query($query);
            while ($row = mysqli_fetch_array($result)) {
                echo '<form method = "POST" action ="/student.php">';
                echo '<tr>';
                echo '<td onclick="redirectToStudent(\'' . $row['MaHS'] . '\', \'' . $row['MaPhanLop'] . '\')">' . $row['Phone'] . '</td>';
                echo '<td onclick="redirectToStudent(\'' . $row['MaHS'] . '\', \'' . $row['MaPhanLop'] . '\')" style= "text-align:left;">' . $row['Ho'] . ' ' . $row['Ten'] . '</td>';
                echo '<td>' . $row['NgaySinh'] . '</td>';
                echo '<td>' . $row['Lop'] . '</td>';
                echo '</form>';
                echo '<td>' . $row['Truong'] . '</td>';
                echo '</tr>';

            }
            ?>
        </table>
</body>

</html>

<script>
    function redirectToStudent(maHS, maPL) {
        window.location.href = `../student/student.php?MaHS=${maHS}&MaPL=${maPL}`;
    }
</script>

<script>
    var table2excel = new Table2Excel();

    document.getElementById('exportExcel').addEventListener('click', function () {
        table2excel.export(document.querySelectorAll('table'));
    });
</script>