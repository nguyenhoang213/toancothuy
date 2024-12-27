<?php
include("../connection.php");
session_start();
include("../side_nav.php");

if (!$_SESSION['uname'])
    echo '
    <script>
        window.location.href="../index.php";
    </script>';

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

// Lấy thông tin lớp học
$class_info = $conn->query("SELECT * FROM lop WHERE MaLop = '$maLop'")->fetch_assoc();
$lesson_info = $conn->query("SELECT * FROM buoihoc WHERE MaBuoiHoc = '$maBH'")->fetch_assoc();
$sort = isset($_GET['sort']) ? $_GET['sort'] : 0;
if ($sort == 0) {
    $orderby = "ORDER BY Ten, Ho";
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
    <title>Điểm Danh</title>
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

            .statistics-bar {
                text-align: left;
                box-shadow: 10px 10px 8px 10px #888888;
                margin-left: 15px;
                width: 300px;
                padding: 30px 0 0 40px;
                font-size: 22px;
                color: rgb(0, 0, 0);
                height: 420px;
            }

            .statistics-bar>p {
                margin: 10px;
            }

            .chart-bar {
                margin-left: 20px;
                width: 55vw;
                box-shadow: 10px 10px 8px 10px #888888;
                margin-bottom: 50px;
                height: 450px;
                padding: 0px 20px 0 10px;
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

            .statistics-bar {
                display: none;
            }

            .chart-bar {
                width: auto;
                height: 450px;
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
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            z-index: 1000;
        }

        .fixed-button:hover {
            background-color: #0056b3;
        }

        .chart-barjs {
            display: flex;
            justify-content: center;
        }

        input[name="diemdanh"] {
            width: 20px;
            height: 20px;
            accent-color: #007bff;
            /* Màu xanh lam */
            cursor: pointer;
            border: 2px solid #ccc;
            border-radius: 4px;
            /* Bo góc */
        }

        .export-button {
            font-size: 16px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            float: right;
        }

        .export-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="content">
        <h1 class="tittle">BẢNG ĐIỂM DANH HỌC SINH <?php echo $class_info['TenLop'] ?></h1>
        <h2>Ngày: <?php echo $lesson_info['Ngay'] ?></h2>
        <button id="exportExcel" class="export-button">Xuất Excel</button>
        <table class="student-list" data-excel-name="Bảng điểm" style="width: 100%">
            <tr>
                <td>SĐT</td>
                <td>Họ Tên</td>
                <td>Ngày Sinh</td>
                <td>Lớp</td>
                <td>Trường</td>
                <td style="width: 5rem">Điểm danh</td>
            </tr>
            <?php
            $query = "SELECT hs.MaHS, Ho, Ten, Lop, Truong, NgaySinh, Phone, pl.MaPhanLop, (SELECT COUNT(*) FROM diemdanh dd WHERE dd.MaBH = bh.MaBuoiHoc and dd.MaPL = pl.MaPhanLop) as DiemDanh 
                    FROM hocsinh hs JOIN phanlop pl ON hs.MaHS = pl.MaHS JOIN buoihoc bh ON pl.MaLop = bh.MaLop WHERE bh.MaBuoiHoc =  '$maBH'
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
                echo '<input name="diemdanh" type="checkbox" '
                    . ($row['DiemDanh'] == 1 ? 'checked' : '')
                    . ' style="width: 50px;" onchange="updateAttendance(\'' . $maBH . '\', \'' . $row['MaPhanLop'] . '\', this)">';
                echo '</td>';
            }
            ?>
        </table>
    </div>
</body>

</html>

<script>
    function updateAttendance(maBuoiHoc, maPhanLop, checkbox) {
        let attended = checkbox.checked ? 1 : 0; // 1 nếu được tích, 0 nếu bỏ tích

        // Gửi AJAX yêu cầu đến server
        fetch('../rollcall/update_attendance.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ maBuoiHoc: maBuoiHoc, maPhanLop: maPhanLop, attended: attended }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    // alert('Cập nhật điểm danh thành công!');
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                    // Khôi phục trạng thái checkbox nếu có lỗi
                    checkbox.checked = !attended;
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('Không thể cập nhật điểm danh. Vui lòng thử lại!');
                // Khôi phục trạng thái checkbox nếu có lỗi
                checkbox.checked = !attended;
            });
    }
</script>

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