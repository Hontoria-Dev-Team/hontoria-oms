<?php if (isset($_SESSION['message']) && $_SESSION['message']): ?>
    <?php
    $bgClass = "darkFadedBG";
    $borderClass = "bordered";

    if (str_starts_with($_SESSION['message'], 'Error:')) {
        $bgClass = "redTransBG";
        $borderClass = "redBorder";
    } else if (str_starts_with($_SESSION['message'], 'Success:')) {
        $bgClass = "yellowTransBG";
        $borderClass = "yellowBorder";
    }
    ?>
    <div class="centerColumnLayout regPadding topZ <?= $bgClass ?> <?= $borderClass ?> shadowed" id="messageBox">
        <h3><?php echo htmlspecialchars($_SESSION['message']); ?></h3>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>