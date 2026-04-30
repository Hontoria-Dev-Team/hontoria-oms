<?php
$orderData = $orderData ?? null;
$orderProcesses = $orderProcesses ?? [];
$error = $error ?? null;

if (!$orderData) {
    $error = $error ?? 'Order not found.';
} else {
    $orderData = (array)$orderData;
}

function formatAmount($amount) {
    return '₱' . number_format($amount, 2);
}

function formatStatusClass($status) {
    switch (strtolower($status)) {
        case 'active':
            return 'status-active';
        case 'idle':
            return 'status-idle';
        case 'unpaid':
            return 'status-unpaid';
        case 'for verification':
            return 'status-verification';
        case 'complete':
            return 'status-complete';
        default:
            return 'status-default';
    }
}

function formatBooleanLabel($value) {
    return $value ? 'Approved' : 'Pending';
}

function getProcessStateClass($status) {
    switch (strtolower($status)) {
        case 'complete':
            return 'process-step complete';
        case 'active':
            return 'process-step active';
        case 'idle':
            return 'process-step pending';
        case 'pending':
            return 'process-step pending';
        default:
            return 'process-step';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Status — Hontoria Printing</title>
    <link rel="stylesheet" href="../.CSS/Shared.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        .order-page {
            padding: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .order-summary,
        .order-section {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 14px 30px rgba(0, 0, 0, .08);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .order-summary-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: 1.4fr 1fr;
        }

        .field-grid {
            display: grid;
            gap: .75rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .field-item {
            display: flex;
            flex-direction: column;
            gap: .25rem;
        }

        .field-label {
            font-weight: 600;
            color: #555;
        }

        .field-value {
            font-size: 1rem;
            color: #111;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .65rem 1rem;
            border-radius: 999px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .status-active {
            background: #fff7d6;
            color: #8a6c08;
        }

        .status-idle {
            background: #ffe5e5;
            color: #a41e22;
        }

        .status-unpaid {
            background: #f1f8ff;
            color: #124e8c;
        }

        .status-verification {
            background: #e6f6e8;
            color: #1c6b43;
        }

        .status-complete {
            background: #d9f5e0;
            color: #1e6b39;
        }

        .process-flow {
            display: flex;
            align-items: stretch;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .process-step {
            flex: 1 1 220px;
            min-width: 180px;
            background: #f4f4f5;
            border-radius: 18px;
            padding: 1rem;
            border: 1px solid #d9d9d9;
            text-align: center;
        }

        .process-step h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
        }

        .process-step p {
            margin: .4rem 0 0;
            color: #555;
            font-size: .92rem;
        }

        .process-step.complete {
            background: #d9f5e0;
            border-color: #a0d19b;
        }

        .process-step.active {
            background: #fff7d6;
            border-color: #f2d97d;
        }

        .process-step.pending {
            background: #ffe5e5;
            border-color: #f1b0aa;
        }

        .process-arrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            font-size: 1.4rem;
            color: #999;
        }

        .approval-pill {
            border-radius: 999px;
            padding: .45rem .85rem;
            font-size: .87rem;
            font-weight: 700;
            display: inline-block;
        }

        .approval-approved {
            background: #e5f6e8;
            color: #1f6d3a;
        }

        .approval-pending {
            background: #fff3d9;
            color: #8a5f15;
        }

        .approval-not-applicable {
            background: #f0f0f0;
            color: #555;
        }

        .approval-button {
            border: none;
            border-radius: 12px;
            padding: .9rem 1.2rem;
            background: #1e6bff;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            margin-top: .75rem;
        }

        .approval-button:hover {
            background: #1558d0;
        }

        .order-note {
            margin-top: 1rem;
            color: #555;
            font-size: .98rem;
        }

        .design-preview {
            width: 100%;
            height: auto;
            max-height: 420px;
            border-radius: 18px;
            object-fit: contain;
            border: 1px solid #e2e2e2;
        }

        .message-banner {
            border-radius: 16px;
            padding: 1rem 1.25rem;
            background: #e6f6e8;
            color: #1b6430;
            margin-bottom: 1rem;
            border: 1px solid #c3e3cb;
        }

        .variable-list-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .variable-list-table th,
        .variable-list-table td {
            border: 1px solid #e1e1e1;
            padding: .75rem;
            text-align: left;
        }

        .variable-list-table th {
            background: #f7f7f8;
            font-weight: 700;
        }

        @media (max-width: 860px) {
            .order-summary-grid {
                grid-template-columns: 1fr;
            }

            .field-grid {
                grid-template-columns: 1fr;
            }

            .process-flow {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../.Components/SharedComponents/HeaderComponents.php'; ?>
    <div class="order-page">
        <?php if ($error): ?>
            <section class="order-section">
                <h1>Order Status</h1>
                <p class="order-note"><?php echo htmlspecialchars($error); ?></p>
            </section>
        <?php else: ?>
            <section class="order-summary">
                <div class="order-summary-grid">
                    <div>
                        <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;">
                            <h1 style="margin:0;">Order #<?php echo htmlspecialchars($orderData['id']); ?></h1>
                            <span class="status-pill <?php echo formatStatusClass($orderData['status']); ?>"><?php echo htmlspecialchars($orderData['status']); ?></span>
                        </div>
                        <p class="field-value" style="margin:0.5rem 0 1rem; color:#444;">Service: <strong><?php echo htmlspecialchars($orderData['serviceName']); ?></strong> &mdash; Subservice: <strong><?php echo htmlspecialchars($orderData['subserviceName']); ?></strong></p>
                        <div class="field-grid">
                            <div class="field-item"><span class="field-label">Customer</span><span class="field-value"><?php echo htmlspecialchars($orderData['customerName']); ?></span></div>
                            <div class="field-item"><span class="field-label">Due Date</span><span class="field-value"><?php echo $orderData['deadlineAt'] ? htmlspecialchars(date('F j, Y', strtotime($orderData['deadlineAt']))) : 'No due date'; ?></span></div>
                            <div class="field-item"><span class="field-label">Order Date</span><span class="field-value"><?php echo htmlspecialchars(date('F j, Y', strtotime($orderData['createdAt']))); ?></span></div>
                            <div class="field-item"><span class="field-label">Days Running</span><span class="field-value"><?php echo (new DateTime($orderData['createdAt']))->diff(new DateTime())->days; ?> days</span></div>
                        </div>
                    </div>
                    <div>
                        <div class="field-grid">
                            <div class="field-item"><span class="field-label">Total Price</span><span class="field-value"><?php echo formatAmount($orderData['priceTotal']); ?></span></div>
                            <div class="field-item"><span class="field-label">Paid Amount</span><span class="field-value"><?php echo formatAmount($orderData['pricePaid']); ?></span></div>
                            <div class="field-item"><span class="field-label">Outstanding</span><span class="field-value"><?php echo formatAmount(max(0, $orderData['priceTotal'] - $orderData['pricePaid'])); ?></span></div>
                        </div>
                        <div class="order-note">
                            This public page shows your order status and approval progress. If the order is archived, the status is marked complete.
                        </div>
                    </div>
                </div>
            </section>

            <?php if (!empty($message)): ?>
                <div class="message-banner"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if ($orderData['designExists']): ?>
                <section class="order-section">
                    <h2>Design Preview</h2>
                    <div class="order-summary-grid">
                        <div>
                            <img src="../../Storage/Designs/<?php echo htmlspecialchars(rawurlencode($orderData['designImage'])); ?>" alt="Design Preview" class="design-preview">
                        </div>
                        <div>
                            <p class="field-label">Design Approval</p>
                            <span class="approval-pill <?php echo $orderData['designApproved'] ? 'approval-approved' : 'approval-pending'; ?>">
                                <?php echo $orderData['designApproved'] ? 'Approved' : 'Pending Approval'; ?>
                            </span>
                            <?php if (!$orderData['designApproved'] && !$orderData['isArchived']): ?>
                                <form method="POST" class="approval-form">
                                    <input type="hidden" name="action" value="approveDesign">
                                    <button type="submit" class="approval-button">Approve Design</button>
                                </form>
                            <?php endif; ?>
                            <p class="order-note">Approve the uploaded design before we move to production. Refresh the page after approving to see the updated status.</p>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <section class="order-section">
                <h2>Variable List</h2>
                <?php if ($orderData['serviceHasVariableList']): ?>
                    <?php if (!empty($variableList)): ?>
                        <div>
                            <span class="approval-pill <?php echo $variableList['approved'] ? 'approval-approved' : 'approval-pending'; ?>">
                                <?php echo $variableList['approved'] ? 'Approved' : 'Pending Approval'; ?>
                            </span>
                            <?php if (!$variableList['approved'] && !$orderData['isArchived']): ?>
                                <form method="POST" class="approval-form">
                                    <input type="hidden" name="action" value="approveVariableList">
                                    <button type="submit" class="approval-button">Approve Variable List</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <table class="variable-list-table">
                            <thead>
                                <tr>
                                    <?php foreach ($variableList['columns'] as $column): ?>
                                        <th><?php echo htmlspecialchars($column['columnName']); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($variableList['rows'] as $row): ?>
                                    <tr>
                                        <?php foreach ($variableList['columns'] as $column): ?>
                                            <td><?php echo htmlspecialchars($row[$column['id']] ?? ''); ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="order-note">The variable list is being prepared. It will appear here once available.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="approval-pill approval-not-applicable">Not Applicable</span>
                    <p class="order-note">This service does not use a variable list.</p>
                <?php endif; ?>
            </section>

            <section class="order-section">
                <h2>Order Process</h2>
                <?php if (!empty($orderProcesses)): ?>
                    <div class="process-flow">
                        <?php foreach ($orderProcesses as $index => $process): ?>
                            <div class="<?php echo getProcessStateClass($process['status']); ?>">
                                <h3><?php echo htmlspecialchars($process['processName']); ?></h3>
                                <p>Phase <?php echo htmlspecialchars($process['phase']); ?></p>
                                <p>Status: <?php echo htmlspecialchars(ucfirst($process['status'])); ?></p>
                                <p>Assigned: <?php echo htmlspecialchars($process['assignedNum']); ?> user<?php echo $process['assignedNum'] == 1 ? '' : 's'; ?></p>
                            </div>
                            <?php if ($index < count($orderProcesses) - 1): ?>
                                <div class="process-arrow">&gt;</div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="order-note">No process data available for this order.</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>
    <?php include __DIR__ . '/../.Components/SharedComponents/FooterComponents.php'; ?>
    <script src="../.JS/Shared.js"></script>
</body>

</html>