<?php
/**
 * OrderProcessComponents.php
 * Renders: process phase steps with status icons and connectors.
 * Expects: $orderProcesses
 */

if (!function_exists('getProcessStateClass')) {
    function getProcessStateClass(string $status): string
    {
        switch (strtolower($status)) {
            case 'completed':
                return 'completed';
            case 'in progress':
            case 'in_progress':
                return 'in-progress';
            case 'pending':
                return 'pending';
            case 'failed':
                return 'failed';
            default:
                return 'unknown';
        }
    }
}

if (!function_exists('getProcessIcon')) {
    function getProcessIcon(string $status): string
    {
        switch (strtolower($status)) {
            case 'completed':
                return 'fa-check-circle';
            case 'in progress':
            case 'in_progress':
                return 'fa-spinner';
            case 'pending':
                return 'fa-clock';
            case 'failed':
                return 'fa-triangle-exclamation';
            default:
                return 'fa-question-circle';
        }
    }
}
?>

<div class="op-card op-card-full">

    <div class="op-card-head">
        <div class="op-card-icon">
            <i class="fas fa-diagram-project"></i>
        </div>
        <span>Order Process</span>
    </div>

    <?php if (!empty($orderProcesses)): ?>

        <div class="op-process-track">
            <?php foreach ($orderProcesses as $index => $process): ?>

                <div class="op-process-step <?php echo getProcessStateClass($process['status']); ?>">
                    <div class="op-step-icon">
                        <i class="fas <?php echo getProcessIcon($process['status']); ?>"></i>
                    </div>
                    <div class="op-step-body">
                        <span class="op-step-phase">Phase <?php echo htmlspecialchars($process['phase']); ?></span>
                        <h3 class="op-step-name"><?php echo htmlspecialchars($process['processName']); ?></h3>
                        <span class="op-step-status"><?php echo ucfirst(htmlspecialchars($process['status'])); ?></span>
                        <span class="op-step-assigned">
                            <i class="fas fa-user-gear"></i>
                            <?php echo htmlspecialchars($process['assignedNum']); ?> assigned
                        </span>
                    </div>
                </div>

                <?php if ($index < count($orderProcesses) - 1): ?>
                    <div class="op-process-connector">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                <?php endif; ?>

            <?php endforeach; ?>
        </div>

    <?php else: ?>

        <p class="op-note">
            <i class="fas fa-circle-info"></i>
            No process data available for this order.
        </p>

    <?php endif; ?>

</div>