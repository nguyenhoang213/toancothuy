<?php
include('../connection.php');
session_start();

if (isset($_GET['MaHS']) && isset($_GET['MaPL'])) {
    $maHS = $_GET['MaHS'];
    $maPL = $_GET['MaPL'];
    $getLop = $conn->query("SELECT MaLop FROM phanlop WHERE MaPhanLop = '$maPL'")->fetch_assoc();
    $maLop = $getLop['MaLop'];
    if (!isset($_SESSION['uname'])) {
        get_ip($conn, $maHS, $maLop);
    }
} else {
    header('Location: ..../index.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/image/logoTH.jpg">
    <title>Thông tin học sinh</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/student_style.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/font/themify-icons/themify-icons.css">
    <script src="https://kit.fontawesome.com/8fcd74b091.js" crossorigin="anonymous"></script>
    <style>
        @media only screen and (max-width: 600px) {
            .student_attribute {
                display: none;
            }

            .student_info_box {
                width: 90vw !important;
                padding: 20px !important;
            }

            .student_info_text {
                width: 60vw !important;
            }
        }
    </style>
</head>

<body>
    <div id="header">
        <div class="contact_phone">
            <p><i class="fa-solid fa-phone icon"></i> 0918083884</p>
            <p style="font-size: 20px;">|</p>
            <p><i class="ti-email icon"></i> lethuyntt0708@gmail.com</p>
        </div>
        <div class="network_contact">
            <a href="https://www.facebook.com/thuyvytrinhkhue?mibextid=LQQJ4d"><i class="ti-facebook icon"></i></a>
            <a><i class="ti-google icon"></i></a>
            <a><i class="ti-sharethis icon"></i></a>
        </div>
    </div>
    <div id="slider">
        <div class="logo">
            <a href="../index.php">
                <img src="../assets/image/logoTH.jpg" alt="logo">
            </a>
            <a href="../index.php">
                <h1>Lê Thị Thanh Thủy</h1>
            </a>
            <!-- Nút menu sẽ chỉ hiển thị ở chế độ responsive -->
            <button class="menu_button" onclick="toggleMenu()"><i class="ti-view-list icon"></i></button>
        </div>
        <div class="menu" id="menu">
            <a href="../index.php">Trang chủ</a>
            <a href="../index.php#content">Tra cứu</a>
            <a href="../login.php">Quản trị</a>
            <a href="../index.php#footer">Liên hệ</a>
        </div>

    </div>
    <div id="content">
        <div class="student_info">
            <h1>Thông Tin Học Sinh</h1>
            <!-- Đặc điểm -->
            <?php
            if (isset($_POST["Delete"])) {
                $Delete = $_POST["Delete"];
                $sqldel = "DELETE FROM dacdiem Where MaHS = '$maHS' and DacDiem= '$Delete'";
                $conn->query($sqldel);
            }
            if (isset($_POST["AddAttr"]) != "") {
                $AddAttr = $_POST["AddAttr"];
                $sqladd = "INSERT INTO dacdiem(MaHS, DacDiem) VALUES ('$maHS' ,'$AddAttr')";
                $conn->query($sqladd);
            }
            ?>

            <!-- Thông tin học sinh -->
            <?php
            $sql = "SELECT DISTINCT * FROM hocsinh hs join phanlop pl on hs.MaHS = pl.MaHS Where hs.MaHS = '$maHS' and pl.MaPhanLop = '$maPL'";
            $result = $conn->query($sql);
            ?>
            <?php
            if (isset($_SESSION['uname'])) {
                echo '<div class ="student_info_box" style = "width: 65vw">';
                $row = mysqli_fetch_array($result = $conn->query($sql));
                if ($row['Anh']) {
                    $avt = '../assets/image/anhhs/' . $row['Anh'];
                } else
                    $avt = "../assets/image/anhhs/avt.png";
                echo '<img src= ' . $avt . '>';
                echo '<div class ="student_info_text" style = "width: 30vw">';
                getInfor($row);
                echo '</div>';
                echo '<div class ="student_attribute" style = "text-align: center; width: 20vw; border-left: solid">';
                echo '<h2 style = "margin-top: -15px">Nhận Xét</h1>';
                echo '<ul style = "text-align: left; height: 30vh">';
                echo '<form action="" method="POST">';
                $sqlatt = "Select * from dacdiem Where MaHS = '$maHS'";
                $resultatt = mysqli_query($conn, $sqlatt);
                while ($rowatt = $resultatt->fetch_assoc()) {
                    echo '<li style ="margin: 4px; font-size: 20px; line-height: 20px">' . $rowatt['DacDiem'] . '<button name ="Delete" method="submit" value = "' . $rowatt['DacDiem'] . '" style ="margin-left: 10px; background-color: inherit; border-radius: 6px; line-height: 20px"> X </button></li>';
                }
                echo '</ul>';
                echo '</form>';
                echo '<form action="" method="POST">';
                echo '<input name = "AddAttr" type="text" style="width:60%; padding: 0 4% ;font-size: 18px; height: 30px; background-color: inherit; border-radius:10px">' . '</input>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
            } else {
                echo '<div class ="student_info_box">';
                $row = mysqli_fetch_array($result = $conn->query($sql));
                if ($row['Anh']) {
                    $avt = '../assets/image/anhhs/' . $row['Anh'];
                } else
                    $avt = "../assets/image/anhhs/avt.png";
                echo '<img src= ' . $avt . '>';
                echo '<div class ="student_info_text">';
                getInfor($row);
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <?php
        $query = "SELECT bh.Ngay, ds.Diem, (SELECT ROUND(AVG(ds2.Diem), 2)
                                                    FROM diemso ds2
                                                    WHERE ds2.MaBuoiHoc = ds.MaBuoiHoc
                                                    AND Diem >= 0 AND Diem <= 10  
                                                    AND NOT ((Diem > 'a*' AND Diem < 'z*') OR (Diem > 'A*' AND Diem < 'Z*'))) AS TBL
                FROM buoihoc bh JOIN diemso ds ON bh.MaBuoiHoc = ds.MaBuoiHoc 
                WHERE ds.MaPhanLop = $maPL and NOT ((Diem > 'a*' AND Diem < 'z*') OR (Diem > 'A*' AND Diem < 'Z*'))";
        $result = mysqli_query($conn, $query);
        $chart_data = '';
        $data_points = [];
        $index = 0;
        if ($result->num_rows > 1) {
            ?>
            <div class="chart">
                <h1>Biểu Đồ Thống Kê</h1>
                <div class="line-chart">
                    <i style="color: gray; margin-bottom: 0; ">Click vào tên đường để ẩn</i>
                    <canvas id="myChart1"></canvas>

                    <?php
                    while ($row = $result->fetch_assoc()) {
                        $chart_data .= "{ Ngày:'" . $row["Ngay"] . "', Điểm:" . $row["Diem"] . ", ĐiểmTBL:" . $row["TBL"] . "}, ";
                        $data_points[] = ["x" => $index, "y" => $row["Diem"]]; // Dữ liệu hồi quy sử dụng chỉ số tuần tự
                        $index++;
                    }
                    function calculateLinearRegression($data_points)
                    {
                        $n = count($data_points);
                        $sum_x = $sum_y = $sum_x2 = $sum_xy = 0;

                        foreach ($data_points as $point) {
                            $x = $point['x'];
                            $y = $point['y'];
                            $sum_x += $x;
                            $sum_y += $y;
                            $sum_x2 += $x * $x;
                            $sum_xy += $x * $y;
                        }

                        $slope = ($n * $sum_xy - $sum_x * $sum_y) / ($n * $sum_x2 - $sum_x * $sum_x);
                        $intercept = ($sum_y - $slope * $sum_x) / $n;

                        return [$slope, $intercept];
                    }

                    list($slope, $intercept) = calculateLinearRegression($data_points);

                    // Tạo dữ liệu hồi quy tuyến tính
                    $regression_points = [];
                    foreach ($data_points as $point) {
                        $regression_points[] = ["x" => $point['x'], "y" => $slope * $point['x'] + $intercept];
                    }
                    ?>

                    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
                    <script
                        src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js">
                        </script>
                    <script
                        src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js">
                        </script>
                    <script>

                        // Chart.register(ChartDataLabels);
                        const data = [
                            <?php echo $chart_data ?>
                        ];

                        // Tạo dataset cho hồi quy tuyến tính
                        const regressionData = [
                            <?php foreach ($regression_points as $point) {
                                echo "{x: " . $point['x'] . ", y: " . $point['y'] . "}, ";
                            } ?>
                        ];

                        const ctx = document.getElementById('myChart1').getContext('2d');
                        const myChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: data.map(row => row.Ngày),
                                datasets: [{
                                    label: 'Điểm Học Sinh',
                                    data: data.map(row => row.Điểm),
                                    borderColor: 'rgb(54, 162, 235)'
                                },
                                {
                                    label: 'Điểm Trung Bình Lớp',
                                    data: data.map(row => row.ĐiểmTBL),
                                    borderColor: 'red'
                                },
                                {
                                    label: 'Hồi Quy Tuyến Tính',
                                    data: regressionData,
                                    borderColor: 'green',
                                    borderDash: [5, 5],
                                    fill: false,
                                    pointRadius: 0
                                }
                                ]
                            },
                            options: {
                                maintainAspectRatio: false,
                                responsive: true,
                                layout: {
                                    padding: 40
                                },
                                scales: {
                                    y: {
                                        suggestedMin: 0,
                                        suggestedMax: 10
                                    }
                                },
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
                </div>
            </div>
        <?php } else {
            echo '<br>';
            echo '<br>';
        }
        ?>

        <div class="score_table">
            <h1>BẢNG ĐIỂM</h1>
            <table>
                <tr>
                    <td style="width: 200px;">Ngày</td>
                    <td style="width: 300px;">Tên Bài</td>
                    <td style="width: 150px;">Điểm</td>
                    <td style="width: 150px;">Điểm TB Lớp</td>
                    <td style="width: 100px;">Xếp Hạng</td>
                    <td style="width: 200px;">Đáp án</td>
                    <!-- <td style="width: 200px;">BTVN</td> -->
                </tr>
                <?php
                $query = "SELECT bh.MaBuoiHoc, bh.Ngay, ds.MaDiemSo, ds.Diem, bh.TenBai, bh.DapAn,
                (SELECT ROUND(AVG(ds2.Diem), 2) FROM diemso ds2 
                WHERE ds2.MaBuoiHoc = ds.MaBuoiHoc AND ds2.Diem >= 0 AND ds2.Diem <= 10 AND NOT ((Diem > 'a*' AND Diem < 'z*') OR (Diem > 'A*' AND Diem < 'Z*'))) AS TBL
                FROM buoihoc bh 
                JOIN diemso ds ON bh.MaBuoiHoc = ds.MaBuoiHoc 
                WHERE ds.MaPhanLop = $maPL
                ORDER BY Ngay DESC
                ";

                $result = mysqli_query($conn, $query);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr> <td>' . $row['Ngay'] . '</td>';
                        echo '<td>' . $row['TenBai'] . '</td>';
                        echo '<td>' . $row['Diem'] . '</td>';
                        echo '<td>' . $row['TBL'] . '</td>';
                        $date = $row['Ngay'];
                        if (is_numeric($row['Diem'])) {
                            $maBH = $row['MaBuoiHoc'];
                            $maDS = $row['MaDiemSo'];
                            $sqlrank = "Select XepHang FROM (SELECT ds.MaBuoiHoc, ds.MaDiemSo, ds.Diem, RANK() OVER (PARTITION BY ds.MaBuoiHoc ORDER BY ds.Diem DESC) AS XepHang FROM diemso ds WHERE ds.MaBuoiHoc = $maBH) as Ranking WHERE Ranking.MaDiemSo =  $maDS";
                            $rank = $conn->query($sqlrank)->fetch_assoc();
                            echo '<td>' . $rank['XepHang'] . '</td>';
                        } else {
                            echo '<td>Không</td>';
                        }
                        if ($row['DapAn']) {
                            echo '<td> <a href = "' . $row['DapAn'] . '" style ="color: #3300ff; text-decoration: underline"> Xem đáp án</a> </td>';
                        } else {
                            echo '<td> Chưa có đáp án </td>';
                        }
                        // if ($row['BTVN']) {
                        //     echo '<td style = "color: blue"> Đã nộp </td>';
                        // } else {
                        //     echo '<td> <a href = "" style ="color: red; text-decoration: underline"> Nộp bài </a> </td>';
                        // }
                        echo '</tr>';
                    }
                }
                ?>
            </table>
        </div>
    </div>
    <div id="footer">
        <div class="address">
            <div class="get-in-touch">
                <div>
                    <h1>Liên hệ</h1>
                </div>
                <div>
                    <h1>Lê Thị Thanh Thủy</h1>
                    <p><i class="ti-map-alt icon"></i> Địa chỉ </p>
                    <p><i class="ti-headphone-alt icon"></i> 0918 083 884</p>
                    <p><a href="https://www.facebook.com/thuyvytrinhkhue?mibextid=LQQJ4d"><i
                                class="ti-facebook icon"></i></a> Tất cả vì
                        sự tiến bộ của học trò!</p>
                    <p><i class="ti-email icon"></i> lethuyntt0708@gmail.com</p>
                </div>
            </div>
        </div>
        <div class="description">
            <p>Copyright ©<a href="https://www.facebook.com/as.royal03/">Nguyễn Như Hoàng</a>. All Rights Reserved.</p>
            <p>Desgined by <a href="https://www.facebook.com/as.royal03/">Nguyễn Như Hoàng</a>.</p>
        </div>
    </div>
</body>
<script>
    function toggleMenu() {
        const menu = document.getElementById("menu");
        const logo = document.querySelector(".logo");

        // Chuyển đổi trạng thái hiển thị của menu
        if (menu.style.display === "block") {
            menu.style.display = "none"; // Ẩn menu
            logo.style.display = "flex"; // Hiện lại logo và tên
        } else {
            menu.style.display = "block"; // Hiện menu
            logo.style.display = "none"; // Ẩn logo và tên
        }
    }


</script>

</html>


<?php
function getInfor($row)
{ {
        echo '<p>Họ Tên: ' . $row['Ho'] . " " . $row['Ten'];
        echo '<p>SĐT: ' . $row['Phone'];
        echo '<p>Ngày Sinh: ' . $row['NgaySinh'];
        echo '<p>Lớp: ' . $row['Lop'];
        echo '<p>Trường: ' . $row['Truong'];
    }
}
?>

<script>
    function Show() {
        document.getElementById("menu").classList.toggle("show");
    }
</script>


<!-- get ip -->
<?php
function get_client_ip()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function get_ip($conn, $maHS, $maLop)
{
    $ip = get_client_ip();
    $date = date('Y-m-d H:i:s');
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    // Phân tích User-Agent để xác định thiết bị
    $device = '';
    if (preg_match('/mobile/i', $user_agent)) {
        $device = 'Mobile';
    } elseif (preg_match('/tablet/i', $user_agent)) {
        $device = 'Tablet';
    } elseif (preg_match('/iPad/i', $user_agent)) {
        $device = 'iPad';
    } elseif (preg_match('/Macintosh|Windows|Linux/i', $user_agent)) {
        $device = 'Desktop';
    } else {
        $device = 'Unknown';
    }

    // Truy vấn chèn thông tin vào cơ sở dữ liệu
    $sqli = "INSERT INTO hoatdonghs(MaHS, MaLop ,IP, ThietBi, ThoiGian) VALUES ('$maHS',' $maLop' , '$ip', '$device', '$date')";
    $conn->query($sqli);
}
?>