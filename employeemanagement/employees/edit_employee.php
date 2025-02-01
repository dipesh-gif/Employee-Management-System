<?php
require_once 'conn.php'; 


if (isset($_GET['id'])) {
    $id = $_GET['id'];

  
    $sql = "SELECT * FROM employee WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

   
    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
    } else {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id']; 
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $salary = $_POST['salary'];
    $dob = $_POST['dob'];

    echo "Received DOB: " . $dob . "<br>";

    $dobFormatted = DateTime::createFromFormat('Y-m-d', $dob);
    if ($dobFormatted && $dobFormatted->format('Y-m-d') === $dob) {
        
        $sql = "UPDATE employee SET name = ?, email = ?, address = ?, salary = ?, dob = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdsi", $name, $email, $address, $salary, $dob, $id);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            echo "Error updating record: " . $stmt->error;
        }
    } else {
        echo "Invalid date format.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Employee</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Employee Details</h2>

    <form method="POST">
       
        <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">

        <div class="form-row">
            <div class="form-group col-md-4">
                <label>Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($employee['name']); ?>" required>
            </div>
            <div class="form-group col-md-4">
                <label>Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required>
            </div>
            <div class="form-group col-md-4">
                <label>Address</label>
                <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($employee['address']); ?>" required>
            </div>
            <div class="form-group col-md-4">
                <label>Salary</label>
                <input type="number" class="form-control" name="salary" value="<?php echo $employee['salary']; ?>" required>
            </div>
            <div class="form-group col-md-4">
                <label>DOB</label>
                <input type="date" class="form-control" name="dob" value="<?php echo $employee['dob']; ?>" required>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Update Employee</button>
    </form>
    
</div>
</body>
</html>