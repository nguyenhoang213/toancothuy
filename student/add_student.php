<?php
include("../connection.php"); // Kết nối CSDL
session_start();
include("../side_nav.php");

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!$_SESSION['uname']) {
  echo '<script>
    alert("Bạn cần đăng nhập để truy cập trang này!");
    window.location.href="https://vatlytruongnghiem.edu.vn/";
    </script>';
  exit();
}

if (isset($_GET['MaLop'])) {
  $maLop = $_GET['MaLop'];
  $class_info = $conn->query("SELECT * FROM lop WHERE MaLop = $maLop")->fetch_assoc();

} else {
  echo '<script>
    alert("Không tìm thấy mã lớp!");
    window.location.href="../class/class_list.php";
    </script>';
}

// Xử lý dữ liệu khi form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Nhận dữ liệu từ form
  $ho = trim($_POST['Ho']);
  $ten = trim($_POST['Ten']);
  $lop = $_POST['Lop'];
  $truong = $_POST['Truong'];
  $ngaySinh = $_POST['NgaySinh'];
  $phone = $_POST['Phone'];
  $anh = $_FILES['Anh']['name'];

  // Kiểm tra xem các trường có rỗng không
  if (empty($ten) || empty($phone)) {
    echo '<script>alert("Tên và SĐT không được để trống!");</script>';
  } else {
    // Kiểm tra SĐT có tồn tại chưa?
    $checkSDT = "SELECT * FROM hocsinh WHERE Phone = '$phone'";
    $result = $conn->query($checkSDT);

    // Nếu đã tồn tại SĐT
    if ($result->num_rows > 0) {
      //Kiểm tra có trong lớp hay chưa
      $checkClass = "SELECT * FROM hocsinh hs JOIN phanlop pl ON hs.MaHS = pl.MaHS WHERE MaLop = '$maLop' and Phone = '$phone'";
      $resultClass = $conn->query($checkClass);

      // Nếu đã tồn tại SĐT trong lớp
      if ($resultClass->num_rows > 0) {
        $row = $resultClass->fetch_assoc();
        echo '<script>alert("SĐT này đã được dùng bởi học sinh ' . $row['Ho'] . ' ' . $row['Ten'] . ' - ' . $row['Lop'] . ' - ' . $row['Truong'] . '!");</script>';
      }

      // Nếu chưa tồn tại SĐT trong lớp 
      else {
        $row = $result->fetch_assoc();
        echo '<script>
          if(confirm("SĐT này đã được dùng bởi học sinh ' . $row['Ho'] . ' ' . $row['Ten'] . ' - ' . $row['Lop'] . ' - ' . $row['Truong'] . '! Bạn có muốn thêm học sinh này vào lớp ' . $class_info['TenLop'] . '")) {
            window.location.href = "add_to_class.php?MaHS=' . $row['MaHS'] . '&MaLop=' . $class_info['MaLop'] . '";
          } else {
            alert("Đã hủy thao tác thêm học sinh");
        }
        </script>';
      }
    }

    // SĐT chưa tồn tại 
    else {
      if (!empty($anh)) {
        $target_dir = "../assets/image/anhhs/";
        $target_file = $target_dir . basename($anh);
        move_uploaded_file($_FILES['Anh']['tmp_name'], $target_file);
      }
      // Cập nhật thông tin học sinh trong cơ sở dữ liệu
      $sqlhs = "INSERT INTO hocsinh(Ho, Ten, Lop, Truong, NgaySinh, Phone, Anh) VALUES ('$ho','$ten','$lop','$truong','$ngaySinh','$phone','$anh')";
      if ($conn->query($sqlhs)) {
        $maHS = $conn->insert_id;
        $sqlpl = "INSERT INTO phanlop(MaLop, MaHS, TinhTrang) VALUES ('$maLop', '$maHS', 1)";
        if ($conn->query($sqlpl)) {
          echo '<script>
            alert("Thêm học sinh thành công!");
            window.location.href="../student/student_list.php?id=' . $maLop . '";
            </script>';
        }
      } else {
        echo '<script>
            alert("Lỗi: Không thể cập nhật học sinh do lỗi hệ thống!");
            </script>';
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <link rel="icon" type="image/x-icon" href="../assets/image/logo.png">
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thêm Học Sinh Mới Lớp <?php echo $class_info['TenLop'] ?></title>
  <link rel="stylesheet" href="../assets/css/admin-navigation.css">
  <link rel="stylesheet" href="../assets/css/admin-statistical.css">

  <style>
    @media screen and (min-width: 600px) {
      .content {
        margin-left: 240px;
        width: 80%;
      }
    }

    @media screen and (max-width: 600px) {
      .content {
        margin-left: 15px;
        width: 90%;
      }
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      font-weight: bold;
    }

    input[type="text"],
    input[type="date"],
    input[type="file"],
    select {
      font-size: 18px;
      width: 100%;
      padding: 8px;
      margin: 4px 0;
      box-sizing: border-box;
    }

    .submit-btn {
      padding: 10px 20px;
      background-color: #007fd5;
      color: white;
      border: none;
      cursor: pointer;
      font-size: 18px;
    }

    .submit-btn:hover {
      background-color: #004ed5;
    }

    .cancel-btn {
      padding: 10px 20px;
      background-color: #f44336;
      color: white;
      border: none;
      cursor: pointer;
      font-size: 18px;
    }

    .cancel-btn:hover {
      background-color: #e53935;
    }
  </style>
</head>

<body>
  <div class="content">
    <h1>Thêm Học Sinh Mới <?php echo $class_info['TenLop'] ?></h1>
    <form action="" method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="TenLop">Số điện thoại:</label>
        <input type="text" id="Phone" name="Phone" required>

        <label for="TenLop">Họ:</label>
        <input type="text" id="Ho" name="Ho" required>

        <label for="TenLop">Tên:</label>
        <input type="text" id="Ten" name="Ten" required>

        <label for="TenLop">Ngày sinh:</label>
        <input type="date" id="NgaySinh" name="NgaySinh">

        <label for=" TenLop">Lớp:</label>
        <input type="text" id="Lop" name="Lop">

        <label for="TenLop">Trường:</label>
        <input type="text" id="Truong" name="Truong">

        <label for="TenLop">Ảnh:</label>
        <input type="file" id="Anh" name="Anh" accept="image/*">
      </div>

      <button type="submit" class="submit-btn">Thêm học sinh</button>
      <a href="../student/student_list.php?id=<?php echo $maLop ?>" class="cancel-btn">Hủy</a>
    </form>
  </div>
</body>

</html>