
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
