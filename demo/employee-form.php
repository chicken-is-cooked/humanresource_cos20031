<?php
require_once "settings.php";

$errors = [];
$success = "";

// Khi form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Kết nối DB
    $conn = @mysqli_connect($host, $user, $pwd, $sql_db);

    if (!$conn) {
        $errors[] = "Unable to connect to the database. Error: " .
                    mysqli_connect_errno() . " - " . mysqli_connect_error();
    } else {
        // Lấy dữ liệu từ form
        $empId          = trim($_POST['employee_id'] ?? '');
        $firstName      = trim($_POST['first_name'] ?? '');
        $lastName       = trim($_POST['last_name'] ?? '');
        $dob            = trim($_POST['date_of_birth'] ?? '');
        $gender         = trim($_POST['gender'] ?? '');
        $address        = trim($_POST['address'] ?? '');
        $contact        = trim($_POST['contact'] ?? '');
        $email          = trim($_POST['email'] ?? '');
        $paymentInfo    = trim($_POST['payment_info'] ?? '');
        $marriageStatus = trim($_POST['marriage_status'] ?? '');
        $children       = trim($_POST['children'] ?? '');
        $password       = trim($_POST['password'] ?? '');
        $healthIns      = trim($_POST['health_insurance'] ?? '');
        $positionId     = trim($_POST['position_id'] ?? '');
        $departmentId   = trim($_POST['department_id'] ?? '');
        $employmentType = trim($_POST['employment_type'] ?? '');
        $role           = trim($_POST['role'] ?? '');

        // Validate đơn giản
        if ($empId === "" || $firstName === "" || $lastName === "") {
            $errors[] = "Employee ID, First Name và Last Name là bắt buộc.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email không hợp lệ.";
        }

        if (empty($errors)) {
            // Escape cho an toàn
            $empId          = mysqli_real_escape_string($conn, $empId);
            $firstName      = mysqli_real_escape_string($conn, $firstName);
            $lastName       = mysqli_real_escape_string($conn, $lastName);
            $dob            = mysqli_real_escape_string($conn, $dob);
            $gender         = mysqli_real_escape_string($conn, $gender);
            $address        = mysqli_real_escape_string($conn, $address);
            $contact        = mysqli_real_escape_string($conn, $contact);
            $email          = mysqli_real_escape_string($conn, $email);
            $paymentInfo    = mysqli_real_escape_string($conn, $paymentInfo);
            $marriageStatus = mysqli_real_escape_string($conn, $marriageStatus);
            $children       = mysqli_real_escape_string($conn, $children);
            $password       = mysqli_real_escape_string($conn, $password);
            $healthIns      = mysqli_real_escape_string($conn, $healthIns);
            $positionId     = mysqli_real_escape_string($conn, $positionId);
            $departmentId   = mysqli_real_escape_string($conn, $departmentId);
            $employmentType = mysqli_real_escape_string($conn, $employmentType);
            $role           = mysqli_real_escape_string($conn, $role);

            $insertSql = "
                INSERT INTO employees7 (
                    EmployeeID, FirstName, LastName, DateOfBirth, Gender,
                    Address, Contact, Email, PaymentInfo, MarriageStatus,
                    Children, Password, HealthInsurance, PositionID,
                    DepartmentID, EmploymentType, Role
                ) VALUES (
                    '$empId', '$firstName', '$lastName', '$dob', '$gender',
                    '$address', '$contact', '$email', '$paymentInfo', '$marriageStatus',
                    '$children', '$password', '$healthIns', '$positionId',
                    '$departmentId', '$employmentType', '$role'
                );
            ";

            $insertResult = mysqli_query($conn, $insertSql);

            if ($insertResult) {
                // Insert ok -> quay lại trang employee
                mysqli_close($conn);
                header("Location: employee.php?added=1");
                exit();
            } else {
                $errors[] = "Không thể thêm nhân viên: " . mysqli_error($conn);
            }
        }

        if ($conn) {
            mysqli_close($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="max-w-4xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Add Employee</h2>
        <a href="employee.php" class="px-3 py-2 border rounded-md text-sm">Back to Employee List</a>
    </div>

    <?php
    if (!empty($errors)) {
        echo "<div class='mb-4 p-3 bg-red-100 text-red-700 rounded'>";
        foreach ($errors as $err) {
            echo "<p>".htmlspecialchars($err)."</p>";
        }
        echo "</div>";
    }
    ?>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="post" action="employee-form.php" class="space-y-4">
            <!-- Row 1 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm mb-1">Employee ID</label>
                    <input type="text" name="employee_id" class="border rounded-md w-full px-2 py-1" required>
                </div>
                <div>
                    <label class="block text-sm mb-1">First Name</label>
                    <input type="text" name="first_name" class="border rounded-md w-full px-2 py-1" required>
                </div>
                <div>
                    <label class="block text-sm mb-1">Last Name</label>
                    <input type="text" name="last_name" class="border rounded-md w-full px-2 py-1" required>
                </div>
            </div>

            <!-- Row 2 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm mb-1">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="border rounded-md w-full px-2 py-1">
                </div>
                <div>
                    <label class="block text-sm mb-1">Gender</label>
                    <select name="gender" class="border rounded-md w-full px-2 py-1">
                        <option value="">-- Select --</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Contact</label>
                    <input type="text" name="contact" class="border rounded-md w-full px-2 py-1" placeholder="Phone number">
                </div>
            </div>

            <!-- Row 3 -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1">Email</label>
                    <input type="email" name="email" class="border rounded-md w-full px-2 py-1">
                </div>
                <div>
                    <label class="block text-sm mb-1">Address</label>
                    <input type="text" name="address" class="border rounded-md w-full px-2 py-1">
                </div>
            </div>

            <!-- Row 4 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm mb-1">Payment Info</label>
                    <input type="text" name="payment_info" class="border rounded-md w-full px-2 py-1" placeholder="Bank / Account">
                </div>
                <div>
                    <label class="block text-sm mb-1">Marriage Status</label>
                    <input type="text" name="marriage_status" class="border rounded-md w-full px-2 py-1" placeholder="Single / Married...">
                </div>
                <div>
                    <label class="block text-sm mb-1">Children</label>
                    <input type="text" name="children" class="border rounded-md w-full px-2 py-1" placeholder="e.g. 0, 1, 2">
                </div>
            </div>

            <!-- Row 5 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm mb-1">Password</label>
                    <input type="text" name="password" class="border rounded-md w-full px-2 py-1">
                </div>
                <div>
                    <label class="block text-sm mb-1">Health Insurance</label>
                    <input type="text" name="health_insurance" class="border rounded-md w-full px-2 py-1">
                </div>
                <div>
                    <label class="block text-sm mb-1">Role</label>
                    <input type="text" name="role" class="border rounded-md w-full px-2 py-1" placeholder="e.g. Staff, Manager">
                </div>
            </div>

            <!-- Row 6 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm mb-1">Position ID</label>
                    <input type="text" name="position_id" class="border rounded-md w-full px-2 py-1">
                </div>
                <div>
                    <label class="block text-sm mb-1">Department ID</label>
                    <input type="text" name="department_id" class="border rounded-md w-full px-2 py-1">
                </div>
                <div>
                    <label class="block text-sm mb-1">Employment Type</label>
                    <input type="text" name="employment_type" class="border rounded-md w-full px-2 py-1" placeholder="Full-time / Part-time...">
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2 pt-4 border-t mt-4">
                <a href="employee.php" class="px-3 py-2 border rounded-md text-sm">Cancel</a>
                <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-md text-sm">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
