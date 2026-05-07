<?php
// Define shorthand escape helper if it doesn't already exist.
// This ensures every dynamic value is safely output against XSS.
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}
?>

<?php if (isset($_SESSION['message']) && $_SESSION['message']): ?>
    <?php
    // Determine styling based on message type (success or error)
    $bgClass = "darkFadedBG";
    $borderClass = "bordered";

    if (str_starts_with($_SESSION['message'], 'Error:')) {
        $bgClass = "lightRedBG";
        $borderClass = "redBorder";
    } else if (str_starts_with($_SESSION['message'], 'Success:')) {
        $bgClass = "lightYellowBG";
        $borderClass = "yellowBorder";
    }
    ?>
    <div class="centerColumnLayout regPadding topZ <?= e($bgClass) ?> <?= e($borderClass) ?> shadowed" id="messageBox">
        <h3 class="whiteText outlineText"><?= e($_SESSION['message']) ?></h3>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>