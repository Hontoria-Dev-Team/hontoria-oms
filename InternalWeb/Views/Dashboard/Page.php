<!DOCTYPE html>
<html>

<head>
    <title>Dashboard - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="centerColumnLayout">
        <section class="centerColumnLayout minGap">
            <h1 class="titleLogo tinHeight">
                <img src="../../Shared/Img/Logo.png" alt="logo"> Hontoria OMS
            </h1>
            <section class="box centerColumnLayout roundedMid">
                <h3>Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>!</h3>
                <p>You are logged in as: <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></p>
                <div class="gradientBorderDiag"></div>
            </section>
        </section>
    </main>
</body>

</html>