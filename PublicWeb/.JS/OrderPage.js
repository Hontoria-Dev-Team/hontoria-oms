// =============================================
//   HONTORIA — OrderPage.js
//   Handles: lightbox, password toggle,
//            strength meter, form confirm.
// =============================================

document.addEventListener('DOMContentLoaded', () => {

    // ── 1. DESIGN IMAGE LIGHTBOX ─────────────────────────────────────────
    const zoomBtn      = document.getElementById('zoomBtn');
    const designImg    = document.getElementById('designImg');
    const lightbox     = document.getElementById('lightbox');
    const lightboxImg  = document.getElementById('lightboxImg');
    const lightboxClose = document.getElementById('lightboxClose');

    if (zoomBtn && designImg && lightbox) {
        zoomBtn.addEventListener('click', () => {
            lightboxImg.src = designImg.src;
            lightbox.classList.add('open');
            document.body.style.overflow = 'hidden';
        });

        lightboxClose.addEventListener('click', closeLightbox);

        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) closeLightbox();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeLightbox();
        });
    }

    function closeLightbox() {
        if (!lightbox) return;
        lightbox.classList.remove('open');
        document.body.style.overflow = '';
    }

    // ── 2. PASSWORD VISIBILITY TOGGLE ────────────────────────────────────
    document.querySelectorAll('.op-eye-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.dataset.target;
            const input    = document.getElementById(targetId);
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type       = isPassword ? 'text' : 'password';

            const icon = btn.querySelector('i');
            if (icon) {
                icon.className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
            }
        });
    });

    // ── 3. PASSWORD STRENGTH METER ────────────────────────────────────────
    const newPasswordInput = document.getElementById('newPassword');
    const strengthBar      = document.getElementById('strengthBar');
    const strengthFill     = document.getElementById('strengthFill');
    const strengthLabel    = document.getElementById('strengthLabel');

    if (newPasswordInput && strengthBar && strengthFill && strengthLabel) {
        newPasswordInput.addEventListener('input', () => {
            const val = newPasswordInput.value;

            if (val.length === 0) {
                strengthBar.style.display  = 'none';
                strengthLabel.textContent  = '';
                return;
            }

            strengthBar.style.display = 'block';

            const score = getStrengthScore(val);

            const levels = [
                { label: 'Too short',   color: '#e55353', width: '15%'  },
                { label: 'Weak',        color: '#e07b39', width: '35%'  },
                { label: 'Fair',        color: '#d4a017', width: '60%'  },
                { label: 'Good',        color: '#4caf7d', width: '80%'  },
                { label: 'Strong',      color: '#1a6b3a', width: '100%' },
            ];

            const level = levels[Math.min(score, levels.length - 1)];
            strengthFill.style.width      = level.width;
            strengthFill.style.background = level.color;
            strengthLabel.textContent     = level.label;
            strengthLabel.style.color     = level.color;
        });
    }

    function getStrengthScore(password) {
        let score = 0;
        if (password.length >= 6)  score++;
        if (password.length >= 10) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^a-zA-Z0-9]/.test(password)) score++;
        return score;
    }

    // ── 4. PASSWORD FORM VALIDATION ──────────────────────────────────────
    const passwordForm    = document.getElementById('passwordForm');
    const confirmPassword = document.getElementById('confirmPassword');

    if (passwordForm && newPasswordInput && confirmPassword) {
        passwordForm.addEventListener('submit', (e) => {
            const pass    = newPasswordInput.value;
            const confirm = confirmPassword.value;

            if (pass.length < 10) {
                e.preventDefault();
                showInlineError(newPasswordInput, 'Password must be at least 10 characters.');
                return;
            }

            if (!/[0-9]/.test(pass)) {
                e.preventDefault();
                showInlineError(newPasswordInput, 'Password must contain at least one number.');
                return;
            }

            if (pass !== confirm) {
                e.preventDefault();
                showInlineError(confirmPassword, 'Passwords do not match.');
                return;
            }
        });
    }

    function showInlineError(input, message) {
        // Remove any existing error
        const existing = input.closest('.op-field-group')?.querySelector('.op-inline-error');
        if (existing) existing.remove();

        const error = document.createElement('p');
        error.className   = 'op-inline-error';
        error.textContent = message;
        error.style.cssText = 'color:#b01e1e; font-size:.8rem; font-weight:600; margin:.3rem 0 0;';

        input.closest('.op-input-wrap')?.insertAdjacentElement('afterend', error);
        input.focus();

        // Auto-remove after 4 seconds
        setTimeout(() => error.remove(), 4000);
    }

    // ── 5. APPROVAL FORM CONFIRM ─────────────────────────────────────────
    document.querySelectorAll('.op-approval-form').forEach(form => {
        form.addEventListener('submit', (e) => {
            const confirmed = confirm('Are you sure you want to approve? This action cannot be undone.');
            if (!confirmed) e.preventDefault();
        });
    });

    // ── 6. PROGRESS BAR ANIMATE ON LOAD ──────────────────────────────────
    const fill = document.querySelector('.op-progress-fill');
    if (fill) {
        const targetWidth  = fill.style.width;
        fill.style.width   = '0%';
        fill.style.transition = 'none';

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                fill.style.transition = 'width 1s ease';
                fill.style.width      = targetWidth;
            });
        });
    }

});