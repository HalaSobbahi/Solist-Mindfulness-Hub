
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
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ item_id: itemId, action: 'add' })
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
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ item_id: itemId, action: 'plus' })
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
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ item_id: itemId, action: 'minus' })
        })
        .then(res => res.json())
        .then(data => {
            updateCartPanel(data.new_quantity);
        });
    });

});

