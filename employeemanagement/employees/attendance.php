<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  
    $employee_id = mysqli_real_escape_string($conn, $_POST['employee_id']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $check_in = mysqli_real_escape_string($conn, $_POST['check_in']);
    $check_out = mysqli_real_escape_string($conn, $_POST['check_out']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $name = mysqli_real_escape_string($conn, $_POST['name']); 

    $checkQuery = "SELECT id FROM employee WHERE id = '$employee_id'";
    $checkResult = mysqli_query($conn, $checkQuery);
    if (mysqli_num_rows($checkResult) == 0) {
        die("Error: Employee with ID $employee_id does not exist.");
    }

 
    $query = "INSERT INTO attendance (name, employee_id, date, check_in, check_out, status) 
              VALUES ('$name', '$employee_id', '$date', '$check_in', '$check_out', '$status')";

    if (mysqli_query($conn, $query)) {
        echo "Attendance record inserted successfully.";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
