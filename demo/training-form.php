<?php
session_start();
require_once "settings.php";

// (tuỳ bạn, có thể bỏ check quyền nếu không cần)
if (!isset($_SESSION['employeeID'], $_SESSION['role']) ||
    !in_array($_SESSION['role'], ['CEO','HR','Manager'])) {
    header('Location: ../login/login.php');
    exit;
}

$errors = [];

// Kết nối DB
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("Database connection failure: " .
        htmlspecialchars(mysqli_connect_errno() . " - " . mysqli_connect_error()));
}

// Xác định đang ADD hay EDIT
$editId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$isEdit = $editId > 0;

$trainingId = "";
$course     = "";
$desc       = "";
$startDate  = "";
$endDate    = "";

// Nếu là edit → load dữ liệu lên form (request GET)
if ($isEdit && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql  = "SELECT * FROM training WHERE TrainingID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $editId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($res)) {
        $trainingId = $row['TrainingID'];
        $course     = $row['Course'];
        $desc       = $row['Description'];
        $startDate  = $row['StartDate'];
        $endDate    = $row['EndDate'];
    } else {
        $errors[] = "Training record not found.";
        $isEdit   = false;
        $editId   = 0;
    }
    mysqli_stmt_close($stmt);
}

// Xử lý submit (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isEdit     = isset($_POST['is_edit']) && $_POST['is_edit'] === '1';
    $trainingId = trim($_POST['training_id'] ?? '');
    $course     = trim($_POST['course'] ?? '');
    $desc       = trim($_POST['description'] ?? '');
    $startDate  = trim($_POST['start_date'] ?? '');
    $endDate    = trim($_POST['end_date'] ?? '');

    if ($course === "") {
        $errors[] = "Course name is required.";
    }

    if (empty($errors)) {
        // escape
        $course    = mysqli_real_escape_string($conn, $course);
        $desc      = mysqli_real_escape_string($conn, $desc);
        $startDate = mysqli_real_escape_string($conn, $startDate);
        $endDate   = mysqli_real_escape_string($conn, $endDate);

        if ($isEdit) {
            // UPDATE: TrainingID không đổi
            $id = intval($_POST['training_id_original']);
            $sql = "
                UPDATE training SET
                    Course      = '$course',
                    Description = '$desc',
                    StartDate   = '$startDate',
                    EndDate     = '$endDate'
                WHERE TrainingID = $id
            ";
        } else {
            // INSERT: yêu cầu TrainingID mới
            $trainingId = intval($trainingId);
            $sql = "
                INSERT INTO training (TrainingID, Course, Description, StartDate, EndDate)
                VALUES ($trainingId, '$course', '$desc', '$startDate', '$endDate')
            ";
        }

        $res = mysqli_query($conn, $sql);

        if ($res) {
            mysqli_close($conn);
            header("Location: training.php?saved=1");
            exit;
        } else {
            $errors[] = "Could not save training: " . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $isEdit ? 'Edit Training' : 'Add Training'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="max-w-3xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">
            <?php echo $isEdit ? 'Edit Training' : 'Add Training'; ?>
        </h2>
        <a href="training.php" class="px-3 py-2 border rounded-md text-sm">Back to Training List</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <?php foreach ($errors as $err): ?>
                <p><?php echo htmlspecialchars($err); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="post" action="training-form.php<?php echo $isEdit ? '?id='.urlencode($trainingId) : ''; ?>" class="space-y-4">
            <input type="hidden" name="is_edit" value="<?php echo $isEdit ? '1' : '0'; ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="training_id_original" value="<?php echo htmlspecialchars($trainingId); ?>">
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1">Training ID</label>
                    <input type="number" name="training_id"
                           class="border rounded-md w-full px-2 py-1"
                           value="<?php echo htmlspecialchars($trainingId); ?>"
                           <?php echo $isEdit ? 'readonly' : 'required'; ?>>
                </div>
                <div>
                    <label class="block text-sm mb-1">Course</label>
                    <input type="text" name="course"
                           class="border rounded-md w-full px-2 py-1"
                           value="<?php echo htmlspecialchars($course); ?>" required>
                </div>
            </div>

            <div>
                <label class="block text-sm mb-1">Description</label>
                <textarea name="description" class="border rounded-md w-full px-2 py-1" rows="3"><?php
                    echo htmlspecialchars($desc);
                ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1">Start Date</label>
                    <input type="date" name="start_date"
                           class="border rounded-md w-full px-2 py-1"
                           value="<?php echo htmlspecialchars($startDate); ?>">
                </div>
                <div>
                    <label class="block text-sm mb-1">End Date</label>
                    <input type="date" name="end_date"
                           class="border rounded-md w-full px-2 py-1"
                           value="<?php echo htmlspecialchars($endDate); ?>">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t mt-4">
                <a href="training.php" class="px-3 py-2 border rounded-md text-sm">Cancel</a>
                <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-md text-sm">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
