// Js code here

// =============================================
//   HONTORIA — services.js
//   Services-specific: filter, modal, scroll reveal.
//   Mobile nav is handled by shared.js (loaded before this file).
// =============================================

document.addEventListener('DOMContentLoaded', () => {

    // ── 1. PRODUCT DATA (Used by the modal) ───────────────────────────────
    const productInfo = {
        // Sublimation
        'Jersey': { desc: 'High-quality full sublimation printing on jerseys. Perfect for sports teams, events, and uniforms. Fade-resistant and durable.', icon: 'fa-tshirt', bg: 'linear-gradient(135deg,#fff5cc,#ffe57a)' },
        'T-Shirt': { desc: 'Custom sublimation printed t-shirts in any design. Great for organizations, teams, and personal use.', icon: 'fa-tshirt', bg: 'linear-gradient(135deg,#fff5cc,#ffe57a)' },
        'Short': { desc: 'Vibrant sublimation printed shorts. Matched perfectly with our jerseys for a complete team uniform.', icon: 'fa-tshirt', bg: 'linear-gradient(135deg,#fff5cc,#ffe57a)' },
        'Warmer': { desc: 'Sublimation warmers for players and athletes. Keeps you warm while looking professional.', icon: 'fa-tshirt', bg: 'linear-gradient(135deg,#fff5cc,#ffe57a)' },
        'Jogging Pants': { desc: 'Full sublimation jogging pants with any design. Comfortable, durable, and eye-catching.', icon: 'fa-tshirt', bg: 'linear-gradient(135deg,#fff5cc,#ffe57a)' },
        'Long Sleeve': { desc: 'Full sublimation long sleeve shirts with vibrant custom designs. Perfect for teams, events, and everyday wear.', icon: 'fa-tshirt', bg: 'linear-gradient(135deg,#fff5cc,#ffe57a)' },
        'Polo Shirt': { desc: 'Custom sublimation polo shirts with full color printing. Great for corporate events, teams, and casual wear.', icon: 'fa-tshirt', bg: 'linear-gradient(135deg,#fff5cc,#ffe57a)' },
        // Uniform
        'School Uniform': { desc: 'Custom-made school uniforms tailored to your school\'s specifications. Durable, comfortable, and neat.', icon: 'fa-user-graduate', bg: 'linear-gradient(135deg,#e8f0ff,#c8d8ff)' },
        'Office Uniform': { desc: 'Professional office uniforms tailored for a sharp, consistent look across your entire team.', icon: 'fa-briefcase', bg: 'linear-gradient(135deg,#e8f0ff,#c8d8ff)' },
        'Professional Uniform': { desc: 'High-quality professional uniforms for healthcare, hospitality, and other industries.', icon: 'fa-user-tie', bg: 'linear-gradient(135deg,#e8f0ff,#c8d8ff)' },
        // Tarpaulin
        'Birthday Tarpaulin': { desc: 'Beautiful custom birthday tarpaulins. Any size, any design — bold and colorful.', icon: 'fa-birthday-cake', bg: 'linear-gradient(135deg,#ffe0e0,#ffb3b3)' },
        'Graduation Tarpaulin': { desc: 'Celebrate achievements with stunning graduation tarpaulins. Custom designs that make the moment unforgettable.', icon: 'fa-graduation-cap', bg: 'linear-gradient(135deg,#ffe0e0,#ffb3b3)' },
        'Congratulation Tarpaulin': { desc: 'Vibrant congratulation tarpaulins for any milestone — promotions, awards, anniversaries, and more.', icon: 'fa-star', bg: 'linear-gradient(135deg,#ffe0e0,#ffb3b3)' },
        // Mugs & Tumbler
        'Mug': { desc: 'Full-wrap sublimation printed mugs with your custom design. Perfect for gifts, souvenirs, and giveaways.', icon: 'fa-mug-hot', bg: 'linear-gradient(135deg,#fff3e0,#ffe0b2)' },
        'Tumbler': { desc: 'Custom sublimation printed tumblers. Keep your drinks hot or cold while showing off your unique design.', icon: 'fa-mug-hot', bg: 'linear-gradient(135deg,#fff3e0,#ffe0b2)' },
        // Lanyards
        'Lanyard': { desc: 'Custom printed lanyards with your logo and colors. Durable and comfortable.', icon: 'fa-id-card', bg: 'linear-gradient(135deg,#f3e5f5,#e1bee7)' },
        // Stitching
        'Customize Stitching': { desc: 'Tailored t-shirts with custom stitching and embroidery. Perfect for teams, events, and branded apparel.', icon: 'fa-cut', bg: 'linear-gradient(135deg,#e8f5e9,#c8e6c9)' },
        // Stickers
        'Motorcycle Decals': { desc: 'High-quality waterproof motorcycle decals in any shape and design. Weather-resistant and long-lasting.', icon: 'fa-motorcycle', bg: 'linear-gradient(135deg,#fce4ec,#f8bbd0)' },
        'Truck Decals': { desc: 'Large-format truck decals and vinyl wraps. Bold, vibrant, and built to withstand the elements.', icon: 'fa-truck', bg: 'linear-gradient(135deg,#fce4ec,#f8bbd0)' },
        'Car Decals': { desc: 'Custom car decals and stickers. Perfect for business branding, personal style, or promotional use.', icon: 'fa-car', bg: 'linear-gradient(135deg,#fce4ec,#f8bbd0)' },
        // Sintra Board
        'Sintra Board': { desc: 'Custom printed sintra boards for signage, displays, and advertising. Lightweight, durable, and weather-resistant.', icon: 'fa-border-all', bg: 'linear-gradient(135deg,#e3f2fd,#bbdefb)' },
        // Photo Frame
        'Photo Frame': { desc: 'Custom sublimation printed photo frames. Perfect for gifts, events, and keepsakes. Available in various sizes.', icon: 'fa-image', bg: 'linear-gradient(135deg,#f3e5f5,#e1bee7)' },
        // Ref Magnet
        'Ref Magnet': { desc: 'Personalized refrigerator magnets with custom designs. Great for souvenirs, giveaways, and promotional items.', icon: 'fa-magnet', bg: 'linear-gradient(135deg,#e8f5e9,#c8e6c9)' },
        // Plaque & Medal
        'Plaque and Trophies': { desc: 'Custom engraved plaques for awards, recognition, and achievements. Professional finish with personalized text and design.', icon: 'fa-award', bg: 'linear-gradient(135deg,#fff8e1,#ffecb3)' },
        'Medal': { desc: 'Custom medals for sports events, competitions, and recognition ceremonies. Available in gold, silver, and bronze.', icon: 'fa-medal', bg: 'linear-gradient(135deg,#fff8e1,#ffecb3)' },
    };

    // ── 2. FILTER ENGINE ──────────────────────────────────────────────────
    const allCards = document.querySelectorAll('.product-card');
    const allSections = document.querySelectorAll('.product-section');
    const filterLabel = document.getElementById('filterLabel');

    function showAll() {
        allSections.forEach(section => section.style.display = '');
        allCards.forEach(card => card.style.display = '');
        if (filterLabel) {
            filterLabel.textContent = 'Click any product to view details & pricing';
        }
    }

    function filterByCategory(categoryId) {
        allSections.forEach(section => {
            section.style.display = (section.id === categoryId) ? '' : 'none';
        });
        
        allCards.forEach(card => card.style.display = '');
        
        if (filterLabel) {
            filterLabel.textContent = 'Showing: ' + categoryId.charAt(0).toUpperCase() + categoryId.slice(1);
        }
    }

    function filterByItem(name) {
        allCards.forEach(card => {
            card.style.display = (card.dataset.name === name) ? '' : 'none';
        });

        allSections.forEach(section => {
            const visibleCards = [...section.querySelectorAll('.product-card')].some(card => card.style.display !== 'none');
            section.style.display = visibleCards ? '' : 'none';
        });

        if (filterLabel) {
            filterLabel.textContent = 'Showing: ' + name;
        }
    }

    function clearActive() {
        document.querySelectorAll('.sb-item, .sb-sub-toggle, .sb-toggle').forEach(element => {
            element.classList.remove('sb-active');
        });
    }

    // ── 3. SIDEBAR STATE MANAGEMENT ───────────────────────────────────────
    const toggleServices = document.getElementById('toggleServices');
    const subServices = document.getElementById('subServices');
    const chevServices = document.getElementById('chevServices');
    
    let subItemsExpanded = false;

    function collapseAllSubItems() {
        document.querySelectorAll('.sb-sub-items').forEach(el => el.classList.remove('open'));
        document.querySelectorAll('.sb-sub-toggle .sb-chevron').forEach(chev => chev.classList.remove('open'));
        subItemsExpanded = false;
    }

    function expandAllSubItems() {
        document.querySelectorAll('.sb-sub-items').forEach(el => el.classList.add('open'));
        document.querySelectorAll('.sb-sub-toggle .sb-chevron').forEach(chev => chev.classList.add('open'));
        subItemsExpanded = true;
    }

    // Initialize sidebar state
    if (subServices) subServices.classList.add('open');
    if (chevServices) chevServices.classList.add('open');
    collapseAllSubItems();
    if (toggleServices) toggleServices.classList.add('active-group', 'sb-active');

    // Master services toggle
    if (toggleServices) {
        toggleServices.addEventListener('click', () => {
            if (subServices) subServices.classList.add('open');
            if (chevServices) chevServices.classList.add('open');

            if (subItemsExpanded) {
                collapseAllSubItems();
            } else {
                expandAllSubItems();
            }

            showAll();
            clearActive();
            toggleServices.classList.add('active-group', 'sb-active');
        });
    }

    // Category toggles
    document.querySelectorAll('.sb-sub-toggle').forEach(button => {
        const categoryId = button.dataset.filter;
        const subElement = document.getElementById('sub_' + categoryId);
        const chevronElement = document.getElementById('chev_' + categoryId);

        button.addEventListener('click', () => {
            const isOpen = subElement?.classList.toggle('open');
            if (chevronElement) chevronElement.classList.toggle('open', isOpen);

            const allSubItems = document.querySelectorAll('.sb-sub-items');
            subItemsExpanded = [...allSubItems].every(el => el.classList.contains('open'));

            clearActive();
            filterByCategory(categoryId);
            button.classList.add('sb-active');
        });
    });

    // Product item links
    document.querySelectorAll('.sb-item').forEach(link => {
        link.addEventListener('click', event => {
            event.preventDefault();
            const productName = link.dataset.name;
            
            if (!productName) return;
            
            clearActive();
            filterByItem(productName);
            link.classList.add('sb-active');
        });
    });

    // ── 4. SEARCH FUNCTIONALITY ───────────────────────────────────────────
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase().trim();
            
            if (query === '') { 
                showAll(); 
                return; 
            }

            allCards.forEach(card => {
                const nameMatch = (card.dataset.name || '').toLowerCase().includes(query);
                const categoryMatch = (card.dataset.category || '').toLowerCase().includes(query);
                card.style.display = (nameMatch || categoryMatch) ? '' : 'none';
            });

            allSections.forEach(section => {
                const visibleCards = [...section.querySelectorAll('.product-card')].some(card => card.style.display !== 'none');
                section.style.display = visibleCards ? '' : 'none';
            });

            if (filterLabel) {
                filterLabel.textContent = 'Search: "' + searchInput.value + '"';
            }
        });
    }

    // ── 5. MODAL LOGIC ────────────────────────────────────────────────────
    const modalOverlay = document.getElementById('modalOverlay');
    const modalClose = document.getElementById('modalClose');
    const modalTitle = document.getElementById('modalTitle');
    const modalDesc = document.getElementById('modalDesc');
    const modalPrice = document.getElementById('modalPrice');
    const modalMainImg = document.getElementById('modalMainImg');
    const qtyInput = document.getElementById('qtyInput');
    const qtyMinus = document.getElementById('qtyMinus');
    const qtyPlus = document.getElementById('qtyPlus');
    const totalDisplay = document.getElementById('totalDisplay');
    
    let currentPrice = 0;

    function updateTotal() {
        const quantity = parseInt(qtyInput?.value) || 1;
        if (totalDisplay) {
            totalDisplay.textContent = currentPrice > 0 ? '₱' + (quantity * currentPrice).toLocaleString() : '—';
        }
    }

    function openModal(productName) {
        const card = [...allCards].find(c => c.dataset.name === productName);
        const info = productInfo[productName];
        
        const desc = info?.desc || card?.querySelector('.card-desc')?.textContent || '';
        const icon = info?.icon || 'fa-image';
        const bg = info?.bg || 'linear-gradient(135deg,#e8e8e8,#f5f5f5)';
        const basePrice = parseFloat(card?.dataset.price || 0);
        
        const photos = card?.dataset.photos ? JSON.parse(card.dataset.photos) : [];
        const variants = card?.dataset.variants ? JSON.parse(card.dataset.variants) : [];

        currentPrice = basePrice;
        let currentPhotoIdx = 0;

        if (modalTitle) modalTitle.textContent = productName;
        if (modalDesc) modalDesc.textContent = desc;
        if (qtyInput) qtyInput.value = 1;

        // Manage Variants
        const variantRow = document.getElementById('modalVariantRow');
        const variantSelect = document.getElementById('modalVariantSelect');

        if (variants.length > 0 && variantRow && variantSelect) {
            variantRow.style.display = 'flex';
            variantSelect.innerHTML = variants.map(variant => 
                `<option value="${variant.price}">${variant.name} — ₱${variant.price.toLocaleString()}</option>`
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
            if (modalPrice) {
                modalPrice.textContent = basePrice > 0 ? '₱' + basePrice.toLocaleString() + ' each' : 'Contact us for pricing';
            }
        }

        updateTotal();

        // Manage Main Image
        function renderMainImage(index) {
            currentPhotoIdx = index;
            
            if (photos.length > 0) {
                modalMainImg.innerHTML = `
                    <img src="${photos[index]}" 
                         alt="${productName}"
                         style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center; display: block; cursor: zoom-in;"
                         id="mainModalImg" 
                         title="Click to expand" />
                `;
                
                const mainModalImg = document.getElementById('mainModalImg');
                if (mainModalImg) {
                    mainModalImg.addEventListener('click', () => {
                        openLightbox(photos, currentPhotoIdx, productName);
                    });
                }
            } else {
                modalMainImg.style.background = bg;
                modalMainImg.innerHTML = `
                    <i class="fas ${icon} modal-ph-icon"></i>
                    <span class="modal-ph-label">${productName}</span>
                `;
            }
        }

        // Manage Thumbnails
        function updateThumbActive(index) {
            document.querySelectorAll('#modalThumbs .thumb').forEach((thumb, i) => {
                thumb.classList.toggle('active', i === index);
            });
        }

        const thumbsContainer = document.getElementById('modalThumbs');
        if (thumbsContainer) {
            if (photos.length > 0) {
                thumbsContainer.innerHTML = photos.map((src, i) => `
                    <div class="thumb ${i === 0 ? 'active' : ''}" data-idx="${i}" data-src="${src}">
                        <img src="${src}" style="width: 100%; height: 100%; object-fit: cover;" />
                    </div>
                `).join('');
                
                thumbsContainer.querySelectorAll('.thumb').forEach((thumb, i) => {
                    thumb.addEventListener('click', () => {
                        renderMainImage(i);
                        updateThumbActive(i);
                    });
                });
            } else {
                thumbsContainer.innerHTML = Array.from({ length: 8 }, (_, i) => `
                    <div class="thumb ${i === 0 ? 'active' : ''}" data-idx="${i}">
                        <i class="fas fa-image"></i>
                    </div>
                `).join('');
            }
        }

        renderMainImage(0);
        
        if (modalOverlay) modalOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    // Lightbox Logic
    function openLightbox(photos, startIdx, name) {
        let currentIndex = startIdx;
        const lightboxElement = document.createElement('div');
        lightboxElement.id = 'lightbox';
        lightboxElement.style.cssText = `
            position: fixed; inset: 0; background: rgba(0, 0, 0, 0.95); 
            z-index: 9999; display: flex; align-items: center; 
            justify-content: center; flex-direction: column;
        `;

        function renderLightboxContent() {
            lightboxElement.innerHTML = `
                <button id="lbClose" style="position: absolute; top: 16px; right: 20px; background: none; border: none; color: #fff; font-size: 28px; cursor: pointer; z-index: 10;">
                    <i class="fas fa-times"></i>
                </button>
                <button id="lbPrev" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); background: rgba(255, 255, 255, 0.15); border: none; color: #fff; font-size: 22px; width: 48px; height: 48px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <img src="${photos[currentIndex]}" alt="${name}" style="max-width: 92vw; max-height: 88vh; object-fit: contain; border-radius: 8px; box-shadow: 0 8px 40px rgba(0, 0, 0, 0.5);" />
                <button id="lbNext" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: rgba(255, 255, 255, 0.15); border: none; color: #fff; font-size: 22px; width: 48px; height: 48px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <span style="color: rgba(255, 255, 255, 0.6); font-size: 13px; margin-top: 12px;">
                    ${currentIndex + 1} / ${photos.length}
                </span>
            `;

            lightboxElement.querySelector('#lbClose').addEventListener('click', () => lightboxElement.remove());
            
            lightboxElement.querySelector('#lbPrev').addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + photos.length) % photos.length;
                renderLightboxContent();
            });
            
            lightboxElement.querySelector('#lbNext').addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % photos.length;
                renderLightboxContent();
            });
        }

        lightboxElement.addEventListener('click', event => { 
            if (event.target === lightboxElement) lightboxElement.remove(); 
        });

        document.addEventListener('keydown', function handleLightboxKey(event) {
            if (event.key === 'Escape') { 
                lightboxElement.remove(); 
                document.removeEventListener('keydown', handleLightboxKey); 
            }
            if (event.key === 'ArrowLeft') { 
                currentIndex = (currentIndex - 1 + photos.length) % photos.length; 
                renderLightboxContent(); 
            }
            if (event.key === 'ArrowRight') { 
                currentIndex = (currentIndex + 1) % photos.length; 
                renderLightboxContent(); 
            }
        });

        renderLightboxContent();
        document.body.appendChild(lightboxElement);
    }

    function closeModal() {
        if (modalOverlay) modalOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    // Modal Events
    if (qtyMinus) {
        qtyMinus.addEventListener('click', () => { 
            const value = parseInt(qtyInput.value) || 1; 
            if (value > 1) { 
                qtyInput.value = value - 1; 
                updateTotal(); 
            } 
        });
    }

    if (qtyPlus) {
        qtyPlus.addEventListener('click', () => { 
            qtyInput.value = (parseInt(qtyInput.value) || 1) + 1; 
            updateTotal(); 
        });
    }

    if (qtyInput) {
        qtyInput.addEventListener('input', updateTotal);
    }

    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', event => {
            if (event.target.closest('.order-btn')) return;
            
            const productName = card.dataset.name;
            if (productName) openModal(productName);
        });
    });

    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', event => {
            event.stopPropagation();
            const productName = button.closest('.product-card')?.dataset.name;
            if (productName) openModal(productName);
        });
    });

    if (modalClose) modalClose.addEventListener('click', closeModal);
    
    if (modalOverlay) {
        modalOverlay.addEventListener('click', event => { 
            if (event.target === modalOverlay) closeModal(); 
        });
    }
    
    document.addEventListener('keydown', event => { 
        if (event.key === 'Escape') closeModal(); 
    });

    // ── 6. SCROLL REVEAL ANIMATIONS ───────────────────────────────────────
    const scrollObserver = new IntersectionObserver(entries => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => { 
                    entry.target.style.opacity = '1'; 
                    entry.target.style.transform = 'translateY(0)'; 
                }, index * 60);
                
                scrollObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    allCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(24px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        scrollObserver.observe(card);
    });

});