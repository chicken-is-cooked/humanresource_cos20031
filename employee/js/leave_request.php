<?php
session_start();
include '../db-connect.php'; 

if (!isset($_SESSION['employeeID'])) { $_SESSION['employeeID'] = 327; }
$employeeID = $_SESSION['employeeID'];
$message = "";

// 1. Calculate Total Days Taken
$sql_count = "SELECT SUM(NoOfDays) as TotalTaken FROM LeaveRequest 
              WHERE EmployeeID = '$employeeID' AND Status = 'Approved'";
$result_count = $conn->query($sql_count);
$total_days_taken = ($result_count && $row = $result_count->fetch_assoc()) ? $row['TotalTaken'] : 0;

// 2. Fetch History
$sql_history = "SELECT * FROM LeaveRequest WHERE EmployeeID = '$employeeID' ORDER BY StartDate DESC";
$result_history = $conn->query($sql_history);

// 3. Handle Form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Matches CSV: LeaveReqID is a number, NOT 'LR123'
    $leaveReqID = rand(1000, 99999); 
    
    $managerID = $_POST['managerID'];
    $type = $_POST['type'];
    $startDate = $_POST['startDate'];
    $noOfDays = $_POST['noOfDays'];
    $note = $_POST['note'];
    $status = 'Pending'; 

    // Matches CSV Column Names: LeaveReqID
    $sql = "INSERT INTO LeaveRequest (LeaveReqID, EmployeeID, ManagerID, Type, StartDate, NoOfDays, Note, Status) 
            VALUES ('$leaveReqID', '$employeeID', '$managerID', '$type', '$startDate', '$noOfDays', '$note', '$status')";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Request submitted! (ID: $leaveReqID)</div>";
        header("Refresh:2"); 
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
    <a href="index.php" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="alert alert-info text-center shadow-sm">
                <h4>🗓️ Total Approved Leave: <strong><?php echo $total_days_taken ? $total_days_taken : 0; ?> days</strong></h4>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark"><h4>📝 New Leave Request</h4></div>
                <div class="card-body">
                    <?php echo $message; ?>
                    <form method="POST" action="">
                        <div class="mb-3"><label>Manager ID</label><input type="number" name="managerID" class="form-control" required></div>
                        <div class="mb-3"><label>Type</label>
                            <select name="type" class="form-select" required>
                                <option>Sick Leave</option><option>Maternity</option><option>Annual Leave</option><option>Personal Leave</option>
                            </select>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6"><label>Start Date</label><input type="date" name="startDate" class="form-control" required></div>
                            <div class="col-6"><label>Days</label><input type="number" step="0.5" name="noOfDays" class="form-control" required></div>
                        </div>
                        <div class="mb-3"><label>Reason</label><textarea name="note" class="form-control" rows="2"></textarea></div>
                        <button type="submit" class="btn btn-success w-100">Submit</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white"><h4>📜 History</h4></div>
                <div class="card-body">
                    <?php if ($result_history && $result_history->num_rows > 0): ?>
                        <table class="table table-striped">
                            <thead><tr><th>Date</th><th>Type</th><th>Days</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php while($row = $result_history->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['StartDate']; ?></td>
                                    <td><?php echo $row['Type']; ?></td>
                                    <td><?php echo $row['NoOfDays']; ?></td>
                                    <td><?php echo $row['Status']; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center mt-3">No history found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>