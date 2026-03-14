<!DOCTYPE html>
<html>

<head>
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/StaffPage.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout">
        <?php include("../Views/.Components/ErrorBox.php"); ?>
        <h1 class="titleLogo minGap tinHeight">
            <img src="../../Shared/Img/PersonIcon.png" alt="Person"> Account Settings
        </h1>
        <div class="centerColumnLayout midGap flexMax">
            <section class="centerColumnLayout box roundedMid extraWidth centerHoriSelf">
                <form method="POST" action="index.php?page=account&action=rename" class="centerColumnLayout minGap fullWidth">
                    <div class="fullWidth columnLayout">
                        <h3 class="leftStart">Change Username</h3>
                        <div class="rowLayout minGap">
                            <input type="text" name="username" required="true" placeholder="<?php echo $_SESSION['username']; ?>" class="flexMax">
                            <input type="submit" name="submit" value="Update" class="fullWidth importantInput flexMin">
                        </div>
                    </div>
                </form>
                <div class="gradientBorderDiag"></div>
            </section>
            <section class="centerColumnLayout box roundedMid extraWidth centerHoriSelf">
                <form method="POST" action="index.php?page=account&action=updateContacts" class="centerColumnLayout minGap fullWidth">
                    <div class="fullWidth">
                        <h3 class="leftStart">Change Contact Information</h3>
                        <div class="triItemLayout minGap rowLayout">
                            <div>
                                <label for="phoneNum" class="leftStart">Phone Number</label>
                                <input type="tel" name="phoneNum" placeholder="<?php echo $_SESSION['phoneNumber']; ?>" pattern="^09\d{9}$" class="fullWidth"
                                    value="<?php echo htmlspecialchars($phoneNum ?? ''); ?>">
                            </div>
                            <div class="flexMax columnLayout">
                                <label for="emailAddress" class="leftStart flexMax">Email Address</label>
                                <input type="email" name="emailAddress" placeholder="<?php echo $_SESSION['email']; ?>">
                            </div>
                        </div>
                    </div>
                    <input type="submit" name="submit" value="Update" class="fullWidth importantInput">
                </form>
                <div class="gradientBorderDiag"></div>
            </section>
            <section class="centerColumnLayout box roundedMid extraWidth centerHoriSelf">
                <form method="POST" action="index.php?page=account&action=changePassword" class="centerColumnLayout minGap fullWidth">
                    <div class="fullWidth">
                        <h3 class="leftStart">Change Password</h3>
                        <p class="marginMicro">Passwords must have at least 8 characters, and must contain a number, alphabet, and symbol.</p>
                        <div class="triItemLayout minGap rowLayout">
                            <div class="fullWidth columnLayout">
                                <label for="passwordCurrent" class="leftStart">Current Password</label>
                                <input type="password" name="passwordCurrent" required="true">
                            </div>
                            <div class="fullWidth columnLayout">
                                <label for="passwordNew" class="leftStart">New Password</label>
                                <input type="password" name="passwordNew" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$" required>
                            </div>
                            <div class="fullWidth columnLayout">
                                <label for="passwordRetype" class="leftStart">Retype Password</label>
                                <input type="password" name="passwordRetype" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$" required>
                            </div>
                        </div>
                    </div>
                    <input type="submit" name="submit" value="Change Password" class="fullWidth importantInput">
                </form>
                <div class="gradientBorderDiag"></div>
            </section>
            <div class="extraWidth rowLayout">
                <section class="centerColumnLayout box roundedMid">
                    <a href="index.php?page=logout" class="fullWidth criticalInput roundedMin minPadding midWidth centerColumnLayout">Logout</a>
                    <div class="gradientBorderDiag"></div>
                </section>
            </div>
        </div>
    </main>
</body>

</html>