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

    <div class="cart-overlay" id="cartOverlay"></div>


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
                <h3>Your Cart</h3>
                <button class="cart-close" id="closeCart">✕</button>
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
        <?php while ($cat = $cat_result->fetch_assoc()): ?>
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
        <?php while ($item = $item_result->fetch_assoc()): ?>
            <a href="item.php?id=<?php echo $item['id']; ?>" class="product-link">
    <div class="product-card" data-id="<?php echo $item['id']; ?>"

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

    sortSelect.addEventListener('change', function() {
        const value = this.value;
        let cards = Array.from(document.querySelectorAll('.product-card'));


        cards = cards.filter(card => !card.classList.contains('hidden'));

        cards.sort((a, b) => {
            const priceA = parseFloat(a.querySelector('.price').innerText.replace('$', ''));
            const priceB = parseFloat(b.querySelector('.price').innerText.replace('$', ''));
            const idA = parseInt(a.getAttribute('data-id'));
            const idB = parseInt(b.getAttribute('data-id'));

            switch (value) {
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
        btn.addEventListener('click', function(e) {
            e.stopPropagation();

            const card = this.closest('.product-card');
            const itemId = card.getAttribute('data-id');

            this.classList.toggle('active');

            fetch('wishlist_action.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        item_id: itemId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status !== 'saved' && data.status !== 'removed') {
                        alert('Wishlist error');
                    }
                });
        });
    });
</script>


<script>
    document.querySelectorAll('.product-card').forEach(card => {

        const addBtn = card.querySelector('.add-cart');
        const plusBtn = card.querySelector('.plus');
        const minusBtn = card.querySelector('.minus');
        const countSpan = card.querySelector('.count');
        const itemId = card.getAttribute('data-id');

        function updateCartPanel(newQuantity) {
            countSpan.innerText = newQuantity; // update product card
            loadCart(); // update cart panel live
        }

        // ADD TO CART
        addBtn.addEventListener('click', () => {
            fetch('cart_action.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        item_id: itemId,
                        action: 'add'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    updateCartPanel(data.new_quantity);
                });
        });

        // PLUS
        plusBtn.addEventListener('click', () => {
            fetch('cart_action.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        item_id: itemId,
                        action: 'plus'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    updateCartPanel(data.new_quantity);
                });
        });

        // MINUS
        minusBtn.addEventListener('click', () => {
            fetch('cart_action.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        item_id: itemId,
                        action: 'minus'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    updateCartPanel(data.new_quantity);
                });
        });

    });
</script>



<script>
    const cartItemsBox = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const cartBadge = document.getElementById('cartBadge'); // optional if exists
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.location.pathname.includes('cart.php')) {
            openCart();
            loadCart();
        }
    });
</script>


<script>
    function loadCart() {
        fetch('get_cart.php')
            .then(res => res.json())
            .then(data => {
                cartItemsBox.innerHTML = '';
                let count = 0;

                data.items.forEach(item => {
                    count += item.quantity;

                    cartItemsBox.innerHTML += `
                <div class="cart-item" data-id="${item.item_id}">
                    <img src="${item.image}">
                    <div class="cart-info">
                        <h4>${item.name}</h4>
                        <div class="price">$${item.price}</div>
                        <div class="cart-qty">
                            <button class="minus-btn">−</button>
                            <span class="qty">${item.quantity}</span>
                            <button class="plus-btn">+</button>
                        </div>
                    </div>
                </div>
            `;
                });

                cartTotal.innerText = data.total.toFixed(2);
                cartBadge.innerText = count;
            });
    }


    // EVENT DELEGATION FOR + / - BUTTONS
    cartItemsBox.addEventListener('click', e => {
        const btn = e.target;
        const cartItem = btn.closest('.cart-item');
        if (!cartItem) return;

        const id = cartItem.getAttribute('data-id');
        const qtySpan = cartItem.querySelector('.qty');
        let currentQty = parseInt(qtySpan.innerText);

        if (btn.classList.contains('plus-btn')) {
            qtySpan.innerText = currentQty + 1;
            cartAction(id, 'plus');
        } else if (btn.classList.contains('minus-btn')) {
            if (currentQty > 1) {
                qtySpan.innerText = currentQty - 1;
                cartAction(id, 'minus');
            } else {
                // Quantity will reach 0 → remove item from DOM
                cartAction(id, 'minus').then(() => {
                    cartItem.remove(); // remove from panel
                    updateCartTotal(); // update total
                });
            }
        }
    });


    function cartAction(id, action) {
        return fetch('cart_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    item_id: id,
                    action: action
                })
            })
            .then(res => res.json())
            .then(data => {
                // 1. Update product card quantity
                updateProductCard(id, data.new_quantity);

                // 2. Refresh the cart panel live
                loadCart(); // <-- THIS ensures the panel always reflects current cart

                return data;
            });
    }


    function updateCartTotal() {
        let total = 0;
        let count = 0;

        cartItemsBox.querySelectorAll('.cart-item').forEach(item => {
            const price = parseFloat(item.querySelector('.price').innerText.replace('$', ''));
            const qty = parseInt(item.querySelector('.qty').innerText);
            total += price * qty;
            count += qty;
        });

        cartTotal.innerText = total.toFixed(2);
        if (cartBadge) cartBadge.innerText = count;
    }



    function updateProductCard(itemId, quantity) {
        const card = document.querySelector(`.product-card[data-id='${itemId}']`);
        if (card) {
            const countSpan = card.querySelector('.count');
            countSpan.innerText = quantity;
        }
    }



    // CLEAR CART BUTTON
    const clearCartBtn = document.getElementById('clearCartBtn');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', () => {
            if (!confirm("Remove all items from cart?")) return;

            fetch('clear_cart.php')
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'cleared') {
                        loadCart();
                        cartBadge.innerText = 0;
                    }
                });
        });
    }
</script>


<script>
    addBtn.addEventListener('click', () => {
        fetch('cart_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    item_id: itemId,
                    action: 'add'
                })
            })
            .then(res => res.json())
            .then(data => {
                countSpan.innerText = data.new_quantity;
                loadCart(); // <-- live update cart panel
            });
    });


    plusBtn.addEventListener('click', () => {
        fetch('cart_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    item_id: itemId,
                    action: 'plus'
                })
            })
            .then(res => res.json())
            .then(data => {
                countSpan.innerText = data.new_quantity;
                loadCart(); // <-- live update cart panel
            });
    });

    minusBtn.addEventListener('click', () => {
        fetch('cart_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    item_id: itemId,
                    action: 'minus'
                })
            })
            .then(res => res.json())
            .then(data => {
                countSpan.innerText = data.new_quantity;
                loadCart(); // <-- live update cart panel
            });
    });
</script>


<script>
    const cartPanel = document.getElementById('cartPanel');
    const cartOverlay = document.getElementById('cartOverlay');
    const closeCart = document.getElementById('closeCart');

    /* OPEN CART */
    function openCart() {
        cartPanel.classList.add('active');
        cartOverlay.classList.add('active');
        document.body.classList.add('cart-open');
    }

    /* CLOSE CART */
    function closeCartFn() {
        cartPanel.classList.remove('active');
        cartOverlay.classList.remove('active');
        document.body.classList.remove('cart-open');

        // redirect after close
        window.location.href = "user.php";
    }

    /* Events */
    if (closeCart) {
        closeCart.addEventListener('click', closeCartFn);
    }

    cartOverlay.addEventListener('click', closeCartFn);
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        openCart();
    });
</script>