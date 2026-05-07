<?php
/**
 * OrderGateComponents.php
 * Renders: password gate form OR error state.
 * Expects: $requiresPassword, $passwordVerified, $error
 */

$error = $error ?? null;
$requiresPassword = $requiresPassword ?? false;
$passwordVerified = $passwordVerified ?? false;
?>

<?php if ($requiresPassword && !$passwordVerified): ?>

    <!-- ── PASSWORD GATE ── -->
    <div class="op-gate-wrap">
        <div class="op-gate-card">

            <div class="op-gate-icon">
                <i class="fas fa-lock"></i>
            </div>

            <h1 class="op-gate-title">Access Restricted</h1>
            <p class="op-gate-sub">This order is password-protected. Enter the correct password to view its details.</p>

            <?php if ($error): ?>
                <div class="op-alert op-alert-error">
                    <i class="fas fa-circle-exclamation"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="op-gate-form">
                <input type="hidden" name="action" value="verifyPassword" />
                <div class="op-field-group">
                    <label class="op-label">Password</label>
                    <div class="op-input-wrap">
                        <i class="fas fa-key op-input-icon"></i>
                        <input type="password"
                               name="password"
                               class="op-input"
                               placeholder="Enter order password"
                               required
                               autofocus />
                    </div>
                </div>
                <button type="submit" class="op-btn op-btn-primary op-btn-full" style="margin-top: 1rem;">
                    <i class="fas fa-unlock-alt"></i> Verify Password
                </button>
            </form>

        </div>
    </div>

<?php elseif ($error): ?>

    <!-- ── ERROR STATE ── -->
    <div class="op-gate-wrap">
        <div class="op-gate-card">

            <div class="op-gate-icon op-gate-icon-error">
                <i class="fas fa-triangle-exclamation"></i>
            </div>

            <h1 class="op-gate-title">Order Not Found</h1>
            <p class="op-gate-sub"><?php echo htmlspecialchars($error); ?></p>

            <a href="?page=home" class="op-btn op-btn-primary op-btn-full" style="margin-top: 1rem;">
                <i class="fas fa-house"></i> Back to Home
            </a>

        </div>
    </div>

<?php endif; ?>