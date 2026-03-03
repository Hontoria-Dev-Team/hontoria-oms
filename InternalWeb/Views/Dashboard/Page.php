<!DOCTYPE html>
<html>

<head>
    <title>Dashboard - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../CSS/Index.css">
</head>

<body class="asideLayout">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="centerColumnLayout midGap">
        <h1 class="titleLogo">
            <img src="../../Shared/Img/Logo.png" alt="logo"> Hontoria OMS
        </h1>

        <div class="gradientBorderDiag roundedBorder">
            <section class="box centerColumnLayout roundedBorder">
                <h3>Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>!</h3>
                <p>You are logged in as: <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></p>
                <hr>
                <a href="index.php?page=logout" class="fullWidth">Logout</a>
            </section>
        </div>
    </main>
</body>

</html>
