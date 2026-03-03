<!DOCTYPE html>
<html>

<head>
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/LoginPage.css">
</head>

<body class="centerColumnLayout midGap">
    <?php if (isset($error) && $error): ?>
        <div class="errorMessage centerColumnLayout">
            <h3>Error: <?php echo htmlspecialchars($error); ?></h3>
        </div>
    <?php endif; ?>

    <div class="gradientBorderDiag roundedBorder">
        <section id="loginBox" class="centerColumnLayout box roundedBorder">
            <?php include("../Views/.Components/TitleLogo.php"); ?>
            <h3>Login to your account</h3>
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
                <input type="submit" name="submit" value="Login" class="fullWidth">
            </form>
        </section>
    </div>
</body>

</html>
