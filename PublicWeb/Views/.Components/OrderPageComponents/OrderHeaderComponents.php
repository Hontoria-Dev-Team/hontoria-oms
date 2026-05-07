<?php
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
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<!-- ── ORDER HEADER ── -->
<div class="op-header">

    <div class="op-header-left">
        <div class="op-order-meta">
            <span class="op-order-label">ORDER</span>
            <h1 class="op-order-number">#<?php echo htmlspecialchars($orderData['id']); ?></h1>
        </div>
        <span class="op-status-pill status-<?php echo strtolower(str_replace(' ', '-', $orderData['status'])); ?>">
            <?php echo htmlspecialchars($orderData['status']); ?>
        </span>
    </div>

    <div class="op-header-right">
        <p class="op-service-tag">
            <i class="fas fa-print"></i>
            <?php echo htmlspecialchars($orderData['serviceName']); ?>
            <span class="op-service-sep">·</span>
            <?php echo htmlspecialchars($orderData['subserviceName']); ?>
        </p>
    </div>

</div>