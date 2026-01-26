// Get the side menu and menu button
const sideMenu = document.getElementById('sideMenu');
const menuBtn = document.getElementById('menuBtn');

// Toggle menu on menu button click
menuBtn.addEventListener('click', () => {
    sideMenu.classList.toggle('active');
});

// Close menu when clicking outside
document.addEventListener('click', (e) => {
    // If click is NOT inside the sideMenu or menuBtn
    if (!sideMenu.contains(e.target) && !menuBtn.contains(e.target)) {
        sideMenu.classList.remove('active');
    }
});
