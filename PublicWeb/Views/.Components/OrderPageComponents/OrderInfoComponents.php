<?php
/**
 * OrderInfoComponents.php
 * Renders: full order summary — order number, status, service,
 *          customer info, dates AND payment side by side.
 * Expects: $orderData
 */

$orderData = $orderData ?? [];
$pct = $orderData['priceTotal'] > 0
    ? round(($orderData['pricePaid'] / $orderData['priceTotal']) * 100)
    : 0;
?>

<div class="op-card op-card-full">

    <div class="op-summary-wrap">

        <!-- ── LEFT: Order + Customer Info ── -->
        <div class="op-summary-left">

            <!-- Order Number + Status -->
            <div class="op-order-heading">
                <h1 class="op-order-number">
                    Order #<?php echo htmlspecialchars($orderData['id']); ?>
                </h1>
                <span class="op-status-pill status-<?php echo strtolower(str_replace(' ', '-', $orderData['status'])); ?>">
                    <?php echo htmlspecialchars($orderData['status']); ?>
                </span>
            </div>

            <!-- Service & Subservice -->
            <p class="op-service-line">
                Service: <strong><?php echo htmlspecialchars($orderData['serviceName']); ?></strong>
                &mdash;
                Subservice: <strong><?php echo htmlspecialchars($orderData['subserviceName']); ?></strong>
            </p>

            <!-- Fields Grid -->
            <div class="op-info-grid">

                <div class="op-info-item">
                    <span class="op-info-label">Customer</span>
                    <span class="op-info-value"><?php echo htmlspecialchars($orderData['customerName']); ?></span>
                </div>

                <div class="op-info-item">
                    <span class="op-info-label">Due Date</span>
                    <span class="op-info-value">
                        <?php echo $orderData['deadlineAt']
                            ? htmlspecialchars(date('M j, Y', strtotime($orderData['deadlineAt'])))
                            : '<span class="op-muted">No due date</span>'; ?>
                    </span>
                </div>

                <div class="op-info-item">
                    <span class="op-info-label">Order Date</span>
                    <span class="op-info-value">
                        <?php echo htmlspecialchars(date('M j, Y', strtotime($orderData['createdAt']))); ?>
                    </span>
                </div>

                <div class="op-info-item">
                    <span class="op-info-label">Days Running</span>
                    <span class="op-info-value">
                        <?php echo (new DateTime($orderData['createdAt']))->diff(new DateTime())->days; ?> days
                    </span>
                </div>

            </div>

        </div>

        <!-- ── DIVIDER ── -->
        <div class="op-summary-divider"></div>

        <!-- ── RIGHT: Payment ── -->
        <div class="op-summary-right">

            <div class="op-info-grid">

                <div class="op-info-item">
                    <span class="op-info-label">Total Price</span>
                    <span class="op-info-value op-fw-bold"><?php echo formatAmount($orderData['priceTotal']); ?></span>
                </div>

                <div class="op-info-item">
                    <span class="op-info-label">Paid Amount</span>
                    <span class="op-info-value op-paid op-fw-bold"><?php echo formatAmount($orderData['pricePaid']); ?></span>
                </div>

                <div class="op-info-item" style="grid-column: 1 / -1;">
                    <span class="op-info-label">Outstanding</span>
                    <span class="op-info-value op-outstanding op-fw-bold">
                        <?php echo formatAmount(max(0, $orderData['priceTotal'] - $orderData['pricePaid'])); ?>
                    </span>
                </div>

            </div>

            <div class="op-progress-wrap" style="margin-top: 1rem;">
                <div class="op-progress-bar">
                    <div class="op-progress-fill" style="width: <?php echo $pct; ?>%"></div>
                </div>
                <span class="op-progress-label"><?php echo $pct; ?>% paid</span>
            </div>

            <p class="op-note" style="margin-top: 1rem;">
                <i class="fas fa-circle-info"></i>
                This public page shows your order status and approval progress. If the order is archived, the status is marked complete.
            </p>

        </div>

    </div>

</div>