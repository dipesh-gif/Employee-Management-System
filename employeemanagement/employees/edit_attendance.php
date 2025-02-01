<?php
$conn = new mysqli("localhost", "root", "", "mydatabase");
$id = $_GET['id'];
$sql = "SELECT * FROM attendance WHERE id='$id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $status = $_POST['status'];

    $updateSQL = "UPDATE attendance SET check_in='$check_in', check_out='$check_out', status='$status' WHERE id='$id'";
    
    if ($conn->query($updateSQL)) {
        echo "Updated successfully!";
        header("Location: index.php"); 
    }
}
?>

<form method="post">
    Check-in: <input type="time" name="check_in" value="<?php echo $row['check_in']; ?>"><br>
    Check-out: <input type="time" name="check_out" value="<?php echo $row['check_out']; ?>"><br>
    Status:
    <select name="status">
        <option value="Present" <?php if ($row['status'] == 'Present') echo "selected"; ?>>Present</option>
        <option value="Absent" <?php if ($row['status'] == 'Absent') echo "selected"; ?>>Absent</option>
        <option value="Leave" <?php if ($row['status'] == 'Leave') echo "selected"; ?>>Leave</option>
    </select>
    <br>
    <button type="submit">Update</button>
</form>
