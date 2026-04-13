// Js code here


// =============================================
//   HONTORIA — shared.js
//   Mobile nav toggle shared by ALL pages.
//
//   Load this on every page BEFORE the page-specific JS:
//     <script src="/js/shared.js"></script>
//     <script src="/js/home.js"></script>  (or services.js / aboutus.js)
// =============================================

document.addEventListener('DOMContentLoaded', () => {

    // ── 1. DOM ELEMENTS ───────────────────────────────────────────────────
    const hamburgerBtn = document.getElementById('hamburger');
    const mobileNavPanel = document.getElementById('mobileNav');
    const closeNavBtn = document.getElementById('closeNav');
    const navOverlay = document.getElementById('overlay');

    // ── 2. MENU LOGIC ─────────────────────────────────────────────────────
    function openMobileMenu() {
        if (mobileNavPanel) mobileNavPanel.classList.add('open');
        if (navOverlay) navOverlay.classList.add('show');
        
        // Prevent background scrolling when menu is open
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        if (mobileNavPanel) mobileNavPanel.classList.remove('open');
        if (navOverlay) navOverlay.classList.remove('show');
        
        // Restore background scrolling
        document.body.style.overflow = '';
    }

    // ── 3. EVENT LISTENERS ────────────────────────────────────────────────
    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', openMobileMenu);
    }

    if (closeNavBtn) {
        closeNavBtn.addEventListener('click', closeMobileMenu);
    }

    if (navOverlay) {
        navOverlay.addEventListener('click', closeMobileMenu);
    }

    // Close menu on Escape key press
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMobileMenu();
        }
    });

});