<?php
session_start();
require_once "settings.php";

// Check login + role admin
if (!isset($_SESSION['employeeID'], $_SESSION['role'])) {
    header('Location: ../login/login.php');
    exit;
}
if (!in_array($_SESSION['role'], ['CEO', 'HR', 'Manager'])) {
    header('Location: ../employee/index.php');
    exit;
}

$conn = @mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("<p>Database connection failure: " . htmlspecialchars(mysqli_connect_error()) . "</p>");
}

// Biến dùng để fill form
$editId     = isset($_GET['id']) ? intval($_GET['id']) : 0;
$employeeId = '';
$date       = '';
$timeIn     = '';
$timeOut    = '';
$status     = 'present';
$note       = '';
$errorMsg   = "";

// Nếu là edit -> load dữ liệu
if ($editId > 0) {
    $sql = "SELECT * FROM `attendance` WHERE AttendanceID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $editId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($res)) {
        $employeeId = $row['EmployeeID'];
        $date       = $row['Date'];
        $timeIn     = $row['TimeIn'];
        $timeOut    = $row['TimeOut'];
        $status     = $row['Status'];
        // bảng của bạn không có Note, nên chỉ để trống cho UI
    } else {
        $errorMsg = "Record not found or already deleted.";
    }
    mysqli_stmt_close($stmt);
}

// Xử lý submit (ADD hoặc EDIT)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postEditId  = intval($_POST['attendance_id'] ?? 0);
    $employeeId  = intval($_POST['employee-id'] ?? 0);
    $date        = $_POST['date'] ?? '';
    $timeIn      = $_POST['time-in'] ?? '';
    $timeOut     = $_POST['time-out'] ?? '';
    $status      = $_POST['status'] ?? 'present';
    $note        = $_POST['note'] ?? '';

    if ($employeeId <= 0 || $date === '' || $timeIn === '' || $timeOut === '') {
        $errorMsg = "Please fill in all required fields.";
    } else {
        if ($postEditId > 0) {
            // UPDATE
            $sql = "UPDATE `attendance`
                    SET EmployeeID = ?, Date = ?, TimeIn = ?, TimeOut = ?, Status = ?
                    WHERE AttendanceID = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "issssi", $employeeId, $date, $timeIn, $timeOut, $status, $postEditId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } else {
            // INSERT mới
            $sql = "INSERT INTO `attendance` (EmployeeID, Date, TimeIn, TimeOut, Status)
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "issss", $employeeId, $date, $timeIn, $timeOut, $status);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // Xong thì quay lại trang list
        header('Location: ../demo/attendance.php');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    
    <script src="/_sdk/element_sdk.js"></script>
    <script src="/_sdk/data_sdk.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --bg: #f8fafc;
            --surface: #fff;
            --text: #111827;
            --primary: #3b82f6;
            --muted: #6b7280;
            --card: #f3f4f6;
        }
        html, body { height: 100%; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        .card { background: var(--surface); }
        .shadow-smooth { box-shadow: 0 10px 30px rgba(0,0,0,.06); }
        .sidebar { background: #fff; }
        .sidebar a { border-radius: .75rem; }
        .sidebar a.active { 
            background: #eef2ff;
            font-weight: 600;
        }
        .btn {
            padding: .5rem .75rem;
            border-radius: .5rem;
            font-weight: 500;
        }
        .btn-primary {
            background: var(--primary);
            color: #fff;
        }
        .btn-outline { border: 1px solid #e5e7eb; }
        .form-control {
            width: 100%;
            padding: .5rem .75rem;
            border-radius: .5rem;
            border: 1px solid #d1d5db;
            font-size: 0.875rem;
        }
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px rgba(59,130,246,.3);
        }
    </style>
</head>
<body>
    <div id="dashboard" class="h-full flex flex-col">
        <!-- Top Navigation -->
        <nav id="top-nav" class="shadow-sm border-b">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <button id="sidebar-toggle" class="p-2 rounded-md lg:hidden">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h1 id="company-name-nav" class="ml-2 text-xl font-semibold">Group 5 HR</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm">Welcome, Admin</span>
                        <button id="logout-btn" class="px-3 py-1 text-sm rounded-md transition-colors hover:opacity-80">
                            <a href="../login/logout.php">Log Out</a>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar giống các trang khác -->
            <div id="sidebar" class="w-64 shadow-lg sidebar-transition lg:translate-x-0 -translate-x-full fixed lg:relative z-30 h-full bg-white">
                <div class="p-4 h-full overflow-y-auto">
                <nav class="space-y-2">

                    <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="employees">
                      <div class="flex items-center">
                        <span class="text-lg mr-3">👥</span>
                        <span><a href="../demo/employee.php">Employees</a></span>
                      </div>
                    </button>

                    <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="salary">
                      <div class="flex items-center">
                        <span class="text-lg mr-3">💰</span>
                        <span><a href="../demo/payroll.php">Salary Management</a></span>
                      </div>
                    </button>

                    <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="attendance">
                      <div class="flex items-center">
                        <span class="text-lg mr-3">📅</span>
                        <span><a href="../demo/attendance.php">Attendance</a></span>
                      </div>
                    </button>

                    <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="departments">
                      <div class="flex items-center">
                        <span class="text-lg mr-3">🏢</span>
                        <span><a href="../demo/department.html">Departments</a></span>
                      </div>
                    </button>

                    <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="leave">
                      <div class="flex items-center">
                        <span class="text-lg mr-3">📊</span>
                        <span><a href="../demo/leave-request.php">Leave Request</a></span>
                      </div>
                    </button>

                    <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="children">
                      <div class="flex items-center">
                        <span class="text-lg mr-3">👶</span>
                        <span><a href="../demo/children.php">Children</a></span>
                      </div>
                    </button>

                    <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="emp-training">
                      <div class="flex items-center">
                        <span class="text-lg mr-3">🎓</span>
                        <span><a href="../demo/employee-training.php">Employee Training</a></span>
                      </div>
                    </button>

                    <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="training">
                      <div class="flex items-center">
                        <span class="text-lg mr-3">🎯</span>
                        <span><a href="../demo/training.php">Training Courses</a></span>
                      </div>
                    </button>

                    <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="job-history">
                      <div class="flex items-center">
                        <span class="text-lg mr-3">🧾</span>
                        <span><a href="../demo/job-history.php">Job History</a></span>
                      </div>
                    </button>

                    <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="position-history">
                      <div class="flex items-center">
                        <span class="text-lg mr-3">📌</span>
                        <span><a href="../demo/position-history.php">Position History</a></span>
                      </div>
                    </button>

                    <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="job-experience">
                      <div class="flex items-center">
                        <span class="text-lg mr-3">💼</span>
                        <span><a href="../demo/job-experience.php">Job Experience</a></span>
                      </div>
                    </button>

                </nav>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 overflow-y-auto bg-gray-50 min-h-screen">
                <div class="max-w-3xl mx-auto p-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold">
                            <?php echo $editId > 0 ? 'Edit Attendance Record' : 'Add Attendance Record'; ?>
                        </h2>
                        <a href="../demo/attendance.php" class="btn btn-outline">Back to Attendance</a>
                    </div>

                    <!-- Error message -->
                    <?php if ($errorMsg): ?>
                        <div class="mb-4 px-4 py-2 rounded-md bg-red-100 text-red-700 text-sm">
                            <?php echo htmlspecialchars($errorMsg); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form Card -->
                    <div id="attendance-form-container" class="rounded-lg shadow-sm bg-white">
                        <div class="px-6 py-5 border-b">
                            <h3 class="text-lg font-semibold">Attendance Form</h3>
                            <p class="text-sm opacity-70 mt-1">
                                Fill in the record and click <strong>Save</strong>. It will appear on the Attendance table immediately.
                            </p>
                        </div>

                        <form method="post" action="" class="px-6 py-5 space-y-5" id="attendance-form">
                            <input type="hidden" name="attendance_id" value="<?php echo $editId; ?>" />

                            <!-- Row 1 -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="employee-id" class="block text-sm mb-1">Employee ID</label>
                                    <input type="number" id="employee-id" name="employee-id"
                                           class="form-control"
                                           placeholder="e.g. 1"
                                           value="<?php echo htmlspecialchars($employeeId); ?>"
                                           required>
                                </div>

                                <div>
                                    <label for="date" class="block text-sm mb-1">Date</label>
                                    <input type="date" id="date" name="date"
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($date); ?>"
                                           required>
                                </div>
                            </div>

                            <!-- Row 2 -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="time-in" class="block text-sm mb-1">Time In</label>
                                    <input type="time" id="time-in" name="time-in"
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($timeIn); ?>"
                                           required>
                                    <small class="opacity-70">Format: 08:00</small>
                                </div>

                                <div>
                                    <label for="time-out" class="block text-sm mb-1">Time Out</label>
                                    <input type="time" id="time-out" name="time-out"
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($timeOut); ?>"
                                           required>
                                    <small class="opacity-70">Format: 17:00</small>
                                </div>
                            </div>

                            <!-- Row 3 -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="status" class="block text-sm mb-1">Status</label>
                                    <select id="status" name="status" class="form-control" required>
                                        <option value="present" <?php echo $status === 'present' ? 'selected' : ''; ?>>Present</option>
                                        <option value="absent"  <?php echo $status === 'absent' ? 'selected' : ''; ?>>Absent</option>
                                        <option value="late"    <?php echo $status === 'late' ? 'selected' : ''; ?>>Late</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="note" class="block text-sm mb-1">Note (optional)</label>
                                    <input type="text" id="note" name="note"
                                           class="form-control"
                                           placeholder="Reason / extra info"
                                           value="<?php echo htmlspecialchars($note); ?>">
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end gap-2 pt-3 border-t">
                                <a href="../demo/attendance.php" class="btn btn-outline">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>

                    <div id="toasts" class="fixed top-4 right-4 z-50 space-y-2"></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
