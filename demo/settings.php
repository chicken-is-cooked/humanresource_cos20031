<?php
$host   = "localhost";
$user   = "root";                 // user XAMPP
$pwd    = "";                     // password MySQL (thường là rỗng nếu chưa đổi)
$sql_db = "csv_db 6";   //

// Tạo kết nối + chọn luôn database
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    die("Database connection failure: " .
        htmlspecialchars(mysqli_connect_errno() . " - " . mysqli_connect_error()));
}
?>
