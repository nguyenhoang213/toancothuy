<?php
$title = '';
$class = '';
if (isset($_GET['class'])) {
    $class = $_GET['class'];
    $sqlten = "select * from lop Where MaLop = '$class'";
    $resultten = mysqli_query($conn, $sqlten);
    while ($row = mysqli_fetch_array($resultten)) {
        $title = $row["TenLop"];
    }
} else {
    // header("Location: /admin.php");
}
?>