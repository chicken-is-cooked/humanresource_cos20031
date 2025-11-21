<?php
session_start();
include '../db-connect.php';

if (!isset($_SESSION['employeeID'])) { $_SESSION['employeeID'] = 327; }
$employeeID = $_SESSION['employeeID'];

// Fetch payroll data
$sql = "SELECT * FROM Payroll WHERE EmployeeID = '$employeeID'";
$result = $conn->query($sql);

$total_earned = 0;
$payroll_data = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $total_earned += $row['TotalPaid'];
        $payroll_data[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Payroll</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="index.php" class="btn btn-outline-secondary">&larr; Dashboard</a>
        <h2 class="fw-bold text-secondary">Payroll Center</h2>
    </div>

    <div class="card shadow-sm border-start border-success border-4 mb-4">
        <div class="card-body">
            <h6 class="text-muted text-uppercase">Total Earned (YTD)</h6>
            <h3 class="fw-bold text-success">$<?php echo number_format($total_earned, 2); ?></h3>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Payment History</h5>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($payroll_data)): ?>
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Payroll ID</th>
                            <th>Breakdown (IDs)</th>
                            <th class="text-end">Net Pay</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($payroll_data as $row): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">
                                <?php echo date("M d, Y"); // Mock Date ?>
                            </td>
                            <td>#<?php echo $row['PayrollID']; ?></td>
                            <td class="small text-muted">
                                SalaryID: <?php echo $row['SalaryID']; ?><br>
                                BonusID: <?php echo $row['BonusID']; ?><br>
                                DedID: <?php echo $row['DeductionID']; ?>
                            </td>
                            <td class="text-end fw-bold fs-5 text-dark">
                                $<?php echo number_format($row['TotalPaid'], 2); ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                                    <i class="fa-solid fa-download"></i> Slip
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="p-5 text-center text-muted">No payroll records found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>