<?php
session_start();
include '../db-connect.php';

// Bắt buộc phải đăng nhập
if (!isset($_SESSION['employeeID'], $_SESSION['role'])) {
    header('Location: ../login/login.php');
    exit;
}

$employeeID = (int)$_SESSION['employeeID'];
$role = $_SESSION['role'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card text-center shadow-sm">
        <div class="card-header bg-dark text-white">
            <h3>Employee Portal</h3>
            <p class="mb-0">Welcome, Employee #<?php echo $employeeID; ?></p>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <a href="training.php" class="btn btn-outline-dark btn-lg w-100 py-4">
                        <i class="fa-solid fa-graduation-cap mb-2"></i><br>Training & Skills
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="profile.php" class="btn btn-outline-primary btn-lg w-100 py-4">
                        <i class="fa-solid fa-user mb-2"></i><br>My Profile
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="attendance.php" class="btn btn-outline-info btn-lg w-100 py-4">
                        <i class="fa-solid fa-clock mb-2"></i><br>My Attendance
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="leave_request.php" class="btn btn-outline-warning btn-lg w-100 py-4">
                        <i class="fa-solid fa-plane-departure mb-2"></i><br>Request Leave
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="payroll.php" class="btn btn-outline-success btn-lg w-100 py-4">
                        <i class="fa-solid fa-money-bill-wave mb-2"></i><br>My Payroll
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>