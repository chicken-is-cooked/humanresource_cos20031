<?php
session_start();
require_once "settings.php";

// ==== BẮT BUỘC LOGIN & ROLE ADMIN (CEO/HR/Manager) ====
if (!isset($_SESSION['employeeID'], $_SESSION['role'])) {
    header('Location: ../login/login.php');
    exit;
}

if (!in_array($_SESSION['role'], ['CEO', 'HR', 'Manager'])) {
    header('Location: ../employee/index.php');
    exit;
}

// ==== KẾT NỐI DB ====
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("<p>Database connection failure: " . htmlspecialchars(mysqli_connect_error()) . "</p>");
}

// ==== XÓA ATTENDANCE (Delete Selected) ====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ids'])) {
    $ids = array_filter(array_map('intval', $_POST['delete_ids'] ?? []));
    if (!empty($ids)) {
        $idList = implode(',', $ids);
        $delSql = "DELETE FROM `attendance` WHERE AttendanceID IN ($idList)";
        mysqli_query($conn, $delSql);
    }
}

// ==== LẤY DỮ LIỆU ATTENDANCE ====
$sql = "
    SELECT 
        AttendanceID,
        EmployeeID,
        Date,
        TimeIn,
        TimeOut,
        Status
    FROM `attendance`
    ORDER BY Date DESC, EmployeeID ASC
";
$result = mysqli_query($conn, $sql);

// ==== THỐNG KÊ CHO 3 CARD: ON TIME TODAY, AVG HOURS, ABSENCES ====
$onTimeToday   = 0;
$avgHours      = 0;
$absencesTotal = 0;

// 1. On Time Today (Status = 'present' và Date = hôm nay)
$today = date('Y-m-d');
$sqlOnTime = "
    SELECT COUNT(*) AS cnt
    FROM `attendance`
    WHERE Date = '$today' AND Status = 'present'
";
if ($res = mysqli_query($conn, $sqlOnTime)) {
    $row = mysqli_fetch_assoc($res);
    $onTimeToday = (int)$row['cnt'];
    mysqli_free_result($res);
}

// 2. Absences (tổng số record Status = 'absent')
$sqlAbs = "
    SELECT COUNT(*) AS cnt
    FROM `attendance`
    WHERE Status = 'absent'
";
if ($res = mysqli_query($conn, $sqlAbs)) {
    $row = mysqli_fetch_assoc($res);
    $absencesTotal = (int)$row['cnt'];
    mysqli_free_result($res);
}

// 3. Avg Hours (giờ trung bình giữa TimeIn và TimeOut, tính trên tất cả record có giờ)
// TIMESTAMPDIFF(MINUTE, Date+TimeIn, Date+TimeOut) -> phút, chia 60 ra giờ
$sqlAvg = "
    SELECT AVG(
        TIMESTAMPDIFF(
            MINUTE,
            CONCAT(Date, ' ', TimeIn),
            CONCAT(Date, ' ', TimeOut)
        )
    ) AS avg_minutes
    FROM `attendance`
    WHERE TimeIn IS NOT NULL 
      AND TimeOut IS NOT NULL 
      AND Status <> 'absent'
";
if ($res = mysqli_query($conn, $sqlAvg)) {
    $row = mysqli_fetch_assoc($res);
    if ($row && $row['avg_minutes'] !== null) {
        $avgHours = round(((float)$row['avg_minutes']) / 60, 1); // vd: 7.5
    }
    mysqli_free_result($res);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    
    <!-- External Dependencies -->
    <script src="/_sdk/element_sdk.js"></script>
    <script src="/_sdk/data_sdk.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Core Styles -->
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

        .badge {
            padding: .125rem .5rem;
            border-radius: .5rem;
            background: #ecfeff;
        }

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
            <!-- Sidebar Navigation -->
            <div id="sidebar" class="w-64 shadow-lg sidebar-transition lg:translate-x-0 -translate-x-full fixed lg:relative z-30 h-full bg-white">
                <div class="p-4 h-full overflow-y-auto">
                <nav class="space-y-2">

                    <!-- CORE -->
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

                    <!-- FAMILY / CHILDREN -->
                    <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="children">
                    <div class="flex items-center">
                        <span class="text-lg mr-3">👶</span>
                        <span><a href="../demo/children.php">Children</a></span>
                    </div>
                    </button>

                    <!-- TRAINING & DEVELOPMENT -->
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

                    <!-- JOB MANAGEMENT -->
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
            <div class="flex-1 overflow-y-auto bg-gray-50">
                <div class="p-6">
                    <!-- Header + Action -->
                    <section class="mb-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold">Attendance Management</h2>
                            <div class="flex gap-2">
                                <a href="../demo/attendance-form.php" class="btn btn-primary">Add Attendance</a>
                                <button id="edit-attendance-btn" class="btn btn-outline">Edit Selected</button>
                                <button id="delete-attendance-btn" class="btn btn-outline text-red-600">Delete Selected</button>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="p-6 rounded-lg shadow-sm bg-white">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">🗓️</span>
                                    <div>
                                        <p class="text-sm opacity-70">Attendance Records</p>
                                        <p id="att-total" class="text-2xl font-bold">
                                            <?php echo $result ? mysqli_num_rows($result) : 0; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 rounded-lg shadow-sm bg-white">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">✅</span>
                                    <div>
                                        <p class="text-sm opacity-70">On Time Today</p>
                                        <p id="att-ontime" class="text-2xl font-bold">
                                            <?php echo $onTimeToday; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 rounded-lg shadow-sm bg-white">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">⏱️</span>
                                    <div>
                                        <p class="text-sm opacity-70">Avg Hours</p>
                                        <p id="att-avg" class="text-2xl font-bold">
                                            <?php echo $avgHours > 0 ? $avgHours . ' h' : '0 h'; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 rounded-lg shadow-sm bg-white">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">🚫</span>
                                    <div>
                                        <p class="text-sm opacity-70">Absences</p>
                                        <p id="att-absent" class="text-2xl font-bold">
                                            <?php echo $absencesTotal; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Attendance Table Section -->
                    <section class="rounded-lg shadow-sm overflow-hidden bg-white">
                        <!-- Toolbar -->
                        <div class="px-6 py-4 border-b">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <!-- Filters -->
                                <div class="flex flex-wrap items-end gap-3">
                                    <div>
                                        <label for="from-date" class="block text-sm mb-1">From</label>
                                        <input type="date" id="from-date" class="form-control" />
                                    </div>
                                    <div>
                                        <label for="to-date" class="block text-sm mb-1">To</label>
                                        <input type="date" id="to-date" class="form-control" />
                                    </div>
                                    <div>
                                        <label for="filter-emp-id" class="block text-sm mb-1">Employee ID</label>
                                        <input type="text" id="filter-emp-id" class="form-control" placeholder="e.g. 1" />
                                    </div>
                                    <button id="apply-filter" class="btn btn-outline">Apply</button>
                                </div>

                                <!-- Export buttons (demo) -->
                                <div class="flex gap-2">
                                    <button class="px-3 py-1 rounded-md text-sm border">Copy</button>
                                    <button class="px-3 py-1 rounded-md text-sm border">PDF</button>
                                    <button class="px-3 py-1 rounded-md text-sm border">Excel</button>
                                    <button class="px-3 py-1 rounded-md text-sm border">CSV</button>
                                    <button class="px-3 py-1 rounded-md text-sm border">Print</button>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <form id="delete-form" method="post">
                                <table id="attendance-table" class="min-w-full divide-y">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-sm font-semibold"></th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Attendance ID</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Employee ID</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Date</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Time In</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Time Out</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y bg-white">
                                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                            <?php mysqli_data_seek($result, 0); ?>
                                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                <tr>
                                                    <td class="px-4 py-3">
                                                        <input type="checkbox" class="row-check" />
                                                    </td>
                                                    <td class="px-4 py-3 text-sm">
                                                        <?php echo htmlspecialchars($row['AttendanceID']); ?>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm">
                                                        <?php echo htmlspecialchars($row['EmployeeID']); ?>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm">
                                                        <?php echo htmlspecialchars($row['Date']); ?>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm">
                                                        <?php echo htmlspecialchars($row['TimeIn']); ?>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm">
                                                        <?php echo htmlspecialchars($row['TimeOut']); ?>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm">
                                                        <?php echo htmlspecialchars($row['Status']); ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="px-4 py-3 text-center text-sm text-gray-500">
                                                    No attendance records found.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

<?php
if ($result) {
    mysqli_free_result($result);
}
mysqli_close($conn);
?>

<script>
  // Lọc theo ngày & EmployeeID (client-side)
  document.getElementById('apply-filter')?.addEventListener('click', () => {
    const from = document.getElementById('from-date')?.value || '';
    const to   = document.getElementById('to-date')?.value || '';
    const emp  = (document.getElementById('filter-emp-id')?.value || '').trim();

    const rows = document.querySelectorAll('#attendance-table tbody tr');
    rows.forEach(row => {
      const dateText = row.children[3].textContent.trim(); // cột Date
      const empId    = row.children[2].textContent.trim(); // cột EmployeeID

      const okEmp  = !emp || empId.includes(emp);
      const okDate = (!from || dateText >= from) && (!to || dateText <= to);

      row.style.display = (okEmp && okDate) ? '' : 'none';
    });
  });

  // Edit Selected -> redirect sang attendance-form.php?id=...
  document.getElementById('edit-attendance-btn')?.addEventListener('click', () => {
    const checked = document.querySelector('#attendance-table tbody input.row-check:checked');
    if (!checked) {
      alert('Please select one record to edit.');
      return;
    }
    const row = checked.closest('tr');
    const id  = row.children[1].textContent.trim(); // AttendanceID
    window.location.href = '../demo/attendance-form.php?id=' + encodeURIComponent(id);
  });

  // Delete Selected -> POST delete_ids[]
  document.getElementById('delete-attendance-btn')?.addEventListener('click', () => {
    const checks = document.querySelectorAll('#attendance-table tbody input.row-check:checked');
    if (!checks.length) {
      alert('Please select at least one record to delete.');
      return;
    }
    if (!confirm('Are you sure you want to delete the selected record(s)?')) return;

    const form = document.getElementById('delete-form');
    // Clear old hidden inputs
    Array.from(form.querySelectorAll('input[name="delete_ids[]"]')).forEach(el => el.remove());

    checks.forEach(chk => {
      const row = chk.closest('tr');
      const id  = row.children[1].textContent.trim();
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'delete_ids[]';
      input.value = id;
      form.appendChild(input);
    });

    form.submit();
  });
</script>
</body>
</html>
