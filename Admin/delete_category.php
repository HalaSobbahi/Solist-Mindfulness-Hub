<?php
$conn = new mysqli("localhost", "root", "", "solist");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if(isset($_POST['id'])){
    $id = intval($_POST['id']);
    $query = "DELETE FROM categories WHERE id = $id";
    if($conn->query($query)){
        echo "deleted";
    } else {
        echo "error";
    }
} else {
    echo "no_id";
}
?>
