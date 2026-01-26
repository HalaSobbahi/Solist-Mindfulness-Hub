<?php
session_start();
$conn = new mysqli("localhost", "root", "", "solist");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get logged-in user ID
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Get cart items for this user
$cart_result = $conn->query("SELECT item_id, quantity FROM cart WHERE user_id = $user_id");

$cart_map = []; // item_id => quantity
while ($row = $cart_result->fetch_assoc()) {
    $cart_map[$row['item_id']] = $row['quantity'];
}



// Get all wishlist items for this user
$wishlist_result = $conn->query("SELECT item_id FROM wishlist WHERE user_id = $user_id");
$wishlist_ids = [];
while ($row = $wishlist_result->fetch_assoc()) {
    $wishlist_ids[] = $row['item_id'];
}


$sort = $_GET['sort'] ?? 'default';

$orderBy = "items.id DESC"; 

switch ($sort) {
    case 'price-asc':
        $orderBy = "items.price ASC";
        break;

    case 'price-desc':
        $orderBy = "items.price DESC";
        break;

    case 'latest':
        $orderBy = "items.id DESC"; 
        break;

    case 'oldest':
        $orderBy = "items.id ASC";
        break;
}


$cat_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");


$item_result = $conn->query("
    SELECT items.*, categories.slug AS category_slug 
    FROM items 
    JOIN categories ON items.category_id = categories.id
    ORDER BY $orderBy
");




?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solist Mindfulness Hub</title>
    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cart.css">
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


<div class="side-menu" id="sideMenu">
    <div class="menu-logo">
        <img src="img/logo.png" alt="Logo">
    </div>
<a href="user.php"><i class="fa fa-home" style="margin-right: 15px;"></i>Home</a>

<a href="cart.php"><i class="fa fa-shopping-cart" style="margin-right: 15px;"></i>Cart</a>


<div class="cart-panel" id="cartPanel">
    <div class="cart-header">
        <h3 style="color: 363535;">Your Cart</h3>
    </div>

    <div class="cart-items" id="cartItems">
        <!-- dynamic -->
    </div>

    <div class="cart-footer">
        <div class="cart-total">
            Total: $<span id="cartTotal">0</span>
        </div>
        <button class="checkout-btn">Checkout</button>
    </div>
    
</div>
    <a href="#"><i class="fa fa-list" style="margin-right: 15px;"></i>Orders</a>
   <a href="wishlist.php">
    <i class="fa fa-heart" style="margin-right: 15px;"></i>Wishlist
</a>

    <a href="#"><i class="fa fa-credit-card" style="margin-right: 15px;"></i>Payment methods</a>
    <a href="profile.php"><i class="fa fa-user" style="margin-right: 15px;"></i>Profile</a>
</div>






<div class="category-buttons">
    <button class="active" data-category="all">All</button>
    <?php while($cat = $cat_result->fetch_assoc()): ?>
        <button data-category="<?php echo $cat['slug']; ?>"><?php echo $cat['name']; ?></button>
    <?php endwhile; ?>
</div>


<div class="product-controls">
    <input type="text" id="searchInput" placeholder="Search products..." aria-label="Search Products">
 <select id="sortSelect">
    <option value="default">Sort by</option>
    <option value="latest">Latest </option>
    <option value="oldest">Oldest</option>
    <option value="price-asc">Price Low → High</option>
    <option value="price-desc">Price High → Low</option>
</select>

</div>


<div class="items-container">
    <?php while($item = $item_result->fetch_assoc()): ?>
  <div class="product-card" 
     data-id="<?php echo $item['id']; ?>" 
     data-category="<?php echo $item['category_slug']; ?>">


        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="product-img">

    
        <div class="wishlist-btn <?= in_array($item['id'], $wishlist_ids) ? 'active' : '' ?>">
    <i class="fa fa-heart"></i>
</div>


        
        <div class="product-overlay">
            <h4><?php echo $item['name']; ?></h4>
            <p class="price">$<?php echo $item['price']; ?></p>

            <div class="qty">
                <button class="minus">−</button>
<span class="count">
    <?= isset($cart_map[$item['id']]) ? $cart_map[$item['id']] : 0 ?>
</span>

                <button class="plus">+</button>
            </div>

            <button class="add-cart"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
        </div>

    </div>
    <?php endwhile; ?>
</div>


    <footer id="Contact">

        <div class="footer-content">


            <div class="footer-logo">
                <img src="img/logo.png" alt="Solist Logo">
            </div>


            <div class="footer-links">
                <h5>My Account</h5>
                <a href="#">Cart</a>
                <a href="#">Orders</a>
                <a href="#">Wishlist</a>
                <a href="#">Payment methods</a>
                <a href="#">Profile</a>
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









