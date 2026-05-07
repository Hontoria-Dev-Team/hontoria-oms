<?php
/**
 * OrderVariableListComponents.php
 * Renders: variable list table + approval status + form.
 * Expects: $orderData, $variableList
 * Only included when $orderData['serviceHasVariableList'] is truthy.
 */
?>

<div class="op-card op-card-full">

    <div class="op-card-head">
        <i class="fas fa-table-list op-card-icon"></i>
        <span>Variable List</span>
        <?php if (!empty($variableList)): ?>
            <span class="op-approval-pill <?php echo $variableList['approved'] ? 'pill-approved' : 'pill-pending'; ?>">
                <?php echo $variableList['approved'] ? 'Approved' : 'Pending'; ?>
            </span>
        <?php endif; ?>
    </div>

    <?php if (!empty($variableList)): ?>

        <?php if (!$variableList['approved'] && empty($orderData['isArchived'])): ?>
            <form method="POST" class="op-approval-form" style="margin-bottom: 1.25rem;">
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
        </div>

    <?php else: ?>

        <p class="op-note">
            <i class="fas fa-hourglass-half"></i>
            The variable list is being prepared and will appear here once available.
        </p>

    <?php endif; ?>

</div>