<!DOCTYPE html>
<?php if (isset($error) && $error): ?>
    <div class="errorMessage centerColumnLayout">
        <h3>Error: <?php echo htmlspecialchars($error); ?></h3>
    </div>
<?php endif; ?>