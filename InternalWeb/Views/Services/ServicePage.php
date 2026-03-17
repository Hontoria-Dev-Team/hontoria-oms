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
                    <div class="columnLayout minGap">
                        <div class="centerHoriRowLayout minGap" id="process"></div>
                        <div class="rowLayout minGap">
                            <button type="button" id="updateProcessButton" class="importantInput flexMax">Update Service Process</button>
                            <button type="button" id="addProcessButton" class="importantInput">Add Service Process</button>
                        </div>
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
                                            <button type="button" class="deleteButton criticalInput centerColumnLayout"
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
                        <div class="flexMax centerHoriRowLayout minGap">
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
    const process = document.getElementById('process');
    const updateProcessButton = document.getElementById('updateProcessButton');
    const addProcessButton = document.getElementById('addProcessButton');
    const processes = <?php echo json_encode($processes); ?>;

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
                confirmationText.innerHTML = "Are you sure to " + elem.dataset.statusInvert + " the " + elem.dataset.name + " subservice?";
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
                confirmationText.innerHTML = "Are you sure to delete the " + elem.dataset.name + " subservice?";
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

    //Process graph logic and functionality
    let processList = <?php echo json_encode($processList); ?>;
    let processRemoves;
    let swapRights;
    let swapLefts;

    function renderProcessList() {
        const container = document.getElementById('process');
        container.innerHTML = '';

        if (processList.length > 0) {
            const firstDiv = document.createElement('div');
            firstDiv.className = 'flexMin minHeight bordered roundedMin centerRowLayout minGap';
            firstDiv.innerHTML = `
                <h3>${processList[0].name}</h3>
                <a class="squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove" data-index="0">
                    <img src="../../Shared/Img/XIcon.png" alt="X">
                </a>
            `;

            if (processList.length > 1) {
                const firstArrow = document.createElement('a');
                firstArrow.className = 'circle squareSize unitHeight souEastAbsolute centerColumnLayout importantInput closeCorner swapRight';
                firstArrow.dataset.index = '0';
                firstArrow.innerHTML = '<img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors">';
                firstDiv.appendChild(firstArrow);
            }

            container.appendChild(firstDiv);

            for (let i = 1; i < processList.length - 1; i++) {
                const arrow = document.createElement('h1');
                arrow.textContent = '>';
                container.appendChild(arrow);

                const div = document.createElement('div');
                div.className = 'flexMin minHeight bordered roundedMin centerColumnLayout';
                div.innerHTML = `
                    <h3>${processList[i].name}</h3>
                    <a class="squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove" data-index="${i}">
                        <img src="../../Shared/Img/XIcon.png" alt="X">
                    </a>
                    <a class="circle squareSize unitHeight souWestAbsolute centerColumnLayout importantInput closeCorner swapLeft" data-index="${i}">
                        <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors mirrorX">
                    </a>
                    <a class="circle squareSize unitHeight souEastAbsolute centerColumnLayout importantInput closeCorner swapRight" data-index="${i}">
                        <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors">
                    </a>
                `;
                container.appendChild(div);
            }

            if (processList.length > 1) {
                const arrow = document.createElement('h1');
                arrow.textContent = '>';
                container.appendChild(arrow);

                const lastDiv = document.createElement('div');
                lastDiv.className = 'flexMin minHeight bordered roundedMin centerRowLayout minGap';
                lastDiv.innerHTML = `
                    <h3>${processList[processList.length - 1].name}</h3>
                    <a class="squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove" data-index="${processList.length - 1}">
                        <img src="../../Shared/Img/XIcon.png" alt="X">
                    </a>
                    <a class="circle squareSize unitHeight souWestAbsolute centerColumnLayout importantInput closeCorner swapLeft" data-index="${processList.length - 1}">
                        <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors mirrorX">
                    </a>
                `;
                container.appendChild(lastDiv);
            }

            processRemoves = document.querySelectorAll('.processRemove');
            swapRights = document.querySelectorAll('.swapRight');
            swapLefts = document.querySelectorAll('.swapLeft');

            processRemoves.forEach(function(elem) {
                elem.addEventListener('click', function() {
                    processList.splice(elem.dataset.index, 1);
                    renderProcessList();
                });
            });

            swapRights.forEach(function(elem) {
                elem.addEventListener('click', function() {
                    const index = Number(elem.dataset.index);
                    [processList[index], processList[index + 1]] = [processList[index + 1], processList[index]];
                    renderProcessList();
                });
            });

            swapLefts.forEach(function(elem) {
                elem.addEventListener('click', function() {
                    const index = Number(elem.dataset.index);
                    [processList[index], processList[index - 1]] = [processList[index - 1], processList[index]];
                    renderProcessList();
                });
            });
        } else {
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'flexMin minHeight bordered roundedMin centerColumnLayout';
            emptyDiv.innerHTML = '<h3>Empty process</h3>';
            container.appendChild(emptyDiv);
        }
    }

    renderProcessList();

    //Update Process Function Logic
    updateProcessButton.addEventListener('click', function() {
        confirmationTitle.innerHTML = "Update Service Process?";
        confirmationForm.action = "index.php?page=services&service=<?= $serviceID ?>&action=updateProcess";

        confirmationText.innerHTML = "Are you sure to update the process of the <?= $service['name'] ?> service?";
        confirmationSubmit.value = "Yes Update";
        confirmationSubmit.classList.add("active");

        processList.forEach(function(process, i) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'processList[]';
            input.value = process.id;
            input.classList.add("processListElement");
            confirmationForm.appendChild(input);
        });

        confirmation.style.display = 'flex';
    });

    //Process addition, creation, and deletion function logic
    let processesContainer;
    let processNameInput;
    let cancelProcessCreationButton;
    let processElement;
    let currentProcesses = new Set(processList.map(p => p.name));

    addProcessButton.addEventListener('click', function() {
        addProcessBox();
    });

    function addProcessBox() {
        currentProcesses = new Set(processList.map(p => p.name));

        if (document.getElementById("processesContainer")) {
            document.getElementById("processesContainer").remove();
        }

        confirmationTitle.innerHTML = "Add Processes";
        confirmationForm.action = "index.php?page=services&service=<?= $serviceID ?>&action=addProcess";

        confirmationText.innerHTML = "Click on processes that you want to add to the <?= $service['name'] ?> service process.";
        confirmationSubmit.classList.add("hidden");

        confirmationCancel.value = "Return";

        processesContainer = document.createElement("div");
        processesContainer.id = "processesContainer";
        processesContainer.className = 'midHeight scrollable columnLayout minGap';
        processesContainer.innerHTML = `
            <a class="tinHeight noShrink roundedMin centerColumnLayout importantInput" id="createProcessButton">Create New Process</a>
        `;

        processes.forEach((item) => {
            if (currentProcesses.has(item.name)) return;

            const processElement = document.createElement('div');
            processElement.className = 'tinHeight noShrink roundedMin centerColumnLayout bordered redTransBG emphasizedText capitalFirst addProcessElement';
            processElement.textContent = item.name;
            processElement.dataset.name = item.name;
            processElement.dataset.id = item.id;
            processesContainer.appendChild(processElement);
        });

        confirmationForm.appendChild(processesContainer);

        document.getElementById('createProcessButton').addEventListener('click', function() {
            document.getElementById("processesContainer").remove();

            confirmationTitle.innerHTML = "Create Process";
            confirmationForm.action = "index.php?page=services&service=<?= $serviceID ?>&action=createProcess";

            processNameInput = document.createElement("input");
            processNameInput.type = "text";
            processNameInput.name = "name";
            processNameInput.placeholder = "Process Name";
            processNameInput.id = "processNameInput";
            confirmationForm.appendChild(processNameInput);

            confirmationText.innerHTML = "To create a process to be used in services, please enter a unique process name."
            confirmationSubmit.value = "Create Processes";
            confirmationSubmit.classList.remove("hidden");
            confirmationSubmit.classList.add("active");

            cancelProcessCreationButton = document.createElement("input");
            cancelProcessCreationButton.type = "button";
            cancelProcessCreationButton.className = 'importantInput flexMax';
            cancelProcessCreationButton.id = "cancelProcessCreationButton";
            cancelProcessCreationButton.value = "No Cancel";

            cancelProcessCreationButton.addEventListener('click', function() {
                addProcessBox();
            });

            confirmationButtons.appendChild(cancelProcessCreationButton);

            confirmationCancel.classList.add("hidden");
        });

        document.querySelectorAll('.addProcessElement').forEach(function(elem) {
            elem.addEventListener('click', function() {
                processList.push({
                    id: elem.dataset.id,
                    name: elem.dataset.name,
                    phase: -1
                });

                renderProcessList();
                addProcessBox();
            });
        });

        if (document.getElementById("cancelProcessCreationButton")) {
            document.getElementById("cancelProcessCreationButton").remove();
        }

        if (document.getElementById("processNameInput")) {
            document.getElementById("processNameInput").remove();
        }

        confirmationCancel.classList.remove("hidden");
        confirmation.style.display = 'flex';
    }

    // Added cancellation events
    confirmationCancel.addEventListener('click', function() {
        confirmationSubmit.classList.remove("active");
        confirmationSubmit.classList.remove("criticalInput");
        confirmationSubmit.classList.remove("hidden");

        confirmationCancel.value = "No Cancel";

        if (document.getElementById("nameInput")) {
            document.getElementById("nameInput").remove();
        }

        if (document.getElementById("processesContainer")) {
            document.getElementById("processesContainer").remove();
        }

        document.querySelectorAll('.processListElement').forEach(function(elem) {
            elem.remove();
        });
    });

    confirmationBG.addEventListener('click', function() {
        confirmationSubmit.classList.remove("active");
        confirmationSubmit.classList.remove("criticalInput");
        confirmationSubmit.classList.remove("hidden");
        confirmationCancel.classList.remove("hidden");

        confirmationCancel.value = "No Cancel";

        if (document.getElementById("nameInput")) {
            document.getElementById("nameInput").remove();
        }

        if (document.getElementById("processesContainer")) {
            document.getElementById("processesContainer").remove();
        }

        if (document.getElementById("cancelProcessCreationButton")) {
            document.getElementById("cancelProcessCreationButton").remove();
        }

        if (document.getElementById("processNameInput")) {
            document.getElementById("processNameInput").remove();
        }

        document.querySelectorAll('.processListElement').forEach(function(elem) {
            elem.remove();
        });
    });
</script>

</html>