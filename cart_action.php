<?php
session_start();
$conn = new mysqli("localhost","root","","solist");

$data = json_decode(file_get_contents('php://input'), true);
$item_id = intval($data['item_id']);
$action = $data['action'];
$user_id = $_SESSION['user_id'] ?? 0;

if(!$user_id || !$item_id){
    echo json_encode(['status'=>'error']);
    exit;
}

// Get current quantity
$res = $conn->query("SELECT quantity FROM cart WHERE user_id=$user_id AND item_id=$item_id");
$row = $res->fetch_assoc();
$quantity = $row['quantity'] ?? 0;

if($action === 'add' || $action === 'plus'){
    if($quantity > 0){
        $conn->query("UPDATE cart SET quantity=quantity+1 WHERE user_id=$user_id AND item_id=$item_id");
    } else {
        $conn->query("INSERT INTO cart (user_id, item_id, quantity) VALUES ($user_id, $item_id, 1)");
    }
    $quantity++;
}

elseif($action === 'minus'){
    if($quantity > 1){
        $quantity--;
        $conn->query("UPDATE cart SET quantity=$quantity WHERE user_id=$user_id AND item_id=$item_id");
    } elseif($quantity == 1){
        // remove item completely if it reaches 0
        $conn->query("DELETE FROM cart WHERE user_id=$user_id AND item_id=$item_id");
        $quantity = 0;
    } else {
        // already 0 or not in cart → do nothing
        $quantity = 0;
    }
}

// Calculate total
$total_res = $conn->query("SELECT SUM(items.price*cart.quantity) as total 
                           FROM cart 
                           JOIN items ON cart.item_id=items.id 
                           WHERE user_id=$user_id");
$total_row = $total_res->fetch_assoc();
$total = $total_row['total'] ?? 0;

echo json_encode([
    'new_quantity' => $quantity,
    'total' => $total
]);
?>
