<?php
// XSS escape helper (define only once across the application)
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}
?>
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
        'permission' => 'canViewServicesPage',
    ],
    'orders' => [
        'label' => 'Orders',
        'icon'  => '../../Shared/Img/ListIcon.png',
        'permission' => 'canViewOrdersPage',
    ],
    'tasks' => [
        'label' => 'Tasks',
        'icon'  => '../../Shared/Img/CheckBoxIcon.png',
        'permission' => 'canTakeTasks',
    ],
    'staff' => [
        'label' => 'Staff',
        'icon'  => '../../Shared/Img/PeopleIcon.png',
        'permission' => 'canViewStaffPage',
    ],
    'inventory' => [
        'label' => 'Inventory',
        'icon'  => '../../Shared/Img/BoxIcon.png',
        'permission' => 'canViewInventory',
    ],
    'sales' => [
        'label' => 'Sales',
        'icon'  => '../../Shared/Img/PesoIcon.png',
        'permission' => 'canViewSales',
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
                // Skip links for which the user lacks the required permission
                if ($linkData['permission'] !== null && !in_array($linkData['permission'], $_SESSION['permissions'])) {
                    continue;
                }

                $active = ($page === $linkPage) ? 'active' : '';
                ?>
                <a href="index.php?page=<?= e($linkPage) ?>"
                    class="iconLink centerHoriRowLayout minGap <?= e($active) ?>">
                    <img src="<?= e($linkData['icon']) ?>" alt="<?= e($linkData['label']) ?>">
                    <span><?= e($linkData['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <div id="sidebarFooter" class="<?= ($page === 'account') ? e('active') : '' ?>">
            <a href="index.php?page=account" class="iconLink centerHoriRowLayout minGap">
                <img src="../../Shared/Img/PersonIcon.png" alt="Person">
                <span class="capitalFirst"><?= e($_SESSION['username']) ?></span>
            </a>
        </div>
        <div class="titleLogo tinHeight selfCenter whiteText clickable" id="menuMobile">
            Menu <img src="../../Shared/Img/3DotsVertIcon.png" alt="3DotsVert" class="invertColors">
        </div>
    </section>
    <div class="gradientBorderVert"></div>
</aside>

<div id="mobileMenuPanel" class="mobileMenuPanel" aria-hidden="true">
    <div class="mobileMenuHeader">
        <h2>Menu</h2>
        <button type="button" id="closeMobileMenu" aria-label="Close menu">✕</button>
    </div>
    <nav class="columnLayout midGap">
        <?php foreach ($navLinks as $linkPage => $linkData): ?>
            <?php
            if ($linkData['permission'] !== null && !in_array($linkData['permission'], $_SESSION['permissions'])) {
                continue;
            }

            $active = ($page === $linkPage) ? 'active' : '';
            ?>
            <a href="index.php?page=<?= e($linkPage) ?>"
                class="iconLink centerHoriRowLayout minGap <?= e($active) ?>">
                <img src="<?= e($linkData['icon']) ?>" alt="<?= e($linkData['label']) ?>">
                <span><?= e($linkData['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    <div id="mobileSidebarFooter">
        <a href="index.php?page=account" class="iconLink centerHoriRowLayout minGap">
            <img src="../../Shared/Img/PersonIcon.png" alt="Person">
            <span class="capitalFirst"><?= e($_SESSION['username']) ?></span>
        </a>
    </div>
</div>

<div id="sidebarOverlay"></div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuPanel = document.getElementById('mobileMenuPanel');
        const menuMobile = document.getElementById('menuMobile');
        const closeMobileMenu = document.getElementById('closeMobileMenu');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function closeMobileMenuPanel() {
            if (!mobileMenuPanel) return;
            mobileMenuPanel.classList.remove('open');
            mobileMenuPanel.setAttribute('aria-hidden', 'true');
            if (sidebarOverlay) sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        function openMobileMenuPanel() {
            if (!mobileMenuPanel) return;
            mobileMenuPanel.classList.add('open');
            mobileMenuPanel.setAttribute('aria-hidden', 'false');
            if (sidebarOverlay) sidebarOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        if (menuMobile) menuMobile.addEventListener('click', openMobileMenuPanel);
        if (closeMobileMenu) closeMobileMenu.addEventListener('click', closeMobileMenuPanel);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeMobileMenuPanel);
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') closeMobileMenuPanel();
        });
    });
</script>