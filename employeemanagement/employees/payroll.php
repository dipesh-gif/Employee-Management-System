<?php
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee_id'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    $basic_salary = $_POST['salary'];
    $bonus = $_POST['bonus']; 
    $deductions = $_POST['deductions'];


    $total_salary = $salary + $bonus - $deductions;

  
    $sql = "INSERT INTO payroll (employee_id, month, year, salary, bonus, deductions, total_salary) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issdddd", $employee_id, $month, $year, $salary, $bonus, $deductions, $total_salary);

    if ($stmt->execute()) {
        echo "✅ Payroll processed successfully!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
