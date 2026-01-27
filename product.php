<?php
session_start();
require_once 'session_check.php';
$conn = new mysqli("localhost", "root", "", "solist");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get logged-in user ID
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Get product ID from URL
$item_id = $_GET['id'] ?? null;
if (!$item_id) {
    header("Location: user.php");
    exit;
}

// Fetch product info
$stmt = $conn->prepare("
    SELECT items.*, categories.name AS category_name 
    FROM items 
    JOIN categories ON items.category_id = categories.id 
    WHERE items.id = ?
");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    echo "Item not found!";
    exit;
}

// Fetch images if multiple
$images_result = $conn->query("SELECT * FROM item_images WHERE item_id = $item_id");
$images = [];
while ($img = $images_result->fetch_assoc()) {
    $images[] = $img['image'];
}

// For clothes, fetch colors and sizes
$colors = [];
$sizes = [];
if (strtolower($item['category_name']) === 'clothes') {
    $color_result = $conn->query("SELECT DISTINCT color FROM item_variations WHERE item_id = $item_id");
    while ($c = $color_result->fetch_assoc()) $colors[] = $c['color'];

    $size_result = $conn->query("SELECT DISTINCT size FROM item_variations WHERE item_id = $item_id");
    while ($s = $size_result->fetch_assoc()) $sizes[] = $s['size'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo $item['name']; ?> - Solist Mindfulness Hub</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/product.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<a href="user.php">← Back to Products</a>

<div class="product-detail-container">

    <!-- Images -->
    <div class="product-images">
        <?php foreach ($images as $img): ?>
            <img src="<?php echo $img; ?>" alt="<?php echo $item['name']; ?>">
        <?php endforeach; ?>
    </div>

    <!-- Info -->
    <div class="product-info">
        <h1><?php echo $item['name']; ?></h1>
        <p><?php echo $item['description']; ?></p>
        <p>Price: $<?php echo $item['price']; ?></p>
        <p>Stock: <?php echo ($item['stock'] > 0) ? "In Stock" : "Out of Stock"; ?></p>

        <?php if (!empty($colors)): ?>
        <div class="product-colors">
            <label for="colorSelect">Color:</label>
            <select id="colorSelect">
                <?php foreach ($colors as $color): ?>
                    <option value="<?php echo $color; ?>"><?php echo ucfirst($color); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <?php if (!empty($sizes)): ?>
        <div class="product-sizes">
            <label for="sizeSelect">Size:</label>
            <select id="sizeSelect">
                <?php foreach ($sizes as $size): ?>
                    <option value="<?php echo $size; ?>"><?php echo strtoupper($size); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <div class="qty">
            <button class="minus">−</button>
            <span class="count">1</span>
            <button class="plus">+</button>
        </div>

        <button class="add-cart"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
    </div>

</div>

<script src="js/product.js"></script>
</body>
</html>
