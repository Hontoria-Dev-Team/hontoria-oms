<!DOCTYPE html>
<html>

<head>
    <title>Services Panel - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/ServicesPage.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <h1 class="titleLogo minGap tinHeight">
            <img src="../../Shared/Img/GearIcon.png" alt="Gear"> Services Panel
        </h1>
        <section class="rowLayout flexMax midGap">
            <section class="centerColumnLayout roundedMid flexMid">
                <div class="box fullHeight fullWidth roundedMid">
                    <section class="minGap gridFlexMid scrollable" id="servicesList">
                        <?php foreach ($servicesList as $service): ?>
                            <?php
                            $name = trim("{$service['name']}");
                            $status = $service['isActive'] ? 'active' : 'disabled';
                            $statusInvert = $service['isActive'] ? 'Disable' : 'Activate';
                            ?>
                            <div class="midHeight minPadding roundedMin rowLayout minGap flexStatic serviceElement columnLayout <?= $status ?>">
                                <div class="serviceImage fullWidth roundedMin"></div>
                                <h2 class="centerHoriRowLayout minGap capitalFirst"><?= $name ?><span>(Active Orders: 100)</span></h2>
                                <div class="rowLayout minGap">
                                    <a href="index.php?page=services&service=<?= $service['id'] ?>" class="importantInput flexMid buttonLike centerColumnLayout">
                                        Modify Service
                                    </a>
                                    <button type="button" class="statusButton flexMin capitalFirst"
                                        data-id="<?= $service['id'] ?>" data-name="<?= htmlspecialchars($name) ?>" data-status-invert="<?= $statusInvert ?>"><?= $status ?></button>
                                    <?php if ($status === 'disabled'): ?>
                                        <button type="button" class="deleteButton criticalInput centerColumnLayout"
                                            data-id="<?= $service['id'] ?>" data-name="<?= $name ?>">
                                            <img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors">
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </section>
                </div>
                <div class="rowLayout minGap souEastAbsolute">
                    <a id="createButton" class="roundedMin centerColumnLayout importantInput regPadding emphasizedText">
                        Create Service</a>
                </div>
                <div class="gradientBorderDiag"></div>
            </section>
            <section class="centerColumnLayout midGap flexMin roundedMid">
                <section class="box columnLayout roundedMid minGap flexMid fullWidth"> </section>
                <div class="gradientBorderDiag"></div>
            </section>
        </section>
    </main>
    <?php include("../Views/.Components/ConfirmationBox.php"); ?>
</body>
<script src="../.JS/ConfirmationBox.js"></script>
<script>
    const statusButtons = document.querySelectorAll('.statusButton');
    const createButton = document.getElementById('createButton');
    const deleteButtons = document.querySelectorAll('.deleteButton');

    document.addEventListener("DOMContentLoaded", () => {
        confirmationCancel.value = "No Cancel";
    });

    const selectedID = document.createElement("input");
    selectedID.type = "hidden";
    selectedID.name = "selectedID";
    confirmationForm.appendChild(selectedID);

    // Toggle service status logic
    document.addEventListener('DOMContentLoaded', function() {
        statusButtons.forEach(function(elem) {
            elem.addEventListener('click', function() {
                confirmationTitle.innerHTML = "Update Service Status?";
                confirmationForm.action = "index.php?page=services&action=updateStatus"

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


    // Subservice creation logic
    let nameInput;

    createButton.addEventListener('click', function() {
        confirmationTitle.innerHTML = "Create Service";
        confirmationForm.action = "index.php?page=services&action=create";

        confirmationText.innerHTML = "Please enter a unique service name.";
        confirmationSubmit.value = "Create";
        confirmationSubmit.classList.add("active");

        nameInput = document.createElement("input");
        nameInput.type = "text";
        nameInput.name = "name";
        nameInput.placeholder = "Service Name";
        nameInput.id = "nameInput";
        confirmationForm.appendChild(nameInput);

        confirmation.style.display = 'flex';
    });

    // Service Deletion logic
    document.addEventListener('DOMContentLoaded', function() {
        deleteButtons.forEach(function(elem) {
            elem.addEventListener('click', function() {
                confirmationTitle.innerHTML = "Delete Service?";
                confirmationForm.action = "index.php?page=services&action=delete"

                selectedID.value = elem.dataset.id;
                confirmationText.innerHTML = "Are you sure to delete the " + elem.dataset.name + " service?";
                confirmationSubmit.value = "Yes delete";

                confirmation.style.display = 'flex';
            });
        });
    });

    // Added cancellation events
    confirmationCancel.addEventListener('click', function() {
        if (document.getElementById("nameInput")) {
            document.getElementById("nameInput").remove();
        }
    });

    confirmationBG.addEventListener('click', function() {
        if (document.getElementById("nameInput")) {
            document.getElementById("nameInput").remove();
        }
    });
</script>

</html>