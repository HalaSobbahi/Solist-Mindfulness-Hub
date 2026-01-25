<?php
session_start();
$conn = new mysqli("localhost", "root", "", "solist");
if ($conn->connect_error) die("DB Error");

$user_id = $_SESSION['user_id'] ?? null;
if(!$user_id){
    echo json_encode([]);
    exit;
}

$q = $conn->query("
    SELECT cart.item_id, cart.quantity, 
           items.name, items.price, items.image
    FROM cart
    JOIN items ON cart.item_id = items.id
    WHERE cart.user_id = $user_id
");

$items = [];
$total = 0;

while($row = $q->fetch_assoc()){
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $items[] = $row;
}

echo json_encode([
    'items' => $items,
    'total' => $total
]);
