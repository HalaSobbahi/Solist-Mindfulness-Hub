<?php
session_start();
$conn = new mysqli("localhost", "root", "", "solist");
if ($conn->connect_error) die("DB Error");

$user_id = $_SESSION['user_id'];

$result = $conn->query("
    SELECT items.*, categories.slug AS category_slug
    FROM wishlist
    JOIN items ON wishlist.item_id = items.id
    JOIN categories ON items.category_id = categories.id
    WHERE wishlist.user_id = $user_id
    ORDER BY wishlist.created_at DESC
");
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Wishlist | Solist</title>
    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/wishlist.css">
    <link rel="icon" href="img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

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
<a href="#"><i class="fa fa-shopping-cart" style="margin-right: 15px;"></i>Cart</a>
<a href="#"><i class="fa fa-list" style="margin-right: 15px;"></i>Orders</a>
<a href="wishlist.php" class="active"><i class="fa fa-heart" style="margin-right: 15px;"></i>Wishlist</a>
<a href="#"><i class="fa fa-credit-card" style="margin-right: 15px;"></i>Payment methods</a>
<a href="profile.php"><i class="fa fa-user" style="margin-right: 15px;"></i>Profile</a>


</div>

<div class="overlay" id="overlay"></div>

<section class="wishlist-section">
    <h2 class="section-title">Your Wishlist</h2>


<div class="items-container">
    <?php if($result->num_rows > 0): ?>
        <?php while($item = $result->fetch_assoc()): ?>
            <div class="product-card" data-id="<?= $item['id'] ?>" data-category="<?= $item['category_slug'] ?>">

                <img src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>" class="product-img">

                <div class="wishlist-btn active"><i class="fa fa-heart"></i></div>

                <div class="product-overlay">
                    <h4><?= $item['name'] ?></h4>
                    <p class="price">$<?= $item['price'] ?></p>

                    <button class="add-cart"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>

<section class="wishlist-section">

    <div class="items-container">
        <?php if($result->num_rows > 0): ?>
            <?php while($item = $result->fetch_assoc()): ?>
                <div class="product-card" data-id="<?= $item['id'] ?>" data-category="<?= $item['category_slug'] ?>">
                    <img src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>" class="product-img">
                    <div class="wishlist-btn active"><i class="fa fa-heart"></i></div>
                    <div class="product-overlay">
                        <h4><?= $item['name'] ?></h4>
                        <p class="price">$<?= $item['price'] ?></p>
                        <button class="add-cart"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa fa-heart-broken empty-icon"></i>
                <h3 class="empty-title">Your wishlist is empty</h3>
                <a href="user.php" class="back-shop-btn">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</section>


    <?php endif; ?>
</div>

</section>

<script>
const menuBtn = document.getElementById('menuBtn');
const sideMenu = document.getElementById('sideMenu');
const overlay = document.getElementById('overlay');

menuBtn.addEventListener('click', () => {
    sideMenu.classList.add('active');
    overlay.classList.add('active');
});

overlay.addEventListener('click', () => {
    sideMenu.classList.remove('active');
    overlay.classList.remove('active');
});
</script>

<script>function checkEmptyWishlist() {
    const container = document.querySelector('.items-container');
    // Only count actual product cards
    const cards = container.querySelectorAll('.product-card');
    if (cards.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fa fa-heart-broken empty-icon"></i>
                <h3 class="empty-title">Your wishlist is empty</h3>
                <a href="user.php" class="back-shop-btn">Start Shopping</a>
            </div>
        `;
    }
}

// Attach click listeners to wishlist buttons dynamically
document.querySelectorAll('.wishlist-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const card = this.closest('.product-card');
        const itemId = card.getAttribute('data-id');

        fetch('wishlist_action.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ item_id: itemId })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'removed'){
                // Animate removal if you want (optional)
                card.remove();          // Remove card from DOM
                checkEmptyWishlist();   // Inject empty state if no cards left
            }
        });
    });
});


</script>

</body>
</html>
