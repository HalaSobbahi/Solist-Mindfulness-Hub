document.addEventListener('DOMContentLoaded', () => {
    const cartPanel = document.getElementById('cartPanel');
    const cartOverlay = document.getElementById('cartOverlay');
    const cartBtn = document.getElementById('cartBtn'); // header button
    const closeCart = document.getElementById('closeCart');
    const cartItemsBox = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');

    /* OPEN CART */
    function openCart(){
        if(!cartPanel || !cartOverlay) return;
        cartPanel.classList.add('active');
        cartOverlay.classList.add('active');
        document.body.classList.add('cart-open');
        loadCart();
    }

    /* CLOSE CART */
    function closeCartFn(){
        cartPanel.classList.remove('active');
        cartOverlay.classList.remove('active');
        document.body.classList.remove('cart-open');
    }

    /* EVENTS */
    if(cartBtn) cartBtn.addEventListener('click', openCart);
    if(closeCart) closeCart.addEventListener('click', closeCartFn);
    if(cartOverlay) cartOverlay.addEventListener('click', closeCartFn);

    /* LOAD CART */
    function loadCart(){
        if(!cartItemsBox) return;
        fetch('get_cart.php')
        .then(res => res.json())
        .then(data => {
            cartItemsBox.innerHTML = '';
            data.items.forEach(item => {
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
        });
    }

    /* + / - buttons */
    if(cartItemsBox){
        cartItemsBox.addEventListener('click', e => {
            const btn = e.target;
            const item = btn.closest('.cart-item');
            if(!item) return;
            const id = item.getAttribute('data-id');

            if(btn.classList.contains('plus-btn')){
                cartAction(id,'plus');
            }
            if(btn.classList.contains('minus-btn')){
                cartAction(id,'minus');
            }
        });
    }

    function cartAction(id, action){
        fetch('cart_action.php', {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({item_id:id, action})
        })
        .then(res=>res.json())
        .then(data=>{
            updateProductCard(id, data.new_quantity);
            loadCart();
        });
    }

    function updateProductCard(id, qty){
        const card = document.querySelector(`.product-card[data-id='${id}']`);
        if(card){
            const countSpan = card.querySelector('.count');
            if(countSpan) countSpan.innerText = qty;
        }
    }
});
