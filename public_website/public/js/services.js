// =============================================
//  HONTORIA — services.js
//  Services-specific: filter, modal, scroll reveal.
//  Mobile nav is handled by shared.js (loaded before this file).
// =============================================

document.addEventListener('DOMContentLoaded', () => {

  // ── PRODUCT DATA (used by the modal) ─────────────────────────────────
  const productInfo = {
    // Sublimation
    'Jersey':                  { desc:'High-quality full sublimation printing on jerseys. Perfect for sports teams, events, and uniforms. Fade-resistant and durable.',    icon:'fa-tshirt',         bg:'linear-gradient(135deg,#fff5cc,#ffe57a)' },
    'T-Shirt':                 { desc:'Custom sublimation printed t-shirts in any design. Great for organizations, teams, and personal use.',                               icon:'fa-tshirt',         bg:'linear-gradient(135deg,#fff5cc,#ffe57a)' },
    'Short':                   { desc:'Vibrant sublimation printed shorts. Matched perfectly with our jerseys for a complete team uniform.',                                icon:'fa-tshirt',         bg:'linear-gradient(135deg,#fff5cc,#ffe57a)' },
    'Warmer':                  { desc:'Sublimation warmers for players and athletes. Keeps you warm while looking professional.',                                           icon:'fa-tshirt',         bg:'linear-gradient(135deg,#fff5cc,#ffe57a)' },
    'Jogging Pants':           { desc:'Full sublimation jogging pants with any design. Comfortable, durable, and eye-catching.',                                           icon:'fa-tshirt',         bg:'linear-gradient(135deg,#fff5cc,#ffe57a)' },
    'Long Sleeve':             { desc:'Full sublimation long sleeve shirts with vibrant custom designs. Perfect for teams, events, and everyday wear.',                    icon:'fa-tshirt',         bg:'linear-gradient(135deg,#fff5cc,#ffe57a)' },
    'Polo Shirt':              { desc:'Custom sublimation polo shirts with full color printing. Great for corporate events, teams, and casual wear.',                      icon:'fa-tshirt',         bg:'linear-gradient(135deg,#fff5cc,#ffe57a)' },
    // Uniform
    'School Uniform':          { desc:'Custom-made school uniforms tailored to your school\'s specifications. Durable, comfortable, and neat.',                            icon:'fa-user-graduate',  bg:'linear-gradient(135deg,#e8f0ff,#c8d8ff)' },
    'Office Uniform':          { desc:'Professional office uniforms tailored for a sharp, consistent look across your entire team.',                                        icon:'fa-briefcase',      bg:'linear-gradient(135deg,#e8f0ff,#c8d8ff)' },
    'Professional Uniform':    { desc:'High-quality professional uniforms for healthcare, hospitality, and other industries.',                                              icon:'fa-user-tie',       bg:'linear-gradient(135deg,#e8f0ff,#c8d8ff)' },
    // Tarpaulin
    'Birthday Tarpaulin':      { desc:'Beautiful custom birthday tarpaulins. Any size, any design — bold and colorful.',                                                   icon:'fa-birthday-cake',  bg:'linear-gradient(135deg,#ffe0e0,#ffb3b3)' },
    'Graduation Tarpaulin':    { desc:'Celebrate achievements with stunning graduation tarpaulins. Custom designs that make the moment unforgettable.',                     icon:'fa-graduation-cap', bg:'linear-gradient(135deg,#ffe0e0,#ffb3b3)' },
    'Congratulation Tarpaulin':{ desc:'Vibrant congratulation tarpaulins for any milestone — promotions, awards, anniversaries, and more.',                                icon:'fa-star',           bg:'linear-gradient(135deg,#ffe0e0,#ffb3b3)' },
    // Mugs & Tumbler
    'Mug':                    { desc:'Full-wrap sublimation printed mugs with your custom design. Perfect for gifts, souvenirs, and giveaways.',                          icon:'fa-mug-hot',        bg:'linear-gradient(135deg,#fff3e0,#ffe0b2)' },
    'Tumbler':                 { desc:'Custom sublimation printed tumblers. Keep your drinks hot or cold while showing off your unique design.',                           icon:'fa-mug-hot',        bg:'linear-gradient(135deg,#fff3e0,#ffe0b2)' },
    // Lanyards
    'School ID Lanyard':       { desc:'Custom printed lanyards with your logo and colors. Durable and comfortable.',                                      icon:'fa-id-card',        bg:'linear-gradient(135deg,#f3e5f5,#e1bee7)' },
    // Stitching
    'Custom Stitched T-Shirt': { desc:'Tailored t-shirts with custom stitching and embroidery. Perfect for teams, events, and branded apparel.',                           icon:'fa-cut',            bg:'linear-gradient(135deg,#e8f5e9,#c8e6c9)' },
    // Stickers
    'Motorcycle Decals':       { desc:'High-quality waterproof motorcycle decals in any shape and design. Weather-resistant and long-lasting.',                            icon:'fa-motorcycle',     bg:'linear-gradient(135deg,#fce4ec,#f8bbd0)' },
    'Truck Decals':            { desc:'Large-format truck decals and vinyl wraps. Bold, vibrant, and built to withstand the elements.',                                    icon:'fa-truck',          bg:'linear-gradient(135deg,#fce4ec,#f8bbd0)' },
    'Car Decals':              { desc:'Custom car decals and stickers. Perfect for business branding, personal style, or promotional use.',                                icon:'fa-car',            bg:'linear-gradient(135deg,#fce4ec,#f8bbd0)' },
  };

  // ── FILTER ENGINE ─────────────────────────────────────────────────────
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
  subServices?.classList.add('open');
  chevServices?.classList.add('open');

  // ── SIDEBAR: Each category toggle ────────────────────────────────────
  document.querySelectorAll('.sb-sub-toggle').forEach(btn => {
    const catId  = btn.dataset.filter;
    const subEl  = document.getElementById('sub_' + catId);
    const chevEl = document.getElementById('chev_' + catId);

    subEl?.classList.add('open');
    chevEl?.classList.add('open');

    btn.addEventListener('click', () => {
      const isOpen = subEl?.classList.toggle('open');
      chevEl?.classList.toggle('open', isOpen);
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

  // ── SEARCH ───────────────────────────────────────────────────────────
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
  const qtyInput     = document.getElementById('qtyInput');
  const qtyMinus     = document.getElementById('qtyMinus');
  const qtyPlus      = document.getElementById('qtyPlus');
  const totalDisplay = document.getElementById('totalDisplay');
  let currentPrice   = 0;

  function updateTotal() {
    const qty = parseInt(qtyInput?.value) || 1;
    if (totalDisplay) totalDisplay.textContent = currentPrice > 0 ? '₱' + (qty * currentPrice).toLocaleString() : '—';
  }

  function openModal(name) {
    const card     = [...allCards].find(c => c.dataset.name === name);
    const info     = productInfo[name];
    const desc     = info?.desc  || card?.querySelector('.card-desc')?.textContent || '';
    const icon     = info?.icon  || 'fa-image';
    const bg       = info?.bg    || 'linear-gradient(135deg,#e8e8e8,#f5f5f5)';
    const price    = parseFloat(card?.dataset.price || 0);
    const photos   = card?.dataset.photos   ? JSON.parse(card.dataset.photos)   : [];
    const variants = card?.dataset.variants ? JSON.parse(card.dataset.variants) : [];

    currentPrice = price;
    let currentPhotoIdx = 0;

    if (modalTitle) modalTitle.textContent = name;
    if (modalDesc)  modalDesc.textContent  = desc;
    if (qtyInput)   qtyInput.value         = 1;

    // ── Variant selector ──────────────────────────────────────────────
    const variantRow    = document.getElementById('modalVariantRow');
    const variantSelect = document.getElementById('modalVariantSelect');

    if (variants.length > 0 && variantRow && variantSelect) {
      variantRow.style.display = 'flex';
      variantSelect.innerHTML  = variants.map(v =>
        `<option value="${v.price}">${v.name} — ₱${v.price.toLocaleString()}</option>`
      ).join('');
      currentPrice = variants[0].price;
      if (modalPrice) modalPrice.textContent = '₱' + currentPrice.toLocaleString() + ' each';
      variantSelect.onchange = () => {
        currentPrice = parseFloat(variantSelect.value);
        if (modalPrice) modalPrice.textContent = '₱' + currentPrice.toLocaleString() + ' each';
        updateTotal();
      };
    } else {
      if (variantRow) variantRow.style.display = 'none';
      if (modalPrice) modalPrice.textContent = price > 0 ? '₱' + price.toLocaleString() + ' each' : 'Contact us for pricing';
    }

    updateTotal();

    // ── Main image ────────────────────────────────────────────────────
    function renderMainImage(idx) {
      currentPhotoIdx = idx;
      if (photos.length > 0) {
        modalMainImg.innerHTML = `
          <img src="${photos[idx]}" alt="${name}"
               style="width:100%;height:100%;object-fit:cover;display:block;cursor:zoom-in"
               id="mainModalImg" title="Click to expand"/>
        `;
        document.getElementById('mainModalImg')?.addEventListener('click', () => {
          openLightbox(photos, currentPhotoIdx, name);
        });
      } else {
        modalMainImg.style.background = bg;
        modalMainImg.innerHTML = `<i class="fas ${icon} modal-ph-icon"></i><span class="modal-ph-label">${name}</span>`;
      }
    }

    // ── Thumbnails ────────────────────────────────────────────────────
    function updateThumbActive(idx) {
      document.querySelectorAll('#modalThumbs .thumb').forEach((t, i) => {
        t.classList.toggle('active', i === idx);
      });
    }

    const thumbsContainer = document.getElementById('modalThumbs');
    if (thumbsContainer) {
      if (photos.length > 0) {
        thumbsContainer.innerHTML = photos.map((src, i) =>
          `<div class="thumb ${i === 0 ? 'active' : ''}" data-idx="${i}" data-src="${src}">
             <img src="${src}" style="width:100%;height:100%;object-fit:cover"/>
           </div>`
        ).join('');
        thumbsContainer.querySelectorAll('.thumb').forEach((thumb, i) => {
          thumb.addEventListener('click', () => {
            renderMainImage(i);
            updateThumbActive(i);
          });
        });
      } else {
        thumbsContainer.innerHTML = Array.from({length: 8}, (_, i) =>
          `<div class="thumb ${i === 0 ? 'active' : ''}" data-idx="${i}">
             <i class="fas fa-image"></i>
           </div>`
        ).join('');
      }
    }

    renderMainImage(0);
    modalOverlay?.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  // ── Lightbox ──────────────────────────────────────────────────────────
  function openLightbox(photos, startIdx, name) {
    let idx = startIdx;
    const lb = document.createElement('div');
    lb.id = 'lightbox';
    lb.style.cssText = `position:fixed;inset:0;background:rgba(0,0,0,0.95);z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column;`;

    function renderLb() {
      lb.innerHTML = `
        <button id="lbClose" style="position:absolute;top:16px;right:20px;background:none;border:none;color:#fff;font-size:28px;cursor:pointer;z-index:10"><i class="fas fa-times"></i></button>
        <button id="lbPrev" style="position:absolute;left:16px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.15);border:none;color:#fff;font-size:22px;width:48px;height:48px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center"><i class="fas fa-chevron-left"></i></button>
        <img src="${photos[idx]}" alt="${name}" style="max-width:92vw;max-height:88vh;object-fit:contain;border-radius:8px;box-shadow:0 8px 40px rgba(0,0,0,0.5)"/>
        <button id="lbNext" style="position:absolute;right:16px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.15);border:none;color:#fff;font-size:22px;width:48px;height:48px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center"><i class="fas fa-chevron-right"></i></button>
        <span style="color:rgba(255,255,255,0.6);font-size:13px;margin-top:12px">${idx + 1} / ${photos.length}</span>
      `;
      lb.querySelector('#lbClose').addEventListener('click', () => lb.remove());
      lb.querySelector('#lbPrev').addEventListener('click',  () => { idx = (idx - 1 + photos.length) % photos.length; renderLb(); });
      lb.querySelector('#lbNext').addEventListener('click',  () => { idx = (idx + 1) % photos.length; renderLb(); });
    }

    lb.addEventListener('click', e => { if (e.target === lb) lb.remove(); });
    document.addEventListener('keydown', function lbKey(e) {
      if (e.key === 'Escape')     { lb.remove(); document.removeEventListener('keydown', lbKey); }
      if (e.key === 'ArrowLeft')  { idx = (idx - 1 + photos.length) % photos.length; renderLb(); }
      if (e.key === 'ArrowRight') { idx = (idx + 1) % photos.length; renderLb(); }
    });

    renderLb();
    document.body.appendChild(lb);
  }

  function closeModal() {
    modalOverlay?.classList.remove('open');
    document.body.style.overflow = '';
  }

  // Qty controls
  qtyMinus?.addEventListener('click', () => { const v = parseInt(qtyInput.value)||1; if(v>1){qtyInput.value=v-1; updateTotal();} });
  qtyPlus?.addEventListener('click',  () => { qtyInput.value=(parseInt(qtyInput.value)||1)+1; updateTotal(); });
  qtyInput?.addEventListener('input', updateTotal);

  // Click card to open modal
  document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', e => {
      if (e.target.closest('.order-btn')) return;
      const name = card.dataset.name;
      if (name) openModal(name);
    });
  });

  document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.stopPropagation();
      const name = btn.closest('.product-card')?.dataset.name;
      if (name) openModal(name);
    });
  });

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