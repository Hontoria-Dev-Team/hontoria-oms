// =============================================
//   HONTORIA PRINTING SERVICES — HomePage.js
//   All home-specific JS combined.
// =============================================

document.addEventListener('DOMContentLoaded', () => {

    // ---- 1. STICKY HEADER SHADOW ----
    const header = document.getElementById('header');
    if (header) {
        window.addEventListener('scroll', () => {
            header.style.boxShadow = window.scrollY > 10
                ? '0 4px 24px rgba(0, 0, 0, 0.15)'
                : '0 2px 8px rgba(0, 0, 0, 0.08)';
        }, { passive: true });
    }

    // ---- 2. ACTIVE NAV LINK (Scroll Spy) ----
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');
    
    function onScroll() {
        const currentScrollY = window.scrollY;
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 120;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');
            
            if (currentScrollY > sectionTop && currentScrollY <= sectionTop + sectionHeight) {
                navLinks.forEach(link => link.classList.remove('active'));
                const activeLink = document.querySelector(`.nav-link[href="#${sectionId}"]`);
                if (activeLink) activeLink.classList.add('active');
            }
        });
    }
    window.addEventListener('scroll', onScroll, { passive: true });

    // ---- 3. SCROLL REVEAL ANIMATIONS ----
    const revealElements = document.querySelectorAll('.service-card, .why-item, .section-header');
    
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                const delay = parseInt(element.dataset.delay || 0, 10);
                
                setTimeout(() => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, delay);
                
                observer.unobserve(element);
            }
        });
    }, { threshold: 0.12 });
    
    revealElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(28px)';
        element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        revealObserver.observe(element);
    });

    // ---- 4. SMOOTH SCROLL FOR ANCHOR LINKS ----
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', (event) => {
            const targetId = anchor.getAttribute('href');
            if (targetId === '#') return; // Skip if it's just "#"

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                event.preventDefault();
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ---- 5. PHOTO CAROUSEL LOGIC ----
    const slides = document.querySelectorAll('.pc-slide');
    const dots = document.querySelectorAll('.pc-dot');
    const btnPrev = document.getElementById('pcPrev');
    const btnNext = document.getElementById('pcNext');
    const photoFrame = document.querySelector('.photo-frame');
    
    let currentSlideIndex = 0;
    let autoPlayTimer;

    function goToSlide(index) {
        if (slides.length === 0) return;

        slides[currentSlideIndex].classList.remove('active');
        if (dots[currentSlideIndex]) dots[currentSlideIndex].classList.remove('active');
        
        currentSlideIndex = (index + slides.length) % slides.length;
        
        slides[currentSlideIndex].classList.add('active');
        if (dots[currentSlideIndex]) dots[currentSlideIndex].classList.add('active');
    }

    function startAutoPlay() {
        clearInterval(autoPlayTimer);
        autoPlayTimer = setInterval(() => goToSlide(currentSlideIndex + 1), 3500);
    }

    // Button Clicks
    if (btnPrev) btnPrev.addEventListener('click', () => { goToSlide(currentSlideIndex - 1); startAutoPlay(); });
    if (btnNext) btnNext.addEventListener('click', () => { goToSlide(currentSlideIndex + 1); startAutoPlay(); });
    
    // Dot Clicks
    dots.forEach(dot => {
        dot.addEventListener('click', () => { 
            goToSlide(parseInt(dot.dataset.idx, 10)); 
            startAutoPlay(); 
        });
    });

    // Touch Swipe Logic for Mobile
    let touchStartX = 0;
    if (photoFrame) {
        photoFrame.addEventListener('touchstart', (event) => { 
            touchStartX = event.changedTouches[0].screenX; 
        }, { passive: true });
        
        photoFrame.addEventListener('touchend', (event) => {
            const touchEndX = event.changedTouches[0].screenX;
            const swipeDistance = touchStartX - touchEndX;
            
            if (Math.abs(swipeDistance) > 40) { 
                const direction = swipeDistance > 0 ? 1 : -1;
                goToSlide(currentSlideIndex + direction); 
                startAutoPlay(); 
            }
        }, { passive: true });

        // Pause autoplay on mouse hover (desktop)
        photoFrame.addEventListener('mouseenter', () => clearInterval(autoPlayTimer));
        photoFrame.addEventListener('mouseleave', startAutoPlay);
    }

    // Initialize AutoPlay
    if (slides.length > 0) {
        startAutoPlay();
    }
});