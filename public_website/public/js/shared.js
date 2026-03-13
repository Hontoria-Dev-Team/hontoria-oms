// =============================================
//  HONTORIA — shared.js
//  Mobile nav toggle shared by ALL pages.
//
//  Load this on every page BEFORE the page-specific JS:
//    <script src="/js/shared.js"></script>
//    <script src="/js/home.js"></script>  (or services.js / aboutus.js)
// =============================================

document.addEventListener('DOMContentLoaded', () => {

  const hamburger = document.getElementById('hamburger');
  const mobileNav = document.getElementById('mobileNav');
  const closeNav  = document.getElementById('closeNav');
  const overlay   = document.getElementById('overlay');

  function openMenu() {
    mobileNav?.classList.add('open');
    overlay?.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeMenu() {
    mobileNav?.classList.remove('open');
    overlay?.classList.remove('show');
    document.body.style.overflow = '';
  }

  hamburger?.addEventListener('click', openMenu);
  closeNav?.addEventListener('click',  closeMenu);
  overlay?.addEventListener('click',   closeMenu);

  // Close on Escape key
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeMenu();
  });

});