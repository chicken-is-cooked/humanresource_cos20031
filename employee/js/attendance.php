<?php
session_start();
include '../db-connect.php';

if (!isset($_SESSION['employeeID'])) { $_SESSION['employeeID'] = 327; }
$employeeID = $_SESSION['employeeID'];

// Fetch attendance data
$sql = "SELECT * FROM Attendance WHERE EmployeeID = '$employeeID' ORDER BY Date DESC";
$result = $conn->query($sql);

// Stats Logic
$total_days = 0;
$late_count = 0;
$attendance_data = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $total_days++;
        if (isset($row['Status']) && stripos($row['Status'], 'Late') !== false) {
            $late_count++;
        }
        $attendance_data[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="index.php" class="btn btn-outline-secondary">&larr; Dashboard</a>
        <h2 class="fw-bold text-secondary">Attendance Tracker</h2>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-start border-primary border-4">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase">Total Days Present</h6>
                    <h3 class="fw-bold text-primary"><?php echo $total_days; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-start border-warning border-4">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase">Late Arrivals</h6>
                    <h3 class="fw-bold text-warning"><?php echo $late_count; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Attendance History</h5>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($attendance_data)): ?>
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($attendance_data as $row): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">
                                <?php echo date("M d, Y", strtotime($row['Date'])); ?>
                            </td>
                            <td><?php echo $row['TimeIn']; ?></td>
                            <td><?php echo $row['TimeOut']; ?></td>
                            <td>
                                <?php 
                                    $status = isset($row['Status']) ? $row['Status'] : 'Unknown';
                                    $badgeClass = 'bg-secondary';
                                    if(stripos($status, 'Present') !== false) $badgeClass = 'bg-success';
                                    if(stripos($status, 'Late') !== false) $badgeClass = 'bg-warning text-dark';
                                    if(stripos($status, 'Absent') !== false) $badgeClass = 'bg-danger';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?> rounded-pill px-3">
                                    <?php echo $status; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="p-5 text-center text-muted">No attendance records found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>