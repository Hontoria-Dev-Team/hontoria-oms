<?php
// XSS escape helper – define once across the application
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}

/**
 * OrderHeaderComponents.php
 * Renders: order number, status pill, service + subservice tag.
 * Expects: $orderData, $message
 */
if (!isset($orderData) || !is_array($orderData)) {
    $orderData = [
        'id' => '',
        'status' => '',
        'serviceName' => '',
        'subserviceName' => '',
    ];
}
?>

<!-- ── MESSAGE BANNER ── -->
<?php if (!empty($message)): ?>
    <div class="op-banner">
        <i class="fas fa-circle-check"></i>
        <?= e($message) ?>
    </div>
<?php endif; ?>

<!-- ── ORDER HEADER ── -->
<div class="op-header">

    <div class="op-header-left">
        <div class="op-order-meta">
            <span class="op-order-label">ORDER</span>
            <h1 class="op-order-number">#<?= e($orderData['id']) ?></h1>
        </div>
        <span class="op-status-pill status-<?= e(strtolower(str_replace(' ', '-', $orderData['status']))) ?>">
            <?= e($orderData['status']) ?>
        </span>
    </div>

    <div class="op-header-right">
        <p class="op-service-tag">
            <i class="fas fa-print"></i>
            <?= e($orderData['serviceName']) ?>
            <span class="op-service-sep">·</span>
            <?= e($orderData['subserviceName']) ?>
        </p>
    </div>

</div>