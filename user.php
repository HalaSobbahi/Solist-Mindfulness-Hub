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

    <a href="#"><i class="fa fa-shopping-cart" style="margin-right: 15px;"></i>Cart</a>
    <a href="#"><i class="fa fa-list" style="margin-right: 15px;"></i>Orders</a>
   <a href="wishlist.php">
    <i class="fa fa-heart" style="margin-right: 15px;"></i>Wishlist
</a>

    <a href="#"><i class="fa fa-credit-card" style="margin-right: 15px;"></i>Payment methods</a>
    <a href="#"><i class="fa fa-user" style="margin-right: 15px;"></i>Profile</a>
</div>


<div class="overlay" id="overlay"></div>




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

    
            if (category.includes(query) || productName.includes(query)) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });

    });
</script>

<script>
const sortSelect = document.getElementById('sortSelect');
const itemsContainer = document.querySelector('.items-container');

sortSelect.addEventListener('change', function () {
    const value = this.value;
    let cards = Array.from(document.querySelectorAll('.product-card'));


    cards = cards.filter(card => !card.classList.contains('hidden'));

    cards.sort((a, b) => {
        const priceA = parseFloat(a.querySelector('.price').innerText.replace('$', ''));
        const priceB = parseFloat(b.querySelector('.price').innerText.replace('$', ''));
        const idA = parseInt(a.getAttribute('data-id'));
        const idB = parseInt(b.getAttribute('data-id'));

        switch(value){
            case 'price-asc':
                return priceA - priceB;
            case 'price-desc':
                return priceB - priceA;
            case 'latest':
                return idB - idA;
            case 'oldest':
                return idA - idB;
            default:
                return 0;
        }
    });

 
    cards.forEach(card => itemsContainer.appendChild(card));
});
</script>
<script>
document.querySelectorAll('.wishlist-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.stopPropagation();

        const card = this.closest('.product-card');
        const itemId = card.getAttribute('data-id');

        this.classList.toggle('active');

        fetch('wishlist_action.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ item_id: itemId })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status !== 'saved' && data.status !== 'removed'){
                alert('Wishlist error');
            }
        });
    });
});
</script>
