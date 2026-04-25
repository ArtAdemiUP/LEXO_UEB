function toggleUserMenu() {
    const dd = document.getElementById('userDropdown');
    if (dd) dd.classList.toggle('open');
}


document.addEventListener('click', function(e) {
    const menu = document.querySelector('.user-menu');
    if (menu && !menu.contains(e.target)) {
        const dd = document.getElementById('userDropdown');
        if (dd) dd.classList.remove('open');
    }
});


document.addEventListener('DOMContentLoaded', function() {
    const flash = document.querySelector('.flash');
    if (flash) {
        setTimeout(() => {
            flash.style.transition = 'opacity 0.4s';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 400);
        }, 4000);
    }
});


function confirmAction(msg) {
    return confirm(msg || 'A jeni i sigurt?');
}
