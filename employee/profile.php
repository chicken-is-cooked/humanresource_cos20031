<?php
session_start();
include '../db-connect.php';

if (!isset($_SESSION['employeeID'])) { $_SESSION['employeeID'] = 327; }
$employeeID = $_SESSION['employeeID'];

// FIX: Changed 'Position.PositionID' to 'Position.Position_ID'
$sql = "SELECT Employee.*, Position.Title, Department.DepartmentName 
        FROM Employee 
        LEFT JOIN Position ON Employee.PositionID = Position.Position_ID 
        LEFT JOIN Department ON Employee.DepartmentID = Department.DepartmentID
        WHERE Employee.EmployeeID = '$employeeID'";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();
} else {
    die("<div class='alert alert-danger m-5'>Error: Employee #$employeeID not found.</div>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <a href="index.php" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>
    
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4>My Personal Profile</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center mb-3">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User" class="img-fluid rounded-circle" style="width: 150px;">
                    <h5 class="mt-2">
                        <?php echo $data['First_Name'] . " " . $data['Last_Name']; ?>
                    </h5>
                    <span class="badge bg-success">
                        <?php echo isset($data['Title']) ? $data['Title'] : 'Employee'; ?>
                    </span>
                    <br>
                    <span class="badge bg-secondary mt-1">
                        <?php echo isset($data['DepartmentName']) ? $data['DepartmentName'] : 'Unknown Dept'; ?>
                    </span>
                </div>
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr><th>Employee ID:</th><td><?php echo $data['EmployeeID']; ?></td></tr>
                        <tr><th>Contact:</th><td><?php echo $data['Contact']; ?></td></tr>
                        <tr><th>Address:</th><td><?php echo $data['Address']; ?></td></tr>
                        <tr><th>Date of Birth:</th>
                            <td><?php echo date("M d, Y", strtotime($data['DateOfBirth'])); ?></td>
                        </tr>
                        <tr><th>Gender:</th><td><?php echo $data['Gender']; ?></td></tr>
                        <tr><th>Employment Type:</th><td><?php echo $data['EmploymentType']; ?></td></tr>
                        <tr><th>Marital Status:</th>
                            <td><?php echo $data['MariageStatus']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>