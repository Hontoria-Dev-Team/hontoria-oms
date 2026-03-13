// =============================================
//  HONTORIA — services.js
//  Services-specific: filter, modal, scroll reveal.
//  Mobile nav is handled by shared.js (loaded before this file).
// =============================================

document.addEventListener('DOMContentLoaded', () => {

  // ── PRODUCT DATA (used by the modal) ─────────────────────────────────
  // Key must exactly match the data-name on each .product-card
  const productInfo = {
    // Sublimation
    'Jersey':                  { desc:'High-quality full sublimation printing on jerseys. Perfect for sports teams, events, and uniforms. Fade-resistant and durable.',    icon:'fa-tshirt',         bg:'linear-gradient(135deg,#fff5cc,#ffe57a)' },
    'T-Shirt':                 { desc:'Custom sublimation printed t-shirts in any design. Great for organizations, teams, and personal use.',                               icon:'fa-tshirt',         bg:'linear-gradient(135deg,#fff5cc,#ffe57a)' },
    'Short':                   { desc:'Vibrant sublimation printed shorts. Matched perfectly with our jerseys for a complete team uniform.',                                icon:'fa-tshirt',         bg:'linear-gradient(135deg,#fff5cc,#ffe57a)' },
    'Warmer':                  { desc:'Sublimation warmers for players and athletes. Keeps you warm while looking professional.',                                           icon:'fa-tshirt',         bg:'linear-gradient(135deg,#fff5cc,#ffe57a)' },
    'Jogging Pants':           { desc:'Full sublimation jogging pants with any design. Comfortable, durable, and eye-catching.',                                           icon:'fa-tshirt',         bg:'linear-gradient(135deg,#fff5cc,#ffe57a)' },
    // Uniform
    'School Uniform':          { desc:'Custom-made school uniforms tailored to your school\'s specifications. Durable, comfortable, and neat.',                            icon:'fa-user-graduate',  bg:'linear-gradient(135deg,#e8f0ff,#c8d8ff)' },
    'Office Uniform':          { desc:'Professional office uniforms tailored for a sharp, consistent look across your entire team.',                                        icon:'fa-briefcase',      bg:'linear-gradient(135deg,#e8f0ff,#c8d8ff)' },
    'Professional Uniform':    { desc:'High-quality professional uniforms for healthcare, hospitality, and other industries.',                                              icon:'fa-user-tie',       bg:'linear-gradient(135deg,#e8f0ff,#c8d8ff)' },
    // Tarpaulin
    'Birthday Tarpaulin':      { desc:'Beautiful custom birthday tarpaulins. Any size, any design — bold and colorful.',                                                   icon:'fa-birthday-cake',  bg:'linear-gradient(135deg,#ffe0e0,#ffb3b3)' },
    'Graduation Tarpaulin':    { desc:'Celebrate achievements with stunning graduation tarpaulins. Custom designs that make the moment unforgettable.',                     icon:'fa-graduation-cap', bg:'linear-gradient(135deg,#ffe0e0,#ffb3b3)' },
    'Congratulation Tarpaulin':{ desc:'Vibrant congratulation tarpaulins for any milestone — promotions, awards, anniversaries, and more.',                                icon:'fa-star',           bg:'linear-gradient(135deg,#ffe0e0,#ffb3b3)' },
    // Mugs
    'Sublimation Mug':         { desc:'Full-wrap sublimation printed mugs with your custom design. Perfect for gifts, souvenirs, and giveaways.',                          icon:'fa-mug-hot',        bg:'linear-gradient(135deg,#fff3e0,#ffe0b2)' },
    // Lanyards
    'School ID Lanyard':       { desc:'Custom printed school ID lanyards with your school logo and colors. Durable and comfortable.',                                      icon:'fa-id-card',        bg:'linear-gradient(135deg,#f3e5f5,#e1bee7)' },
    'Office ID Lanyard':       { desc:'Professional office ID lanyards customized with your company branding.',                                                            icon:'fa-id-badge',       bg:'linear-gradient(135deg,#f3e5f5,#e1bee7)' },
    'Professional ID Lanyard': { desc:'High-quality lanyards for professionals, events, and conferences.',                                                                 icon:'fa-id-card-alt',    bg:'linear-gradient(135deg,#f3e5f5,#e1bee7)' },
    // Stitching
    'Custom Stitched T-Shirt': { desc:'Tailored t-shirts with custom stitching and embroidery. Perfect for teams, events, and branded apparel.',                           icon:'fa-cut',            bg:'linear-gradient(135deg,#e8f5e9,#c8e6c9)' },
    // Stickers
    'Motorcycle Decals':       { desc:'High-quality waterproof motorcycle decals in any shape and design. Weather-resistant and long-lasting.',                            icon:'fa-motorcycle',     bg:'linear-gradient(135deg,#fce4ec,#f8bbd0)' },
    'Truck Decals':            { desc:'Large-format truck decals and vinyl wraps. Bold, vibrant, and built to withstand the elements.',                                    icon:'fa-truck',          bg:'linear-gradient(135deg,#fce4ec,#f8bbd0)' },
    'Car Decals':              { desc:'Custom car decals and stickers. Perfect for business branding, personal style, or promotional use.',                                icon:'fa-car',            bg:'linear-gradient(135deg,#fce4ec,#f8bbd0)' },
  };

  // ── FILTER ENGINE ─────────────────────────────────────────────────────
  // Works dynamically — no hardcoded section IDs
  const allCards    = document.querySelectorAll('.product-card');
  const allSections = document.querySelectorAll('.product-section');
  const filterLabel = document.getElementById('filterLabel');

  function showAll() {
    allSections.forEach(s => s.style.display = '');
    allCards.forEach(c => c.style.display = '');
    if (filterLabel) filterLabel.textContent = 'Click any product to view details & pricing';
  }

  function filterByCategory(categoryId) {
    allSections.forEach(s => s.style.display = s.id === categoryId ? '' : 'none');
    allCards.forEach(c => c.style.display = '');
    if (filterLabel) filterLabel.textContent = 'Showing: ' + categoryId.charAt(0).toUpperCase() + categoryId.slice(1);
  }

  function filterByItem(name) {
    allCards.forEach(c => c.style.display = c.dataset.name === name ? '' : 'none');
    allSections.forEach(s => {
      const hasVisible = [...s.querySelectorAll('.product-card')].some(c => c.style.display !== 'none');
      s.style.display = hasVisible ? '' : 'none';
    });
    if (filterLabel) filterLabel.textContent = 'Showing: ' + name;
  }

  function clearActive() {
    document.querySelectorAll('.sb-item,.sb-sub-toggle,.sb-toggle').forEach(el => el.classList.remove('sb-active'));
  }

  // ── SIDEBAR: SERVICES master toggle ──────────────────────────────────
  const toggleServices = document.getElementById('toggleServices');
  const subServices    = document.getElementById('subServices');
  const chevServices   = document.getElementById('chevServices');

  toggleServices?.addEventListener('click', () => {
    const isOpen = subServices.classList.toggle('open');
    chevServices?.classList.toggle('open', isOpen);
    showAll();
    clearActive();
    toggleServices.classList.add('sb-active');
  });
  // Open SERVICES group by default
  subServices?.classList.add('open');
  chevServices?.classList.add('open');

  // ── SIDEBAR: Each category toggle (sublimation, uniform, etc.) ────────
  document.querySelectorAll('.sb-sub-toggle').forEach(btn => {
    const catId = btn.dataset.filter;
    const subEl = document.getElementById('sub_' + catId);
    const chevEl = document.getElementById('chev_' + catId);

    // Open all category item lists by default
    subEl?.classList.add('open');
    chevEl?.classList.add('open');

    btn.addEventListener('click', () => {
      // Toggle open/close the item list for this category
      const isOpen = subEl?.classList.toggle('open');
      chevEl?.classList.toggle('open', isOpen);

      // Filter content to show only this category
      clearActive();
      filterByCategory(catId);
      btn.classList.add('sb-active');
    });
  });

  // ── SIDEBAR: Individual product item links ────────────────────────────
  document.querySelectorAll('.sb-item').forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      const name = link.dataset.name;
      if (!name) return;
      clearActive();
      filterByItem(name);
      link.classList.add('sb-active');
    });
  });

  // ── SEARCH — works from both sidebar input and mobile input ──────────
  // Both inputs share the same id="searchInput" — only one is visible at a time
  const searchInput = document.getElementById('searchInput');
  searchInput?.addEventListener('input', () => {
    const q = searchInput.value.toLowerCase().trim();
    if (q === '') { showAll(); return; }

    allCards.forEach(c => {
      const match = (c.dataset.name || '').toLowerCase().includes(q) || (c.dataset.category || '').toLowerCase().includes(q);
      c.style.display = match ? '' : 'none';
    });
    allSections.forEach(s => {
      const hasVisible = [...s.querySelectorAll('.product-card')].some(c => c.style.display !== 'none');
      s.style.display = hasVisible ? '' : 'none';
    });
    if (filterLabel) filterLabel.textContent = 'Search: "' + searchInput.value + '"';
  });

  // ── MODAL ─────────────────────────────────────────────────────────────
  const modalOverlay = document.getElementById('modalOverlay');
  const modalClose   = document.getElementById('modalClose');
  const modalTitle   = document.getElementById('modalTitle');
  const modalDesc    = document.getElementById('modalDesc');
  const modalPrice   = document.getElementById('modalPrice');
  const modalMainImg = document.getElementById('modalMainImg');
  const modalPhIcon  = document.getElementById('modalPhIcon');
  const modalPhLabel = document.getElementById('modalPhLabel');
  const qtyInput     = document.getElementById('qtyInput');
  const qtyMinus     = document.getElementById('qtyMinus');
  const qtyPlus      = document.getElementById('qtyPlus');
  const totalDisplay = document.getElementById('totalDisplay');
  const thumbs       = document.querySelectorAll('.thumb');
  let currentPrice   = 0;

  function updateTotal() {
    const qty = parseInt(qtyInput?.value) || 1;
    if (totalDisplay) totalDisplay.textContent = currentPrice > 0 ? '₱' + (qty * currentPrice).toLocaleString() : '—';
  }

  function openModal(name) {
    const info = productInfo[name];
    // Fallback: read description directly from the card if not in productInfo
    const cardDesc = [...allCards].find(c => c.dataset.name === name)?.querySelector('.card-desc')?.textContent || '';
    const desc  = info?.desc || cardDesc || '';
    const icon  = info?.icon || 'fa-image';
    const bg    = info?.bg   || 'linear-gradient(135deg,#e8e8e8,#f5f5f5)';
    currentPrice = 0;

    if (modalTitle)   modalTitle.textContent  = name;
    if (modalDesc)    modalDesc.textContent   = desc;
    if (modalPrice)   modalPrice.textContent  = 'Contact us for pricing';
    if (qtyInput)     qtyInput.value          = 1;
    updateTotal();

    if (modalMainImg) modalMainImg.style.background = bg;
    if (modalPhIcon)  modalPhIcon.className = `fas ${icon} modal-ph-icon`;
    if (modalPhLabel) modalPhLabel.textContent = name;

    thumbs.forEach(t => { t.classList.remove('active'); t.style.background = bg; });
    thumbs[0]?.classList.add('active');

    modalOverlay?.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    modalOverlay?.classList.remove('open');
    document.body.style.overflow = '';
  }

  // Qty controls
  qtyMinus?.addEventListener('click', () => { const v = parseInt(qtyInput.value)||1; if(v>1){qtyInput.value=v-1; updateTotal();} });
  qtyPlus?.addEventListener('click',  () => { qtyInput.value=(parseInt(qtyInput.value)||1)+1; updateTotal(); });
  qtyInput?.addEventListener('input', updateTotal);

  // Thumbnails
  thumbs.forEach(t => t.addEventListener('click', () => { thumbs.forEach(x => x.classList.remove('active')); t.classList.add('active'); }));

  // Click card or View Details button to open modal
  document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', e => {
      if (e.target.closest('.order-btn')) return; // Order Now goes to Facebook, not modal
      const name = card.dataset.name;
      if (name) openModal(name);
    });
  });

  // Also wire view-btn directly as backup
  document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.stopPropagation();
      const name = btn.closest('.product-card')?.dataset.name;
      if (name) openModal(name);
    });
  });

  // Close modal
  modalClose?.addEventListener('click', closeModal);
  modalOverlay?.addEventListener('click', e => { if (e.target === modalOverlay) closeModal(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

  // ── SCROLL REVEAL ─────────────────────────────────────────────────────
  const observer = new IntersectionObserver(entries => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        setTimeout(() => { entry.target.style.opacity='1'; entry.target.style.transform='translateY(0)'; }, i * 60);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  allCards.forEach(card => {
    card.style.opacity   = '0';
    card.style.transform = 'translateY(24px)';
    card.style.transition= 'opacity 0.5s ease, transform 0.5s ease';
    observer.observe(card);
  });

});