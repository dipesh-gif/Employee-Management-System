<?php

$conn = new mysqli("localhost", "root", "", "mydatabase");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    die("Invalid request. No payroll record selected.");
}


$sql = "SELECT * FROM payroll WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Payroll record not found.");
}

$payroll = $result->fetch_assoc();
$stmt->close();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $employee_id = trim($_POST['employee_id'] ?? '');
    $month = trim($_POST['month'] ?? '');
    $year = intval($_POST['year'] ?? 0);
    $salary = floatval($_POST['salary'] ?? 0);
    $bonus = floatval($_POST['bonus'] ?? 0);
    $deductions = floatval($_POST['deductions'] ?? 0);
    $total_salary = $salary + $bonus - $deductions;

    if (empty($name) || empty($employee_id) || empty($month) || empty($year)) {
        echo "<div class='alert alert-danger'>All fields are required!</div>";
    } else {
        $update_sql = "UPDATE payroll SET name=?, employee_id=?, month=?, year=?, salary=?, bonus=?, deductions=?, total_salary=? WHERE id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sissddddd", $name, $employee_id, $month, $year, $salary, $bonus, $deductions, $total_salary, $id);

        if ($stmt->execute()) {
            header("Location: index.php?success=updated");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Error updating record: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Payroll Record</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

    <h2 class="mb-4">Edit Payroll Record</h2>
    
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($payroll['name']); ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Employee ID</label>
            <input type="text" name="employee_id" class="form-control" value="<?php echo htmlspecialchars($payroll['employee_id']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Month</label>
            <input type="text" name="month" class="form-control" value="<?php echo htmlspecialchars($payroll['month']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Year</label>
            <input type="number" name="year" class="form-control" value="<?php echo htmlspecialchars($payroll['year']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Salary</label>
            <input type="number" name="salary" class="form-control" value="<?php echo htmlspecialchars($payroll['salary']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Bonus</label>
            <input type="number" name="bonus" class="form-control" value="<?php echo htmlspecialchars($payroll['bonus']); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Deductions</label>
            <input type="number" name="deductions" class="form-control" value="<?php echo htmlspecialchars($payroll['deductions']); ?>">
        </div>

        <button type="submit" class="btn btn-primary">Update Payroll</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>

</body>
</html>
