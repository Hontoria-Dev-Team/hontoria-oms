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
            <div class="rowLayout minGap flexMax contentFlexEnd">
                <a href="index.php?page=services&action=manageProcesses" class="roundedMin centerColumnLayout importantInput regPadding emphasizedText shadowed">
                    Manage Processes
                </a>
            </div>
        </h1>
        <?php include("../Views/.Components/MessageBox.php"); ?>
        <section class="rowLayout flexMax midGap">
            <div class="flexMinExtra columnLayout midGap">
                <section class="centerColumnLayout roundedMid flexMid">
                    <div class="box fullHeight fullWidth roundedMid columnLayout tinGap">
                        <div class="centerHoriRowLayout">
                            <h2 class="flexMax">Services:</h2>
                            <button type="button" class="darkBG emphasizedText noBorder shadowed whiteText centerColumnLayout fullHeight"
                                id="createServiceButton">
                                <b>Create</b>
                            </button>
                        </div>
                        <section class="minGap columnLayout scrollable flexMax noFlexBasis noMinHeight contentFlexStart regMinPadding" id="servicesList">
                            <?php foreach ($servicesList as $service): ?>
                                <?php
                                $name = trim("{$service['name']}");
                                $statusInvert = $service['isActive'] ? 'Disable' : 'Activate';
                                $borderClass = $service['isActive'] ? 'yellowBorder' : 'redBorder';
                                $bgClass = $service['isActive'] ? 'yellowTransBG' : 'redTransBG';
                                $orderCount = $serviceOrderCountMap[$service['id']] ?? 0;
                                ?>
                                <div class="roundedMin centerHoriRowLayout flexStatic serviceElement <?= $borderClass ?> shadowed clickable fixedScreen noShrink"
                                    data-id="<?= $service['id'] ?>" data-name="<?= $service['name'] ?>" data-is-active="<?= $service['isActive'] ?>"
                                    data-has-design="<?= $service['hasDesign'] ?>" data-has-variable-list="<?= $service['hasVariableList'] ?>"
                                    data-order-count="<?= $orderCount ?>">
                                    <div class="capitalFirst centerText regMinPadding flexMax skewedXNegBG shadowed <?= $bgClass ?>">
                                        <h3 class="whiteText outlineText"><?= $name ?></h3>
                                    </div>
                                    <h5 class="capitalFirst centerText regMinPadding minWidth">Orders: <?= $orderCount ?></h5>
                                </div>
                            <?php endforeach; ?>
                        </section>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
            </div>
            <section class="columnLayout midGap flexMax">
                <section class="centerColumnLayout roundedMid minGap">
                    <div class="centerHoriRowLayout minGap box roundedMid fullHeight fullWidth">
                        <h2 id="selectedServiceTitle" class="capitalFirst flexMax">No Service Selected</h2>
                        <div class="flexMax centerHoriRowLayout minGap fullHeight" id="objectiveButtonsContainer"></div>
                        <div class="flexMid centerHoriRowLayout minGap fullHeight" id="serviceStatusButtonsContainer"></div>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
                <div class="columnLayout flexMax midGap">
                    <section class="centerColumnLayout roundedMid minGap flexMid">
                        <div class="columnLayout minGap box roundedMid fullHeight fullWidth">
                            <div class="centerHoriRowLayout minGap">
                                <h2 class="flexMax">Service Process:</h2>
                                <div class="centerHoriRowLayout minGap" id="objectiveButtonsContainer">
                                    <button type="button" class="darkBG emphasizedText noBorder shadowed whiteText centerColumnLayout hidden" id="updateServiceProcessButton">
                                        Update Service Process
                                    </button>
                                </div>
                            </div>
                            <div class="centerHoriRowLayout minGap flexMax" id="serviceProcess">
                                <h2 class="centerMarginsSelf">No Service Selected</h2>
                            </div>
                        </div>
                        <div class="gradientBorderDiag"></div>
                    </section>
                    <div class="rowLayout flexMax midGap noMinHeight noFlexBasis">
                        <section class="centerColumnLayout roundedMid minGap flexMid">
                            <div class="box fullHeight fullWidth roundedMid columnLayout tinGap">
                                <div class="centerHoriRowLayout">
                                    <h2 class="flexMax">Subservices:</h2>
                                    <button type="button" class="darkBG emphasizedText noBorder shadowed whiteText centerColumnLayout fullHeight hidden"
                                        id="createSubserviceButton">
                                        <b>Create</b>
                                    </button>
                                </div>
                                <section class="minGap columnLayout scrollable flexMax noFlexBasis noMinHeight contentFlexStart regMinPadding" id="subservicesContainer">
                                    <h2 class="centerMarginsSelf">No Service Selected</h2>
                                </section>
                            </div>
                            <div class="gradientBorderDiag"></div>
                        </section>
                        <div class="columnLayout midGap flexMax">
                            <section class="centerRowLayout roundedMid">
                                <div class="centerHoriRowLayout minGap box roundedMid fullHeight fullWidth">
                                    <h2 class="flexMax capitalFirst" id="selectedSubserviceTitle">No Subservice Selected</h2>
                                    <div class="centerHoriRowLayout minGap fullHeight flexMid" id="subserviceStatusButtonsContainer"></div>
                                </div>
                                <div class="gradientBorderDiag"></div>
                            </section>
                            <section class="centerRowLayout roundedMid flexMax">
                                <div class="centerHoriRowLayout minGap box roundedMid fullHeight fullWidth" id="subserviceDataContainer">
                                    <h2 class="centerMarginsSelf">No Subservice Selected</h2>
                                    <div class="centerHoriRowLayout minGap fullHeight fullWidth hidden">
                                        <form method="POST" class="columnLayout minGap fullWidth flexMid fullHeight" action="index.php?page=services&action=updateSubserviceInfo">
                                            <input type="hidden" name="selectedServiceID">
                                            <input type="hidden" name="selectedSubserviceID">
                                            <div class="flexMax columnLayout tinGap">
                                                <b>Description</b>
                                                <textarea name="description" class="scrollableTextarea minHeight fullWidth flexMax minPadding justifiedText unresizeable"
                                                    id="descriptionText"></textarea>
                                            </div>
                                            <div class="centerHoriRowLayout tinGap">
                                                <b>Price Per Unit</b>
                                                <input type="number" name="pricePerUnit" class="flexMid" id="priceInput" min="1">
                                            </div>
                                            <input type="submit" name="submit" value="Update" class="importantInput shadowed noBorder">
                                        </form>
                                        <div class="flexMid fullHeight columnLayout minGap">
                                            <div class="centerHoriRowLayout">
                                                <b class="flexMax">Images</b>
                                                <button type="button" class="darkBG noBorder shadowed whiteText centerColumnLayout fullHeight roundedTin"
                                                    id="addSubserviceImageButton">
                                                    <h5>Add Image</h5>
                                                </button>
                                            </div>
                                            <div class="gridFlex minGap midGrids flexMax contentFlexStart noFlexBasis noMinHeight scrollable regMinPadding"
                                                id="subserviceImagesContainer"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gradientBorderDiag"></div>
                            </section>
                        </div>
                    </div>
                </div>
            </section>
        </section>
    </main>
    <?php include("../Views/.Components/ConfirmationBox.php"); ?>
    <?php include("../Views/.Components/ImageBox.php"); ?>
</body>
<script src="../.JS/ConfirmationBox.js"></script>
<script src="../.JS/ImageBox.js"></script>
<script>
    /**
     * Services Management Page Script
     * Handles service selection, process management, subservice CRUD, and image uploads.
     * Depends on global confirmation dialog elements (confirmationForm, confirmationTitle, etc.)
     */

    // ================================
    // DOM Elements
    // ================================
    const createServiceButton = document.getElementById('createServiceButton');
    const serviceElements = document.querySelectorAll('.serviceElement');
    const serviceStatusButtonsContainer = document.getElementById('serviceStatusButtonsContainer');
    const objectiveButtonsContainer = document.getElementById('objectiveButtonsContainer');
    const updateServiceProcessButton = document.getElementById('updateServiceProcessButton');
    const serviceProcess = document.getElementById('serviceProcess');
    const subservicesContainer = document.getElementById('subservicesContainer');
    const createSubserviceButton = document.getElementById('createSubserviceButton');
    const subserviceStatusButtonsContainer = document.getElementById('subserviceStatusButtonsContainer');
    const subserviceDataContainer = document.getElementById('subserviceDataContainer');
    const addSubserviceImageButton = document.getElementById('addSubserviceImageButton');
    const subserviceImagesContainer = document.getElementById('subserviceImagesContainer');

    // ================================
    // Server Data (injected via PHP)
    // ================================
    const serviceProcessList = <?php echo json_encode($serviceProcessList); ?>; // [{serviceID, id, name}, ...]
    const subserviceList = <?php echo json_encode($subserviceList); ?>; // [{serviceID, id, name, isActive, description, pricePerUnit}, ...]
    const subserviceOrderCountTally = <?php echo json_encode($subserviceOrderCountTally); ?>; // [{subserviceID, orderCount}]
    const processesList = <?php echo json_encode($processesList); ?>; // [{id, name}, ...]
    const subserviceImageList = <?php echo json_encode($subserviceImageList); ?>; // [{subserviceID, id, imageName}, ...]
    const lastServiceID = <?php echo $serviceID; ?>; // Previously selected service ID (for persistence)
    const lastSubserviceID = <?php echo $subserviceID; ?>; // Previously selected subservice ID

    // ================================
    // Build Lookup Maps
    // ================================
    const serviceProcessMap = {}; // serviceID -> array of {id, name}
    serviceProcessList.forEach(item => {
        if (!serviceProcessMap[item.serviceID]) serviceProcessMap[item.serviceID] = [];
        serviceProcessMap[item.serviceID].push({
            id: item.id,
            name: item.name
        });
    });

    const subserviceMap = {}; // serviceID -> array of full subservice objects
    subserviceList.forEach(item => {
        if (!subserviceMap[item.serviceID]) subserviceMap[item.serviceID] = [];
        subserviceMap[item.serviceID].push({
            id: item.id,
            name: item.name,
            isActive: item.isActive,
            description: item.description,
            pricePerUnit: item.pricePerUnit
        });
    });

    const subserviceOrderCountMap = {}; // subserviceID -> order count
    subserviceOrderCountTally.forEach(item => {
        subserviceOrderCountMap[item.subserviceID] = item.orderCount;
    });

    const subserviceImageMap = {}; // subserviceID -> array of {id, name}
    subserviceImageList.forEach(item => {
        if (!subserviceImageMap[item.subserviceID]) subserviceImageMap[item.subserviceID] = [];
        subserviceImageMap[item.subserviceID].push({
            id: item.id,
            name: item.imageName
        });
    });

    // ================================
    // Set default cancel button text
    // ================================
    document.addEventListener("DOMContentLoaded", () => {
        confirmationCancel.value = "No Cancel";
    });

    // ================================
    // Hidden inputs for confirmation form
    // ================================
    const selectedServiceIdInput = document.createElement("input");
    selectedServiceIdInput.type = "hidden";
    selectedServiceIdInput.name = "selectedServiceID";
    selectedServiceIdInput.value = lastServiceID || -1;
    confirmationForm.appendChild(selectedServiceIdInput);

    const selectedSubserviceIdInput = document.createElement("input");
    selectedSubserviceIdInput.type = "hidden";
    selectedSubserviceIdInput.name = "selectedSubserviceID";
    selectedSubserviceIdInput.value = lastSubserviceID || -1;
    confirmationForm.appendChild(selectedSubserviceIdInput);

    // ================================
    // Reusable temporary variables
    // ================================
    let tempElement;
    let tempDiv;

    // ================================
    // Central State Objects (separate)
    // ================================
    let currentService = {
        id: null,
        name: '',
        status: 0, // 1 = active, 0 = inactive
        orderCount: 0,
        hasDesign: false,
        hasVariableList: false,
        processes: [], // array of {id, name}
        subservices: [], // array of full subservice objects
        subservicesMap: {} // subserviceID -> {description, pricePerUnit}
    };

    let currentSubservice = {
        id: null,
        name: '',
        isActive: 0,
        orderCount: 0,
        description: '',
        pricePerUnit: 0,
        images: [] // array of {id, name}
    };

    // ================================
    // Helper: rebuild subservices map from currentService.subservices
    // ================================
    function RebuildSubservicesMap() {
        currentService.subservicesMap = {};
        currentService.subservices.forEach(sub => {
            currentService.subservicesMap[sub.id] = {
                description: sub.description,
                pricePerUnit: sub.pricePerUnit
            };
        });
    }

    // ================================
    // Helper: update currentSubservice from a subservice object
    // ================================
    function SetCurrentSubservice(sub) {
        currentSubservice.id = sub.id;
        currentSubservice.name = sub.name;
        currentSubservice.isActive = sub.isActive;
        currentSubservice.orderCount = subserviceOrderCountMap[sub.id] || 0;
        currentSubservice.description = sub.description;
        currentSubservice.pricePerUnit = sub.pricePerUnit;
        currentSubservice.images = [...(subserviceImageMap[sub.id] || [])];
    }

    // ================================
    // SERVICE SELECTION & INITIALIZATION
    // ================================
    function OnServiceClick(elem) {
        // Update service state
        currentService.id = elem.dataset.id;
        currentService.name = elem.dataset.name;
        currentService.status = parseInt(elem.dataset.isActive);
        currentService.orderCount = parseInt(elem.dataset.orderCount);
        currentService.hasDesign = elem.dataset.hasDesign === '1';
        currentService.hasVariableList = elem.dataset.hasVariableList === '1';

        selectedServiceIdInput.value = currentService.id;

        document.getElementById('selectedServiceTitle').textContent = currentService.name + " Service";

        // Load processes and subservices from maps
        currentService.processes = [...(serviceProcessMap[currentService.id] || [])];
        currentService.subservices = [...(subserviceMap[currentService.id] || [])];
        RebuildSubservicesMap();

        // Reset subservice state
        currentSubservice.id = null;
        currentSubservice.name = '';
        selectedSubserviceIdInput.value = -1;
        document.getElementById('selectedSubserviceTitle').textContent = "No Subservice Selected";

        // Render UI
        ShowServiceStatusButtonsContainer();
        ShowObjectiveButtonsContainer();
        ShowServiceProcess();
        ResetSubserviceHeader();
        ShowSubservices();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Attach click handlers to service elements
        serviceElements.forEach(elem => {
            elem.addEventListener('click', () => OnServiceClick(elem));
        });

        // Persistence: if a service ID was provided, simulate click
        if (lastServiceID != -1) {
            for (const elem of serviceElements) {
                if (elem.dataset.id == lastServiceID) {
                    OnServiceClick(elem);
                    break;
                }
            }
        }
    });

    // ================================
    // SERVICE STATUS BUTTONS
    // ================================
    function ShowServiceStatusButtonsContainer() {
        serviceStatusButtonsContainer.innerHTML = '';

        if (currentService.status == 1) {
            // Active -> Disable button
            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "redBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
            tempElement.textContent = "Disable";
            tempElement.id = "serviceStatusButton";
            serviceStatusButtonsContainer.appendChild(tempElement);
        } else {
            // Inactive -> Activate button
            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "yellowBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
            tempElement.textContent = "Activate";
            tempElement.id = "serviceStatusButton";
            serviceStatusButtonsContainer.appendChild(tempElement);

            const canActivate = currentService.processes.length > 0 &&
                currentService.subservices.length > 0 &&
                currentService.subservices[0].isActive == 1;
            if (!canActivate) tempElement.classList.add("faded", "unclickable");

            // Delete button (only for inactive services)
            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "redBG noBorder shadowed centerColumnLayout fullHeight";
            tempElement.innerHTML = '<img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors unitHeight">';
            tempElement.id = "deleteServiceButton";
            serviceStatusButtonsContainer.appendChild(tempElement);

            if (currentService.orderCount > 0) {
                tempElement.classList.add("faded", "unclickable");
            } else {
                document.getElementById('deleteServiceButton').addEventListener('click', () => {
                    confirmationTitle.innerHTML = "Delete Service?";
                    confirmationForm.action = "index.php?page=services&action=deleteService";
                    confirmationText.innerHTML = "Are you sure to delete the " + currentService.name + " service?";
                    confirmationSubmit.value = "Yes delete";
                    confirmation.style.display = 'flex';
                });
            }
        }

        // Toggle status
        document.getElementById('serviceStatusButton').addEventListener('click', function() {
            const canActivate = currentService.processes.length > 0 &&
                currentService.subservices.length > 0 &&
                currentService.subservices[0].isActive == 1;
            if (canActivate) {
                confirmationTitle.innerHTML = "Toggle Service Status?";
                confirmationForm.action = "index.php?page=services&action=toggleServiceStatus";
                confirmationText.innerHTML = "Are you sure to " + this.textContent + " the " + currentService.name + " service?";
                confirmationSubmit.value = "Yes " + this.textContent;
                if (this.textContent == "Activate") confirmationSubmit.classList.add("yellowBG");
                confirmation.style.display = 'flex';
            }
        });
    }

    // ================================
    // OBJECTIVE BUTTONS
    // ================================
    function ShowObjectiveButtonsContainer() {
        objectiveButtonsContainer.innerHTML = '';

        // Has Design button
        tempElement = document.createElement("button");
        tempElement.type = "button";
        tempElement.className = currentService.hasDesign ?
            "yellowBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight" :
            "redBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
        tempElement.textContent = currentService.hasDesign ? "Has Design" : "No Design";
        tempElement.id = "hasDesignButton";
        objectiveButtonsContainer.appendChild(tempElement);

        // Has Variable List button
        tempElement = document.createElement("button");
        tempElement.type = "button";
        tempElement.className = currentService.hasVariableList ?
            "yellowBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight" :
            "redBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
        tempElement.textContent = currentService.hasVariableList ? "Has Variable List" : "No Variable List";
        tempElement.id = "hasVariableListButton";
        objectiveButtonsContainer.appendChild(tempElement);

        const editable = (currentService.orderCount == 0 && currentService.status == 0);
        if (editable) {
            document.getElementById('hasDesignButton').addEventListener('click', function() {
                confirmationTitle.innerHTML = "Toggle Design Objective?";
                confirmationForm.action = "index.php?page=services&action=toggleHasDesign";
                if (this.textContent == "No Design") {
                    confirmationSubmit.classList.add("yellowBG");
                    confirmationText.innerHTML = "Are you sure to activate the design objective?";
                    confirmationSubmit.value = "Yes Active";
                } else {
                    confirmationText.innerHTML = "Are you sure to disable the design objective?";
                    confirmationSubmit.value = "Yes Disable";
                }
                confirmation.style.display = 'flex';
            });

            document.getElementById('hasVariableListButton').addEventListener('click', function() {
                confirmationTitle.innerHTML = "Toggle Variable List Objective?";
                confirmationForm.action = "index.php?page=services&action=toggleHasVariableList";
                if (this.textContent == "No Variable List") {
                    confirmationSubmit.classList.add("yellowBG");
                    confirmationText.innerHTML = "Are you sure to activate the variable list objective?";
                    confirmationSubmit.value = "Yes Active";
                } else {
                    confirmationText.innerHTML = "Are you sure to disable the variable list objective?";
                    confirmationSubmit.value = "Yes Disable";
                }
                confirmation.style.display = 'flex';
            });
        } else {
            document.getElementById('hasDesignButton').classList.add("faded", "unclickable");
            document.getElementById('hasVariableListButton').classList.add("faded", "unclickable");
        }
    }

    // ================================
    // SERVICE PROCESS MANAGEMENT
    // ================================
    function ShowServiceProcess() {
        const editable = (currentService.orderCount == 0 && currentService.status == 0);
        if (editable) {
            updateServiceProcessButton.classList.remove("faded", "unclickable");
            updateServiceProcessButton.dataset.interactable = "1";
        } else {
            updateServiceProcessButton.classList.add("faded", "unclickable");
            updateServiceProcessButton.dataset.interactable = "0";
        }

        serviceProcess.innerHTML = '';
        updateServiceProcessButton.classList.remove("hidden");

        if (currentService.processes.length === 0) {
            tempElement = document.createElement("div");
            tempElement.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";
            tempElement.innerHTML = "<b class='whiteText outlineText'>No Service Process</b>";
            serviceProcess.appendChild(tempElement);

            if (editable) {
                tempElement = document.createElement("div");
                tempElement.className = "circle squareSize duoHeight darkBG roundedMin shadowed centerRowLayout regMinPadding";
                tempElement.innerHTML = '<img src="../../Shared/Img/CrossIcon.png" alt="Cross" class="invertColors">';
                tempElement.id = "addProcessButton";
                serviceProcess.appendChild(tempElement);
                document.getElementById('addProcessButton').addEventListener('click', ShowAddProcessesBox);
            }
            return;
        }

        // First process
        tempElement = document.createElement("div");
        tempElement.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";
        tempElement.innerHTML = currentService.processes.length === 1 ? `
        <b class='whiteText outlineText'>${currentService.processes[0].name}</b>
        <a class="squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove" data-index="0">
            <img src="../../Shared/Img/XIcon.png" alt="X">
        </a>
    ` : `
        <b class='whiteText outlineText'>${currentService.processes[0].name}</b>
        <a class="squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove" data-index="0">
            <img src="../../Shared/Img/XIcon.png" alt="X">
        </a>
        <a class="circle squareSize unitHeight souEastAbsolute centerColumnLayout importantInput closeCorner swapRight shadowed" data-index="0">
            <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors">
        </a>
    `;
        serviceProcess.appendChild(tempElement);

        // Middle processes
        for (let i = 1; i < currentService.processes.length - 1; i++) {
            tempElement = document.createElement("h2");
            tempElement.textContent = ">";
            serviceProcess.appendChild(tempElement);

            tempElement = document.createElement("div");
            tempElement.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";
            tempElement.innerHTML = `
            <b class='whiteText outlineText'>${currentService.processes[i].name}</b>
            <a class="squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove" data-index="${i}">
                <img src="../../Shared/Img/XIcon.png" alt="X">
            </a>
            <a class="circle squareSize unitHeight souWestAbsolute centerColumnLayout importantInput closeCorner swapLeft shadowed" data-index="${i}">
                <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors mirrorX">
            </a>
            <a class="circle squareSize unitHeight souEastAbsolute centerColumnLayout importantInput closeCorner swapRight shadowed" data-index="${i}">
                <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors">
            </a>
        `;
            serviceProcess.appendChild(tempElement);
        }

        // Last process (if more than one)
        if (currentService.processes.length > 1) {
            tempElement = document.createElement("h2");
            tempElement.textContent = ">";
            serviceProcess.appendChild(tempElement);

            tempElement = document.createElement("div");
            tempElement.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";
            tempElement.innerHTML = `
            <b class='whiteText outlineText'>${currentService.processes[currentService.processes.length - 1].name}</b>
            <a class="squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove" data-index="${currentService.processes.length - 1}">
                <img src="../../Shared/Img/XIcon.png" alt="X">
            </a>
            <a class="circle squareSize unitHeight souWestAbsolute centerColumnLayout importantInput closeCorner swapLeft shadowed"
                data-index="${currentService.processes.length - 1}">
                <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors mirrorX">
            </a>
        `;
            serviceProcess.appendChild(tempElement);
        }

        // Add process button (plus)
        if (editable) {
            tempElement = document.createElement("div");
            tempElement.className = "circle squareSize duoHeight darkBG roundedMin shadowed centerRowLayout regMinPadding";
            tempElement.innerHTML = '<img src="../../Shared/Img/CrossIcon.png" alt="Cross" class="invertColors">';
            tempElement.id = "addProcessButton";
            serviceProcess.appendChild(tempElement);
            document.getElementById('addProcessButton').addEventListener('click', ShowAddProcessesBox);
        }

        // Event handlers for remove/swap (only if editable)
        document.querySelectorAll('.processRemove').forEach(el => {
            if (editable) {
                el.addEventListener('click', function() {
                    const idx = parseInt(this.dataset.index);
                    currentService.processes.splice(idx, 1);
                    ShowServiceProcess();
                });
            } else {
                el.classList.add("hidden");
            }
        });

        document.querySelectorAll('.swapRight').forEach(el => {
            if (editable) {
                el.addEventListener('click', function() {
                    const idx = parseInt(this.dataset.index);
                    [currentService.processes[idx], currentService.processes[idx + 1]] = [currentService.processes[idx + 1], currentService.processes[idx]];
                    ShowServiceProcess();
                });
            } else {
                el.classList.add("hidden");
            }
        });

        document.querySelectorAll('.swapLeft').forEach(el => {
            if (editable) {
                el.addEventListener('click', function() {
                    const idx = parseInt(this.dataset.index);
                    [currentService.processes[idx], currentService.processes[idx - 1]] = [currentService.processes[idx - 1], currentService.processes[idx]];
                    ShowServiceProcess();
                });
            } else {
                el.classList.add("hidden");
            }
        });
    }

    function ShowAddProcessesBox() {
        const currentNames = new Set(currentService.processes.map(p => p.name));
        let hasAddable = false;

        confirmationTitle.innerHTML = "Add Processes";
        confirmationText.innerHTML = "Click on processes that you want to add to the " + currentService.name + " service process.";
        confirmationSubmit.classList.add("hidden");
        confirmationCancel.value = "Return";

        document.querySelectorAll('.tempElement').forEach(el => el.remove());

        tempDiv = document.createElement("div");
        tempDiv.className = 'midHeight scrollable columnLayout minGap regMinPadding tempElement';

        processesList.forEach(proc => {
            if (currentNames.has(proc.name)) return;
            tempElement = document.createElement('div');
            tempElement.className = 'tinHeight noShrink roundedMin centerColumnLayout bordered darkTransBG emphasizedText capitalFirst shadowed clickable addProcessElement';
            tempElement.innerHTML = '<b>' + proc.name + '</b>';
            tempElement.dataset.id = proc.id;
            tempElement.dataset.name = proc.name;
            tempDiv.appendChild(tempElement);
            hasAddable = true;
        });

        if (!hasAddable) {
            tempElement = document.createElement("b");
            tempElement.className = "centerMarginsSelf";
            tempElement.textContent = "No Processes To Add";
            tempDiv.appendChild(tempElement);
        }

        confirmationForm.appendChild(tempDiv);

        document.querySelectorAll('.addProcessElement').forEach(el => {
            el.addEventListener('click', () => {
                currentService.processes.push({
                    id: el.dataset.id,
                    name: el.dataset.name
                });
                ShowServiceProcess();
                ShowAddProcessesBox(); // refresh modal
            });
        });

        confirmation.style.display = 'flex';
    }

    updateServiceProcessButton.addEventListener('click', function() {
        if (updateServiceProcessButton.dataset.interactable === "0") return;
        confirmationTitle.innerHTML = "Update Service Process?";
        confirmationForm.action = "index.php?page=services&action=updateServiceProcess";
        confirmationText.innerHTML = "Are you sure to update the process of the " + currentService.name + " service?";
        confirmationSubmit.value = "Yes Update";
        confirmationSubmit.classList.add("yellowBG");

        document.querySelectorAll('.processListElement').forEach(el => el.remove());
        currentService.processes.forEach(proc => {
            tempElement = document.createElement('input');
            tempElement.type = 'hidden';
            tempElement.name = 'processList[]';
            tempElement.value = proc.id;
            tempElement.className = "processListElement tempElement";
            confirmationForm.appendChild(tempElement);
        });
        confirmation.style.display = 'flex';
    });

    // ================================
    // SUB SERVICE LIST AND INTERACTION
    // ================================
    function ShowSubservices() {
        subservicesContainer.innerHTML = '';
        createSubserviceButton.classList.remove("hidden");

        if (currentService.subservices.length === 0) {
            tempElement = document.createElement("h2");
            tempElement.className = "centerMarginsSelf";
            tempElement.innerHTML = "No Subservices";
            subservicesContainer.appendChild(tempElement);
            return;
        }

        currentService.subservices.forEach(sub => {
            tempDiv = document.createElement("div");
            tempDiv.className = 'roundedMin centerHoriRowLayout flexStatic shadowed clickable fixedScreen noShrink subserviceElement';
            tempDiv.dataset.id = sub.id;
            subservicesContainer.appendChild(tempDiv);

            tempElement = document.createElement("div");
            tempElement.className = 'capitalFirst centerText regMinPadding flexMax skewedXNegBG shadowed';
            tempElement.innerHTML = `<h4 class="whiteText outlineText">${sub.name}</h4>`;
            tempDiv.appendChild(tempElement);

            if (sub.isActive == 1) {
                tempDiv.classList.add("yellowBorder");
                tempElement.classList.add("yellowTransBG");
            } else {
                tempDiv.classList.add("redBorder");
                tempElement.classList.add("redTransBG");
            }

            tempElement = document.createElement("h5");
            tempElement.className = 'capitalFirst centerText regMinPadding minWidth';
            tempElement.textContent = "Orders: " + (subserviceOrderCountMap[sub.id] || 0);
            tempDiv.appendChild(tempElement);

            tempDiv.addEventListener('click', () => {
                SetCurrentSubservice(sub);
                selectedSubserviceIdInput.value = currentSubservice.id;
                document.getElementById('selectedSubserviceTitle').textContent = currentSubservice.name;
                ShowSubserviceStatusButtonsContainer();
                ShowSubserviceDataContainer();
                ShowSubserviceImages();
            });

            // Persistence
            if (lastSubserviceID != -1 && Number(sub.id) == Number(lastSubserviceID)) {
                SetCurrentSubservice(sub);
                selectedSubserviceIdInput.value = currentSubservice.id;
                document.getElementById('selectedSubserviceTitle').textContent = currentSubservice.name;
                ShowSubserviceStatusButtonsContainer();
                ShowSubserviceDataContainer();
                ShowSubserviceImages();
            }
        });
    }

    // ================================
    // SUB SERVICE STATUS BUTTONS
    // ================================
    function ShowSubserviceStatusButtonsContainer() {
        subserviceStatusButtonsContainer.innerHTML = '';

        if (currentSubservice.isActive == 1) {
            // Disable button
            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "redBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
            tempElement.textContent = "Disable";
            tempElement.id = "subserviceStatusButton";
            subserviceStatusButtonsContainer.appendChild(tempElement);

            // Prevent disabling if this is the only active subservice
            const activeCount = currentService.subservices.filter(s => s.isActive == 1).length;
            if (activeCount === 1) {
                tempElement.classList.add("faded", "unclickable");
            }
        } else {
            // Activate button
            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "yellowBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
            tempElement.textContent = "Activate";
            tempElement.id = "subserviceStatusButton";
            subserviceStatusButtonsContainer.appendChild(tempElement);

            // Delete button
            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "redBG noBorder shadowed centerColumnLayout fullHeight";
            tempElement.innerHTML = '<img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors unitHeight">';
            tempElement.id = "deleteSubserviceButton";
            subserviceStatusButtonsContainer.appendChild(tempElement);

            if (currentSubservice.orderCount > 0) {
                tempElement.classList.add("faded", "unclickable");
            } else {
                document.getElementById('deleteSubserviceButton').addEventListener('click', () => {
                    confirmationTitle.innerHTML = "Delete Subservice?";
                    confirmationForm.action = "index.php?page=services&action=deleteSubservice";
                    confirmationText.innerHTML = "Are you sure to delete the " + currentSubservice.name + " subservice?";
                    confirmationSubmit.value = "Yes delete";
                    confirmation.style.display = 'flex';
                });
            }
        }

        // Toggle status (if not blocked)
        const isBlocked = (currentSubservice.isActive == 1 && currentService.subservices.filter(s => s.isActive == 1).length === 1);
        if (!isBlocked) {
            document.getElementById('subserviceStatusButton').addEventListener('click', function() {
                confirmationTitle.innerHTML = "Toggle Subservice Status?";
                confirmationForm.action = "index.php?page=services&action=toggleSubserviceStatus";
                confirmationText.innerHTML = "Are you sure to " + this.textContent + " the " + currentSubservice.name + " subservice?";
                confirmationSubmit.value = "Yes " + this.textContent;
                if (this.textContent == "Activate") confirmationSubmit.classList.add("yellowBG");
                confirmation.style.display = 'flex';
            });
        }
    }

    // ================================
    // SUB SERVICE DATA CONTAINER (description & price)
    // ================================
    function ShowSubserviceDataContainer() {
        subserviceDataContainer.getElementsByTagName('h2')[0].classList.add("hidden");

        const container = subserviceDataContainer.getElementsByTagName('div')[0];
        const formElement = container.getElementsByTagName('form')[0];
        const descriptionInput = formElement.getElementsByTagName('textarea')[0];
        const serviceIdInput = formElement.getElementsByTagName('input')[0];
        const subserviceIdInput = formElement.getElementsByTagName('input')[1];
        const priceInput = formElement.getElementsByTagName('input')[2];

        container.classList.remove("hidden");

        serviceIdInput.value = currentService.id;
        subserviceIdInput.value = currentSubservice.id;
        descriptionInput.value = currentSubservice.description;
        descriptionInput.placeholder = currentSubservice.description;
        priceInput.value = currentSubservice.pricePerUnit;
        priceInput.placeholder = currentSubservice.pricePerUnit;
    }

    // ================================
    // SUB SERVICE IMAGES GALLERY
    // ================================
    function ShowSubserviceImages() {
        subserviceImagesContainer.innerHTML = '';

        if (currentSubservice.images.length === 0) {
            subserviceImagesContainer.innerHTML = `<div class="centerMarginsSelf fullHeight centerColumnLayout fitWidth"><b>No Images</b></div>`;
            return;
        }

        currentSubservice.images.forEach(img => {
            tempDiv = document.createElement("div");
            tempDiv.className = "squareSize fixedScreen centerColumnLayout relatived shadowed roundedTin";

            // Remove button
            tempElement = document.createElement("a");
            tempElement.className = "circle squareSize unitHeight norEastAbsolute centerColumnLayout importantInput closeCorner swapRight shadowed minZ removeImageButton";
            tempElement.innerHTML = '<img src="../../Shared/Img/XIcon.png" alt="X" class="invertColors">';
            tempElement.dataset.id = img.id;
            tempElement.dataset.imageName = img.name;
            tempDiv.appendChild(tempElement);

            // Image
            tempElement = document.createElement("img");
            tempElement.className = "fullHeight absoluted clickable subserviceImageElement";
            tempElement.src = "../../Storage/SubserviceImages/" + img.name;
            tempElement.alt = "Image";
            tempDiv.appendChild(tempElement);

            subserviceImagesContainer.appendChild(tempDiv);
        });

        document.querySelectorAll('.subserviceImageElement').forEach(el => {
            el.addEventListener('click', () => {
                imageBoxImage.src = el.src;
                imageBox.style.display = 'flex';
            });
        });

        document.querySelectorAll('.removeImageButton').forEach(el => {
            el.addEventListener('click', () => {
                confirmationTitle.innerHTML = "Remove Subservice Image?";
                confirmationForm.action = "index.php?page=services&action=removeSubserviceImage";
                confirmationText.innerHTML = "Are you sure to remove this image from the " + currentSubservice.name + " subservice?";
                confirmationSubmit.value = "Yes Remove";

                const hiddenId = document.createElement("input");
                hiddenId.type = "hidden";
                hiddenId.name = "selectedID";
                hiddenId.value = el.dataset.id;
                hiddenId.className = "tempElement";
                confirmationForm.appendChild(hiddenId);

                const previewDiv = document.createElement("div");
                previewDiv.className = "fullWidth tempElement centerHoriRowLayout regMinPadding";
                confirmationForm.appendChild(previewDiv);

                const previewImg = document.createElement("img");
                previewImg.className = "fullWidth roundedMin shadowed";
                previewImg.src = "../../Storage/SubserviceImages/" + el.dataset.imageName;
                previewDiv.appendChild(previewImg);

                confirmation.style.display = 'flex';
            });
        });
    }

    // ================================
    // SUB SERVICE IMAGE UPLOAD (Multiple)
    // ================================
    addSubserviceImageButton.addEventListener('click', function() {
        confirmationContent.classList.add("fitWidth");
        confirmationForm.action = "index.php?page=services&action=uploadSubserviceImages";

        // File input row
        tempDiv = document.createElement("div");
        tempDiv.className = "tempElement centerHoriRowLayout minGap";
        confirmationForm.appendChild(tempDiv);

        tempElement = document.createElement("b");
        tempElement.textContent = "Upload File:";
        tempDiv.appendChild(tempElement);

        const fileInput = document.createElement("input");
        fileInput.type = "file";
        fileInput.name = "images[]";
        fileInput.accept = "image/*";
        fileInput.multiple = true;
        fileInput.required = true;
        fileInput.className = "flexMax";
        tempDiv.appendChild(fileInput);

        // Preview container
        const previewDiv = document.createElement("div");
        previewDiv.className = "tempElement hidden centerHoriRowLayout minGap regPadding fitWidth scrollableX halfScreenMaxWidth fullMinWidth halfScreenHeight";
        confirmationForm.appendChild(previewDiv);

        confirmationTitle.innerHTML = "Upload Design Image";
        confirmationText.innerHTML = "Please upload images for this subservice.";
        confirmationSubmit.value = "Upload";
        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");

        confirmationForm.enctype = "multipart/form-data";
        confirmation.style.display = 'flex';

        fileInput.addEventListener('change', () => {
            previewDiv.innerHTML = '';
            const files = fileInput.files;
            if (files.length === 0) {
                previewDiv.classList.add("hidden");
                return;
            }

            for (let i = 0; i < files.length; i++) {
                if (!files[i].type.startsWith("image/")) {
                    alert("Only images are allowed. File: " + files[i].name);
                    fileInput.value = "";
                    return;
                }
            }

            if (files.length === 1) {
                const uploadedImage = document.createElement("img");
                uploadedImage.className = "fullHeight roundedMin shadowed centerMarginsSelf";
                uploadedImage.src = URL.createObjectURL(files[0]);
                previewDiv.appendChild(uploadedImage);
            } else {
                Array.from(files).forEach(file => {
                    const uploadedImage = document.createElement("img");
                    uploadedImage.className = "fullHeight roundedMin shadowed";
                    uploadedImage.src = URL.createObjectURL(file);
                    previewDiv.appendChild(uploadedImage);
                });
            }
            previewDiv.classList.remove("hidden");
        });
    });

    // ================================
    // UTILITIES: Reset subservice panel when service changes
    // ================================
    function ResetSubserviceHeader() {
        document.getElementById('selectedSubserviceTitle').textContent = "No Subservice Selected";
        subserviceStatusButtonsContainer.innerHTML = '';
        subserviceDataContainer.getElementsByTagName('h2')[0].classList.remove("hidden");
        subserviceDataContainer.getElementsByTagName('div')[0].classList.add("hidden");
    }

    // ================================
    // CREATE SERVICE MODAL
    // ================================
    createServiceButton.addEventListener('click', () => {
        confirmationTitle.innerHTML = "Create Service";
        confirmationForm.action = "index.php?page=services&action=createService";
        confirmationText.innerHTML = "Please enter a unique service name.";
        confirmationSubmit.value = "Create";
        confirmationSubmit.classList.add("yellowBG", "noBorder");

        tempElement = document.createElement("input");
        tempElement.type = "text";
        tempElement.name = "name";
        tempElement.placeholder = "Service Name";
        tempElement.id = "nameInput";
        tempElement.className = "tempElement";
        tempElement.required = true;
        confirmationForm.appendChild(tempElement);

        confirmation.style.display = 'flex';
    });

    // ================================
    // CREATE SUBSERVICE MODAL
    // ================================
    createSubserviceButton.addEventListener('click', () => {
        if (!currentService.id) {
            alert("Please select a service first.");
            return;
        }
        confirmationTitle.innerHTML = "Create Subservice";
        confirmationForm.action = "index.php?page=services&action=createSubservice";
        confirmationText.innerHTML = "Please enter a unique subservice name for the " + currentService.name + " service.";
        confirmationSubmit.value = "Create";
        confirmationSubmit.classList.add("yellowBG", "noBorder");

        tempElement = document.createElement("input");
        tempElement.type = "text";
        tempElement.name = "name";
        tempElement.placeholder = "Subservice Name";
        tempElement.id = "nameInput";
        tempElement.className = "tempElement";
        tempElement.required = true;
        confirmationForm.appendChild(tempElement);

        confirmation.style.display = 'flex';
    });

    // ================================
    // CONFIRMATION DIALOG CLEANUP
    // ================================
    confirmationCancel.addEventListener('click', () => {
        confirmationSubmit.classList.remove("yellowBG", "hidden");
        document.querySelectorAll('.tempElement').forEach(el => el.remove());
    });

    confirmationBG.addEventListener('click', () => {
        confirmationSubmit.classList.remove("yellowBG", "hidden");
        document.querySelectorAll('.tempElement').forEach(el => el.remove());
    });
</script>

</html>