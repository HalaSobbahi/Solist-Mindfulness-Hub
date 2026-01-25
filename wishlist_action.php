<?php
session_start();
$conn = new mysqli("localhost", "root", "", "solist");
if ($conn->connect_error) die("DB Error");

$user_id = $_SESSION['user_id']; // user must be logged in

$data = json_decode(file_get_contents("php://input"), true);
$item_id = intval($data['item_id']);

// Check if exists
$check = $conn->prepare("SELECT id FROM wishlist WHERE user_id=? AND item_id=?");
$check->bind_param("ii", $user_id, $item_id);
$check->execute();
$check->store_result();

if($check->num_rows > 0){
    // Remove
    $del = $conn->prepare("DELETE FROM wishlist WHERE user_id=? AND item_id=?");
    $del->bind_param("ii", $user_id, $item_id);
    $del->execute();
    echo json_encode(["status" => "removed"]);
} else {
    // Add
    $add = $conn->prepare("INSERT INTO wishlist (user_id, item_id) VALUES (?,?)");
    $add->bind_param("ii", $user_id, $item_id);
    $add->execute();
    echo json_encode(["status" => "saved"]);
}
?>
