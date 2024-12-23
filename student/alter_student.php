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

if ($_GET['MaLop']) {
  $maLop = $_GET['MaLop'];
} else {
  echo '<script>
    alert("Không tìm thấy mã lớp để chỉnh sửa!");
    window.location.href="/class/class_list.php";
    </script>';
}

// Lấy ID học sinh từ URL để xác định học sinh cần chỉnh sửa
if (isset($_GET['MaHS'])) {
  $maHS = $_GET['MaHS'];

  // Truy vấn lấy thông tin học sinh hiện tại từ CSDL
  $sql = "SELECT * FROM hocsinh WHERE MaHS = '$maHS'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
  } else {
    echo '<script>
        alert("Học sinh không tồn tại!");
        window.location.href="/student/student_list.php?id=' . $maLop . '";
        </script>';
    exit();
  }
} else {
  echo '<script>
    alert("Không tìm thấy mã học sinh để chỉnh sửa!");
    window.location.href="/student/student_list.php?id=' . $maLop . '";
    </script>';
  exit();
}

// Xử lý dữ liệu khi form được gửi để cập nhật học sinh
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $ho = trim($_POST['Ho']);
  $ten = trim($_POST['Ten']);
  $lop = $_POST['Lop'];
  $truong = $_POST['Truong'];
  $ngaySinh = $_POST['NgaySinh'];
  $phone = $_POST['Phone'];
  $anh = $_FILES['Anh']['name'];

  if (empty($ten) || empty($phone)) {
    echo '<script>alert("Tên và SĐT không được để trống!");</script>';
  } else {
    // Xử lý upload ảnh nếu có
    if (!empty($anh)) {
      $target_dir = "../assets/image/anhhs/";
      $target_file = $target_dir . basename($anh);

      if (!empty($student['Anh'])) { // Kiểm tra ảnh cũ trong cơ sở dữ liệu
        $old_file = $target_dir . $student['Anh']; // Đường dẫn ảnh cũ
        if (file_exists($old_file)) { // Kiểm tra file có tồn tại
          unlink($old_file); // Xóa file cũ
        }
      }

      move_uploaded_file($_FILES['Anh']['tmp_name'], $target_file);
    } else {
      $anh = $student['Anh']; // Giữ nguyên ảnh cũ nếu không cập nhật
    }

    // Cập nhật thông tin học sinh trong cơ sở dữ liệu
    $sql = "UPDATE hocsinh SET Ho = '$ho', Ten = '$ten', Lop = '$lop', Truong = '$truong', NgaySinh = '$ngaySinh', Phone = '$phone', Anh = '$anh' WHERE MaHS = '$maHS'";
    if ($conn->query($sql) === TRUE) {
      echo '<script>
            alert("Cập nhật học sinh thành công!");
            window.location.href="/student/student_list.php?id=' . $maLop . '";
            </script>';
    } else {
      echo '<script>
            alert("Lỗi: Không thể cập nhật học sinh do lỗi hệ thống!");
            </script>';
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
  <title>Chỉnh sửa Học sinh</title>
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
    <h1>Chỉnh sửa Học sinh</h1>
    <form action="" method="POST" enctype="multipart/form-data">
      <!-- Họ -->
      <div class="form-group">
        <label for="Ho">Họ:</label>
        <input type="text" id="Ho" name="Ho" value="<?php echo htmlspecialchars($student['Ho']); ?>" required>
      </div>

      <!-- Số Điện Thoại -->
      <div class="form-group">
        <label for="Phone">Số Điện Thoại:</label>
        <input type="text" id="Phone" name="Phone" value="<?php echo htmlspecialchars($student['Phone']); ?>" required>
      </div>


      <!-- Tên -->
      <div class="form-group">
        <label for="Ten">Tên:</label>
        <input type="text" id="Ten" name="Ten" value="<?php echo htmlspecialchars($student['Ten']); ?>" required>
      </div>

      <!-- Lớp -->
      <div class="form-group">
        <label for="Lop">Lớp:</label>
        <input type="text" id="Lop" name="Lop" value="<?php echo htmlspecialchars($student['Lop']); ?>">
      </div>

      <!-- Trường -->
      <div class="form-group">
        <label for="Truong">Trường:</label>
        <input type="text" id="Truong" name="Truong" value="<?php echo htmlspecialchars($student['Truong']); ?>">
      </div>

      <!-- Ngày Sinh -->
      <div class="form-group">
        <label for="NgaySinh">Ngày Sinh:</label>
        <input type="date" id="NgaySinh" name="NgaySinh" value="<?php echo htmlspecialchars($student['NgaySinh']); ?>">
      </div>


      <!-- Ảnh -->
      <div class="form-group">
        <label for="Anh">Ảnh:</label>
        <input type="file" id="Anh" name="Anh" accept="image/*">
        <?php if (!empty($student['Anh'])): ?>
          <p>Ảnh hiện tại: <img src="../assets/image/anhhs/<?php echo htmlspecialchars($student['Anh']); ?>"
              alt="Ảnh học sinh" width="100"></p>
        <?php endif; ?>
      </div>

      <!-- Nút Cập Nhật -->
      <button type="submit" class="submit-btn">Cập nhật học sinh</button>
      <a href="/student/student_list.php?id=<?php echo $maLop ?>" class=" cancel-btn">Hủy</a>
    </form>
  </div>
</body>

</html>