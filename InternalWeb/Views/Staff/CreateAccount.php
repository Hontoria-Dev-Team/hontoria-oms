<!DOCTYPE html>
<html>

<head>
    <title>Account Creation - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/StaffPage.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <?php include("../Views/.Components/BackLink.php"); ?>
        <?php include("../Views/.Components/ErrorBox.php"); ?>
        <section class="centerColumnLayout">
            <section class="centerColumnLayout extraWidth roundedMid">
                <div class="box roundedMid maxHeight centerColumnLayout">
                    <h1 class="titleLogo minGap tinHeight selfCenter">
                        <img src="../../Shared/Img/PeopleIcon.png" alt="People"> Staff Creation
                    </h1>
                    <form method="POST" action="index.php?page=staff&action=createFinal" class="centerColumnLayout minGap fullWidth">
                        <div class="fullWidth columnLayout">
                            <label for="username" class="leftStart">Username (Unique)</label>
                            <input type="text" name="username" required="true">
                        </div>
                        <div class="fullWidth">
                            <h3 class="leftStart">Personal Details</h3>
                            <div class="triItemLayout minGap rowLayout">
                                <div>
                                    <label for="firstName" class="leftStart">First Name</label>
                                    <input type="text" name="firstName" required="true" class="fullWidth" value="<?php echo htmlspecialchars($firstName ?? ''); ?>">
                                </div>
                                <div>
                                    <label for="middleName" class="leftStart">Middle Name</label>
                                    <input type="text" name="middleName" class="fullWidth" value="<?php echo htmlspecialchars($middleName ?? ''); ?>">
                                </div>
                                <div>
                                    <label for="lastName" class="leftStart">Last Name</label>
                                    <input type="text" name="lastName" required="true" class="fullWidth" value="<?php echo htmlspecialchars($lastName ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="fullWidth">
                            <h3 class="leftStart">Contact Information</h3>
                            <div class="triItemLayout minGap rowLayout">
                                <div>
                                    <label for="phoneNum" class="leftStart">Phone Number</label>
                                    <input type="tel" name="phoneNum" required="true" placeholder="09171234567" pattern="^09\d{9}$" class="fullWidth"
                                        value="<?php echo htmlspecialchars($phoneNum ?? ''); ?>">
                                </div>
                                <div class="flexMax columnLayout">
                                    <label for="emailAddress" class="leftStart flexMax">Email Address</label>
                                    <input type="email" name="emailAddress" required="true" value="<?php echo htmlspecialchars($emailAddress ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        <input type="submit" name="submit" value="Create Account" class="fullWidth importantInput">
                    </form>
                </div>
                <div class="gradientBorderDiag"></div>
            </section>
        </section>
    </main>
</body>

</html>