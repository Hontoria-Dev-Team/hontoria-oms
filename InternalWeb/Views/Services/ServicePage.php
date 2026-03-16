<!DOCTYPE html>
<html>

<head>
    <title><?= $service['name'] ?> Service - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/ServicesPage.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <?php include("../Views/.Components/BackLink.php"); ?>
        <section class="rowLayout flexMax midGap">
            <section class="centerColumnLayout roundedMid flexMid">
                <div class="box columnLayout roundedMid minGap fullHeight fullWidth">
                    <h1 class="titleLogo minGap tinHeight">
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
                            <div class="tinHeight"></div>
                        </div>
                    <?php else: ?>
                        <div class="flexMax centerColumnLayout">
                            <h3>No subservice</h3>
                        </div>
                    <?php endif; ?>
                    <div class="rowLayout minGap souEastAbsolute">
                        <a href="index.php?page=service&action=create" class="roundedMin centerColumnLayout importantInput regPadding emphasizedText">Create Subservice</a>
                    </div>
                </div>
                <div class="gradientBorderDiag">
            </section>
            <section class="centerColumnLayout flexMin roundedMid">
                <div class="box roundedMid fullHeight fullWidth"></div>
                <div class="gradientBorderDiag"></div>
            </section>
        </section>
    </main>
    <?php include("../Views/.Components/ConfirmationBox.php"); ?>
</body>
<script src="../.JS/ConfirmationBox.js"></script>
<script>
    const statusButtons = document.querySelectorAll('.statusButton');

    // Toggle service status logic
    document.addEventListener("DOMContentLoaded", () => {
        confirmationTitle.innerHTML = "Update Subservice Status?";
        confirmationCancel.value = "No Cancel";
    });

    const selectedID = document.createElement("input");
    selectedID.type = "hidden";
    selectedID.name = "selectedID";
    confirmationForm.appendChild(selectedID);
    confirmationForm.action = "index.php?page=services&service=<?= $serviceID ?>&action=updateStatus";

    document.addEventListener('DOMContentLoaded', function() {
        statusButtons.forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedID.value = elem.dataset.id;
                confirmationText.innerHTML = "Are you sure to " + elem.dataset.statusInvert + " the " + elem.dataset.name + " service?";
                confirmationSubmit.value = "Yes " + elem.dataset.statusInvert;

                if (elem.dataset.statusInvert == "Activate") {
                    confirmationSubmit.classList.add("active");
                } else {
                    confirmationSubmit.classList.remove("active");
                }

                confirmation.style.display = 'flex';
            });
        });
    });
</script>

</html>