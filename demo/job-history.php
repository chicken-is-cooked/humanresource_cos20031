<?php
require_once "settings.php";

// Kết nối DB
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("<p>Database connection failure: " . htmlspecialchars(mysqli_connect_error()) . "</p>");
}

// ===== LẤY DỮ LIỆU JOB HISTORY =====
// ĐỔI tên bảng nếu bạn đặt khác (vd: job_history, JobHistory, ...)
$tableName = "jobhistory";

$sql = "
    SELECT 
        EmployeeID,
        StartDate,
        DepartmentID,
        EndDate,
        PositionID,
        PayrollID
    FROM `$tableName`
    ORDER BY EmployeeID ASC, StartDate DESC
";

$result = mysqli_query($conn, $sql);
$rowCount = $result ? mysqli_num_rows($result) : 0;
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

        .btn {
            padding: .5rem .75rem;
            border-radius: .5rem;
            font-weight: 500;
        }
        .btn-primary {
            background: var(--primary);
            color: #fff;
        }
        .btn-outline {
            border: 1px solid #e5e7eb;
            background: #fff;
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

        .sidebar a { border-radius: .75rem; }
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
                        <a href="../demo/login.php">Log Out</a>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar -->
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
                    <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors bg-gray-100" data-section="job-history">
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

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto bg-gray-50">
            <div class="p-6">
                <!-- Header + Stats -->
                <section class="mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Employee Job History</h2>
                        <div class="flex gap-2">
                            <a href="../demo/job-history-form.html" class="btn btn-primary">Add Job History</a>
                            <button id="jh-bulk-remove" class="btn btn-outline">Remove Selected</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-6 rounded-lg shadow-sm bg-white">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">🧾</span>
                                <div>
                                    <p class="text-sm opacity-70">Total Job History Records</p>
                                    <p class="text-2xl font-bold">
                                        <?php echo $rowCount; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- Bạn có thể thêm các card khác nếu muốn -->
                    </div>
                </section>

                <!-- Job History Table -->
                <section class="rounded-lg shadow-sm overflow-hidden bg-white">
                    <!-- Toolbar đơn giản -->
                    <div class="px-6 py-4 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <h3 class="text-lg font-semibold">Job History List</h3>

                        <div class="flex flex-wrap items-end gap-3">
                            <div>
                                <label for="filter-emp" class="block text-sm mb-1">Employee ID</label>
                                <input id="filter-emp" type="text" class="form-control" placeholder="e.g. 327" />
                            </div>
                            <div>
                                <label for="filter-dept" class="block text-sm mb-1">Department ID</label>
                                <input id="filter-dept" type="text" class="form-control" placeholder="e.g. 10" />
                            </div>
                            <button id="jh-apply-filter" class="btn btn-outline mt-4">Apply</button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="jh-table" class="min-w-full divide-y">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold"></th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">Employee ID</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">Start Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">Department ID</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">End Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">Position ID</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">Payroll ID</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y bg-white">
                                <?php
                                if ($result && mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td class='px-4 py-3'><input type='checkbox' class='row-check' /></td>";
                                        echo "<td class='px-4 py-3'>" . htmlspecialchars($row['EmployeeID']) . "</td>";
                                        echo "<td class='px-4 py-3'>" . htmlspecialchars($row['StartDate']) . "</td>";
                                        echo "<td class='px-4 py-3'>" . htmlspecialchars($row['DepartmentID']) . "</td>";
                                        echo "<td class='px-4 py-3'>" . htmlspecialchars($row['EndDate']) . "</td>";
                                        echo "<td class='px-4 py-3'>" . htmlspecialchars($row['PositionID']) . "</td>";
                                        echo "<td class='px-4 py-3'>" . htmlspecialchars($row['PayrollID']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='px-4 py-3 text-center text-sm text-gray-500'>No job history records found.</td></tr>";
                                }

                                if ($result) {
                                    mysqli_free_result($result);
                                }
                                mysqli_close($conn);
                                ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<script>
  // Filter client-side
  document.getElementById('jh-apply-filter')?.addEventListener('click', () => {
    const empFilter  = (document.getElementById('filter-emp')?.value || '').trim().toLowerCase();
    const deptFilter = (document.getElementById('filter-dept')?.value || '').trim().toLowerCase();

    const rows = document.querySelectorAll('#jh-table tbody tr');
    rows.forEach(row => {
      const empId  = row.children[1].textContent.trim().toLowerCase();
      const deptId = row.children[3].textContent.trim().toLowerCase();

      const okEmp  = !empFilter  || empId.includes(empFilter);
      const okDept = !deptFilter || deptId.includes(deptFilter);

      row.style.display = (okEmp && okDept) ? '' : 'none';
    });
  });
</script>
</body>
</html>
