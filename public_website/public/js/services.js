// =============================================
//  HONTORIA — services.js
// =============================================

document.addEventListener('DOMContentLoaded', () => {

  // ---- MOBILE NAV ----
  const hamburger = document.getElementById('hamburger');
  const mobileNav = document.getElementById('mobileNav');
  const closeNav  = document.getElementById('closeNav');
  const overlay   = document.getElementById('overlay');

  function openMenu()  { mobileNav.classList.add('open');    overlay.classList.add('show');    document.body.style.overflow='hidden'; }
  function closeMenu() { mobileNav.classList.remove('open'); overlay.classList.remove('show'); document.body.style.overflow=''; }

  hamburger?.addEventListener('click', openMenu);
  closeNav?.addEventListener('click', closeMenu);
  overlay?.addEventListener('click', closeMenu);

  // ================================================================
  //  FILTER ENGINE
  //  Levels:
  //    'all'          → show every card + every section header
  //    'sublimation'  → show only sublimation section + its cards
  //    'tarpaulin'    → show only tarpaulin section + its cards
  //    'item:<name>'  → show only the card whose data-name === name
  // ================================================================
  const allCards       = document.querySelectorAll('.product-card');
  const sublimSection  = document.getElementById('sublimation');
  const tarpSection    = document.getElementById('tarpaulin');
  const filterLabel    = document.getElementById('filterLabel');

  function applyFilter(mode, value) {

    // Show both section wrappers first, then decide
    sublimSection.style.display = '';
    tarpSection.style.display   = '';
    allCards.forEach(c => c.style.display = '');

    if (mode === 'all') {
      // Everything visible
      if (filterLabel) filterLabel.textContent = 'Click any product to view details & pricing';

    } else if (mode === 'category') {
      // Show one category section, hide the other
      if (value === 'sublimation') {
        tarpSection.style.display = 'none';
        if (filterLabel) filterLabel.textContent = 'Showing: Sublimation products';
      } else {
        sublimSection.style.display = 'none';
        if (filterLabel) filterLabel.textContent = 'Showing: Tarpaulin products';
      }

    } else if (mode === 'item') {
      // Hide every card except the clicked one
      allCards.forEach(card => {
        if (card.dataset.name === value) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
      // Hide section whose cards are all hidden
      const sublimVisible = [...allCards].some(c => c.dataset.category === 'sublimation' && c.style.display !== 'none');
      const tarpVisible   = [...allCards].some(c => c.dataset.category === 'tarpaulin'   && c.style.display !== 'none');
      if (!sublimVisible) sublimSection.style.display = 'none';
      if (!tarpVisible)   tarpSection.style.display   = 'none';
      if (filterLabel) filterLabel.textContent = 'Showing: ' + value;
    }

    // Mark active sidebar item
    document.querySelectorAll('.sb-item, .sb-sub-toggle, .sb-toggle').forEach(el => {
      el.classList.remove('sb-active');
    });
  }

  // ---- SIDEBAR TOGGLES + FILTER ----

  // SERVICES button → show ALL
  const toggleServices = document.getElementById('toggleServices');
  const subServices    = document.getElementById('subServices');
  const chevServices   = document.getElementById('chevServices');

  toggleServices?.addEventListener('click', () => {
    const isOpen = subServices.classList.toggle('open');
    chevServices.classList.toggle('open', isOpen);
    applyFilter('all');
    toggleServices.classList.add('sb-active');
  });
  // Open by default
  subServices?.classList.add('open');
  chevServices?.classList.add('open');

  // SUBLIMATION button → show only sublimation
  const toggleSublim = document.getElementById('toggleSublim');
  const subSublim    = document.getElementById('subSublim');
  const chevSublim   = document.getElementById('chevSublim');

  toggleSublim?.addEventListener('click', () => {
    const isOpen = subSublim.classList.toggle('open');
    chevSublim.classList.toggle('open', isOpen);
    applyFilter('category', 'sublimation');
    toggleSublim.classList.add('sb-active');
  });
  subSublim?.classList.add('open');
  chevSublim?.classList.add('open');

  // TARPAULIN button → show only tarpaulin
  const toggleTarp = document.getElementById('toggleTarp');
  const subTarp    = document.getElementById('subTarp');
  const chevTarp   = document.getElementById('chevTarp');

  toggleTarp?.addEventListener('click', () => {
    const isOpen = subTarp.classList.toggle('open');
    chevTarp.classList.toggle('open', isOpen);
    applyFilter('category', 'tarpaulin');
    toggleTarp.classList.add('sb-active');
  });
  subTarp?.classList.add('open');
  chevTarp?.classList.add('open');

  // INDIVIDUAL ITEM links → show only that one card
  document.querySelectorAll('.sb-item').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const name = link.dataset.name;
      if (name) {
        applyFilter('item', name);
        // Highlight active item in sidebar
        document.querySelectorAll('.sb-item').forEach(i => i.classList.remove('sb-active'));
        link.classList.add('sb-active');
      }
    });
  });

  // ---- SEARCH FILTER (overrides sidebar filter) ----
  const searchInput = document.getElementById('searchInput');
  searchInput?.addEventListener('input', () => {
    const q = searchInput.value.toLowerCase().trim();

    if (q === '') {
      // Restore all on clear
      applyFilter('all');
      return;
    }

    // Show/hide cards by search
    allCards.forEach(card => {
      const name = card.dataset.name?.toLowerCase() || '';
      const cat  = card.dataset.category?.toLowerCase() || '';
      card.style.display = (name.includes(q) || cat.includes(q)) ? '' : 'none';
    });

    // Show/hide sections based on visible cards
    const sublimVisible = [...allCards].some(c => c.dataset.category === 'sublimation' && c.style.display !== 'none');
    const tarpVisible   = [...allCards].some(c => c.dataset.category === 'tarpaulin'   && c.style.display !== 'none');
    sublimSection.style.display = sublimVisible ? '' : 'none';
    tarpSection.style.display   = tarpVisible   ? '' : 'none';

    if (filterLabel) filterLabel.textContent = 'Search results for: "' + searchInput.value + '"';
  });

  // ---- MODAL ----
  const modalOverlay  = document.getElementById('modalOverlay');
  const modalClose    = document.getElementById('modalClose');
  const modalTitle    = document.getElementById('modalTitle');
  const modalDesc     = document.getElementById('modalDesc');
  const modalPrice    = document.getElementById('modalPrice');
  const modalMainImg  = document.getElementById('modalMainImg');
  const modalPhIcon   = document.getElementById('modalPhIcon');
  const modalPhLabel  = document.getElementById('modalPhLabel');
  const qtyInput      = document.getElementById('qtyInput');
  const qtyMinus      = document.getElementById('qtyMinus');
  const qtyPlus       = document.getElementById('qtyPlus');
  const totalDisplay  = document.getElementById('totalDisplay');
  const thumbs        = document.querySelectorAll('.thumb');

  let currentPrice = 0;

  const productInfo = {
    'Jersey':                  { desc:'High-quality full sublimation printing on jerseys. Perfect for sports teams, events, and uniforms. Fade-resistant and durable.',   price: 0, icon:'fa-tshirt',        bg:'linear-gradient(135deg,#fff5cc,#ffe57a)' },
    'T-Shirt':                 { desc:'Custom sublimation printed t-shirts in any design. Great for organizations, teams, and personal use.',                              price: 0, icon:'fa-tshirt',        bg:'linear-gradient(135deg,#fff5cc,#ffe57a)' },
    'Short':                   { desc:'Vibrant sublimation printed shorts. Matched perfectly with our jerseys for a complete team uniform.',                               price: 0, icon:'fa-tshirt',        bg:'linear-gradient(135deg,#fff5cc,#ffe57a)' },
    'Warmer':                  { desc:'Sublimation warmers for players and athletes. Keeps you warm while looking professional on and off the court.',                     price: 0, icon:'fa-tshirt',        bg:'linear-gradient(135deg,#fff5cc,#ffe57a)' },
    'Jogging Pants':           { desc:'Full sublimation jogging pants with any design. Comfortable, durable, and eye-catching for any team or individual.',                price: 0, icon:'fa-tshirt',        bg:'linear-gradient(135deg,#fff5cc,#ffe57a)' },
    'Birthday Tarpaulin':      { desc:'Beautiful custom birthday tarpaulins made to celebrate your special day. Any size, any design — bold and colorful.',                price: 0, icon:'fa-birthday-cake', bg:'linear-gradient(135deg,#ffe0e0,#ffb3b3)' },
    'Graduation Tarpaulin':    { desc:'Celebrate achievements with stunning graduation tarpaulins. Custom designs that make the moment unforgettable.',                    price: 0, icon:'fa-graduation-cap',bg:'linear-gradient(135deg,#ffe0e0,#ffb3b3)' },
    'Congratulation Tarpaulin':{ desc:'Vibrant congratulation tarpaulins for any milestone — promotions, awards, anniversaries, and more.',                                price: 0, icon:'fa-star',          bg:'linear-gradient(135deg,#ffe0e0,#ffb3b3)' },
  };

  function updateTotal() {
    const qty = parseInt(qtyInput.value) || 1;
    totalDisplay.textContent = currentPrice > 0
      ? '₱' + (qty * currentPrice).toLocaleString()
      : '—';
  }

  function openModal(name) {
    const info = productInfo[name];
    if (!info) return;

    modalTitle.textContent = name;
    modalDesc.textContent  = info.desc;
    modalPrice.textContent = info.price > 0 ? '₱' + info.price.toLocaleString() : 'Contact us for pricing';
    currentPrice = info.price;

    qtyInput.value = 1;
    updateTotal();

    modalMainImg.style.background = info.bg;
    modalPhIcon.className  = `fas ${info.icon} modal-ph-icon`;
    modalPhLabel.textContent = name;

    thumbs.forEach(t => {
      t.classList.remove('active');
      t.style.background = info.bg;
    });
    thumbs[0]?.classList.add('active');

    modalOverlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    modalOverlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  // Qty controls
  qtyMinus?.addEventListener('click', () => {
    const v = parseInt(qtyInput.value) || 1;
    if (v > 1) { qtyInput.value = v - 1; updateTotal(); }
  });
  qtyPlus?.addEventListener('click', () => {
    qtyInput.value = (parseInt(qtyInput.value) || 1) + 1;
    updateTotal();
  });
  qtyInput?.addEventListener('input', updateTotal);

  // Thumbnail click
  thumbs.forEach(thumb => {
    thumb.addEventListener('click', () => {
      thumbs.forEach(t => t.classList.remove('active'));
      thumb.classList.add('active');
    });
  });

  // Open modal on view-btn click
  document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const card = btn.closest('.product-card');
      openModal(card.dataset.name);
    });
  });

  modalClose?.addEventListener('click', closeModal);
  modalOverlay?.addEventListener('click', (e) => { if (e.target === modalOverlay) closeModal(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

  // ---- SCROLL REVEAL ----
  const cards = document.querySelectorAll('.product-card');
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        setTimeout(() => {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }, i * 60);
        revealObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  cards.forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(24px)';
    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    revealObserver.observe(card);
  });

});