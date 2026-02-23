// =============================================
//  HONTORIA PRINTING SERVICES — home.js
// =============================================

document.addEventListener('DOMContentLoaded', () => {

  // ---- MOBILE NAV TOGGLE ----
  const hamburger = document.getElementById('hamburger');
  const mobileNav = document.getElementById('mobileNav');
  const closeNav  = document.getElementById('closeNav');
  const overlay   = document.getElementById('overlay');

  function openMenu() {
    mobileNav.classList.add('open');
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
  function closeMenu() {
    mobileNav.classList.remove('open');
    overlay.classList.remove('show');
    document.body.style.overflow = '';
  }
  hamburger?.addEventListener('click', openMenu);
  closeNav?.addEventListener('click', closeMenu);
  overlay?.addEventListener('click', closeMenu);
  document.querySelectorAll('.mob-link').forEach(link => {
    link.addEventListener('click', closeMenu);
  });

  // ---- STICKY HEADER SHADOW ----
  const header = document.getElementById('header');
  window.addEventListener('scroll', () => {
    header.style.boxShadow = window.scrollY > 10
      ? '0 4px 24px rgba(0,0,0,0.15)'
      : '0 2px 8px rgba(0,0,0,0.08)';
  });

  // ---- ACTIVE NAV LINK (scroll spy) ----
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.nav-link');
  function onScroll() {
    const scrollY = window.pageYOffset;
    sections.forEach(section => {
      const sTop = section.offsetTop - 120;
      const sH   = section.offsetHeight;
      const id   = section.getAttribute('id');
      if (scrollY > sTop && scrollY <= sTop + sH) {
        navLinks.forEach(l => l.classList.remove('active'));
        document.querySelector(`.nav-link[href="#${id}"]`)?.classList.add('active');
      }
    });
  }
  window.addEventListener('scroll', onScroll);

  // ---- SCROLL REVEAL ----
  const revealEls = document.querySelectorAll(
    '.service-card, .why-item, .section-header'
  );
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const delay = entry.target.dataset.delay || 0;
        setTimeout(() => {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }, parseInt(delay));
        revealObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });
  revealEls.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(28px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    revealObserver.observe(el);
  });

  // ---- SMOOTH SCROLL for anchor links ----
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', (e) => {
      const target = document.querySelector(anchor.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ---- PHOTO CAROUSEL ----
  const slides  = document.querySelectorAll('.pc-slide');
  const dots    = document.querySelectorAll('.pc-dot');
  const btnPrev = document.getElementById('pcPrev');
  const btnNext = document.getElementById('pcNext');
  let cur = 0, timer;

  function goTo(idx) {
    slides[cur].classList.remove('active');
    dots[cur].classList.remove('active');
    cur = (idx + slides.length) % slides.length;
    slides[cur].classList.add('active');
    dots[cur].classList.add('active');
  }
  function startAuto() {
    clearInterval(timer);
    timer = setInterval(() => goTo(cur + 1), 3500);
  }

  btnPrev?.addEventListener('click', () => { goTo(cur - 1); startAuto(); });
  btnNext?.addEventListener('click', () => { goTo(cur + 1); startAuto(); });
  dots.forEach(d => d.addEventListener('click', () => { goTo(+d.dataset.idx); startAuto(); }));

  // Touch swipe
  let tx = 0;
  const frame = document.querySelector('.photo-frame');
  frame?.addEventListener('touchstart', e => { tx = e.changedTouches[0].screenX; }, { passive: true });
  frame?.addEventListener('touchend', e => {
    const diff = tx - e.changedTouches[0].screenX;
    if (Math.abs(diff) > 40) { goTo(diff > 0 ? cur + 1 : cur - 1); startAuto(); }
  }, { passive: true });

  // Pause on hover
  frame?.addEventListener('mouseenter', () => clearInterval(timer));
  frame?.addEventListener('mouseleave', startAuto);

  if (slides.length) startAuto();

});