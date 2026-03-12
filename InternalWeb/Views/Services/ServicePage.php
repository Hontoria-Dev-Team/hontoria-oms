<!DOCTYPE html>
<html>

<head>
    <title><?= $service['name'] ?> Service - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/ServicesPage.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout leftAlign midGap midPadding flexStretch">
        <?php include("../Views/.Components/BackLink.php"); ?>
        <section class="rowLayout flexMax midGap">
            <div class="gradientBorderDiag roundedMid flexMid">
                <section class="box columnLayout roundedMid minGap">
                    <h1 class="titleLogo minGap">
                        <img src="../../Shared/Img/GearIcon.png" alt="Gear"> <?= $service['name'] ?> Service
                    </h1>
                    <h2>Service Process:</h2>
                    <div class="centerRowLayout minGap" id="process">
                        <?php if (count($processList) > 0): ?>
                            <div class="flexMin minHeight bordered roundedMin centerColumnLayout">
                                <h3><?= $processList[0]['name'] ?></h3>
                            </div>
                            <?php for ($i = 1; $i < count($processList); $i++): ?>
                                <h1>></h1>
                                <div class="flexMin minHeight bordered roundedMin centerColumnLayout">
                                    <h3><?= $processList[$i]['name'] ?></h3>
                                </div>
                            <?php endfor; ?>
                        <?php else: ?>
                            <div class="flexMin minHeight bordered roundedMin centerColumnLayout">
                                <h3>Empty process</h3>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h2>Subservices:</h2>
                    <?php if (count($subservicesList) > 0): ?>
                        <div class="minGap scrollable gridFlexMid" id="subservicesList">
                            <?php foreach ($subservicesList as $subservice): ?>
                                <?php
                                $id = $subservice['id'];
                                $name = trim("{$subservice['name']}");
                                $status = $subservice['isActive'] ? 'active' : 'disabled';
                                $statusInvert = $subservice['isActive'] ? 'Disable' : 'Activate';
                                $pricePerUnit = $subservice['pricePerUnit'];
                                $description = $subservice['description'];
                                ?>
                                <div class="maxHeight roundedMin minPadding columnLayout minGap subserviceElement <?= $status ?>">
                                    <div class="subserviceImage fullWidth roundedMin"></div>
                                    <h3><?= $name ?> <?= $service['name'] ?></h3>
                                    <p class="orderCount">(Active Orders: 100)</p>
                                    <form method="POST" action="index.php?page=services&service=<?= $serviceID ?>&action=updateInfo" class="columnLayout minGap fullWidth">
                                        <input type="hidden" name="selectedID" value="<?= $id ?>">
                                        <input type="hidden" name="setPricePerUnit" value="<?= $pricePerUnit ?>">
                                        <input type="hidden" name="setDescription" value="<?= $description ?>">
                                        <label for="description">Description</label>
                                        <textarea name="description" class="scrollableTextarea minHeight minPadding justifiedText" placeholder="<?= $description ?>"></textarea>
                                        <div class="flexMax centerRowLayout minGap">
                                            <label for="pricePerUnit">Price Per Unit</label>
                                            <input type="number" name="pricePerUnit" class="flexMid" placeholder="<?php echo $pricePerUnit; ?>">
                                        </div>
                                        <div class="rowLayout minGap">
                                            <input type="submit" name="submit" value="Update" class="importantInput flexMid">
                                            <button type="button" class="statusButton flexMin capitalFirst"
                                                data-id="<?= $id ?>" data-name="<?= $name ?>" data-status-invert="<?= $statusInvert ?>"><?= $status ?></button>
                                        </div>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="flexMax centerColumnLayout">
                            <h3>No subservice</h3>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
            <section class="columnLayout midGap flexMin">
                <div class="gradientBorderDiag roundedMid flexMid">
                    <section class="box columnLayout roundedMid minGap">
                    </section>
                </div>
            </section>
        </section>
    </main>
    <section id="changeConfirmation" class="centerColumnLayout" style="display: none;">
        <div class="gradientBorderDiag roundedMid maxWidth midHeight">
            <section class="box centerColumnLayout roundedMid minGap">
                <h1>Update Subservice Status?</h1>
                <h4 id="confirmationText">Are you sure to activate the Sublimation subservice?</h4>
                <form action="index.php?page=services&service=<?= $serviceID ?>&action=updateStatus" method="POST" class="rowLayout fullWidth minGap">
                    <input type="hidden" name="selectedID" id="selectedID">
                    <input type="submit" class="flexMax" id="confirmButton" value="Yes Activate">
                    <input type="button" class="importantInput flexMax" id="cancelButton" value="No Cancel">
                </form>
            </section>
        </div>
    </section>
    <div id="confirmationBackground" style="display: none;"></div>
</body>

<script>
    const statusButtons = document.querySelectorAll('.statusButton');
    const idSelected = document.getElementById('selectedID');
    const confirmation = document.getElementById('changeConfirmation');
    const confirmationBG = document.getElementById('confirmationBackground');
    const text = document.getElementById('confirmationText');

    // Toggle subservice status logic
    document.addEventListener('DOMContentLoaded', function() {
        statusButtons.forEach(function(elem) {
            elem.addEventListener('click', function() {
                idSelected.value = elem.dataset.id;
                text.innerHTML = "Are you sure to " + elem.dataset.statusInvert + " the " + elem.dataset.name + " service?";
                confirmButton.value = "Yes " + elem.dataset.statusInvert;

                if (elem.dataset.statusInvert == "Activate") {
                    confirmButton.classList.add("active");
                } else {
                    confirmButton.classList.remove("active");
                }

                confirmation.style.display = 'flex';
                confirmationBG.style.display = 'unset';
            });
        });
    });

    document.getElementById('cancelButton').addEventListener('click', function() {
        confirmation.style.display = 'none';
        confirmationBG.style.display = 'none';
    });
    document.getElementById('confirmationBackground').addEventListener('click', function() {
        confirmation.style.display = 'none';
        confirmationBG.style.display = 'none';
    });
</script>

</html>