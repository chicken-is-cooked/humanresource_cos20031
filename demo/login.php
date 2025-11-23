<?php
session_start();
include 'settings.php'; // dùng chung file connect DB như các trang khác

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeId = trim($_POST['employeeId'] ?? '');
    $password   = trim($_POST['password'] ?? '');

    if ($employeeId === '' || $password === '') {
        $error = 'Please enter both EmployeeID and password.';
    } else {
        // Chuẩn bị câu lệnh lấy EmployeeID, Password, Role
        $stmt = $conn->prepare("SELECT EmployeeID, Password, Role FROM Employees7 WHERE EmployeeID = ? LIMIT 1");
        $stmt->bind_param("i", $employeeId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Ở assignment này, Password đang lưu plain text (VD: 'AM5855')
            if ($row['Password'] === $password) {
                // Lưu session cho các trang khác dùng
                $_SESSION['employeeID'] = $row['EmployeeID'];
                $_SESSION['role']       = $row['Role'];

                $role = $row['Role'];

                // Admin roles
                if (in_array($role, ['CEO', 'HR', 'Manager'])) {
                    header('Location: ../demo/employee.php');
                    exit;
                }

                // Employee (staff)
                if ($role === 'Employee') {
                    header('Location: ../employee/index.php');
                    exit;
                }

                // Nếu là role khác lạ thì cho về employee dashboard luôn
                header('Location: ../employee/index.php');
                exit;
            } else {
                $error = 'Invalid EmployeeID or password.';
            }
        } else {
            $error = 'Invalid EmployeeID or password.';
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <script src="https://cdn.tailwindcss.com"></script>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />

  <style>
    body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Inter,system-ui,sans-serif}
  </style>
</head>
<body id="login" class="min-h-screen bg-gray-50 flex items-center justify-center p-6">

  <div class="w-full max-w-md">
    <div class="rounded-lg shadow-sm bg-white">
      <div class="px-6 py-5 border-b">
        <h2 class="text-2xl font-bold text-center">Login</h2>
        <p class="text-sm opacity-70 text-center mt-1">
          Sign in to access the dashboard
        </p>
      </div>

      <!-- Form POST về chính trang này -->
      <form id="login-form" class="px-6 py-5 space-y-4" method="POST" autocomplete="off">
        <!-- EmployeeID -->
        <div>
          <label for="employeeId" class="block text-sm mb-1">Employee ID</label>
          <input type="number" id="employeeId" name="employeeId"
                 class="form-control"
                 placeholder="e.g. 1"
                 required />
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-sm mb-1">Password</label>
          <div class="input-group">
            <input type="password" id="password" name="password"
                   class="form-control"
                   placeholder="••••••••"
                   required />
            <button class="btn btn-outline-secondary" type="button" id="toggle-pass">Show</button>
          </div>
        </div>

        <!-- Remember (chỉ lưu EmployeeID trên trình duyệt) -->
        <div class="d-flex align-items-center justify-content-between">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="rememberMe">
            <label class="form-check-label" for="rememberMe">Remember me</label>
          </div>
          <small class="opacity-70">
            Only <strong>HR/CEO/Manager</strong> accounts can access admin pages.
          </small>
        </div>

        <!-- Error box -->
        <?php if ($error): ?>
          <div id="error-box" class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error) ?>
          </div>
        <?php else: ?>
          <div id="error-box" class="alert alert-danger d-none" role="alert"></div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="pt-2">
          <button class="btn btn-primary w-100" type="submit">Login</button>
        </div>
      </form>
    </div>

    <p class="text-center text-sm opacity-70 mt-4">
      Trouble signing in? Contact your administrator.
    </p>
  </div>

  <div id="toasts" class="fixed top-4 right-4 z-50 space-y-2"></div>

  <!-- JS nhỏ: show/hide password + remember EmployeeID -->
  <script>
    // Toggle password
    document.getElementById('toggle-pass')?.addEventListener('click', () => {
      const input = document.getElementById('password');
      const btn = document.getElementById('toggle-pass');
      const isPw = input.type === 'password';
      input.type = isPw ? 'text' : 'password';
      btn.textContent = isPw ? 'Hide' : 'Show';
    });

    // Remember EmployeeID
    const form = document.getElementById('login-form');
    form?.addEventListener('submit', () => {
      const remember = document.getElementById('rememberMe').checked;
      const empId = document.getElementById('employeeId').value;
      if (remember) {
        localStorage.setItem('remember_employee_id', empId);
      } else {
        localStorage.removeItem('remember_employee_id');
      }
    });

    // Restore EmployeeID nếu có nhớ
    window.addEventListener('DOMContentLoaded', () => {
      const saved = localStorage.getItem('remember_employee_id');
      if (saved) {
        const input = document.getElementById('employeeId');
        const cb = document.getElementById('rememberMe');
        if (input) input.value = saved;
        if (cb) cb.checked = true;
      }
    });
  </script>
</body>
</html>
