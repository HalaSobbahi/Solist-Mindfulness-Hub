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

$CLOTHES_ID = 2; // clothes category id


$sizes = [];
$colors = [];

if ((int)$item['category_id'] === $CLOTHES_ID) {

    // Sizes
    $size_stmt = $conn->prepare("SELECT size FROM item_sizes WHERE item_id = ?");
    $size_stmt->bind_param("i", $item_id);
    $size_stmt->execute();
    $size_res = $size_stmt->get_result();
    while ($row = $size_res->fetch_assoc()) {
        $sizes[] = $row['size'];
    }

    // Colors
    $color_stmt = $conn->prepare("SELECT color, color_code FROM item_colors WHERE item_id = ?");
    $color_stmt->bind_param("i", $item_id);
    $color_stmt->execute();
    $color_res = $color_stmt->get_result();
    while ($row = $color_res->fetch_assoc()) {
        $colors[] = $row;
    }
}


// Fetch average rating and total votes
$stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_votes FROM item_ratings WHERE item_id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$rating_data = $result->fetch_assoc();

$avg_rating = round($rating_data['avg_rating'], 1) ?? 0;
$total_votes = $rating_data['total_votes'] ?? 0;

// Optional: get logged-in user's rating if logged in
$user_rating = 0;
if ($user_id) {
    $stmt2 = $conn->prepare("SELECT rating FROM item_ratings WHERE item_id = ? AND user_id = ?");
    $stmt2->bind_param("ii", $item_id, $user_id);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    if ($row = $res2->fetch_assoc()) $user_rating = $row['rating'];
}


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
                    <?php if ($item['is_in_stock'] == 1): ?>
                        <span class="in-stock">
                            <i class="fa-solid fa-circle-check"></i> In Stock
                        </span>
                    <?php else: ?>
                        <span class="out-stock">
                            <i class="fa-solid fa-circle-xmark"></i> Out of Stock
                        </span>
                    <?php endif; ?>
                </div>

                <div class="item-rating">
                    <div id="starRating" class="stars" data-user-rating="<?php echo $user_rating; ?>" data-avg-rating="<?php echo $avg_rating; ?>">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fa fa-star" data-value="<?php echo $i; ?>"></i>
                        <?php endfor; ?>
                        <span id="ratingInfo">(<?php echo $avg_rating; ?> / 5, <?php echo $total_votes; ?> votes)</span>
                    </div>
                </div>

                <div class="item-description">
                    <h3>Details</h3>
                    <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                </div>


                <?php if ((int)$item['category_id'] === $CLOTHES_ID): ?>

                    <div class="item-variants">

                        <!-- Sizes -->
                        <?php if (!empty($sizes)): ?>
                            <div class="variant-group">
                                <h4>Size</h4>
                                <div class="size-options">
                                    <?php foreach ($sizes as $s): ?>
                                        <button class="size-btn" data-size="<?= $s ?>"><?= $s ?></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Colors -->
                        <?php if (!empty($colors)): ?>
                            <div class="variant-group">
                                <h4>Color</h4>
                                <div class="color-options">
                                    <?php foreach ($colors as $c): ?>
                                        <span class="color-dot"
                                            data-color="<?= $c['color'] ?>"
                                            style="background: <?= $c['color_code'] ?>"></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                    <div id="variantMessage" class="variant-message">
                        Please choose size and color
                    </div>

                <?php endif; ?>
<div class="quantity-selector">
    <button type="button" class="qty-btn" id="decreaseQty">-</button>
    <input type="text" id="itemQty" value="1" readonly>
    <button type="button" class="qty-btn" id="increaseQty">+</button>
</div>

                <button class="add-cart"
                    <?php echo ((int)$item['category_id'] === 2 ? 'disabled' : ''); ?>>
                    Add to Cart
                </button>


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

<script>
    const stars = document.querySelectorAll('#starRating i');
    const ratingInfo = document.getElementById('ratingInfo');
    const starContainer = document.getElementById('starRating');

    const avgRating = parseFloat(starContainer.dataset.avgRating);
    const userRating = parseFloat(starContainer.dataset.userRating);

    // Function to render stars (supports fractions)
    function renderStars(rating) {
        stars.forEach(star => {
            const val = parseInt(star.dataset.value);
            if (val <= Math.floor(rating)) {
                star.classList.add('filled');
                star.classList.remove('half');
            } else if (val - 1 < rating && rating < val) {
                star.classList.add('half');
                star.classList.remove('filled');
            } else {
                star.classList.remove('filled', 'half');
            }
        });
    }

    // Render on page load with avg rating
    renderStars(userRating || avgRating);

    // Optional: allow logged-in users to rate
    stars.forEach(star => {
        star.addEventListener('mouseenter', () => {
            renderStars(parseInt(star.dataset.value));
        });
        star.addEventListener('mouseleave', () => {
            renderStars(userRating || avgRating);
        });
        star.addEventListener('click', () => {
            const val = parseInt(star.dataset.value);

            if (parseInt(starContainer.dataset.userRating) === val) {
                val = 0; // unset rating
            }

            fetch('rate_item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        item_id: <?php echo $item_id; ?>,
                        rating: val
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        renderStars(val);
                        ratingInfo.textContent = `(${data.avg_rating} / 5, ${data.total_votes} votes)`;
                    } else {
                        alert('Error saving rating');
                    }
                });
        });
    });
</script>

<script>
    let selectedSize = null;
    let selectedColor = null;

    document.querySelectorAll('.size-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedSize = btn.dataset.size;
        });
    });

    document.querySelectorAll('.color-dot').forEach(dot => {
        dot.addEventListener('click', () => {
            document.querySelectorAll('.color-dot').forEach(d => d.classList.remove('active'));
            dot.classList.add('active');
            selectedColor = dot.dataset.color;
        });
    });


    document.querySelector('.add-cart').addEventListener('click', () => {
        if ("<?= $item['category'] ?>" === "clothes") {
            if (!selectedSize || !selectedColor) {
                alert("Please select size and color");
                return;
            }
        }

        // continue add-to-cart logic here
    });
</script>








<script>
    let selectedSize = null;
    let selectedColor = null;

    const isClothes = <?= ((int)$item['category_id'] === 2 ? 'true' : 'false') ?>;
    const addCartBtn = document.querySelector('.add-cart');
    const msgBox = document.getElementById('variantMessage');

    function updateCartState() {
        if (isClothes) {
            if (selectedSize && selectedColor) {
                addCartBtn.disabled = false;
                addCartBtn.classList.remove('disabled-btn');
                msgBox.classList.remove('show');
            } else {
                addCartBtn.disabled = true;
                addCartBtn.classList.add('disabled-btn');
                msgBox.classList.add('show');
            }
        }
    }

    // Sizes (toggle)
    document.querySelectorAll('.size-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const size = btn.dataset.size;

            if (selectedSize === size) {
                // clicked same size => remove selection
                selectedSize = null;
                btn.classList.remove('active');
            } else {
                selectedSize = size;
                document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            }

            updateCartState();
        });
    });

    // Colors (toggle)
    document.querySelectorAll('.color-dot').forEach(dot => {
        dot.addEventListener('click', () => {
            const color = dot.dataset.color;

            if (selectedColor === color) {
                // clicked same color => remove selection
                selectedColor = null;
                dot.classList.remove('active');
            } else {
                selectedColor = color;
                document.querySelectorAll('.color-dot').forEach(d => d.classList.remove('active'));
                dot.classList.add('active');
            }

            updateCartState();
        });
    });

    // Init
    updateCartState();
</script>

<script>
    const decreaseBtn = document.getElementById('decreaseQty');
const increaseBtn = document.getElementById('increaseQty');
const qtyInput = document.getElementById('itemQty');

let qty = 1; // default

decreaseBtn.addEventListener('click', () => {
    if (qty > 1) {
        qty--;
        qtyInput.value = qty;
    }
});

increaseBtn.addEventListener('click', () => {
    qty++;
    qtyInput.value = qty;
});

</script>

<script>
    addCartBtn.addEventListener('click', () => {
    if (isClothes) {
        if (!selectedSize || !selectedColor) {
            alert("Please select size and color");
            return;
        }
    }

    const selectedQty = parseInt(qtyInput.value);

    // Now send item_id, quantity, size, color to your add-to-cart logic
    console.log("Add to cart:", {
        item_id: <?= $item_id ?>,
        quantity: selectedQty,
        size: selectedSize,
        color: selectedColor
    });

    // Example: fetch('add_to_cart.php', { ... })
});

</script>