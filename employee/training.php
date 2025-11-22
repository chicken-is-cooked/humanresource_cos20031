<?php
session_start();
include '../db-connect.php';

if (!isset($_SESSION['employeeID'])) { $_SESSION['employeeID'] = 327; }
$employeeID = $_SESSION['employeeID'];

// FIXED QUERY: Removed 'Score'.
// Pulls Status from EmployeeTraining.
// Pulls Course, Description, Dates from Training.
$sql = "SELECT EmployeeTraining.Status, 
               Training.Course, Training.Description, Training.StartDate, Training.EndDate
        FROM EmployeeTraining 
        LEFT JOIN Training ON EmployeeTraining.TrainingID = Training.TrainingID
        WHERE EmployeeTraining.EmployeeID = '$employeeID'
        ORDER BY Training.StartDate DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Training</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="index.php" class="btn btn-outline-secondary">&larr; Dashboard</a>
        <h2 class="fw-bold text-secondary">Training Center</h2>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">My Training Record</h5>
        </div>
        <div class="card-body p-0">
            <?php if ($result && $result->num_rows > 0): ?>
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Course Name</th>
                            <th>Description</th>
                            <th>Dates</th>
                            <th>Status</th> </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-dark">
                                <?php echo $row['Course']; ?>
                            </td>
                            <td class="text-muted small">
                                <?php echo $row['Description']; ?>
                            </td>
                            
                            <td class="small">
                                <div>Start: <?php echo date("M d, Y", strtotime($row['StartDate'])); ?></div>
                                <?php if($row['EndDate']): ?>
                                    <div>End: <?php echo date("M d, Y", strtotime($row['EndDate'])); ?></div>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php 
                                    $status = isset($row['Status']) ? $row['Status'] : 'Assigned';
                                    $badgeClass = 'bg-secondary';

                                    if (stripos($status, 'Completed') !== false) {
                                        $badgeClass = 'bg-success';
                                    } elseif (stripos($status, 'Progress') !== false) {
                                        $badgeClass = 'bg-warning text-dark';
                                    } elseif (stripos($status, 'Fail') !== false) {
                                        $badgeClass = 'bg-danger';
                                    }
                                ?>
                                <span class="badge <?php echo $badgeClass; ?> rounded-pill px-3">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="p-5 text-center text-muted">
                    No training assigned yet.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>