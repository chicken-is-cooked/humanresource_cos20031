<?php
require_once "settings.php";
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
            <!-- Sidebar Navigation -->
            <div id="sidebar" class="w-64 shadow-lg sidebar-transition lg:translate-x-0 -translate-x-full fixed lg:relative z-30 h-full bg-white">
                <div class="p-4">
                    <nav class="space-y-2">
                        <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="employees">
                            <div class="flex items-center">
                                <span class="text-lg mr-3">👥</span>
                                <span><a href="../demo/employee.html">Employees</a></span>
                            </div>
                        </button>
                        <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="salary">
                            <div class="flex items-center">
                                <span class="text-lg mr-3">💰</span>
                                <span><a href="../demo/salary.html">Salary Management</a></span>
                            </div>
                        </button>
                        <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="attendance">
                            <div class="flex items-center">
                                <span class="text-lg mr-3">📅</span>
                                <span><a href="../demo/attendance.html">Attendance</a></span>
                            </div>
                        </button>
                        <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="departments">
                            <div class="flex items-center">
                                <span class="text-lg mr-3">🏢</span>
                                <span><a href="../demo/department.html">Departments</a></span>
                            </div>
                        </button>
                        <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="reports">
                            <div class="flex items-center">
                                <span class="text-lg mr-3">📊</span>
                                <span><a href="../demo/leave-request.html">Leave Request</a></span>
                            </div>
                        </button>
                        <button class="sidebar-item w-full text-left px-4 py-3 rounded-lg transition-colors" data-section="settings">
                            <div class="flex items-center">
                                <span class="text-lg mr-3">👥</span>
                                <span><a href="../demo/job-history.html">Job History</a></span>
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
          <a href="../demo/attendance-form.html" class="btn btn-primary">Add Attendance</a>
          <button id="edit-attendance-btn" class="btn btn-outline">Edit Selected</button>
        </div>
      </div>

      <!-- Quick Stats (optional—giống Employee) -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="p-6 rounded-lg shadow-sm bg-white">
          <div class="flex items-center">
            <span class="text-2xl mr-3">🗓️</span>
            <div>
              <p class="text-sm opacity-70">Attendance Records</p>
              <p id="att-total" class="text-2xl font-bold">2</p>
            </div>
          </div>
        </div>
        <div class="p-6 rounded-lg shadow-sm bg-white">
          <div class="flex items-center">
            <span class="text-2xl mr-3">✅</span>
            <div>
              <p class="text-sm opacity-70">On Time Today</p>
              <p id="att-ontime" class="text-2xl font-bold">2</p>
            </div>
          </div>
        </div>
        <div class="p-6 rounded-lg shadow-sm bg-white">
          <div class="flex items-center">
            <span class="text-2xl mr-3">⏱️</span>
            <div>
              <p class="text-sm opacity-70">Avg Hours</p>
              <p id="att-avg" class="text-2xl font-bold">8h</p>
            </div>
          </div>
        </div>
        <div class="p-6 rounded-lg shadow-sm bg-white">
          <div class="flex items-center">
            <span class="text-2xl mr-3">🚫</span>
            <div>
              <p class="text-sm opacity-70">Absences</p>
              <p id="att-absent" class="text-2xl font-bold">0</p>
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
              <input type="text" id="filter-emp-id" class="form-control" placeholder="e.g. 001" />
            </div>
            <button id="apply-filter" class="btn btn-outline">Apply</button>
          </div>

          <!-- Export buttons -->
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
        <table id="attendance-table" class="min-w-full divide-y">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-sm font-semibold"></th>
              <th class="px-4 py-3 text-left text-sm font-semibold">Attendance ID</th>
              <th class="px-4 py-3 text-left text-sm font-semibold">Employee ID</th>
              <th class="px-4 py-3 text-left text-sm font-semibold">Work Date</th>
              <th class="px-4 py-3 text-left text-sm font-semibold">Time In</th>
              <th class="px-4 py-3 text-left text-sm font-semibold">Time Out</th>
              <th class="px-4 py-3 text-right text-sm font-semibold">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y bg-white">
            <tr>
              <td class="px-4 py-3"><input type="checkbox" class="row-check" /></td>
              <td class="px-4 py-3">A-001</td>
              <td class="px-4 py-3">001</td>
              <td class="px-4 py-3">Khanh</td>
              <td class="px-4 py-3">Ngu</td>
              <td class="px-4 py-3">2004-11-30</td>
              <td class="px-4 py-3">08:00</td>
              <td class="px-4 py-3">17:00</td>
              <td class="px-4 py-3">540</td>
              <td class="px-4 py-3">Regular shift</td>
              <td class="px-4 py-3 text-right">
                <button class="text-sm underline text-red-600 remove-row">Delete</button>
              </td>
            </tr>
            <tr>
              <td class="px-4 py-3"><input type="checkbox" class="row-check" /></td>
              <td class="px-4 py-3">A-002</td>
              <td class="px-4 py-3">002</td>
              <td class="px-4 py-3">Nguyen</td>
              <td class="px-4 py-3">Khanh</td>
              <td class="px-4 py-3">2004-11-30</td>
              <td class="px-4 py-3">09:00</td>
              <td class="px-4 py-3">18:00</td>
              <td class="px-4 py-3">540</td>
              <td class="px-4 py-3">Late 1h</td>
              <td class="px-4 py-3 text-right">
                <button class="text-sm underline text-red-600 remove-row">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</div>

<!-- Optional JS: match behavior with Employee table -->
<script>
  // Toggle sidebar on mobile (giữ nguyên nếu bạn đã có)
  document.querySelector('.sidebar-toggle')?.addEventListener('click', () => {
    document.getElementById('appSidebar')?.classList.toggle('open');
  });

  // Demo: remove row
  document.querySelectorAll('#attendance-table .remove-row').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const tr = e.target.closest('tr');
      tr?.parentNode?.removeChild(tr);
      // TODO: toast giống Employee nếu bạn đã có hệ thống toast
    });
  });

  // Demo: filter (client-side)
  document.getElementById('apply-filter')?.addEventListener('click', () => {
    const from = document.getElementById('from-date')?.value || '';
    const to = document.getElementById('to-date')?.value || '';
    const empId = (document.getElementById('filter-emp-id')?.value || '').trim();

    const rows = document.querySelectorAll('#attendance-table tbody tr');
    rows.forEach(row => {
      const date = row.children[5].textContent.trim(); // Work Date col (YYYY-MM-DD)
      const eid  = row.children[2].textContent.trim();

      const okEmp = !empId || eid.includes(empId);
      const okDate = (!from || date >= from) && (!to || date <= to);

      row.style.display = (okEmp && okDate) ? '' : 'none';
    });
  });
</script>
</body>
</html>