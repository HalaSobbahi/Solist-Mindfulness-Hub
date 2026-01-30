document.querySelectorAll('.product-card').forEach(card => {

    const addBtn = card.querySelector('.add-cart');
    const plusBtn = card.querySelector('.plus');
    const minusBtn = card.querySelector('.minus');
    const countSpan = card.querySelector('.count');
    const itemId = card.getAttribute('data-id');

    function send(action){
        return fetch('cart_action.php',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({item_id:itemId, action})
        }).then(res=>res.json());
    }

    function sync(qty){
        countSpan.innerText = qty;
        if(typeof loadCart === "function") loadCart();
    }

    addBtn.addEventListener('click',()=>{
        send('add').then(d=>sync(d.new_quantity));
    });

    plusBtn.addEventListener('click',()=>{
        send('plus').then(d=>sync(d.new_quantity));
    });

    minusBtn.addEventListener('click',()=>{
        send('minus').then(d=>sync(d.new_quantity));
    });

});
