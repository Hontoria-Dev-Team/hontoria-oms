<!DOCTYPE html>
<html>

<head>
    <title>Tasks Panel - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <h1 class="titleLogo minGap tinHeight">
            <img src="../../Shared/Img/CheckBoxIcon.png" alt="CheckBox"> Tasks Panel
        </h1>
        <?php include("../Views/.Components/ErrorBox.php"); ?>
        <section class="rowLayout flexMax midGap">
            <section class="flexMid roundedMid centerColumnLayout">
                <div class="columnLayout minGap box roundedMid fullHeight fullWidth">
                    <h3>Available Tasks</h3>
                    <div class="gridFlex minGrids minGap scrollable flexMax noFlexBasis noMinHeight contentFlexStart">
                        <div class="darkFadedBG midHeight"></div>
                        <div class="darkFadedBG midHeight"></div>
                        <div class="darkFadedBG midHeight"></div>
                        <div class="darkFadedBG midHeight"></div>
                        <div class="darkFadedBG midHeight"></div>
                        <div class="darkFadedBG midHeight"></div>
                        <div class="darkFadedBG midHeight"></div>
                        <div class="darkFadedBG midHeight"></div>
                        <div class="darkFadedBG midHeight"></div>
                        <div class="darkFadedBG midHeight"></div>
                        <div class="darkFadedBG midHeight"></div>
                    </div>
                </div>
                <div class="gradientBorderDiag"></div>
            </section>
            <section class="columnLayout midGap flexMid">
                <section class="box centerColumnLayout roundedMid minGap flexMax">
                    <div class="fullDimensions columnLayout minGap">
                        <h3>Assigned Tasks</h3>
                        <div class="columnLayout minGap scrollable flexMax noFlexBasis noMinHeight contentFlexStart">
                            <div class="yellowTransBG columnLayout tinGap regPadding roundedMin">
                                <div class="centerHoriRowLayout minGap">
                                    <div class="flexMax">
                                        <h3>Order #6</h3>
                                        <div class="centerHoriRowLayout minGap">
                                            <div class="flexMax columnLayout">
                                                <b>Service: Jersey Sublimation</b>
                                                <b>Task: Designing</b>
                                                <b>Customer: Rheyan Remendia</b>
                                            </div>
                                            <div class="flexMax columnLayout">
                                                <b>Due In: <span class="dueInText" data-due-date="2026-03-31 00:00:00">4d 2h (March 31, 2026)</span></b>
                                                <b class="centerHoriRowLayout tinGap">Assigned: 2/3 <img src="../../Shared/Img/PersonIcon.png" alt="Person" class="unitHeight"></b>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="tinHeight squareSize regMinPadding blueBG roundedMin centerColumnLayout circle">
                                        <img src="../../Shared/Img/MessengerIcon.png" alt="Messenger" class="invertColors">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
                <section class="box centerColumnLayout roundedMid minGap flexMid">
                    <div class="fullDimensions rowLayout minGap">
                        <div class="columnLayout tinGap flexMid">
                            <h3>Assigned to Task:</h3>
                            <b class="columnLayout scrollable flexMax noFlexBasis noMinHeight">
                                <span class="indentText yellowText">Josh Rabia - ✓</span>
                                <span class="indentText yellowText">John Hempon - ✓</span>
                                <span class="indentText redText">Ace Galves - X</span>
                            </b>
                        </div>
                        <div class="columnLayout tinGap flexMax">
                            <h3>Tasks Objectives</h3>
                        </div>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
            </section>
        </section>
    </main>
    <?php include("../Views/.Components/ConfirmationBox.php"); ?>
</body>
<script src="../.JS/ConfirmationBox.js"></script>
<script>

</script>

</html>