<?php
session_start();
$conn = new mysqli("localhost", "root", "", "solist");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: user.php");
    exit;
}

$stmt = $conn->prepare("SELECT items.*, categories.name AS category_name 
                        FROM items 
                        JOIN categories ON items.category_id = categories.id
                        WHERE items.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    echo "Item not found";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $item['name'] ?> | Solist</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="item-details-page">

    <div class="item-image">
        <img src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>">
    </div>

    <div class="item-info">
        <h1><?= $item['name'] ?></h1>
        <p class="category"><?= $item['category_name'] ?></p>
        <p class="price">$<?= $item['price'] ?></p>

        <p class="description">
            <?= $item['description'] ?? 'No description available.' ?>
        </p>

        <button class="add-cart">Add to Cart</button>
    </div>

</div>

</body>
</html>
