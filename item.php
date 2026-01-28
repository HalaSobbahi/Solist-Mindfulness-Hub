<?php
session_start();
require_once 'session_check.php';

// Database connection
$conn = new mysqli("localhost", "root", "", "solist");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get logged-in user ID
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Get cart items
$cart_map = [];
$cart_result = $conn->query("SELECT item_id, quantity FROM cart WHERE user_id = $user_id");
while ($row = $cart_result->fetch_assoc()) {
    $cart_map[$row['item_id']] = $row['quantity'];
}

// Get wishlist items
$wishlist_ids = [];
$wishlist_result = $conn->query("SELECT item_id FROM wishlist WHERE user_id = $user_id");
while ($row = $wishlist_result->fetch_assoc()) {
    $wishlist_ids[] = $row['item_id'];
}

// Get item ID from URL
$item_id = $_GET['id'] ?? null;
if (!$item_id) {
    header("Location: user.php");
    exit;
}

// Fetch item
$stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    echo "Item not found";
    exit;
}

// Fetch all images for this item from item_images table
$images = [];
$img_stmt = $conn->prepare("SELECT image FROM item_images WHERE item_id = ?");
$img_stmt->bind_param("i", $item_id);
$img_stmt->execute();
$img_result = $img_stmt->get_result();
while ($row = $img_result->fetch_assoc()) {
    $images[] = $row['image'];
}

// Fallback if no images
if (empty($images)) {
    $images = [$item['image']]; // single image fallback
}

$main_image = $images[0]; // default main image
?>


<!-- HTML -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Solist Mindfulness Hub</title>
    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="css/item.css">
    <link rel="stylesheet" href="">
    <link rel="icon" href="img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>
<style>
    .hidden {
        display: none !important;
    }
</style>

<body>
    <!-- Header -->
    <header>
        <div class="menu-box" id="menuBtn">
            <i class="fa fa-bars menu-icon"></i>
        </div>
        <img src="img/logo.png" alt="" class="logo">
        <div class="logout-box">
            <a href="logout.php">
                <i class="fa-solid fa-arrow-right-from-bracket logout-icon"></i>
            </a>
        </div>
    </header>

        <!-- SideMenu -->
    <div class="side-menu" id="sideMenu">
        <div class="menu-logo">
            <img src="img/logo.png" alt="Logo">
        </div>
        <a href="user.php"><i class="fa fa-home" style="margin-right: 15px;"></i>Home</a>
        <a href="cart.php"><i class="fa fa-shopping-cart" style="margin-right: 15px;"></i>Cart</a>
        <div class="cart-panel" id="cartPanel"></div>
        <a href="#"><i class="fa fa-list" style="margin-right: 15px;"></i>Orders</a>
        <a href="wishlist.php">
            <i class="fa fa-heart" style="margin-right: 15px;"></i>Wishlist</a>
        <a href="#"><i class="fa fa-credit-card" style="margin-right: 15px;"></i>Payment methods</a>
        <a href="profile.php"><i class="fa fa-user" style="margin-right: 15px;"></i>Profile</a>

        <!-- Cart Header -->
        <div class="cart-header">
            <h3 style="color: 363535;">Your Cart</h3>
        </div>

        <div class="cart-items" id="cartItems"></div>

        <!-- Cart Footer -->
        <div class="cart-footer">
            <div class="cart-total">
                Total: $<span id="cartTotal">0</span>
            </div>
            <button class="checkout-btn">Checkout</button>
        </div>

    </div>
    
<div class="item-page">
    <div class="item-container">

<div class="item-image-gallery">
    <!-- Vertical Thumbnails -->
    <div class="thumbnails-slider">
        <?php foreach ($images as $img): ?>
            <img src="<?php echo $img; ?>" class="thumbnail <?php echo $img === $main_image ? 'active' : ''; ?>" alt="Thumbnail">
        <?php endforeach; ?>
    </div>

    <!-- Main Image with zoom/pan -->
    <div class="main-image-zoom">
        <img src="<?php echo $main_image; ?>" id="mainItemImage" alt="Main Image">
    </div>
</div>



        <!-- Right: Details -->
        <div class="item-details">
            <h1 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h1>
            <div class="item-price">$<?php echo number_format($item['price'], 2); ?></div>
            <div class="item-stock">
                <?php if ($item['stock'] > 0): ?>
                    <span class="in-stock">In Stock</span>
                <?php else: ?>
                    <span class="out-stock">Out of Stock</span>
                <?php endif; ?>
            </div>
            <div class="item-rating">⭐ <?php echo $item['rating'] ?? '4.5'; ?>/5</div>
            <div class="item-description">
                <h3>Details</h3>
                <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
            </div>
            <div class="item-actions">
                <button class="add-cart">Add to Cart</button>
                <button class="add-wishlist">♡ Wishlist</button>
            </div>
        </div>

    </div>
</div>


</div>
</div>










    <!-- Footer -->
    <footer id="Contact">

        <div class="footer-content">


            <div class="footer-logo">
                <img src="img/logo.png" alt="Solist Logo">
            </div>


            <div class="footer-links">
                <h5>My Account</h5>
                <a href="cart.php">Cart</a>
                <a href="">Orders</a>
                <a href="wishlist.php">Wishlist</a>
                <a href="#">Payment methods</a>
                <a href="profile.php">Profile</a>
            </div>


            <div class="footer-links">
                <h5>Connect With Us</h5>

                <div class="social-media-icons">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>

                <div class="support-box">
                    <p class="support-title">Get support from Solist</p>

                    <div class="support-item">
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:hello@solistmindfulness.com">hello@solistmindfulness.com</a>
                    </div>

                    <div class="support-item">
                        <i class="fas fa-phone-alt"></i>
                        <a href="tel:+96171447314">+961 71 447 314</a>
                    </div>

                    <p class="support-note">We will gladly assist you in a short time.</p>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="footer-text">© 2026 Solist Mindfulness Hub. All rights reserved.</p>
                <img src="img/visa-master.png" alt="Payment Methods" class="visa-master">
            </div>
            <button id="scrollTopBtn" aria-label="Scroll to top">
                <i class="fas fa-arrow-up"></i>
            </button>
    </footer>


    <!-- JS -->

    <script src="js/menu.js"></script>
    <script src="js/scroll.js"></script>
    <script src="js/category.js"></script>
    <script src="js/search.js"></script>
    <script src="js/wishlist.js"></script>
    <script src="js/cart.js"></script>
    <script src="js/cart-panel.js"></script>
    <script src="js/sort.js"></script>

</body>

</html>


<script>
const thumbnails = document.querySelectorAll('.thumbnail');
const mainImage = document.getElementById('mainItemImage');
const container = mainImage.parentElement;

let zoomLevel = 2;

// Change main image on thumbnail click
thumbnails.forEach(thumb => {
    thumb.addEventListener('click', () => {
        mainImage.src = thumb.src;

        // Highlight active thumbnail
        thumbnails.forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');

        // Reset zoom when image changes
        mainImage.style.transform = 'scale(1)';
        mainImage.style.transformOrigin = 'center center';
    });
});

// Zoom & Pan
container.addEventListener('mousemove', (e) => {
    const rect = container.getBoundingClientRect();
    const x = (e.clientX - rect.left) / rect.width;
    const y = (e.clientY - rect.top) / rect.height;

    mainImage.style.transformOrigin = `${x * 100}% ${y * 100}%`;
    mainImage.style.transform = `scale(${zoomLevel})`;
});

container.addEventListener('mouseleave', () => {
    mainImage.style.transform = 'scale(1)';
    mainImage.style.transformOrigin = 'center center';
});

</script>
