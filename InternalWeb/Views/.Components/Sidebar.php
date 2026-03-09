<!DOCTYPE html>
<div class="gradientBorderVert">
    <aside id="sidebar" class="columnLayout minGap">
        <?php include("TitleLogo.php"); ?>
        <nav class="columnLayout midGap">
            <a href="index.php?page=dashboard" class="iconLink centerRowLayout minGap <?= ($page === 'dashboard') ? 'active' : '' ?>">
                <img src="../../Shared/Img/4BoxIcon.png" alt="4 Boxes">
                <span>Dashboard</span>
            </a>
            <?php if (in_array('canManageStaff', $_SESSION['permissions'])): ?>
                <a href="index.php?page=staff" class="iconLink centerRowLayout minGap <?= ($page === 'staff') ? 'active' : '' ?>">
                    <img src="../../Shared/Img/PeopleIcon.png" alt="People">
                    <span>Staff</span>
                </a>
            <?php endif; ?>
        </nav>
        <div id="sidebarFooter" class="<?= ($page === 'account') ? 'active' : '' ?>">
            <a href="index.php?page=account" class="iconLink centerRowLayout minGap">
                <img src="../../Shared/Img/PersonIcon.png" alt="Person">
                <span class="capitalFirst"><?= $_SESSION['username'] ?></span>
            </a>
        </div>
    </aside>
</div>
