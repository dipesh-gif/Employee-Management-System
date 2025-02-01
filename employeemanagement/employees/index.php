<?php
require_once 'conn.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['add_employee'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $salary = $_POST['salary'];
        $dob = $_POST['dob'];

        
        $sql = "INSERT INTO employee (name, email, address, salary, dob) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssds", $name, $email, $address, $salary, $dob);
        $stmt->execute();
    }

    if (isset($_POST['delete_employee'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM employee WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    if (isset($_POST['process_payroll'])) {
        $name = $_POST['name'];
        $employee_id = $_POST['employee_id'];
        $month = $_POST['month'];
        $year = $_POST['year'];
        $salary = $_POST['salary'];
        $bonus = $_POST['bonus'];
        $deductions = $_POST['deductions'];
        $total_salary = $salary + $bonus - $deductions;

        
        $sql_check = "SELECT * FROM payroll WHERE employee_id = ? AND month = ? AND year = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("isi", $employee_id, $month, $year);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows == 0) {
            $sql = "INSERT INTO payroll (name, employee_id, month, year, salary, bonus, deductions, total_salary) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sissdddd", $name, $employee_id, $month, $year, $salary, $bonus, $deductions, $total_salary);
            $stmt->execute();
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

  
    if (isset($_POST['add_attendance'])) {
        $employee_id = $_POST['employee_id'];
        $date = $_POST['date'];
        $check_in = $_POST['check_in'];
        $check_out = $_POST['check_out'];
        $status = $_POST['status'];

       
        $sql = "INSERT INTO attendance (employee_id, date, check_in, check_out, status) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $employee_id, $date, $check_in, $check_out, $status);
        $stmt->execute();
    }
}

$employees = $conn->query("SELECT * FROM employee");
$payrolls = $conn->query("SELECT * FROM payroll");
$attendance = $conn->query("SELECT * FROM attendance");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Employee Management System</h2>

  
    <h3 class="mt-4">Add Employee</h3>
    <form method="POST">
        <input type="hidden" name="add_employee">
        <div class="form-row">
            <div class="form-group col-md-4"><label>Name</label><input type="text" class="form-control" name="name" required></div>
            <div class="form-group col-md-4"><label>Email</label><input type="email" class="form-control" name="email" required></div>
            <div class="form-group col-md-4"><label>Address</label><input type="text" class="form-control" name="address" required></div>
            <div class="form-group col-md-4"><label>Salary</label><input type="number" class="form-control" name="salary" required></div>
            <div class="form-group col-md-4"><label>DOB</label><input type="date" class="form-control" name="dob" required></div>
        </div>
        <button type="submit" class="btn btn-primary">Add Employee</button>
    </form>

    
    <h3 class="mt-4">Employee Records</h3>
    <table class="table table-bordered">
        <thead><tr><th>ID</th><th>Email</th><th>Address</th><th>Salary</th><th>DOB</th><th>Actions</th></tr></thead>
        <tbody>
            <?php while ($row = $employees->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['salary']; ?></td>
                    <td><?php echo $row['dob']; ?></td>
                    <td>
                        <a href="edit_employee.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_employee">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    
    <h3 class="mt-4">Add Attendance</h3>
    <form method="POST">
        <input type="hidden" name="add_attendance">
        <div class="form-group"><label>Employee ID</label><input type="number" class="form-control" name="employee_id" required></div>
        <div class="form-group"><label>Date</label><input type="date" class="form-control" name="date" required></div>
        <div class="form-group"><label>Check-in</label><input type="time" class="form-control" name="check_in"></div>
        <div class="form-group"><label>Check-out</label><input type="time" class="form-control" name="check_out"></div>
        <div class="form-group"><label>Status</label>
            <select name="status" class="form-control" required>
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
                <option value="Leave">Leave</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Attendance</button>
    </form>

  
<h3 class="mt-4">Attendance Records</h3>

<table border="1">
    <tr>
        <th>Employee ID</th>
        <th>Date</th>
        <th>Check-in</th>
        <th>Check-out</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    <?php
    $conn = new mysqli("localhost", "root", "", "mydatabase");
    $sql = "SELECT * FROM attendance";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['employee_id']}</td>
            <td>{$row['date']}</td>
            <td>{$row['check_in']}</td>
            <td>{$row['check_out']}</td>
            <td>{$row['status']}</td>
            <td>
                <a href='edit_attendance.php?id={$row['id']}' class='btn btn-warning'>Edit</a>
                <a href='delete_attendance.php?id={$row['id']}' class='btn btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
            </td>
        </tr>";
    }
    ?>
</table>


<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }
    th {
        background-color: #f8f9fa;
    }
    .btn {
        padding: 5px 10px;
        border-radius: 5px;
        text-decoration: none;
        color: white;
        display: inline-block;
        margin: 2px;
    }
    .btn-warning {
        background-color: orange;
    }
    .btn-danger {
        background-color: red;
    }
    .btn:hover {
        opacity: 0.8;
    }
</style>


    


  
    <h3 class="mt-4">Process Payroll</h3>
    <form method="POST">
        <input type="hidden" name="process_payroll">
        <div class="form-group"><label>Name</label><input type="text" class="form-control" name="name" required></div>
        <div class="form-group"><label>Employee ID</label><input type="number" class="form-control" name="employee_id" required></div>
        <div class="form-group"><label>Month</label><input type="text" class="form-control" name="month" required></div>
        <div class="form-group"><label>Year</label><input type="number" class="form-control" name="year" required></div>
        <div class="form-group"><label>Salary</label><input type="number" class="form-control" name="salary" required></div>
        <div class="form-group"><label>Bonus</label><input type="number" class="form-control" name="bonus" required></div>
        <div class="form-group"><label>Deductions</label><input type="number" class="form-control" name="deductions" required></div>
        <button type="submit" class="btn btn-primary">Process Payroll</button>
    </form>

    
    <h3 class="mt-4">Payroll Records</h3>
    <table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Employee ID</th>
            <th>Month</th>
            <th>Year</th>
            <th>Salary</th>
            <th>Bonus</th>
            <th>Deductions</th>
            <th>Total Salary</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $payrolls->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['employee_id']); ?></td>
                <td><?php echo htmlspecialchars($row['month']); ?></td>
                <td><?php echo htmlspecialchars($row['year']); ?></td>
                <td><?php echo htmlspecialchars($row['salary']); ?></td>
                <td><?php echo htmlspecialchars($row['bonus']); ?></td>
                <td><?php echo htmlspecialchars($row['deductions']); ?></td>
                <td><?php echo htmlspecialchars($row['total_salary']); ?></td>
                <td>
                   
                    <a href="edit_payroll.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                    
                   
                    <form method="POST" action="delete_payroll.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>



<style>
    .btn-purple {
        background-color: #6f42c1;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
    }
    .btn-purple:hover {
        background-color: #5a32a3;
    }
</style>

</div>
</body>
</html>
