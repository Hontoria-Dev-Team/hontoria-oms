<!DOCTYPE html>
<!-- Back navigation link using dynamic page references.
     Both values are properly escaped to prevent XSS. -->
<a href="<?= htmlspecialchars($backLink, ENT_QUOTES, 'UTF-8') ?>"
    class="centerHoriRowLayout minGap importantInput roundedMid tinHeight fitWidth regPadding capitalFirst minFont shadowed">
    <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors mirrorX">
    <!-- REVIEW: Ensure a space is intended between $lastPage and "Page".
         If not, "StaffPage" may be a typo. -->
    <h3><?= htmlspecialchars($lastPage, ENT_QUOTES, 'UTF-8') ?> Page</h3>
</a>