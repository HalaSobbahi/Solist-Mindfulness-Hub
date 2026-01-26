
 // CART PANEL ELEMENTS
const cartPanel = document.getElementById('cartPanel');
const cartLink = document.querySelector('a[href="#"]:has(.fa-shopping-cart)');
const closeCart = document.getElementById('closeCart');
const cartItemsBox = document.getElementById('cartItems');
const cartTotal = document.getElementById('cartTotal');
const cartBadge = document.getElementById('cartBadge');

// OPEN CART
cartLink.addEventListener('click', e => {
    e.preventDefault();
    sideMenu.classList.remove('active');

    // overlay removed
    cartPanel.classList.add('active'); // show cart panel
    loadCart(); // load items
});


// LOAD CART ITEMS
function loadCart(){
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
    if(!cartItem) return;

    const id = cartItem.getAttribute('data-id');
    const qtySpan = cartItem.querySelector('.qty');
    let currentQty = parseInt(qtySpan.innerText);

    if(btn.classList.contains('plus-btn')){
        qtySpan.innerText = currentQty + 1;
        cartAction(id, 'plus');
    } 
    else if(btn.classList.contains('minus-btn')){
        if(currentQty > 1){
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


function cartAction(id, action){
    return fetch('cart_action.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({item_id: id, action: action})
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


function updateCartTotal(){
    let total = 0;
    let count = 0;

    cartItemsBox.querySelectorAll('.cart-item').forEach(item => {
        const price = parseFloat(item.querySelector('.price').innerText.replace('$',''));
        const qty = parseInt(item.querySelector('.qty').innerText);
        total += price * qty;
        count += qty;
    });

    cartTotal.innerText = total.toFixed(2);
    if(cartBadge) cartBadge.innerText = count;
}



function updateProductCard(itemId, quantity){
    const card = document.querySelector(`.product-card[data-id='${itemId}']`);
    if(card){
        const countSpan = card.querySelector('.count');
        countSpan.innerText = quantity;
    }
}



// CLEAR CART BUTTON
const clearCartBtn = document.getElementById('clearCartBtn');
if(clearCartBtn){
    clearCartBtn.addEventListener('click', () => {
        if(!confirm("Remove all items from cart?")) return;

        fetch('clear_cart.php')
        .then(res => res.json())
        .then(data => {
            if(data.status === 'cleared'){
                loadCart();
                cartBadge.innerText = 0;
            }
        });
    });
}


    // OPEN CART
cartLink.addEventListener('click', e => {
    e.preventDefault();
    sideMenu.classList.remove('active');

    overlay.classList.add('active'); // show overlay
    cartPanel.classList.add('active'); // show cart panel
    loadCart(); // load cart items
});


    addBtn.addEventListener('click', () => {
    fetch('cart_action.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ item_id: itemId, action: 'add' })
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
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ item_id: itemId, action: 'plus' })
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
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ item_id: itemId, action: 'minus' })
    })
    .then(res => res.json())
    .then(data => {
        countSpan.innerText = data.new_quantity;
        loadCart(); // <-- live update cart panel
    });
});


    document.addEventListener('click', (e) => {
    // If the cart is open
    if(cartPanel.classList.contains('active')){
        // If the click is NOT inside the cart or the cart icon
        if(!cartPanel.contains(e.target) && !cartLink.contains(e.target)){
            cartPanel.classList.remove('active'); // hide cart
            overlay.classList.remove('active');   // hide overlay if used
        }
    }
});


document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.get("openCart") === "1") {
        // simulate cart button click or open cart function
        if (typeof openCart === "function") {
            openCart(); // if you already have a function
        } else {
            // manual open (adjust IDs/classes if needed)
            const cartPanel = document.getElementById("cartPanel");
            const overlay = document.getElementById("overlay");

            if (cartPanel) cartPanel.classList.add("active");
            if (overlay) overlay.classList.add("active");

            document.body.classList.add("no-scroll"); // prevent scroll
        }
    }
});
