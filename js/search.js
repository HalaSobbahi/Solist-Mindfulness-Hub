
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
