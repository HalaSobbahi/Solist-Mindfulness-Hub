<?php
session_start();
require_once 'config/db.php'; // DB connection

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) { 
    echo json_encode(['success'=>false,'message'=>'Login required']); 
    exit; 
}

$data = json_decode(file_get_contents('php://input'), true);
$item_id = $data['item_id'];
$rating = $data['rating'];

// Insert or update rating
$stmt = $conn->prepare("INSERT INTO item_ratings (user_id, item_id, rating) VALUES (?, ?, ?) 
    ON DUPLICATE KEY UPDATE rating = VALUES(rating)");
$stmt->bind_param("iii", $user_id, $item_id, $rating);
$stmt->execute();

// Get updated average rating
$avg_result = $conn->query("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_votes FROM item_ratings WHERE item_id = $item_id");
$avg_data = $avg_result->fetch_assoc();
$avg_rating = round($avg_data['avg_rating'], 1);
$total_votes = $avg_data['total_votes'];

echo json_encode(['success'=>true,'avg_rating'=>$avg_rating,'total_votes'=>$total_votes]);
?>
