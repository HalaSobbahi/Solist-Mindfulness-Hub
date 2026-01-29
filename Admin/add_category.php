<?php
$conn = new mysqli("localhost", "root", "", "solist");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if(isset($_POST['name'], $_POST['slug'])){
    $name = $conn->real_escape_string($_POST['name']);
    $slug = $conn->real_escape_string($_POST['slug']);

    $conn->query("INSERT INTO categories (name, slug) VALUES ('$name', '$slug')");
    echo "success";
}
?>
