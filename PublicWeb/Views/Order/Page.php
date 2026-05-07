<?php
/**
 * Page.php
 * Location: publicWeb/Views/Order/Page.php
 * Assembles all OrderPageComponents.
 */

$orderData        = $orderData        ?? null;
$orderProcesses   = $orderProcesses   ?? [];
$requiresPassword = $requiresPassword ?? false;
$passwordVerified = $passwordVerified ?? false;
$error            = $error            ?? null;
$message          = $message          ?? null;
$variableList     = $variableList     ?? null;

if ($orderData) {
    $orderData = (array)$orderData;
} elseif (!$requiresPassword && !$error) {
    $error = 'Order not found.';
}

// ── Helpers (available to all included components) ────────────────────────────

function formatAmount($amount) {
    return '₱' . number_format($amount, 2);
}

function formatStatusClass($status) {
    switch (strtolower($status)) {
        case 'active':           return 'status-active';
        case 'idle':             return 'status-idle';
        case 'unpaid':           return 'status-unpaid';
        case 'for verification': return 'status-verification';
        case 'complete':         return 'status-complete';
        default:                 return 'status-default';
    }
}

function getProcessStateClass($status) {
    switch (strtolower($status)) {
        case 'complete': return 'step-complete';
        case 'active':   return 'step-active';
        default:         return 'step-pending';
    }
}

function getProcessIcon($status) {
    switch (strtolower($status)) {
        case 'complete': return 'fa-check';
        case 'active':   return 'fa-spinner fa-spin';
        default:         return 'fa-clock';
    }
}

$componentsDir = __DIR__ . '/../.Components/OrderPageComponents/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Status — Hontoria Printing</title>
    <link rel="stylesheet" href="../.CSS/Shared.css" />
    <link rel="stylesheet" href="../.CSS/OrderPage.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,700&display=swap" rel="stylesheet" />
</head>
<body class="op-body">

    <?php include __DIR__ . '/../.Components/SharedComponents/HeaderComponents.php'; ?>

    <main class="op-page">

        <?php if (($requiresPassword && !$passwordVerified) || $error): ?>

            <?php include $componentsDir . 'OrderGateComponents.php'; ?>

        <?php else: ?>

            <?php include $componentsDir . 'OrderInfoComponents.php'; ?>

            <?php if ($orderData['designExists']): ?>
                <?php if (file_exists($componentsDir . 'OrderDesignComponents.php')) include $componentsDir . 'OrderDesignComponents.php'; ?>
            <?php endif; ?>

            <?php if ($orderData['serviceHasVariableList']): ?>
                <?php if (file_exists($componentsDir . 'OrderVariableListComponents.php')) include $componentsDir . 'OrderVariableListComponents.php'; ?>
            <?php endif; ?>

            <?php include $componentsDir . 'OrderProcessComponents.php'; ?>
            <?php include $componentsDir . 'OrderPasswordComponents.php'; ?>

        <?php endif; ?>

    </main>

    <!-- Lightbox -->
    <div class="op-lightbox" id="lightbox">
        <button class="op-lightbox-close" id="lightboxClose">
            <i class="fas fa-times"></i>
        </button>
        <img src="" alt="Design Full View" class="op-lightbox-img" id="lightboxImg" />
    </div>

    <?php include __DIR__ . '/../.Components/SharedComponents/FooterComponents.php'; ?>

    <script src="../.JS/Shared.js"></script>
    <script src="../.JS/OrderPage.js"></script>

</body>
</html>