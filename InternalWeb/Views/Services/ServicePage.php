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
        <?php include("../Views/.Components/ErrorBox.php"); ?>
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
                                ?>
                                <div class="midHeight roundedMin minPadding centerHoriColumnLayout minGap subserviceElement <?= $status ?>"
                                    data-id="<?= $id ?>" data-name="<?= $name ?> <?= $service['name'] ?>" data-description="<?= $subservice['description'] ?>" data-price="<?= $subservice['pricePerUnit'] ?>">
                                    <div class="subserviceImage fullWidth roundedMin"></div>
                                    <h3><?= $name ?> <?= $service['name'] ?></h3>
                                    <p class="orderCount">(Active Orders: 100)</p>
                                    <div class="rowLayout minGap">
                                        <button type="button" class="statusButton capitalFirst flexMax"
                                            data-id="<?= $id ?>" data-name="<?= $name ?>" data-status-invert="<?= $statusInvert ?>"><?= $status ?></button>
                                        <?php if ($status === 'disabled'): ?>
                                            <button type="button" class="deleteButton criticalInput flexMin centerColumnLayout"
                                                data-id="<?= $id ?>" data-name="<?= $name ?>">
                                                <img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors">
                                            </button>
                                        <?php endif; ?>
                                    </div>
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
                        <a id="createButton" class="roundedMin centerColumnLayout importantInput regPadding emphasizedText">
                            Create Subservice</a>
                    </div>
                </div>
                <div class="gradientBorderDiag">
            </section>
            <section class="columnLayout midGap flexMin">
                <section class="box centerColumnLayout roundedMid minGap flexMid">
                    <h3 id="selectedName">No Subservice Selected</h3>
                    <form method="POST" class="columnLayout minGap fullWidth" id="subserviceDataForm" style="display: none;"
                        action="index.php?page=services&service=<?= $serviceID ?>&action=updateInfo">
                        <input type="hidden" name="selectedID" id="subserviceID">
                        <input type="hidden" name="setPricePerUnit" id="setPricePerUnit">
                        <input type="hidden" name="setDescription" id="setDescription">
                        <label for="description">Description</label>
                        <textarea name="description" class="scrollableTextarea minHeight fullWidth minPadding justifiedText" id="descriptionText"></textarea>
                        <div class="flexMax centerRowLayout minGap">
                            <label for="pricePerUnit">Price Per Unit</label>
                            <input type="number" name="pricePerUnit" class="flexMid" id="priceInput">
                        </div>
                        <input type="submit" name="submit" value="Update" class="importantInput">
                    </form>
                    <div class="gradientBorderDiag"></div>
                </section>
                <section class="box centerColumnLayout roundedMid flexMid">
                    <div class="gradientBorderDiag"></div>
                </section>
            </section>
        </section>
    </main>
    <?php include("../Views/.Components/ConfirmationBox.php"); ?>
</body>
<script src="../.JS/ConfirmationBox.js"></script>
<script>
    const statusButtons = document.querySelectorAll('.statusButton');
    const deleteButtons = document.querySelectorAll('.deleteButton');
    const createButton = document.getElementById('createButton');
    const subserviceElements = document.querySelectorAll('.subserviceElement');
    const nameDisplay = document.getElementById('selectedName');
    const form = document.getElementById('subserviceDataForm');
    const subserviceID = document.getElementById('subserviceID');
    const setPricePerUnit = document.getElementById('setPricePerUnit');
    const setDescription = document.getElementById('setDescription');
    const descriptionText = document.getElementById('descriptionText');
    const priceInput = document.getElementById('priceInput');

    // Reactive clickable subservice data script
    document.addEventListener('DOMContentLoaded', function() {
        subserviceElements.forEach(function(elem) {
            elem.addEventListener('click', function() {
                nameDisplay.textContent = elem.dataset.name;
                nameDisplay.style.alignSelf = 'baseline';

                subserviceID.value = elem.dataset.id;
                setPricePerUnit.value = elem.dataset.price;
                priceInput.placeholder = elem.dataset.price;
                setDescription.value = elem.dataset.description;
                descriptionText.placeholder = elem.dataset.description;

                form.style.display = 'flex';
            });
        });
    });

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
                confirmationTitle.innerHTML = "Update Subservice Status?";
                confirmationForm.action = "index.php?page=services&service=<?= $serviceID ?>&action=updateStatus";

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

    // Toggle delete status logic
    document.addEventListener('DOMContentLoaded', function() {
        deleteButtons.forEach(function(elem) {
            elem.addEventListener('click', function() {
                confirmationTitle.innerHTML = "Delete Subservice?";
                confirmationForm.action = "index.php?page=services&service=<?= $serviceID ?>&action=delete";

                selectedID.value = elem.dataset.id;
                confirmationText.innerHTML = "Are you sure to delete the " + elem.dataset.name + " service?";
                confirmationSubmit.value = "Yes Delete";

                confirmation.style.display = 'flex';
            });
        });
    });

    // Subservice creation logic
    let nameInput;

    createButton.addEventListener('click', function() {
        confirmationTitle.innerHTML = "Create Subservice";
        confirmationForm.action = "index.php?page=services&service=<?= $serviceID ?>&action=create";

        confirmationText.innerHTML = "To create a subservice for the <?= $service['name'] ?> service, please enter a unique subservice name.";
        confirmationSubmit.value = "Create";
        confirmationSubmit.classList.add("active");

        nameInput = document.createElement("input");
        nameInput.type = "text";
        nameInput.name = "name";
        nameInput.placeholder = "Subservice Name";
        nameInput.id = "nameInput";
        confirmationForm.appendChild(nameInput);

        confirmation.style.display = 'flex';
    });

    confirmationCancel.addEventListener('click', function() {
        confirmationSubmit.classList.remove("active");
        confirmationSubmit.classList.remove("criticalInput");

        if (nameInput) {
            document.getElementById("nameInput").remove();
        }
    });

    confirmationBG.addEventListener('click', function() {
        confirmationSubmit.classList.remove("active");
        confirmationSubmit.classList.remove("criticalInput");

        if (nameInput) {
            document.getElementById("nameInput").remove();
        }
    });
</script>

</html>