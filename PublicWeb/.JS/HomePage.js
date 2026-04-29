document.addEventListener('DOMContentLoaded', () => {

    const header = document.getElementById('header');
    if (header) {
        window.addEventListener('scroll', () => {
            header.style.boxShadow = window.scrollY > 10
                ? '0 4px 24px rgba(0, 0, 0, 0.15)'
                : '0 2px 8px rgba(0, 0, 0, 0.08)';
        }, { passive: true });
    }

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

    const slides = document.querySelectorAll('.pc-slide');
    const dotsContainer = document.getElementById('pcDots');
    const btnPrev = document.getElementById('pcPrev');
    const btnNext = document.getElementById('pcNext');
    const photoFrame = document.querySelector('.photo-frame');

    let currentSlideIndex = 0;
    let autoPlayTimer;
    let dots = [];

    // ✅ CREATE DOTS DYNAMICALLY
    if (dotsContainer && slides.length > 0) {
        slides.forEach((_, index) => {
            const dot = document.createElement('span');
            dot.classList.add('pc-dot');
            if (index === 0) dot.classList.add('active');
            dot.dataset.idx = index;

            dot.addEventListener('click', () => {
                goToSlide(index);
                startAutoPlay();
            });

            dotsContainer.appendChild(dot);
        });

        dots = document.querySelectorAll('.pc-dot');
    }

    function goToSlide(index) {
        slides[currentSlideIndex].classList.remove('active');
        if (dots[currentSlideIndex]) dots[currentSlideIndex].classList.remove('active');

        currentSlideIndex = (index + slides.length) % slides.length;

        slides[currentSlideIndex].classList.add('active');
        if (dots[currentSlideIndex]) dots[currentSlideIndex].classList.add('active');
    }

    // ✅ 5 SECONDS
    function startAutoPlay() {
        clearInterval(autoPlayTimer);
        autoPlayTimer = setInterval(() => goToSlide(currentSlideIndex + 1), 5000);
    }

    if (btnPrev) btnPrev.addEventListener('click', () => { goToSlide(currentSlideIndex - 1); startAutoPlay(); });
    if (btnNext) btnNext.addEventListener('click', () => { goToSlide(currentSlideIndex + 1); startAutoPlay(); });

    let touchStartX = 0;

    if (photoFrame) {
        photoFrame.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        photoFrame.addEventListener('touchend', (e) => {
            const swipe = touchStartX - e.changedTouches[0].screenX;

            if (Math.abs(swipe) > 40) {
                goToSlide(currentSlideIndex + (swipe > 0 ? 1 : -1));
                startAutoPlay();
            }
        }, { passive: true });

        photoFrame.addEventListener('mouseenter', () => clearInterval(autoPlayTimer));
        photoFrame.addEventListener('mouseleave', startAutoPlay);
    }

    if (slides.length > 0) startAutoPlay();
});