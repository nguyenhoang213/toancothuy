<?php
include("../connection.php");
session_start();
include("../side_nav.php");

if (!$_SESSION['uname'])
    echo '
    <script>
        window.location.href="../index.php";
    </script>';

if (isset($_GET['id'])) {
    $maLop = $_GET['id'];
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all'; // "all" hoặc "month"
$start_date = '';
$end_date = '';

if ($filter == 'month') {
    $month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
    $start_date = $month . "-01";
    $end_date = date("Y-m-t", strtotime($start_date));
    $where_clause = "bh.Ngay >= '$start_date' AND bh.Ngay <= '$end_date'";
} else {
    $where_clause = "1 = 1"; // Toàn bộ dữ liệu
}

$query = "
    SELECT
        hs.MaHS,
        CONCAT(hs.Ho, ' ', hs.Ten) AS HoTen,
        COUNT(ds.Diem) AS SoBaiThi,
        ROUND(AVG(CAST(ds.Diem AS FLOAT)), 2) AS DiemTrungBinh,
        ROUND(SUM(CAST(ds.Diem AS FLOAT)),2) AS TongDiem,
        RANK() OVER (ORDER BY SUM(CAST(ds.Diem AS FLOAT)) DESC) AS XepHang
    FROM
        hocsinh hs
    LEFT JOIN phanlop pl ON hs.MaHS = pl.MaHS
    LEFT JOIN diemso ds ON pl.MaPhanLop = ds.MaPhanLop
    LEFT JOIN buoihoc bh ON ds.MaBuoiHoc = bh.MaBuoiHoc
    WHERE
        $where_clause and pl.MaLop = '$maLop'
    GROUP BY
        hs.MaHS, hs.Ho, hs.Ten
    ORDER BY XepHang
";

$class_info = $conn->query("SELECT * FROM lop WHERE MaLop = '$maLop'")->fetch_assoc();

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>

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
    <script src="../assets/table2excel.js"></script>
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

        table {
            width: 80vw;
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
    </style>
</head>

<body>

    <div class="content">
        <h1 style="padding-top: 20px;">Xếp hạng <?php echo $class_info['TenLop'] ?></h1>
        <form method="GET" style="margin: 15px 0">
            <label for="filter">Lọc theo:</label>
            <input type="hidden" name="id" value="<?php echo $maLop ?>">
            <select name="filter" id="filter" onchange="this.form.submit()" style="font-size:20px">
                <option value="all" <?php echo $filter == 'all' ? 'selected' : ''; ?>>Toàn bộ</option>
                <option value="month" <?php echo $filter == 'month' ? 'selected' : ''; ?>>Theo tháng</option>
            </select>
            <?php if ($filter == 'month') { ?>
                <input type="month" name="month" value="<?php echo isset($month) ? $month : ''; ?>"
                    onchange="this.form.submit()" style="font-size:20px">
            <?php } ?>
        </form>

        <table border="1">
            <tr>
                <th>Thứ Hạng</th>
                <th>Họ Tên</th>
                <th>Số Bài Thi</th>
                <th>Tổng Điểm</th>
                <th>Điểm Trung Bình</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) {
                $maHS = $row['MaHS'];
                $phanlop_info = $conn->query("SELECT MaPhanLop FROM phanlop WHERE MaLop = '$maLop' and MaHS='$maHS'")->fetch_assoc();
                $maPL = $phanlop_info['MaPhanLop'];
                echo '<tr>
                    <td onclick="redirectToStudent(\'' . $row['MaHS'] . '\', \'' . $maPL . '\'")>' . $row['XepHang'] . '</td>
                    <td onclick="redirectToStudent(\'' . $row['MaHS'] . '\', \'' . $maPL . '\')" style="text-align: left">' . $row['HoTen'] . '</td>
                    <td>' . $row['SoBaiThi'] . '</td>
                    <td>' . $row['TongDiem'] . '</td>
                    <td>' . $row['DiemTrungBinh'] . '</td>
                </tr>';
            } ?>
        </table>
    </div>
</body>

</html>

<script>
    function redirectToStudent(maHS, maPL) {
        window.location.href = `../student/student.php?MaHS=${maHS}&MaPL=${maPL}`;
    }
</script>