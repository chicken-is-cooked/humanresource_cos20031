<?php
$host = "feenix-mariadb.swin.edu.au";
$username = "s105549964";
$password = "061206";
$dbname   = "s105549964_db";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_errno) {
    die("Failed to connect to MariaDB: (" . $conn->connect_errno . ") " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
