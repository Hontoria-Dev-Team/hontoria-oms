<?php
/**
 * OrderPasswordComponents.php
 * Renders: set/update order password form with strength meter.
 * Expects: no specific variables (standalone form)
 */
?>

<div class="op-card op-card-full">

    <div class="op-card-head">
        <div class="op-card-icon"><i class="fas fa-shield-halved"></i></div>
        <span>Order Password</span>
    </div>

    <p class="op-note" style="margin-bottom: 1.25rem;">
        <i class="fas fa-circle-info"></i>
        Protect this order with a password. Must be at least 10 characters and contain at least one number.
    </p>

    <form method="POST" class="op-password-form" id="passwordForm">
        <input type="hidden" name="action" value="setPassword" />

        <div class="op-form-row">

            <div class="op-field-group">
                <label class="op-label">New Password</label>
                <div class="op-input-wrap">
                    <i class="fas fa-lock op-input-icon"></i>
                    <input type="password"
                           name="password"
                           id="newPassword"
                           class="op-input"
                           placeholder="At least 10 characters with 1 number"
                           required />
                    <button type="button" class="op-eye-btn" data-target="newPassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="op-field-group">
                <label class="op-label">Confirm Password</label>
                <div class="op-input-wrap">
                    <i class="fas fa-lock op-input-icon"></i>
                    <input type="password"
                           name="passwordConfirm"
                           id="confirmPassword"
                           class="op-input"
                           placeholder="Confirm password"
                           required />
                    <button type="button" class="op-eye-btn" data-target="confirmPassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

        </div>

        <div class="op-password-strength" id="strengthBar" style="display:none;">
            <div class="op-strength-fill" id="strengthFill"></div>
        </div>
        <p class="op-strength-label" id="strengthLabel"></p>

        <button type="submit" class="op-btn op-btn-primary">
            <i class="fas fa-floppy-disk"></i> Save Password
        </button>

    </form>

</div>