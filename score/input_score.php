<?php
include("../connection.php");
session_start();
include("../class.php");
include("../side_nav.php");

if (!$_SESSION['uname'])
    header('Location: https://vatlytruongnghiem.edu.vn/');

if (isset($_GET['MaLop']) && isset($_GET['MaBH'])) {
    $maLop = $_GET['MaLop'];
    $maBH = $_GET['MaBH'];
} else if (!isset($_GET['MaLop'])) {
    echo '<script>
    alert("Không tìm thấy mã lớp");
    window.location.href="/class/class_list.php";
    </script>';
} else if (!isset($_GET['MaBuoiHoc'])) {
    $maLop = $_GET['MaLop'];
    echo '<script>
    alert("Không tìm thấy mã buổi học!");
    window.location.href="/lesson/lesson_list.php?id=' . $maLop . '";
    </script>';
}
$class_info = $conn->query("SELECT * FROM lop WHERE MaLop = '$maLop'")->fetch_assoc();
$lesson_info = $conn->query("SELECT * FROM buoihoc WHERE MaBuoiHoc = '$maBH'")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/x-icon" href="../assets/image/logo.png">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm điểm học sinh <?php echo $title; ?></title>
    <link rel="icon" type="image/x-icon" href="../assets/image/logo.png">
    <link rel="stylesheet" href="../assets/css/admin-input.css">
    <link rel="stylesheet" href="../assets/css/admin-statistical.css">
    <link rel="stylesheet" href="../assets/css/admin-navigation.css">
    <link rel="stylesheet" href="../assets/font/themify-icons/themify-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        @media screen and (min-width: 600px) {
            .Score {
                text-align: center;
                padding: 10px 20px;
                font-size: 22px;
                width: 100% !important;
                border: none;
                font-family: Times New Roman;
            }
        }

        @media screen and (max-width: 600px) {
            .Score {
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
            padding: 5px;
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
    <!-- Fixed Button -->
    <button class="fixed-button" onclick="nhapDiem()">
        <i class="fa-solid fa-chart-bar"></i>
    </button>

    <script>
        function nhapDiem() {
            window.location.href = "score_statistical.php?MaBuoiHoc=<?php echo $maBH ?>&MaLop=<?php echo $maLop ?>";
        }
    </script>

    <!-- Fixed Button -->
    <button class="fixed-button" onclick="themHS()" style="right: 85px">
        <i class="fa-solid fa-user-plus"></i>
    </button>

    <script>
        function themHS() {
            window.open("/student/add_student.php?MaLop=<?php echo $maLop ?>");
        }
    </script>

    <div class="content">
        <div class="input-score">
            <h1 style="margin-left:-40px; padding: 20px 0">Nhập điểm
                <?php echo $class_info['TenLop'] . "<br> " . $lesson_info['TenBai'] ?>
            </h1>

        </div>
        <div style="margin-bottom: 20px;">
            <input type="text" id="searchInput" placeholder="Tìm kiếm học sinh..."
                style="padding: 10px; width: 100%; border: 1px solid #ccc; border-radius: 5px; font-size: 16px;">
        </div>
        <table class="student-list" style="width: 100%">
            <tr>
                <td>SĐT</td>
                <td>Họ Tên</td>
                <td>Ngày Sinh</td>
                <td>Lớp</td>
                <td>Trường</td>
                <td style="width: 7rem">Điểm</td>
            </tr>

            <?php
            $query = "SELECT distinct hs.MaHS, Ho, Ten, NgaySinh, Lop, Truong, Phone, pl.MaPhanLop, pl.MaLop
                FROM hocsinh hs JOIN phanlop pl ON hs.MaHS = pl.MaHS
                WHERE pl.MaPhanLop not in (SELECT MaPhanLop FROM diemso WHERE MaBuoiHoc = '$maBH') and pl.MaLop = '$maLop' and pl.TinhTrang = 1
                ORDER BY Ten, Ho";
            $result = $conn->query($query);
            while ($row = mysqli_fetch_array($result)) {
                echo '<tr>';
                echo '<td>' . $row['Phone'] . '</td>';
                echo '<td style= "text-align:left;padding-left:20px">' . $row['Ho'] . ' ' . $row['Ten'] . '</td>';
                echo '<td>' . $row['NgaySinh'] . '</td>';
                echo '<td>' . $row['Lop'] . '</td>';
                echo '<td>' . $row['Truong'] . '</td>';
                echo '<td>' . '<input class="Score" name="Score" type="text" autocomplete="off" data-mapl="' . $row['MaPhanLop'] . '" data-mabh="' . $maBH . '" data-malop="' . $maLop . '"></td>';
                echo '</tr>';
            }
            ?>
        </table>
        <h2 style="margin-left:50px">Đã nghỉ</h2>
        <table class="student-list" style="width: 100%">
            <tr>
                <td>SĐT</td>
                <td>Họ Tên</td>
                <td>Ngày Sinh</td>
                <td>Lớp</td>
                <td>Trường</td>
                <td style="width: 7rem">Điểm</td>
            </tr>
            <?php
            $query = "SELECT distinct hs.MaHS, Ho, Ten, NgaySinh, Lop, Truong, Phone, pl.MaPhanLop, pl.MaLop
                FROM hocsinh hs JOIN phanlop pl ON hs.MaHS = pl.MaHS
                WHERE pl.MaPhanLop not in (SELECT MaPhanLop FROM diemso WHERE MaBuoiHoc = '$maBH') and pl.MaLop = '$maLop' and pl.TinhTrang = 0
                ";
            $result = $conn->query($query);
            while ($row = mysqli_fetch_array($result)) {
                echo '<tr>';
                echo '<td>' . $row['Phone'] . '</td>';
                echo '<td style= "text-align:left;padding-left:20px">' . $row['Ho'] . ' ' . $row['Ten'] . '</td>';
                echo '<td>' . $row['NgaySinh'] . '</td>';
                echo '<td>' . $row['Lop'] . '</td>';
                echo '<td>' . $row['Truong'] . '</td>';
                echo '<td>' . '<input class="Score" name="Score" type="text" autocomplete="off" data-mapl="' . $row['MaPhanLop'] . '" data-mabh="' . $maBH . '" data-malop="' . $maLop . '"></td>';
                echo '</tr>';
            }
            ?>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById("searchInput");
            const tableRows = document.querySelectorAll(".student-list tr:not(:first-child)");
            let scoreInputs = Array.from(document.querySelectorAll(".Score"));

            // Cập nhật danh sách các ô nhập điểm sau khi tìm kiếm
            function updateScoreInputs() {
                scoreInputs = Array.from(document.querySelectorAll(".Score")).filter(input => {
                    const row = input.closest("tr");
                    return row && row.style.display !== "none"; // Chỉ giữ các ô trong hàng hiển thị
                });
            }

            // Xử lý phím mũi tên để điều hướng giữa các ô nhập điểm
            document.addEventListener("keydown", function (event) {
                const activeElement = document.activeElement;
                if (!activeElement.classList.contains("Score")) return;

                const currentIndex = scoreInputs.indexOf(activeElement);
                if (event.key === "ArrowUp") {
                    event.preventDefault();
                    if (currentIndex > 0) scoreInputs[currentIndex - 1].focus();
                } else if (event.key === "ArrowDown") {
                    event.preventDefault();
                    if (currentIndex < scoreInputs.length - 1) scoreInputs[currentIndex + 1].focus();
                }
            });

            // Bắt sự kiện khi nhấn Ctrl+F hoặc F3 để focus vào ô tìm kiếm
            document.addEventListener("keydown", function (event) {
                if ((event.ctrlKey && event.key === "f") || event.key === "F3") {
                    event.preventDefault();
                    searchInput.focus();
                    searchInput.select();
                }
            });

            // Xử lý sự kiện tìm kiếm
            searchInput.addEventListener("keyup", function (event) {
                const query = this.value.toLowerCase();
                tableRows.forEach(row => {
                    const rowText = Array.from(row.querySelectorAll("td"))
                        .map(cell => cell.textContent.toLowerCase())
                        .join(" ");
                    row.style.display = rowText.includes(query) ? "" : "none";
                });

                updateScoreInputs(); // Cập nhật danh sách ô nhập điểm sau khi tìm kiếm

                // Xử lý khi nhấn Enter
                if (event.key === "Enter") {
                    event.preventDefault();
                    const visibleScoreInput = scoreInputs.find(input => input.closest("tr").style.display !== "none");
                    if (visibleScoreInput) visibleScoreInput.focus();
                }
            });

            // Xử lý sự kiện lưu điểm
            scoreInputs.forEach(input => {
                input.addEventListener("change", function () {
                    const score = this.value;
                    const maPhanLop = this.dataset.mapl;
                    const maBuoiHoc = this.dataset.mabh;
                    const maLop = this.dataset.malop;
                    const row = this.closest("tr");

                    fetch("./add_score.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            Score: score,
                            MaPhanLop: maPhanLop,
                            MaBuoiHoc: maBuoiHoc,
                            MaLop: maLop
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert("Đã lưu điểm thành công!");
                                row.remove(); // Xóa hàng sau khi lưu thành công
                                updateScoreInputs(); // Cập nhật danh sách ô nhập điểm
                            } else {
                                alert("Lỗi khi lưu điểm: " + data.message);
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            alert("Lỗi kết nối! Vui lòng thử lại.");
                        });
                });
            });
        });
    </script>


    </div>
</body>

</html>