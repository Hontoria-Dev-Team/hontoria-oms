<!DOCTYPE html>
<html>

<head>
    <title>Services Panel - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/ServicesPage.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout leftAlign midGap midPadding flexStretch">
        <h1 class="titleLogo minGap">
            <img src="../../Shared/Img/GearIcon.png" alt="Gear"> Services Panel
        </h1>
        <section class="rowLayout flexMax midGap">
            <div class="gradientBorderDiag roundedMid flexMid">
                <section class="box columnLayout roundedMid minGap">
                    <section class="minGap scrollable gridFlexMid" id="servicesList">
                        <?php foreach ($servicesList as $service): ?>
                            <?php
                            $name = trim("{$service['name']}");
                            $status = $service['isActive'] ? 'active' : 'disabled';
                            $statusInvert = $service['isActive'] ? 'Disable' : 'Activate';
                            ?>
                            <div class="midHeight minPadding roundedMin rowLayout minGap flexStatic serviceElement columnLayout <?= $status ?>">
                                <div class="serviceImage fullWidth roundedMin"></div>
                                <h2 class="centerRowLayout minGap capitalFirst"><?= $name ?><span>(Active Orders: 100)</span></h2>
                                <div class="rowLayout minGap">
                                    <input type="button" class="importantInput flexMid" value="Modify Service">
                                    <button type="button" class="statusButton flexMin capitalFirst"
                                        data-id="<?= $service['id'] ?>" data-name="<?= htmlspecialchars($name) ?>" data-status-invert="<?= $statusInvert ?>"><?= $status ?></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </section>
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
                <h1>Update Service Status?</h1>
                <h4 id="confirmationText">Are you sure to activate the Sublimation service?</h4>
                <form id="permissionForm" action="index.php?page=services&action=updateStatus" method="POST" class="rowLayout fullWidth minGap">
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

    // Toggle service status logic
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
