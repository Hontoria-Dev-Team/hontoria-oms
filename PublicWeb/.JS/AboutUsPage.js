// =============================================
//   HONTORIA — aboutus.js
//   About Us-specific JS only.
//   Mobile nav is handled by shared.js (loaded before this file).
//   Admin employee management is handled in the internal admin panel.
// =============================================

document.addEventListener('DOMContentLoaded', () => {

    // ── Elements ────────────────────────────────────────────────────────────
    const sidebarLinks = document.querySelectorAll('.about-sidebar-link');
    const sections = document.querySelectorAll('.about-content section[id]');

    // ── Sidebar scroll spy — highlights active section link ─────────────────
    function updateActiveSidebarLink() {
        let currentId = '';

        sections.forEach(section => {
            const top = section.getBoundingClientRect().top;
            
            // Offset to trigger the active state slightly before hitting the exact top
            if (top <= 120) {
                currentId = section.id;
            }
        });

        sidebarLinks.forEach(link => {
            const isActive = link.dataset.section === currentId;
            link.classList.toggle('active', isActive);
        });
    }

    // Attach scroll listener with passive flag for better scroll performance
    window.addEventListener('scroll', updateActiveSidebarLink, { passive: true });
    
    // Initialize the active state on page load
    updateActiveSidebarLink();

    // ── Smooth scroll when clicking sidebar links ───────────────────────────
    sidebarLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            
            const targetId = link.dataset.section;
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
        });
    });

});