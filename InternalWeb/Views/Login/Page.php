<!DOCTYPE html>
<html>

<head>
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/LoginPage.css">
</head>

<body class="centerColumnLayout midGap">
    <?php include("../Views/.Components/ErrorBox.php"); ?>
    <div id="loginBox" class="gradientBorderDiag roundedMid centerHoriRowLayout">
        <div class="flexMin centerColumnLayout">
            <h3>Welcome Employee, please login to your account</h3>
        </div>
        <section class="centerColumnLayout flexMin fullHeight">
            <?php include("../Views/.Components/TitleLogo.php"); ?>
            <h3>Input credentials below</h3>
            <hr>
            <form method="POST" action="index.php?page=login&action=authenticate" class="centerColumnLayout minGap fullWidth">
                <div class="fullWidth columnLayout">
                    <label for="name" class="leftStart">Name</label>
                    <input type="text" name="name" required="true" value="<?php echo htmlspecialchars($username ?? ''); ?>">
                </div>
                <div class="fullWidth columnLayout">
                    <label for="password" class="leftStart">Password</label>
                    <input type="password" name="password" required="true">
                </div>
                <input type="submit" name="submit" value="Login" class="fullWidth importantInput">
            </form>
            <div class="box"></div>
        </section>
    </div>
</body>

</html>