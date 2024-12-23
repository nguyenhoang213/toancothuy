<!-- Start: Side-navigation -->
<link rel="icon" type="image/x-icon" href="/assets/image/logo.png">
<link rel="stylesheet" href="../assets/css/admin-style.css">
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/admin-navigation.css">
<link rel="stylesheet" href="../assets/font/themify-icons/themify-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://kit.fontawesome.com/8fcd74b091.js" crossorigin="anonymous"></script>

<style>
    * {
        font-family: 'Montserrat';
    }

    .sidenav {
        overflow-y: scroll;
        /* Kích hoạt thanh cuộn dọc khi nội dung vượt quá chiều cao */
        scrollbar-width: none;
        /* Ẩn thanh cuộn trên Firefox */
    }

    .sidenav::-webkit-scrollbar {
        display: none;
        /* Ẩn thanh cuộn trên Chrome, Safari */
    }
</style>


<button class="menu">Menu</button>
<div class="sidenav">
    <div class="dropdown-admin ">
        <h2>ADMIN</h2>
    </div>

    <button class="dropdown-btn ">
        <div class="dropItem"><i class="fa-solid fa-house"></i> Home </div>
        <i class="nav-arrow-down ti-angle-down"></i>
    </button>
    <div class="dropdown-container">
        <a href="../admin/admin.php">Admin</a>
        <a href="../index.php">Trang Chủ</a>
    </div>

    <button class="dropdown-btn">
        <div class="dropItem"><i class="fa-solid fa-chart-simple"></i> Thống Kê</div>
        <i class="nav-arrow-down ti-angle-down"></i>
    </button>
    <div class="dropdown-container">
        <form method="GET" action="../admin/admin_statistical.php">
            <?php
            $sql = "SELECT * FROM lop WHERE TinhTrang = 1 ORDER BY TenLop DESC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<button type="submit" name="id" value="' . $row["MaLop"] . '"> ' . $row["TenLop"] . ' </button>';
                }
            } ?>
        </form>
    </div>

    <button class="dropdown-btn">
        <div class="dropItem"> <i class="fa-solid fa-pen"></i> Quản lý Điểm </div>
        <i class="nav-arrow-down ti-angle-down"></i>
    </button>
    <div class="dropdown-container">
        <form method="GET" action="../lesson/lesson_list.php">
            <?php
            $sql = "SELECT * FROM lop WHERE TinhTrang = 1 ORDER BY TenLop DESC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<button type="submit" name="id" value="' . $row["MaLop"] . '"> ' . $row["TenLop"] . ' </button>';
                }
            } ?>
        </form>
    </div>

    <button class="dropdown-btn">
        <div class="dropItem"><i class="fa-solid fa-user"></i> Quản lý học sinh </div>
        <i class="nav-arrow-down ti-angle-down"></i>
    </button>
    <div class="dropdown-container">
        <form method="GET" action="../student/student_list.php">
            <?php
            $sql = "SELECT * FROM lop WHERE TinhTrang = 1 ORDER BY TenLop DESC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<button type="submit" name="id" value="' . $row["MaLop"] . '"> ' . $row["TenLop"] . ' </button>';
                }
            } ?>
        </form>
    </div>

    <a class="a_navigator" href="../class/class_list.php">
        <button class="dropdown-btn">
            <div class="dropItem"> <i class="fa-solid fa-user-group"></i> Quản lý lớp</div>
        </button>
        <div class="dropdown-container">
        </div>
    </a>

    <button class="dropdown-btn">
        <div class="dropItem"><i class="fa-solid fa-house-circle-check"></i> Quản lý BTVN </div>
        <i class="nav-arrow-down ti-angle-down"></i>
    </button>
    <div class="dropdown-container">
        <form method="GET" action="../homework/homework.php">
            <?php
            $sql = "SELECT * FROM lop WHERE TinhTrang = 1 ORDER BY TenLop DESC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<button type="submit" name="id" value="' . $row["MaLop"] . '"> ' . $row["TenLop"] . ' </button>';
                }
            } ?>
        </form>
    </div>
    <button class="dropdown-btn">
        <div class="dropItem"><i class="fa-solid fa-ranking-star"></i> Xếp Hạng </div>
        <i class="nav-arrow-down ti-angle-down"></i>
    </button>
    <div class="dropdown-container">
        <form method="GET" action="../admin/admin_ranking.php">
            <?php
            $sql = "SELECT * FROM lop WHERE TinhTrang = 1 ORDER BY TenLop DESC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<button type="submit" name="id" value="' . $row["MaLop"] . '"> ' . $row["TenLop"] . ' </button>';
                }
            } ?>
        </form>
    </div>

    <button class="dropdown-btn">
        <div class="dropItem"> <i class="fa-solid fa-chart-line"></i> Hoạt động </div>
        <i class="nav-arrow-down ti-angle-down"></i>
    </button>
    <div class="dropdown-container">
        <form method="GET" action="../student/student_activity.php">
            <?php
            $sql = "SELECT * FROM lop WHERE TinhTrang = 1 ORDER BY TenLop DESC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<button type="submit" name="id" value="' . $row["MaLop"] . '"> ' . $row["TenLop"] . ' </button>';
                }
            } ?>
        </form>
    </div>

    <button class="dropdown-btn">
        <div class="dropItem"> <i class="fa-solid fa-right-from-bracket"></i> <a style="color: #FFFFFF80;"
                href="../logout.php">Đăng xuất</a>
        </div>
    </button>
    <div class="dropdown-container">
    </div>

</div>


<script>
    var dropdown = document.getElementsByClassName("dropdown-btn");
    for (let i = 0; i < dropdown.length; i++) {
        dropdown[i].addEventListener("click", function () {
            this.classList.toggle("active");
            var dropdownContent = this.nextElementSibling;
            dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
        });
    }

    var menu = document.getElementsByClassName("menu");
    for (let i = 0; i < menu.length; i++) {
        menu[i].addEventListener("click", function () {
            this.classList.toggle("active");
            var sidenav = this.nextElementSibling;
            sidenav.style.display = sidenav.style.display === "block" ? "none" : "block";
        });
    }
    var dropdown = document.getElementsByClassName("dropdown-btn");
    for (let i = 0; i < dropdown.length; i++) {
        dropdown[i].addEventListener("click", function () {
            this.classList.toggle("active");
            var dropdownContent = this.nextElementSibling;

            if (dropdownContent.style.maxHeight) {
                // Nếu dropdown đã mở, ẩn nó mượt mà
                dropdownContent.style.maxHeight = dropdownContent.scrollHeight + "px";
            } else {
                // Nếu dropdown đang đóng, mở nó mượt mà
                dropdownContent.style.maxHeight = dropdownContent.scrollHeight + "px";
            }
        });
    }
    const dropItem = document.getElementById('dropItem');

    dropItem.addEventListener('click', function () {
        dropItem.classList.toggle('clicked');
    });
</script>
<!-- End: Side-navigation -->