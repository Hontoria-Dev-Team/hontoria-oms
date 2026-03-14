// =============================================
//  HONTORIA — aboutus.js
//  About Us-specific JS only.
//  Mobile nav is handled by shared.js (loaded before this file).
//  Admin employee management is handled in the internal admin panel.
// =============================================

document.addEventListener('DOMContentLoaded', () => {

    // ── Sidebar scroll spy — highlights active section link ──────────────
    const sidebarLinks = document.querySelectorAll('.about-sidebar-link');
    const sections     = document.querySelectorAll('.about-content section[id]');

    function updateActiveSidebarLink() {
        let currentId = '';
        sections.forEach(section => {
            const top = section.getBoundingClientRect().top;
            if (top <= 120) currentId = section.id;
        });
        sidebarLinks.forEach(link => {
            link.classList.toggle('active', link.dataset.section === currentId);
        });
    }
    window.addEventListener('scroll', updateActiveSidebarLink, { passive: true });
    updateActiveSidebarLink();

    // ── Smooth scroll when clicking sidebar links ─────────────────────────
    sidebarLinks.forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const target = document.getElementById(link.dataset.section);
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

});