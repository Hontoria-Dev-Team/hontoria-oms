<!DOCTYPE html>
<html>

<head>
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../CSS/Index.css">
</head>

<body class="centerColumnLayout midGap">
    <h1 class="titleLogo">
        <img src="../../Shared/Img/Logo.png" alt="logo"> Hontoria OMS
    </h1>
    <div class="gradientBorder">
        <section id="loginBox" class="centerColumnLayout box">
            <h3>Login to your account</h3>
            <hr>

            <?php if (isset($error) && $error): ?>
                <div class="error-message" style="color: red; text-align: center; margin-bottom: 10px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

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