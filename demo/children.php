<?php
require_once "settings.php";

// Kết nối DB
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("<p>Database connection failure: " . htmlspecialchars(mysqli_connect_error()) . "</p>");
}

// Lấy dữ liệu từ bảng children
$sql = "
    SELECT 
        ChildID,
        EmployeeID,
        Name,
        DateOfBirth,
        Gender
    FROM `children`
    ORDER BY EmployeeID ASC, ChildID ASC
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
                            <a href="../demo/login.html">Log Out</a>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!--sidebar-->

       <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar Navigation -->
            <div id="sidebar" class="w-64 shadow-lg sidebar-transition lg:translate-x-0 -translate-x-full fixed lg:relative z-30 h-full bg-white">
                <!-- h-full + overflow-y-auto để sidebar có thể scroll xuống -->
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

                    <!-- JOB MANAGEMENT (scroll xuống sẽ thấy hết) -->
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


            <!-- Main Content Area (giống y attendance, chỉ đổi bảng) -->
            <div class="flex-1 overflow-y-auto bg-gray-50">
                <div class="p-6">
                    <!-- Header + Action -->
                    <section class="mb-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold">Children</h2>
                            <div class="flex gap-2">
                                <a href="../demo/attendance-form.html" class="btn btn-primary">Add Information</a>
                                <button id="edit-attendance-btn" class="btn btn-outline">Edit Selected</button>
                            </div>
                        </div>

                        <!-- Quick Stats (để nguyên, chỉ dùng số record từ children) -->
                        <div class="grid grid-cols-1 md:grid-cols gap-6">
                            <div class="p-6 rounded-lg shadow-sm bg-white">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">🗓️</span>
                                    <div>
                                        <p class="text-sm opacity-70">Kids Records</p>
                                        <p id="att-total" class="text-2xl font-bold">
                                            <?php echo $rowCount; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                    </section>

                    <!-- Children Table Section (chỉ đổi chỗ này so với attendance) -->
                    <section class="rounded-lg shadow-sm overflow-hidden bg-white">
                        <!-- Toolbar giữ nguyên -->
                        <div class="px-6 py-4 border-b">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
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

                                <div class="flex gap-2">
                                    <button class="px-3 py-1 rounded-md text-sm border">Copy</button>
                                    <button class="px-3 py-1 rounded-md text-sm border">PDF</button>
                                    <button class="px-3 py-1 rounded-md text-sm border">Excel</button>
                                    <button class="px-3 py-1 rounded-md text-sm border">CSV</button>
                                    <button class="px-3 py-1 rounded-md text-sm border">Print</button>
                                </div>
                            </div>
                        </div>

                        <!-- Table: Children -->
                        <div class="overflow-x-auto">
                            <table id="attendance-table" class="min-w-full divide-y">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-sm font-semibold"></th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Children ID</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Employee ID</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Name</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Date Of Birth</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Gender</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y bg-white">
                                    <?php if ($result && $rowCount > 0): ?>
                                        <?php mysqli_data_seek($result, 0); ?>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <input type="checkbox" class="row-check" />
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <?php echo htmlspecialchars($row['ChildID']); ?>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <?php echo htmlspecialchars($row['EmployeeID']); ?>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <?php echo htmlspecialchars($row['Name']); ?>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <?php echo htmlspecialchars($row['DateOfBirth']); ?>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <?php echo htmlspecialchars($row['Gender']); ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500">
                                                No children records found.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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
  // Filter theo EmployeeID, tạm bỏ lọc ngày vì children không có field date range
  document.getElementById('apply-filter')?.addEventListener('click', () => {
    const emp = (document.getElementById('filter-emp-id')?.value || '').trim();
    const rows = document.querySelectorAll('#attendance-table tbody tr');

    rows.forEach(row => {
      const empId = row.children[2].textContent.trim(); // cột EmployeeID
      const okEmp = !emp || empId.includes(emp);
      row.style.display = okEmp ? '' : 'none';
    });
  });
</script>
</body>
</html>
