<?php
$conn = new mysqli("localhost", "root", "", "mydatabase");
$id = $_GET['id'];
$sql = "DELETE FROM attendance WHERE id='$id'";

if ($conn->query($sql)) {
    echo "Deleted successfully!";
    header("Location: index.php"); 
}
?>
