<?php
    include 'settings.php'; // hoặc settings.php, tuỳ project của bạn
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

    <script src="../demo/framework/salary.js" defer></script>

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

        /* Base Styles */
        html, body { height: 100%; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        /* Component Styles */
        .card { background: var(--surface); }
        .shadow-smooth { box-shadow: 0 10px 30px rgba(0,0,0,.06); }
        
        /* Sidebar Styles */
        .sidebar { background: #fff; }
        .sidebar a { border-radius: .75rem; }
        .sidebar a.active { 
            background: #eef2ff;
            font-weight: 600;
        }

        /* Button Styles */
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

        /* Table Styles */
        .table th, .table td { white-space: nowrap; }
        
        /* Utility Classes */
        .badge {
            padding: .125rem .5rem;
            border-radius: .5rem;
            background: #ecfeff;
        }
        .modal-backdrop { background: rgba(0,0,0,.35); }
    </style>
</head>
<body>
    <div id="dashboard" class="h-full flex flex-col">
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
                        <button id="logout-btn" class="px-3 py-1 text-sm rounded-md transition-colors hover:opacity-80"><a href="../demo/login.html">Log Out</a></button>
                    </div>
                </div>
            </div>
        </nav>

        <!--sidebar-->

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

      <!-- Main Content Area -->
      <div class="flex-1 overflow-y-auto bg-gray-50">
        <div class="p-6">
          <!-- Header + Actions -->
          <section class="mb-8">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-bold">Employee Salary</h2>
              <div class="flex gap-2">
                <a href="../pages/salary-edit.html" class="btn btn-primary">Edit Salary</a>
                <button id="remove-employee" class="btn btn-outline">Remove Selected</button>
              </div>
            </div>
          </section>

          <!-- Salary Table -->
          <section class="rounded-lg shadow-sm overflow-hidden bg-white">
            <!-- Toolbar -->
            <div class="px-6 py-4 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-4">

              <!-- (Optional) Filters -->
              <div class="flex flex-wrap items-end gap-3">
                <div>
                  <label for="filter-emp-id" class="block text-sm mb-1">Employee ID</label>
                  <input id="filter-emp-id" type="text" class="form-control" placeholder="e.g. 001" />
                </div>
                <button id="apply-filter" class="btn btn-outline">Apply</button>
              </div>

              <!-- Export -->
              <div class="flex gap-2">
                <button class="px-3 py-1 rounded-md text-sm border">Copy</button>
                <button class="px-3 py-1 rounded-md text-sm border">PDF</button>
                <button class="px-3 py-1 rounded-md text-sm border">Excel</button>
                <button class="px-3 py-1 rounded-md text-sm border">CSV</button>
                <button class="px-3 py-1 rounded-md text-sm border">Print</button>
              </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
              <table id="salary-table" class="min-w-full divide-y">
                  <thead class="bg-gray-50">
                    <tr>
                      <!-- Checkbox -->
                      <th class="px-4 py-3 text-left text-sm font-semibold"></th>
                      <!-- Cột hiển thị chính -->
                      <th class="px-4 py-3 text-left text-sm font-semibold">Salary ID</th>
                      <th class="px-4 py-3 text-left text-sm font-semibold">Employee ID</th>
                      <th class="px-4 py-3 text-left text-sm font-semibold">Basic Salary</th>
                      <!-- Actions -->
                      <th class="px-4 py-3 text-right text-sm font-semibold">Actions</th>
                    </tr>
                  </thead>

                  <tbody class="divide-y bg-white">
                      <?php
                        // Lấy dữ liệu từ bảng Salary
                        // GIẢ SỬ bảng Salary có cột: SalaryID, EmployeeID, BasicSalary
                        $sql = "SELECT SalaryID, EmployeeID, BasicSalary FROM Salary";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0):
                          while ($row = $result->fetch_assoc()):
                            $basic = (float)$row['BasicSalary'];

                            // ====== Tạo dữ liệu chi tiết “bịa nhẹ” ======
                            $hoursWorked = 160;                           // cố định 160 giờ
                            $hourlyRate  = $hoursWorked > 0 ? $basic / $hoursWorked : 0;
                            $bonus       = $basic * 0.10;                 // 10% lương cơ bản
                            $deduction   = $basic * 0.05;                 // 5% khấu trừ
                            $totalPaid   = $basic + $bonus - $deduction;  // tổng thực nhận
                      ?>
                        <tr class="salary-row"
                            data-id="<?= htmlspecialchars($row['SalaryID']) ?>"
                            data-empid="<?= htmlspecialchars($row['EmployeeID']) ?>"
                            data-hours="<?= htmlspecialchars(number_format($hoursWorked, 0)) ?>"
                            data-hourly="<?= htmlspecialchars(number_format($hourlyRate, 2)) ?>"
                            data-basesalary="<?= htmlspecialchars(number_format($basic, 2)) ?>"
                            data-bonus="<?= htmlspecialchars(number_format($bonus, 2)) ?>"
                            data-deduction="<?= htmlspecialchars(number_format($deduction, 2)) ?>"
                            data-total="<?= htmlspecialchars(number_format($totalPaid, 2)) ?>">

                          <!-- Checkbox -->
                          <td class="px-4 py-3">
                            <input type="checkbox" class="row-check" />
                          </td>

                          <!-- Hiển thị chính -->
                          <td class="px-4 py-3"><?= htmlspecialchars($row['SalaryID']) ?></td>
                          <td class="px-4 py-3"><?= htmlspecialchars($row['EmployeeID']) ?></td>
                          <td class="px-4 py-3">$<?= htmlspecialchars(number_format($basic, 2)) ?></td>

                          <!-- Nút View -->
                          <td class="px-4 py-3 text-right">
                            <button type="button" class="text-sm underline text-blue-600 view-details">
                              View
                            </button>
                          </td>
                        </tr>
                      <?php
                          endwhile;
                        else:
                      ?>
                        <tr>
                          <td colspan="5" class="px-4 py-3 text-center text-sm text-gray-500">
                            No salary records found.
                          </td>
                        </tr>
                      <?php endif; ?>
                    </tbody>

                </table>


              
            </div>
          </section>

          <!-- Salary Detail Panel -->
          <section id="salary-detail" class="rounded-lg shadow-sm bg-white mt-6 px-6 py-5 hidden">
            <h3 class="text-lg font-semibold mb-4">Salary Detail</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div><strong>Salary ID:</strong> <span id="d-id"></span></div>
              <div><strong>Employee ID:</strong> <span id="d-empid"></span></div>
              <div><strong>Total Hours:</strong> <span id="d-hours"></span></div>
              <div><strong>Hourly Rate:</strong> $<span id="d-hourly"></span></div>
              <div><strong>Base Salary:</strong> $<span id="d-base"></span></div>
              <div><strong>Bonus:</strong> $<span id="d-bonus"></span></div>
              <div><strong>Deduction:</strong> $<span id="d-deduction"></span></div>
              <div class="font-semibold"><strong>Total Paid:</strong> $<span id="d-total"></span></div>
            </div>
          </section>
        </div>
      </div>

</body>
</html>