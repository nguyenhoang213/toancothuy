<?php
include("../connection.php"); // Kết nối CSDL
session_start();
include("../side_nav.php");

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!$_SESSION['uname'])
  echo '
    <script>
        window.location.href="../index.php";
    </script>';

if (isset($_GET['MaLop'])) {
  $maLop = $_GET['MaLop'];
  $class_info = $conn->query("SELECT * FROM lop WHERE MaLop = $maLop")->fetch_assoc();

} else {
  echo '<script>
    alert("Không tìm thấy mã lớp!");
    window.location.href="../class/class_list.php";
    </script>';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Nhận dữ liệu từ form
$ho = preg_replace('/\s+/', ' ', trim($_POST['Ho']));
$ten = preg_replace('/\s+/', ' ', trim($_POST['Ten']));
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

    if ($result->num_rows > 0) {
      // Kiểm tra có trong lớp hay chưa
      $checkClass = "SELECT * FROM hocsinh hs JOIN phanlop pl ON hs.MaHS = pl.MaHS WHERE MaLop = '$maLop' and Phone = '$phone'";
      $resultClass = $conn->query($checkClass);

      if ($resultClass->num_rows > 0) {
        $row = $resultClass->fetch_assoc();
        echo '<script>alert("SĐT này đã được dùng bởi học sinh ' . $row['Ho'] . ' ' . $row['Ten'] . ' - ' . $row['Lop'] . ' - ' . $row['Truong'] . '!");</script>';
      } else {
        $row = $result->fetch_assoc();
        echo '<script>
          if(confirm("SĐT này đã được dùng bởi học sinh ' . $row['Ho'] . ' ' . $row['Ten'] . ' - ' . $row['Lop'] . ' - ' . $row['Truong'] . '! Bạn có muốn thêm học sinh này vào lớp ' . $class_info['TenLop'] . '")) {
            window.location.href = "add_to_class.php?MaHS=' . $row['MaHS'] . '&MaLop=' . $class_info['MaLop'] . '";
          } else {
            alert("Đã hủy thao tác thêm học sinh");
          }
        </script>';
      }
    } else {
      if (!empty($anh)) {
        $target_dir = "../assets/image/anhhs/";
        $file_extension = pathinfo($anh, PATHINFO_EXTENSION);
        $new_file_name = $maHS . '.' . $file_extension;
        $target_file = $target_dir . $new_file_name;

        if (move_uploaded_file($_FILES['Anh']['tmp_name'], $target_file)) {
          $anh = $new_file_name;
        } else {
          echo '<script>alert("Lỗi khi tải lên file ảnh!");</script>';
          $anh = '';
        }
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
        margin-left: 250px;
        width: 80%;
        padding: 40px;
      }
    }

    @media screen and (max-width: 600px) {
      .content {
        margin-left: 15px;
        width: 90%;
        padding: 40px;
      }
    }
    
    .content{
        text-align: left;
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
        <input type="text" id="Ho" name="Ho">

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

      <div style="text-align: center">
          <button type="submit" class="submit-btn">Thêm học sinh</button>
          <a href="../student/student_list.php?id=<?php echo $maLop ?>" class="cancel-btn">Hủy</a>
          </div>
    </form>
  </div>
</body>

</html>