<?php
session_start();
require_once "settings.php";

// Kết nối DB
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("Unable to connect to the database. Error: " .
        htmlspecialchars(mysqli_connect_errno() . " - " . mysqli_connect_error()));
}

// Chưa đăng nhập
if (!isset($_SESSION['employeeID'], $_SESSION['role'])) {
    header('Location: ../login/login.php');
    exit;
}

// Không phải admin role
if (!in_array($_SESSION['role'], ['CEO', 'HR', 'Manager'])) {
    header('Location: ../employee/index.php');
    exit;
}

$employeeID = (int)$_SESSION['employeeID'];
$role       = $_SESSION['role'];

// ================== BIẾN DÙNG CHUNG ==================
$db_error         = "";
$totalEmployees   = 0;
$activeEmployees  = 0;
$totalDepartments = 0;
$avgSalary        = 0;  // chưa có bảng lương nên tạm để 0

// ================== XỬ LÝ XOÁ (REMOVE SELECTED) ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ids'])) {
    $ids = array_filter(array_map('intval', $_POST['delete_ids'] ?? []));
    if (!empty($ids)) {
        $idList = implode(',', $ids);
        $delSql = "DELETE FROM Employees7 WHERE EmployeeID IN ($idList)";
        mysqli_query($conn, $delSql);
    }
}

// ================== LẤY THAM SỐ SEARCH ==================
$searchEmpId = isset($_GET['emp_id']) ? trim($_GET['emp_id']) : '';
$searchName  = isset($_GET['name'])   ? trim($_GET['name'])   : '';

// ================== THỐNG KÊ ==================
$countSql = "
    SELECT 
        COUNT(*) AS total,
        COUNT(CASE WHEN EmploymentType <> 'contractor' THEN 1 END) AS active,
        COUNT(DISTINCT DepartmentID) AS departments
    FROM Employees7
";

if ($countRes = mysqli_query($conn, $countSql)) {
    $row = mysqli_fetch_assoc($countRes);
    $totalEmployees   = (int)$row['total'];
    $activeEmployees  = (int)$row['active'];
    $totalDepartments = (int)$row['departments'];
    mysqli_free_result($countRes);
}

// ================== QUERY DANH SÁCH EMPLOYEE ==================
$sql = "SELECT * FROM Employees7";
$conditions = [];

// Lọc theo EmployeeID
if ($searchEmpId !== '') {
    $empIdInt     = (int)$searchEmpId;
    $conditions[] = "EmployeeID = $empIdInt";
}

// Lọc theo tên
if ($searchName !== '') {
    $safeName     = mysqli_real_escape_string($conn, $searchName);
    $conditions[] = "(FirstName LIKE '%$safeName%' OR LastName LIKE '%$safeName%')";
}

if ($conditions) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY EmployeeID ASC";

$result = mysqli_query($conn, $sql);
if (!$result) {
    $db_error = "Database error: " . mysqli_error($conn);
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
                        <a href="../login/login.php">Log Out</a>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Layout -->
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
                            <span><a href="../demo/salary.php">Salary Management</a></span>
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

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto bg-gray-50">
            <div class="p-6">
                <!-- Stats -->
                <section class="mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Employee Management</h2>
                        <a href="employee-form.php" class="btn btn-primary">Add Employee</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Total Employees -->
                        <div class="p-6 rounded-lg shadow-sm bg-white">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">👥</span>
                                <div>
                                    <p class="text-sm opacity-70">Total Employees</p>
                                    <p id="total-employees" class="text-2xl font-bold">
                                        <?php echo $totalEmployees; ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Active -->
                        <div class="p-6 rounded-lg shadow-sm bg-white">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">✅</span>
                                <div>
                                    <p class="text-sm opacity-70">Active</p>
                                    <p id="active-employees" class="text-2xl font-bold">
                                        <?php echo $activeEmployees; ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Departments -->
                        <div class="p-6 rounded-lg shadow-sm bg-white">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">🏢</span>
                                <div>
                                    <p class="text-sm opacity-70">Departments</p>
                                    <p id="total-departments" class="text-2xl font-bold">
                                        <?php echo $totalDepartments; ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Avg Salary (demo) -->
                        <div class="p-6 rounded-lg shadow-sm bg-white">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">📈</span>
                                <div>
                                    <p class="text-sm opacity-70">Avg Salary</p>
                                    <p id="avg-salary" class="text-2xl font-bold">
                                        $<?php echo $avgSalary; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Employee Table -->
                <section class="rounded-lg shadow-sm overflow-hidden bg-white">
                    <div class="px-6 py-4 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <h3 class="text-lg font-semibold">Employee Directory</h3>

                        <!-- Search form -->
                        <form method="get" class="flex flex-wrap items-end gap-3">
                            <div>
                                <label for="emp_id" class="block text-sm mb-1">Employee ID</label>
                                <input
                                    type="text"
                                    id="emp_id"
                                    name="emp_id"
                                    class="border rounded-md px-2 py-1 text-sm"
                                    placeholder="e.g. 101"
                                    value="<?php echo htmlspecialchars($searchEmpId); ?>"
                                >
                            </div>
                            <div>
                                <label for="name" class="block text-sm mb-1">Name</label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    class="border rounded-md px-2 py-1 text-sm"
                                    placeholder="First or Last name"
                                    value="<?php echo htmlspecialchars($searchName); ?>"
                                >
                            </div>
                            <div class="flex gap-2 mb-1">
                                <button type="submit" class="btn btn-outline mt-4">Search</button>
                                <a href="employee.php" class="btn btn-outline mt-4">Clear</a>
                            </div>
                        </form>

                        <!-- Toolbar -->
                        <div class="flex gap-2" id="employee-toolbar">
                            <button id="edit-employee" class="btn btn-outline">Edit Selected</button>
                            <button id="remove-employee" class="btn btn-outline">Remove Selected</button>
                            <button class="px-3 py-1 rounded-md text-sm border">Copy</button>
                            <button class="px-3 py-1 rounded-md text-sm border">PDF</button>
                            <button class="px-3 py-1 rounded-md text-sm border">Excel</button>
                            <button class="px-3 py-1 rounded-md text-sm border">CSV</button>
                            <button class="px-3 py-1 rounded-md text-sm border">Print</button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <form id="employee-delete-form" method="post">
                            <table id="employee-table" class="min-w-full divide-y">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-sm font-semibold"></th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Employee ID</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">First Name</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Last Name</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Date of Birth</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Gender</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Address</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Contact</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Email</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Payment Info</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Marriage Status</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Children</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Password</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Health Insurance</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Position ID</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Department ID</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Employment Type</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Role</th>
                                        <th class="px-4 py-3 text-right text-sm font-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y bg-white">
                                <?php
                                if ($db_error !== "") {
                                    echo "<tr><td colspan='19' class='px-4 py-3 text-red-600'>" . htmlspecialchars($db_error) . "</td></tr>";
                                } else {
                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td class='px-4 py-3'><input type='checkbox' class='row-check'></td>";
                                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['EmployeeID']) . "</td>";
                                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['FirstName']) . "</td>";
                                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['LastName']) . "</td>";
                                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['DateOfBirth']) . "</td>";
                                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['Gender']) . "</td>";
                                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['Address']) . "</td>";
                                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['Contact']) . "</td>";
                                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['Email']) . "</td>";
                                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['PaymentInfo']) . "</td>";
                                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['MarriageStatus']) . "</td>";
                                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['Children']) . "</td>";
                                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['Password']) . "</td>";
                                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['HealthInsurance']) . "</td>";
                                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['PositionID']) . "</td>";
                                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['DepartmentID']) . "</td>";
                                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['EmploymentType']) . "</td>";
                                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['Role']) . "</td>";
                                            echo "<td class='px-4 py-3 text-right'>";
                                            echo "<button type='button' class='text-sm underline text-red-600 remove-row'>Delete</button>";
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='19' class='px-4 py-3'>No employees found.</td></tr>";
                                    }
                                }
                                ?>
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
if ($result instanceof mysqli_result) {
    mysqli_free_result($result);
}
mysqli_close($conn);
?>

<!-- Toast Notifications -->
<div id="toasts" class="fixed top-4 right-4 z-50 space-y-2"></div>

<!-- Script gốc -->
<script src="../demo/framework/employee.js" defer></script>

<!-- JS cho Edit / Remove Selected -->
<script>
document.getElementById('edit-employee')?.addEventListener('click', function (e) {
    e.preventDefault();
    const checked = document.querySelector('#employee-table tbody input.row-check:checked');
    if (!checked) {
        alert('Please select one employee to edit.');
        return;
    }
    const row   = checked.closest('tr');
    const empId = row.children[1].textContent.trim(); // cột Employee ID
    window.location.href = 'employee-form.php?id=' + encodeURIComponent(empId);
});

document.getElementById('remove-employee')?.addEventListener('click', function (e) {
    e.preventDefault();
    const checks = document.querySelectorAll('#employee-table tbody input.row-check:checked');
    if (!checks.length) {
        alert('Please select at least one employee to remove.');
        return;
    }
    if (!confirm('Are you sure you want to delete the selected employee(s)?')) return;

    const form = document.getElementById('employee-delete-form');

    // Xoá hidden input cũ
    Array.from(form.querySelectorAll('input[name="delete_ids[]"]')).forEach(el => el.remove());

    checks.forEach(chk => {
        const row   = chk.closest('tr');
        const empId = row.children[1].textContent.trim();
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'delete_ids[]';
        input.value = empId;
        form.appendChild(input);
    });

    form.submit();
});
</script>

</body>
</html>
