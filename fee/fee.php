<?php
include("../connection.php");
session_start();
include("../side_nav.php");

if (!$_SESSION['uname']) {
    echo '<script>
        window.location.href="../index.php";
    </script>';
}

if (isset($_GET['id'])) {
    $maLop = $_GET['id'];
} else {
    echo '<script>
        alert("Không tìm thấy mã lớp");
        window.location.href="../class/class_list.php";
    </script>';
}

if (isset($_GET['month']) && !empty($_GET['month'])) {
    // Tách tháng và năm từ giá trị input type="month"
    $month = $_GET['month'];
    $monthParts = explode('-', $month); // Tách thành năm và tháng
    $year = $monthParts[0]; // Phần năm
    $selectedMonth = $monthParts[1]; // Phần tháng
    $WHERE = "AND year(Ngay) = '$year' and month(Ngay) = '$selectedMonth'";
} else {
    $WHERE = "";
}


// Lấy thông tin lớp học
$class_info = $conn->query("SELECT * FROM lop WHERE MaLop = '$maLop'")->fetch_assoc();

// Lấy danh sách học sinh và tính học phí
$query = "
SELECT hs.MaHS, CONCAT(hs.Ho, ' ', hs.Ten) AS HoTen, NgaySinh, Lop, Truong, Phone, MaPL, SoBuoi, lop.HocPhi
FROM hocsinh hs JOIN phanlop pl ON hs.MaHS = pl.MaHS JOIN lop ON pl.MaLop = lop.MaLop
LEFT JOIN (SELECT MaPL, COUNT(MaPL) as SoBuoi FROM buoihoc bh JOIN diemdanh dd ON bh.MaBuoiHoc = dd.MaBH WHERE 1 $WHERE GROUP By MaPL) as dd ON pl.MaPhanLop = dd.MaPL 
WHERE Lop.MaLop = '$maLop' ORDER BY Ten, Ho";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tính Học Phí</title>
    <link rel="stylesheet" href="../assets/css/admin-navigation.css">
    <link rel="stylesheet" href="../assets/css/admin-statistical.css">
    <link rel="icon" type="image/x-icon" href="../assets/image/logo.png">
    <script src="../assets/table2excel.js"></script>
    <style>
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

        /* Overlay nền mờ */
        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        /* Modal chính */
        #modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            border-radius: 8px;
            width: 40%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            display: none;
            padding: 20px;
            text-align: center;
        }

        /* Tiêu đề modal */
        #modal h2 {
            margin: 0 0 20px 0;
            font-size: 24px;
            color: #333;
        }

        /* Danh sách buổi học */
        #modal ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            max-height: 200px;
            overflow-y: auto;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }

        #modal ul li {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        #modal ul li:last-child {
            border-bottom: none;
        }

        /* Nút đóng modal */
        #modal button.close-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #d9534f;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        #modal button.close-btn:hover {
            background-color: #c9302c;
        }
    </style>
</head>

<body>
    <div class="content">
        <h1 class="tittle">BẢNG TÍNH HỌC PHÍ LỚP <?php echo $class_info['TenLop']; ?></h1>

        <form class="form" method="GET" action="" style="margin: 20px 0;">
            <input type="hidden" name="id" value="<?php echo $maLop; ?>">
            <!-- Các trường tìm kiếm khác -->
            <label for="month">Chọn tháng và năm:</label>
            <input type="month" name="month"
                value="<?php echo isset($_GET['month']) ? htmlspecialchars($_GET['month']) : ''; ?>"
                onchange="this.form.submit()" style="padding: 8px; width: 250px; font-size: 16px;">

            <button type="submit" class="submit-btn" style="padding: 8px; font-size: 16px">Tìm kiếm</button>
        </form>


        <button id="exportExcel" class="export-button">Xuất Excel</button>
        <table class="student-list" data-excel-name="Bang_Hoc_Phi" style="width: 100%; margin-top: 20px;">
            <thead>
                <tr>
                    <th>Số Thứ Tự</th>
                    <th>Họ Tên</th>
                    <th>Ngày Sinh</th>
                    <th>Lớp</th>
                    <th>Trường</th>
                    <th>Số Buổi Học</th>
                    <th>Học Phí 1 Buổi</th>
                    <th>Tổng Học Phí</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stt = 1;
                while ($row = $result->fetch_assoc()) {
                    $hocPhi1Buoi = $row['HocPhi'];
                    $soBuoi = $row['SoBuoi'];
                    $tongHocPhi = $hocPhi1Buoi * $soBuoi;

                    echo "<tr>";
                    echo "<td>" . $stt++ . "</td>";
                    echo "<td style='text-align: left' class='ho-ten' data-mapl='" . $row['MaPL'] . "'>";
                    echo $row['HoTen'];
                    echo "</td>";

                    echo "<td>" . $row['NgaySinh'] . "</td>";
                    echo "<td>" . $row['Lop'] . "</td>";
                    echo "<td>" . $row['Truong'] . "</td>";
                    echo "<td class='so-buoi' data-mapl='" . $row['MaPL'] . "'>";
                    if ($soBuoi) {
                        echo $soBuoi;
                    } else {
                        echo "0";
                    }
                    echo "</td>";

                    echo "<td>" . number_format($hocPhi1Buoi, 0, ',', '.') . " VND</td>";
                    echo "<td>" . number_format($tongHocPhi, 0, ',', '.') . " VND</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <div id="modal"
            style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:#fff; border:1px solid #ddd; padding:20px; z-index:1000;">
            <h3>Danh sách buổi học</h3>
            <ul id="list-buoi-hoc"></ul>
            <button onclick="closeModal()">Đóng</button>
        </div>
        <div id="overlay"
            style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;"
            onclick="closeModal()"></div>

    </div>
    <script>
        var table2excel = new Table2Excel();
        document.getElementById('exportExcel').addEventListener('click', function () {
            table2excel.export(document.querySelectorAll('table'));
        });
    </script>
</body>

</html>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".so-buoi").forEach(function (cell) {
            cell.addEventListener("click", function () {
                var maPL = this.getAttribute("data-mapl");
                fetchBuoiHoc(maPL);
            });
        });
        document.querySelectorAll(".ho-ten").forEach(function (cell) {
            cell.addEventListener("click", function () {
                var maPL = this.getAttribute("data-mapl");
                fetchBuoiHoc(maPL);
            });
        });
    });

    function fetchBuoiHoc(maPL) {
        // Lấy giá trị tháng/năm từ input
        const monthInput = document.querySelector("input[name='month']");
        let year = "";
        let month = "";

        if (monthInput && monthInput.value) {
            const monthParts = monthInput.value.split("-");
            year = monthParts[0]; // Phần năm
            month = monthParts[1]; // Phần tháng
        }

        // Gửi AJAX yêu cầu danh sách buổi học theo tháng/năm
        fetch(`get_lesson.php?mapl=${maPL}&year=${year}&month=${month}`)
            .then(response => response.json())
            .then(data => {
                // Hiển thị danh sách buổi học
                const list = document.getElementById("list-buoi-hoc");
                list.innerHTML = "";
                if (data.length > 0) {
                    data.forEach(buoi => {
                        const li = document.createElement("li");
                        li.textContent = `Ngày: ${buoi.Ngay}, Tên bài: ${buoi.TenBai}`;
                        list.appendChild(li);
                    });
                } else {
                    const li = document.createElement("li");
                    li.textContent = "Không có buổi học nào.";
                    list.appendChild(li);
                }
                openModal();
            })
            .catch(error => console.error("Error:", error));
    }

    function openModal() {
        document.getElementById("modal").style.display = "block";
        document.getElementById("overlay").style.display = "block";
    }

    function closeModal() {
        document.getElementById("modal").style.display = "none";
        document.getElementById("overlay").style.display = "none";
    }
</script>