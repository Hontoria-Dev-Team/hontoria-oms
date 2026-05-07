<!DOCTYPE html>
<html>

<head>
    <title>Dashboard - Hontoria OMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="centerColumnLayout">
        <section class="centerColumnLayout minGap">
            <h1 class="titleLogo tinHeight">
                <img src="../../Shared/Img/Logo.png" alt="logo"> Hontoria OMS
            </h1>
            <!-- All dynamic output is properly escaped with htmlspecialchars -->
            <section class="box centerColumnLayout roundedMid">
                <h3>Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?>!</h3>
                <p>You are logged in as: <?php echo htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="gradientBorderDiag"></div>
            </section>
        </section>
        <?php include("../Views/.Components/MessageBox.php"); ?>
    </main>
</body>

</html>