<?php
/**
 * OrderDesignComponents.php
 * Renders: design image preview, zoom button, approval status + form.
 * Expects: $orderData
 * Only included when $orderData['designExists'] is truthy.
 */

$orderData = $orderData ?? [];
$orderData += [
    'designApproved' => false,
    'designExists'   => false,
    'isArchived'     => false,
    'designImage'    => '',
];
?>

<div class="op-card op-card-full">

    <div class="op-card-head">
        <div class="op-card-icon"><i class="fas fa-image"></i></div>
        <span>Design Preview</span>
        <span class="op-approval-pill <?php echo $orderData['designApproved'] ? 'pill-approved' : 'pill-pending'; ?>">
            <?php echo $orderData['designApproved'] ? 'Approved' : 'Pending'; ?>
        </span>
    </div>

    <div class="op-design-wrap">

        <!-- Image -->
        <div class="op-design-img-wrap">
            <img src="../../Storage/Designs/<?php echo htmlspecialchars(rawurlencode($orderData['designImage'])); ?>"
                 alt="Design Preview"
                 class="op-design-img"
                 id="designImg" />
            <button class="op-zoom-btn" id="zoomBtn" title="View full size">
                <i class="fas fa-expand"></i>
            </button>
        </div>

        <!-- Actions -->
        <div class="op-design-actions">

            <div class="op-design-status">
                <span class="op-info-label">Design Approval</span>
                <span class="op-approval-pill <?php echo $orderData['designApproved'] ? 'pill-approved' : 'pill-pending'; ?>"
                      style="margin-left: 0; margin-top: .4rem; display: inline-block;">
                    <?php echo $orderData['designApproved'] ? 'Approved' : 'Pending Approval'; ?>
                </span>
            </div>

            <p class="op-note">
                <i class="fas fa-circle-info"></i>
                Review the uploaded design carefully before approving. Once approved, we will proceed to production. Refresh the page after approving to see the updated status.
            </p>

            <?php if (!$orderData['designApproved'] && !$orderData['isArchived']): ?>
                <form method="POST" class="op-approval-form">
                    <input type="hidden" name="action" value="approveDesign">
                    <button type="submit" class="op-btn op-btn-primary">
                        <i class="fas fa-check"></i> Approve Design
                    </button>
                </form>
            <?php endif; ?>

        </div>

    </div>

</div>