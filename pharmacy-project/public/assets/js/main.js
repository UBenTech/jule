// Wait for the DOM to be fully loaded before running scripts.
document.addEventListener('DOMContentLoaded', function () {

    // --- Mobile Menu Toggle ---
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    const mobileMenu = document.querySelector('.mobile-menu'); // Assuming a menu element with this class will be added.

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function () {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // --- Simple confirmation for delete links ---
    // Note: This is a fallback. The `onclick` attribute is more direct,
    // but this demonstrates adding event listeners from a central JS file.
    const deleteLinks = document.querySelectorAll('a[onclick*="confirm"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            if (!confirm(link.getAttribute('onclick').match(/confirm\('([^']*)'\)/)[1])) {
                event.preventDefault();
            }
        });
    });

});
