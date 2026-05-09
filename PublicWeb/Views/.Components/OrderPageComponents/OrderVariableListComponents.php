<?php
// XSS escape helper – define once across the application
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}

/**
 * OrderVariableListComponents.php
 * Renders: variable list table + approval status + form.
 * Expects: $orderData, $variableList
 * Only included when $orderData['serviceHasVariableList'] is truthy.
 */
?>

<div class="op-card op-card-full">

    <div class="op-card-head">
        <div class="op-card-icon">
            <i class="fas fa-table-list"></i>
        </div>
        <span>Variable List</span>
        <?php if (!empty($variableList)): ?>
            <!-- Status pill: class and text are hardcoded based on boolean, safe -->
            <span class="op-approval-pill <?= $variableList['approved'] ? 'pill-approved' : 'pill-pending' ?>">
                <?= $variableList['approved'] ? 'Approved' : 'Pending' ?>
            </span>
        <?php endif; ?>
    </div>

    <?php if (!empty($variableList)): ?>

        <?php if (!$variableList['approved'] && empty($orderData['isArchived'])): ?>
            <form method="POST" class="op-approval-form" style="margin-bottom: 1.25rem;">
                <?php echo CsrfM::getTokenField(); ?>
                <input type="hidden" name="action" value="approveVariableList">
                <button type="submit" class="op-btn op-btn-primary">
                    <i class="fas fa-check"></i> Approve Variable List
                </button>
            </form>
        <?php endif; ?>

        <div class="op-table-wrap">
            <table class="op-table">
                <thead>
                    <tr>
                        <?php foreach ($variableList['columns'] as $column): ?>
                            <!-- Column name escaped to prevent XSS -->
                            <th><?= e($column['columnName']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($variableList['rows'] as $row): ?>
                        <tr>
                            <?php foreach ($variableList['columns'] as $column): ?>
                                <!-- Cell value escaped to prevent XSS -->
                                <td><?= e($row[$column['id']] ?? '') ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>

        <p class="op-note">
            <i class="fas fa-hourglass-half"></i>
            The variable list is being prepared and will appear here once available.
        </p>

    <?php endif; ?>

</div>