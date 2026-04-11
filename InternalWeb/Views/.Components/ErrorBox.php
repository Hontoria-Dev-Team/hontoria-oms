<?php if (isset($_SESSION['error']) && $_SESSION['error']): ?>
    <div class="errorMessage centerColumnLayout regPadding topZ">
        <h3>Error: <?php echo htmlspecialchars($_SESSION['error']); ?></h3>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>