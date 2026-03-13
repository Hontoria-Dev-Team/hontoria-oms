/* =============================================
   HONTORIA — aboutus.js
   ============================================= */

document.addEventListener('DOMContentLoaded', () => {

    // ── Mobile nav toggle ─────────────────────────────────────────────────
    const hamburger = document.getElementById('hamburger');
    const mobileNav = document.getElementById('mobileNav');
    const closeNav  = document.getElementById('closeNav');
    const overlay   = document.getElementById('overlay');

    hamburger?.addEventListener('click', () => {
        mobileNav.classList.add('open');
        overlay.classList.add('show');
    });
    closeNav?.addEventListener('click', closeMenu);
    overlay?.addEventListener('click', closeMenu);

    function closeMenu() {
        mobileNav.classList.remove('open');
        overlay.classList.remove('show');
    }

    // ── Admin mode toggle (double-click the employees title to enable) ────
    // This lets the admin see delete buttons on employee cards
    const employeesTitle = document.getElementById('employeesTitle');
    const employeesGrid  = document.getElementById('employeesGrid');

    employeesTitle?.addEventListener('dblclick', () => {
        employeesGrid.classList.toggle('admin-mode');
        const isAdmin = employeesGrid.classList.contains('admin-mode');
        employeesTitle.style.color = isAdmin ? 'var(--red)' : '';
        showToast(isAdmin ? 'Admin mode ON — you can now remove employees' : 'Admin mode OFF');
    });

    // ── Add Employee Modal ────────────────────────────────────────────────
    const addBtn        = document.getElementById('btnAddEmployee');
    const modalOverlay  = document.getElementById('addEmployeeModal');
    const modalClose    = document.getElementById('modalClose');
    const addForm       = document.getElementById('addEmployeeForm');
    const photoInput    = document.getElementById('empPhoto');
    const photoPreview  = document.getElementById('photoPreview');

    addBtn?.addEventListener('click', () => modalOverlay.classList.add('open'));
    modalClose?.addEventListener('click', closeModal);
    modalOverlay?.addEventListener('click', (e) => { if (e.target === modalOverlay) closeModal(); });

    function closeModal() {
        modalOverlay.classList.remove('open');
        addForm.reset();
        photoPreview.style.display = 'none';
    }

    // ── Photo preview when user picks a file ─────────────────────────────
    photoInput?.addEventListener('change', () => {
        const file = photoInput.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.src = e.target.result;
            photoPreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });

    // ── Submit new employee form ──────────────────────────────────────────
    addForm?.addEventListener('submit', (e) => {
        e.preventDefault();

        const name  = document.getElementById('empName').value.trim();
        const role  = document.getElementById('empRole').value.trim();
        const photo = photoInput.files[0];

        if (!name || !role) return showToast('Please fill in all fields.', true);

        // If photo selected, read it; otherwise use placeholder
        if (photo) {
            const reader = new FileReader();
            reader.onload = (ev) => addEmployeeCard(name, role, ev.target.result);
            reader.readAsDataURL(photo);
        } else {
            addEmployeeCard(name, role, null);
        }

        // Also save via AJAX to the server
        saveEmployeeToServer(name, role, photo);

        closeModal();
        showToast(`${name} added successfully!`);
    });

    // ── Build and insert a new employee card into the grid ────────────────
    function addEmployeeCard(name, role, photoSrc) {
        const card = document.createElement('div');
        card.className = 'employee-card';
        card.dataset.name = name;

        const imgHtml = photoSrc
            ? `<img src="${photoSrc}" alt="${name}" class="employee-img"/>`
            : `<div class="employee-img-placeholder">
                 <i class="fas fa-user"></i>
                 <span>NO PHOTO</span>
               </div>`;

        card.innerHTML = `
            ${imgHtml}
            <div class="employee-info">
                <div class="employee-name">${escapeHtml(name)}</div>
                <div class="employee-role">${escapeHtml(role)}</div>
            </div>
            <button class="btn-remove-employee" title="Remove employee">
                <i class="fas fa-times"></i>
            </button>`;

        // Wire up the remove button
        card.querySelector('.btn-remove-employee').addEventListener('click', () => removeEmployee(card, name));

        employeesGrid.appendChild(card);
    }

    // ── Remove employee card ──────────────────────────────────────────────
    function removeEmployee(card, name) {
        if (!confirm(`Remove ${name} from the team?`)) return;

        card.style.transition = 'opacity 0.3s, transform 0.3s';
        card.style.opacity = '0';
        card.style.transform = 'scale(0.8)';
        setTimeout(() => card.remove(), 300);

        // Tell the server to delete this employee
        deleteEmployeeFromServer(name);
        showToast(`${name} removed.`);
    }

    // Wire remove buttons on existing (server-rendered) cards
    document.querySelectorAll('.btn-remove-employee').forEach(btn => {
        btn.addEventListener('click', () => {
            const card = btn.closest('.employee-card');
            removeEmployee(card, card.dataset.name);
        });
    });

    // ── AJAX: Save new employee to server ─────────────────────────────────
    function saveEmployeeToServer(name, role, photoFile) {
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('name', name);
        formData.append('role', role);
        if (photoFile) formData.append('photo', photoFile);

        fetch('?page=about&action=employee', { method: 'POST', body: formData })
            .catch(err => console.error('Save employee failed:', err));
    }

    // ── AJAX: Delete employee from server ─────────────────────────────────
    function deleteEmployeeFromServer(name) {
        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('name', name);

        fetch('?page=about&action=employee', { method: 'POST', body: formData })
            .catch(err => console.error('Remove employee failed:', err));
    }

    // ── Toast notification ────────────────────────────────────────────────
    function showToast(msg, isError = false) {
        const toast = document.createElement('div');
        toast.textContent = msg;
        toast.style.cssText = `
            position:fixed;bottom:28px;right:28px;z-index:9999;
            background:${isError ? '#CC1A00' : '#1A0800'};color:#FFD000;
            padding:12px 22px;border-radius:8px;font-size:13px;font-weight:700;
            letter-spacing:0.5px;box-shadow:0 8px 24px rgba(0,0,0,0.25);
            animation:slideUp 0.3s ease both;
        `;
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity='0'; toast.style.transition='opacity 0.3s'; }, 2500);
        setTimeout(() => toast.remove(), 2800);
    }

    // ── Helper: escape HTML to prevent XSS ───────────────────────────────
    function escapeHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

});