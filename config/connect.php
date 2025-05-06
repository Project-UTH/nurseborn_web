<?php
$servername = "localhost";
$username = "root"; // Mặc định XAMPP
$password = ""; // Mặc định không có mật khẩu
$dbname = "db_nurseborn"; // Tên CSDL

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>