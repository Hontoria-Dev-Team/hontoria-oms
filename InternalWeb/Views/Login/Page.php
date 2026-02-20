<!DOCTYPE html>
<html>

<head>
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/Index.css">
</head>

<body class="centerColumnLayout midGap">
    <h1 class="titleLogo">
        <img src="../../Shared/Img/Logo.png" alt="logo"> Hontoria OMS
    </h1>
    <div class="gradientBorder">
        <section id="loginBox" class="centerColumnLayout box">
            <h3>Login to your account</h3>
            <hr>
            <form method="POST" action="loginPage.php" class="centerColumnLayout minGap fullWidth">
                <div class="fullWidth columnLayout">
                    <label for="name" class="leftStart">Name</label>
                    <input type="text" name="name" required="true">
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