<!DOCTYPE html>
<html>

<head>
    <title>Orders Panel - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <h1 class="titleLogo minGap tinHeight">
            <img src="../../Shared/Img/ListIcon.png" alt="List"> Orders Panel
        </h1>
        <section class="rowLayout flexMax midGap">
            <section class="centerColumnLayout roundedMid flexMid">
                <div class="box fullHeight fullWidth roundedMid columnLayout minGap">
                    <form method="GET" action="index.php?page=staff" class="rowLayout fullWidth minGap">
                        <input type="hidden" name="page" value="orders">
                        <input type="hidden" name="action" value="filter">

                        <div class="iconInput flexMax centerHoriRowLayout">
                            <input type="search" name="search" placeholder="Search Order" class="fullWidth" value="<?= $search ?>">
                            <img src="../../Shared/Img/MagnifierIcon.png" alt="Magnifier">
                        </div>

                        <select name="status">
                            <option value="" <?= $status === '' ? 'selected' : '' ?>>Any Status</option>
                            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="idle" <?= $status === 'idle' ? 'selected' : '' ?>>Idle</option>
                        </select>

                        <input type="submit" value="Search" class="importantInput">
                    </form>
                    <section class="minGap gridFlexMid scrollable" id="orderList">
                        <?php foreach ($orderList as $order): ?>
                            <div class="midHeight minPadding roundedMin rowLayout minGap flexStatic staffElement" style="background-color: yellow;">
                                <?= $order['serviceName'] ?><?= $order['subserviceName'] ?><?= $order['customerName'] ?>
                            </div>
                        <?php endforeach; ?>
                        <div class="tinHeight"></div>
                    </section>
                </div>
                <div class="rowLayout minGap souEastAbsolute">
                    <a href="index.php?page=orders&action=create" class="roundedMin centerColumnLayout importantInput regPadding emphasizedText">Create Order</a>
                </div>
                <div class="gradientBorderDiag"></div>
            </section>
            <section class="centerColumnLayout midGap flexMin roundedMid">
                <section class="box columnLayout roundedMid minGap flexMid fullWidth"> </section>
                <div class="gradientBorderDiag"></div>
            </section>
        </section>
    </main>
</body>

</html>