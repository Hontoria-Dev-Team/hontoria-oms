<?php
/**
 * ServicesPage.php
 * Location: publicWeb/public/Views/Services/ServicesPage.php
 */

// Basic variables (these would ideally be passed down by PublicC.php in the future)
$siteName = $siteName ?? 'Hontoria Printing Services';
$logoPath = $logoPath ?? '/.Images/logo.jpg';
$fbLink   = $fbLink   ?? 'https://www.facebook.com/jhong.hontoria.3';
$address  = $address  ?? 'Feeder Road 2, Brgy. Tibal-og Santo tomas, Davao del Norte';

// This would normally be passed from the Controller (PublicC.php)
$products = $products ?? [];

// 🟢 UPDATED: Page Links based on your new Views folder structure
$navItems = [
    ['label' => 'HOME',     'url' => '../Home/HomePage.php',       'active' => false],
    ['label' => 'SERVICES', 'url' => 'ServicesPage.php',           'active' => true],
    ['label' => 'ABOUT US', 'url' => '../AboutUs/AboutusPage.php', 'active' => false],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    
    <title>Services — <?php echo htmlspecialchars($siteName); ?></title>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($logoPath); ?>"/>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    
    <link rel="stylesheet" href="../.CSS/Shared.css"/>
    <link rel="stylesheet" href="../.CSS/ServicesPage.css"/>
</head>
<body>
    
    <?php include __DIR__ . '/../.Components/SharedComponents/HeaderComponents.php'; ?>
    
    <div class="page-body">
        
        <aside class="services-sidebar">
            </aside>
        
        <main class="services-content">
            </main>
        
    </div>
    
    <?php include __DIR__ . '/../.Components/SharedComponents/FooterComponents.php'; ?>
    
    <div class="services-modal-wrapper">
         </div>
    
    <script src="../.JS/Shared.js"></script>
    <script src="../.JS/ServicesPage.js"></script>
    
</body>
</html>