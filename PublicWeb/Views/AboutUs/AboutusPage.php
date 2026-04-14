<?php
/**
 * AboutusPage.php
 * Location: publicWeb/public/Views/AboutUs/AboutusPage.php
 */

// Basic variables (these would ideally be passed down by PublicC.php in the future)
$siteName = $siteName ?? 'Hontoria Printing Services';
$logoPath = $logoPath ?? '/.Images/logo.jpg';
$fbLink   = $fbLink   ?? 'https://www.facebook.com/jhong.hontoria.3';
$address  = $address  ?? 'Feeder Road 2, Brgy. Tibal-og Santo tomas, Davao del Norte';

// 🟢 UPDATED: Page Links based on your new Views folder structure
$navItems = [
    ['label' => 'HOME',     'url' => '../Home/HomePage.php',         'active' => false],
    ['label' => 'SERVICES', 'url' => '../Services/ServicesPage.php', 'active' => false],
    ['label' => 'ABOUT US', 'url' => 'AboutusPage.php',              'active' => true],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    
    <title>About Us — <?php echo htmlspecialchars($siteName); ?></title>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($logoPath); ?>"/>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    
    <link rel="stylesheet" href="/.CSS/Shared.css"/>
    <link rel="stylesheet" href="/.CSS/AboutUsPage.css"/>
</head>
<body>
    
    <?php include __DIR__ . '/../.Components/SharedComponents/HeaderComponents.php'; ?>
    
    <main>
        <section class="about-container">
            <h1>About Us</h1>
            <p>Welcome to Hontoria Printing Services.</p>
        </section>
    </main>
    
    <?php include __DIR__ . '/../.Components/SharedComponents/FooterComponents.php'; ?>
    
    <script src="/.JS/Shared.js"></script>
    <script src="/.JS/AboutUsPage.js"></script>
    
</body>
</html>