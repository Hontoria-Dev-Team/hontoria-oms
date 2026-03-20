<!DOCTYPE html>
<?php
$navLinks = [
    'dashboard' => [
        'label' => 'Dashboard',
        'icon'  => '../../Shared/Img/4BoxIcon.png',
        'permission' => null,
    ],
    'services' => [
        'label' => 'Services',
        'icon'  => '../../Shared/Img/GearIcon.png',
        'permission' => 'canViewServiceList',
    ],
    'orders' => [
        'label' => 'Orders',
        'icon'  => '../../Shared/Img/ListIcon.png',
        'permission' => 'canViewOrders',
    ],
    'staff' => [
        'label' => 'Staff',
        'icon'  => '../../Shared/Img/PeopleIcon.png',
        'permission' => 'canViewStaffList',
    ],
];
?>
<aside id="sidebar" class="columnLayout">
    <section class="columnLayout minGap flexMax">
        <h1 class="titleLogo tinHeight selfCenter">
            <img src="../../Shared/Img/Logo.png" alt="logo"> Hontoria OMS
        </h1>
        <nav class="columnLayout midGap">
            <?php foreach ($navLinks as $linkPage => $linkData): ?>
                <?php
                if ($linkData['permission'] !== null && !in_array($linkData['permission'], $_SESSION['permissions'])) {
                    continue;
                }

                $active = ($page === $linkPage) ? 'active' : '';
                ?>
                <a href="index.php?page=<?= $linkPage ?>"
                    class="iconLink centerHoriRowLayout minGap <?= $active ?>">
                    <img src="<?= $linkData['icon'] ?>" alt="<?= $linkData['label'] ?>">
                    <span><?= $linkData['label'] ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <div id="sidebarFooter" class="<?= ($page === 'account') ? 'active' : '' ?>">
            <a href="index.php?page=account" class="iconLink centerHoriRowLayout minGap">
                <img src="../../Shared/Img/PersonIcon.png" alt="Person">
                <span class="capitalFirst"><?= $_SESSION['username'] ?></span>
            </a>
        </div>
    </section>
    <div class="gradientBorderVert"></div>
</aside>