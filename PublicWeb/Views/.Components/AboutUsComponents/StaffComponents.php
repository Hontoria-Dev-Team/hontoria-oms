<?php
/**
 * StaffComponents.php
 * Renders the Meet Our Staff section grouped by role.
 * Loaded via include() in AboutusPage.php
 */

// ── Role display order and labels ─────────────────────────────────────
$roleConfig = [
    'Layout Artist' => ['icon' => 'fa-desktop',      'label' => 'Layout Artist'],
    'Printing'      => ['icon' => 'fa-print',        'label' => 'Printing Staff'],
    'Tailor'        => ['icon' => 'fa-cut',          'label' => 'Tailoring Staff'],
    'Heatpress'     => ['icon' => 'fa-fire',         'label' => 'Heatpress Staff'],
    'Admin/Cashier' => ['icon' => 'fa-cash-register', 'label' => 'Admin / Cashier Staff'],
];

// Fallback just in case $employees isn't defined in AboutusPage.php before including this
if (!isset($employees)) {
    $employees = []; 
}

// ── Group employees by role ───────────────────────────────────────
$grouped = [];
foreach ($employees as $emp) {
    $role = $emp['role'] ?? 'Other';
    $grouped[$role][] = $emp;
}
?>

<section class="section employees-section" id="employees">
    <div class="section-inner">

        <p class="section-eyebrow">The Team</p>
        <h2 class="section-title">Meet Our Staff</h2>
        <div class="section-line"></div>

        <div class="staff-groups">
            <?php foreach ($roleConfig as $roleKey => $roleMeta): ?>
                <?php $staffInRole = $grouped[$roleKey] ?? []; ?>

                <div class="staff-group">

                    <div class="staff-role-header">
                        <i class="fas <?php echo $roleMeta['icon']; ?> staff-role-icon"></i>
                        <span class="staff-role-label"><?php echo htmlspecialchars($roleMeta['label']); ?></span>
                        <div class="staff-role-line"></div>
                    </div>

                    <div class="staff-cards">
                        <?php if (!empty($staffInRole)): ?>
                            <?php foreach ($staffInRole as $emp): ?>
                                <div class="staff-card">
                                    <?php if (!empty($emp['photo'])): ?>
                                        <img src="<?php echo htmlspecialchars($emp['photo']); ?>"
                                             alt="<?php echo htmlspecialchars($emp['name']); ?>"
                                             class="staff-photo"/>
                                    <?php else: ?>
                                        <div class="staff-photo-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="staff-name"><?php echo htmlspecialchars($emp['name']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="staff-card staff-card-empty">
                                <div class="staff-photo-placeholder staff-photo-empty">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div class="staff-name staff-name-empty">Coming Soon</div>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>