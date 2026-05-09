<?php
// XSS escape helper – define once across the application
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}

/**
 * AboutusPage.php
 * Location: publicWeb/public/Views/AboutUs/AboutusPage.php
 */

// Basic variables
$siteName = $siteName ?? 'Hontoria Printing Services';
$logoPath = $logoPath ?? '../.Images/Logo.png';
$fbLink   = $fbLink   ?? 'https://www.facebook.com/jhong.hontoria.3';
$address  = $address  ?? 'Feeder Road 2, Brgy. Tibal-og Santo tomas, Davao del Norte';

// Page Links
$navItems = [
    ['label' => 'HOME',     'url' => '../Home/HomePage.php',        'active' => false],
    ['label' => 'SERVICES', 'url' => '../Services/ServicesPage.php', 'active' => false],
    ['label' => 'ABOUT US', 'url' => 'AboutusPage.php',              'active' => true],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>About Us — <?= e($siteName) ?></title>
    <link rel="icon" type="image/png" href="<?= e($logoPath) ?>" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <link rel="stylesheet" href="../.CSS/Shared.css" />
    <link rel="stylesheet" href="../.CSS/AboutUsPage.css" />
</head>

<body>

    <?php include __DIR__ . '/../.Components/SharedComponents/HeaderComponents.php'; ?>

    <main>
        <?php include __DIR__ . '/../.Components/AboutUsComponents/HeroSectionComponents.php'; ?>

        <section class="about-container">

            <?php include __DIR__ . '/../.Components/AboutUsComponents/AboutUsSidebarComponents.php'; ?>

            <div class="about-content">
                <?php include __DIR__ . '/../.Components/AboutUsComponents/HistoryComponents.php'; ?>
                <?php include __DIR__ . '/../.Components/AboutUsComponents/LocationComponents.php'; ?>
                <?php include __DIR__ . '/../.Components/AboutUsComponents/WorkPlaceComponents.php'; ?>
                <?php include __DIR__ . '/../.Components/AboutUsComponents/OwnerComponents.php'; ?>
            </div>

        </section>
    </main>

    <?php include __DIR__ . '/../.Components/SharedComponents/FooterComponents.php'; ?>

    <script src="../.JS/Shared.js"></script>
    <script src="../.JS/AboutUsPage.js"></script>

</body>

</html>