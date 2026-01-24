<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "solist";
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch categories
$cat_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");

// Fetch items
$item_result = $conn->query("SELECT items.*, categories.slug AS category_slug 
                             FROM items 
                             JOIN categories ON items.category_id = categories.id");
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solist Mindfulness Hub</title>
    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="css/style.css">
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




<!-- Categories -->
<div class="category-buttons">
    <button class="active" data-category="all">All</button>
    <?php while($cat = $cat_result->fetch_assoc()): ?>
        <button data-category="<?php echo $cat['slug']; ?>"><?php echo $cat['name']; ?></button>
    <?php endwhile; ?>
</div>

<!-- Product controls -->
<div class="product-controls">
    <input type="text" id="searchInput" placeholder="Search products..." aria-label="Search Products">
    <select id="sortSelect" aria-label="Sort Products">
        <option value="default">Sort by</option>
        <option value="name-asc">Name A → Z</option>
        <option value="name-desc">Name Z → A</option>
        <option value="price-asc">Price Low → High</option>
        <option value="price-desc">Price High → Low</option>
    </select>
</div>

<!-- Items container -->
<div class="items-container">
    <?php while($item = $item_result->fetch_assoc()): ?>
    <div class="product-card" data-category="<?php echo $item['category_slug']; ?>">

        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="product-img">

        <!-- Wishlist -->
        <div class="wishlist-btn"><i class="fa fa-heart"></i></div>

        <!-- Hover content -->
        <div class="product-overlay">
            <h4><?php echo $item['name']; ?></h4>
            <p class="price">$<?php echo $item['price']; ?></p>

            <div class="qty">
                <button class="minus">−</button>
                <span class="count">1</span>
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

</body>

</html>





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


<script>
    const scrollBtn = document.getElementById("scrollTopBtn");

    window.addEventListener("scroll", () => {
        if (window.scrollY > 300) {
            scrollBtn.style.display = "flex";
        } else {
            scrollBtn.style.display = "none";
        }
    });

    scrollBtn.addEventListener("click", () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });
</script>








<script>
    const buttons = document.querySelectorAll('.category-buttons button');
    const items = document.querySelectorAll('.items-container .product-card'); // Corrected selector

    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            buttons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const category = btn.getAttribute('data-category');

            items.forEach(item => {
                if (category === 'all' || item.getAttribute('data-category') === category) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        });
    });
</script>


<script>
    const searchInput = document.getElementById('searchInput');
    const productCards = document.querySelectorAll('.items-container .product-card');

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase().trim();

        productCards.forEach(card => {
            const category = card.getAttribute('data-category').toLowerCase();
            const productName = card.querySelector('h4')?.textContent.toLowerCase() || '';

            // Show card if query matches category or product name
            if (category.includes(query) || productName.includes(query)) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });

        // Optional: highlight the matched text in category name or product name
    });
</script>
