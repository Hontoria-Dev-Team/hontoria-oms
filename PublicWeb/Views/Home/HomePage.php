<?php
// XSS escape helper – define once across the application
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}

/**
 * HomePage.php
 */
$siteName = 'Hontoria Printing Services';
$logoPath = '../.Images/Logo.png';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title><?= e($siteName) ?></title>
    <!-- The logo path is now properly escaped -->
    <link rel="icon" href="<?= e($logoPath) ?>" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <link rel="stylesheet" href="../.CSS/Shared.css" />
    <link rel="stylesheet" href="../.CSS/HomePage.css" />
    <link rel="stylesheet" href="../.CSS/AboutUsPage.css" />
</head>

<body>

    <?php include __DIR__ . '/../.Components/SharedComponents/HeaderComponents.php'; ?>

    <main>
        <?php include __DIR__ . '/../.Components/HomeComponents/HeroComponents.php'; ?>
        <?php include __DIR__ . '/../.Components/HomeComponents/WhyUsComponents.php'; ?>
        <?php include __DIR__ . '/../.Components/HomeComponents/MissionVisionComponents.php'; ?>
    </main>

    <?php include __DIR__ . '/../.Components/SharedComponents/FooterComponents.php'; ?>

    <script src="../.JS/Shared.js"></script>
    <script src="../.JS/HomePage.js"></script>

    <script>
        // Mark the current page as active in navigation
        document.addEventListener("DOMContentLoaded", function() {
            const navLinks = document.querySelectorAll('.nav-link, .mob-link');
            navLinks.forEach(link => {
                if (link.getAttribute('href') === '?page=home') {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        });
    </script>
</body>

</html>