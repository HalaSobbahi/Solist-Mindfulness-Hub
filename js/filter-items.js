const buttons = document.querySelectorAll('.category-buttons button');
const products = document.querySelectorAll('.product-card');

/* Category filter */
buttons.forEach(btn=>{
  btn.addEventListener('click',()=>{
    buttons.forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');

    const cat = btn.dataset.category;

    products.forEach(p=>{
      if(cat==="all" || p.dataset.category===cat){
        p.classList.remove('hidden');
      }else{
        p.classList.add('hidden');
      }
    });
  });
});

/* Wishlist toggle */
document.querySelectorAll('.wishlist').forEach(btn=>{
  btn.addEventListener('click',()=>{
    btn.classList.toggle('active');
  });
});

/* Add to cart */
document.querySelectorAll('.cart').forEach(btn=>{
  btn.addEventListener('click',()=>{
    btn.innerHTML = "✓ Added";
    btn.style.background = "#4CAF50";
  });
});
