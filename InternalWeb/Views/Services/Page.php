<!DOCTYPE html>
<html>

<head>
    <title>Services Panel - Hontoria OMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <style>
        @media (max-width: 500px) {
            .asideLayout>main>section {
                min-width: fit-content;
            }

            .asideLayout>main>section>*:nth-child(1) {
                min-width: calc(100vw - 3rem);
                max-width: calc(100vw - 3rem);
            }

            .asideLayout>main>section>*:nth-child(2) {
                min-width: 120vw;
                max-width: 120vw;
            }

            .gridFlex.midGrids.minGap>* {
                width: 47% !important;
            }
        }

        @media (max-width: 450px) {
            .asideLayout>main>section>*:nth-child(2) {
                min-width: 140vw;
                max-width: 140vw;
            }

            .gridFlex.midGrids.minGap>* {
                width: 47% !important;
            }

            .asideLayout>main>section>*:nth-child(2)>*:nth-child(1) {
                min-width: 130vw;
                max-width: 130vw;
                position: sticky;
                left: calc(-30vw - 2rem);
            }

            .asideLayout>main>section>*:nth-child(2)>*:nth-child(2)>*:nth-child(1) {
                min-width: 130vw;
                max-width: 130vw;
                position: sticky;
                left: calc(-30vw - 2rem);
            }
        }

        @media (max-width: 400px) {
            .asideLayout>main>section>*:nth-child(2) {
                min-width: 160vw;
                max-width: 160vw;
            }
        }

        @media (max-width: 350px) {
            .asideLayout>main>section>*:nth-child(2)>*:nth-child(1) {
                min-width: 140vw;
                max-width: 140vw;
                position: sticky;
                left: calc(-40vw - 2rem);
            }

            .asideLayout>main>section>*:nth-child(2)>*:nth-child(2)>*:nth-child(1) {
                min-width: 140vw;
                max-width: 140vw;
                position: sticky;
                left: calc(-40vw - 2rem);
            }

            .asideLayout>main>section>*:nth-child(2) {
                min-width: 180vw;
                max-width: 180vw;
            }
        }

        @media (max-width: 300px) {
            .asideLayout>main>section>*:nth-child(2) {
                min-width: 200vw;
                max-width: 200vw;
            }
        }
    </style>
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <h1 class="titleLogo minGap tinHeight">
            <img src="../../Shared/Img/GearIcon.png" alt="Gear"> Services Panel
            <div class="rowLayout minGap flexMax contentFlexEnd">
                <a href="index.php?page=services&action=manageProcesses"
                    class="roundedMin centerColumnLayout importantInput regPadding emphasizedText shadowed">
                    Manage Processes
                </a>
            </div>
        </h1>

        <?php include("../Views/.Components/MessageBox.php"); ?>

        <section class="rowLayout flexMax midGap">
            <!-- ========== SERVICES LIST (LEFT) ========== -->
            <div class="flexMinExtra columnLayout midGap">
                <section class="centerColumnLayout roundedMid flexMid">
                    <div class="box fullHeight fullWidth roundedMid columnLayout tinGap">
                        <div class="centerHoriRowLayout">
                            <h2 class="flexMax">Services:</h2>
                            <button type="button"
                                class="darkBG emphasizedText noBorder shadowed whiteText centerColumnLayout fullHeight"
                                id="createServiceButton">
                                <b>Create</b>
                            </button>
                        </div>
                        <section class="minGap columnLayout scrollable flexMax noFlexBasis noMinHeight contentFlexStart regMinPadding"
                            id="servicesList">
                            <?php foreach ($servicesList as $service): ?>
                                <?php
                                $name = htmlspecialchars(trim($service['name']));
                                $borderClass = $service['isActive'] ? 'yellowBorder' : 'redBorder';
                                $bgClass = $service['isActive'] ? 'yellowTransBG' : 'redTransBG';
                                $orderCount = $serviceOrderCountMap[$service['id']] ?? 0;
                                ?>
                                <div class="roundedMin centerHoriRowLayout flexStatic serviceElement <?= $borderClass ?> shadowed clickable fixedScreen noShrink"
                                    data-id="<?= $service['id'] ?>"
                                    data-name="<?= $name ?>"
                                    data-is-active="<?= $service['isActive'] ?>"
                                    data-has-design="<?= $service['hasDesign'] ?>"
                                    data-has-variable-list="<?= $service['hasVariableList'] ?>"
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

            <!-- ========== SERVICE DETAILS (RIGHT) ========== -->
            <section class="columnLayout midGap flexMax">
                <!-- Service title & status/objective buttons -->
                <section class="centerColumnLayout roundedMid minGap">
                    <div class="centerHoriRowLayout minGap box roundedMid fullHeight fullWidth">
                        <h2 id="selectedServiceTitle" class="capitalFirst flexMax">No Service Selected</h2>
                        <div class="flexMax centerHoriRowLayout minGap fullHeight" id="objectiveButtonsContainer"></div>
                        <div class="flexMid centerHoriRowLayout minGap fullHeight" id="serviceStatusButtonsContainer"></div>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>

                <!-- Service Process -->
                <div class="columnLayout flexMax midGap">
                    <section class="centerColumnLayout roundedMid minGap flexMid">
                        <div class="columnLayout minGap box roundedMid fullHeight fullWidth">
                            <div class="centerHoriRowLayout minGap">
                                <h2 class="flexMax">Service Process:</h2>
                                <button type="button"
                                    class="darkBG emphasizedText noBorder shadowed whiteText centerColumnLayout hidden"
                                    id="updateServiceProcessButton">
                                    Update Service Process
                                </button>
                            </div>
                            <div class="centerHoriRowLayout minGap flexMax" id="serviceProcess">
                                <h2 class="centerMarginsSelf">No Service Selected</h2>
                            </div>
                        </div>
                        <div class="gradientBorderDiag"></div>
                    </section>

                    <!-- Subservices & subservice details -->
                    <div class="rowLayout flexMax midGap noMinHeight noFlexBasis">
                        <section class="centerColumnLayout roundedMid minGap flexMid">
                            <div class="box fullHeight fullWidth roundedMid columnLayout tinGap">
                                <div class="centerHoriRowLayout">
                                    <h2 class="flexMax">Subservices:</h2>
                                    <button type="button"
                                        class="darkBG emphasizedText noBorder shadowed whiteText centerColumnLayout fullHeight hidden"
                                        id="createSubserviceButton">
                                        <b>Create</b>
                                    </button>
                                </div>
                                <section class="minGap columnLayout scrollable flexMax noFlexBasis noMinHeight contentFlexStart regMinPadding"
                                    id="subservicesContainer">
                                    <h2 class="centerMarginsSelf">No Service Selected</h2>
                                </section>
                            </div>
                            <div class="gradientBorderDiag"></div>
                        </section>

                        <div class="columnLayout midGap flexMax">
                            <!-- Subservice title & status -->
                            <section class="centerRowLayout roundedMid">
                                <div class="centerHoriRowLayout minGap box roundedMid fullHeight fullWidth">
                                    <h2 class="flexMax capitalFirst" id="selectedSubserviceTitle">No Subservice Selected</h2>
                                    <div class="centerHoriRowLayout minGap fullHeight flexMid" id="subserviceStatusButtonsContainer"></div>
                                </div>
                                <div class="gradientBorderDiag"></div>
                            </section>

                            <!-- Subservice Data (description, price, images) -->
                            <section class="centerRowLayout roundedMid flexMax">
                                <div class="centerHoriRowLayout minGap box roundedMid fullHeight fullWidth" id="subserviceDataContainer">
                                    <h2 class="centerMarginsSelf">No Subservice Selected</h2>
                                    <div class="centerHoriRowLayout minGap fullHeight fullWidth hidden">
                                        <form method="POST" class="columnLayout minGap fullWidth flexMid fullHeight"
                                            action="index.php?page=services&action=updateSubserviceInfo">
                                            <input type="hidden" name="selectedServiceID">
                                            <input type="hidden" name="selectedSubserviceID">
                                            <div class="flexMax columnLayout tinGap">
                                                <b>Description</b>
                                                <textarea name="description"
                                                    class="scrollableTextarea minHeight fullWidth flexMax minPadding justifiedText unresizeable"
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
                                                <button type="button"
                                                    class="darkBG noBorder shadowed whiteText centerColumnLayout fullHeight roundedTin"
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
    // ================================
    // DOM References
    // ================================
    const createServiceButton = document.getElementById('createServiceButton');
    const serviceElements = document.querySelectorAll('.serviceElement');
    const serviceStatusContainer = document.getElementById('serviceStatusButtonsContainer');
    const objectiveContainer = document.getElementById('objectiveButtonsContainer');
    const updateServiceProcessButton = document.getElementById('updateServiceProcessButton');
    const serviceProcess = document.getElementById('serviceProcess');
    const subservicesContainer = document.getElementById('subservicesContainer');
    const createSubserviceButton = document.getElementById('createSubserviceButton');
    const subserviceStatusContainer = document.getElementById('subserviceStatusButtonsContainer');
    const subserviceDataContainer = document.getElementById('subserviceDataContainer');
    const addImageButton = document.getElementById('addSubserviceImageButton');
    const imagesContainer = document.getElementById('subserviceImagesContainer');

    // ================================
    // Server Data (injected via PHP)
    // ================================
    const serviceProcessList = <?= json_encode($serviceProcessList) ?>;
    const subserviceList = <?= json_encode($subserviceList) ?>;
    const subserviceOrderTally = <?= json_encode($subserviceOrderCountTally) ?>;
    const processesList = <?= json_encode($processesList) ?>;
    const subserviceImageList = <?= json_encode($subserviceImageList) ?>;
    const lastServiceID = <?= $serviceID ?>;
    const lastSubserviceID = <?= $subserviceID ?>;

    // Build fast lookup maps
    const serviceProcessMap = {}; // serviceID -> [{id, name}]
    serviceProcessList.forEach(p => {
        if (!serviceProcessMap[p.serviceID]) serviceProcessMap[p.serviceID] = [];
        serviceProcessMap[p.serviceID].push({
            id: p.id,
            name: p.name
        });
    });

    const subserviceMap = {}; // serviceID -> full subservice objects
    subserviceList.forEach(s => {
        if (!subserviceMap[s.serviceID]) subserviceMap[s.serviceID] = [];
        subserviceMap[s.serviceID].push(s);
    });

    const subserviceOrderCountMap = {}; // subserviceID -> count
    subserviceOrderTally.forEach(t => subserviceOrderCountMap[t.subserviceID] = t.orderCount);

    const subserviceImageMap = {}; // subserviceID -> [{id, name}]
    subserviceImageList.forEach(img => {
        if (!subserviceImageMap[img.subserviceID]) subserviceImageMap[img.subserviceID] = [];
        subserviceImageMap[img.subserviceID].push({
            id: img.id,
            name: img.imageName
        });
    });

    // ================================
    // State
    // ================================
    const currentService = {
        id: null,
        name: '',
        status: 0,
        orderCount: 0,
        hasDesign: false,
        hasVariableList: false,
        processes: [],
        subservices: [],
        subservicesMap: {}
    };

    const currentSubservice = {
        id: null,
        name: '',
        isActive: 0,
        orderCount: 0,
        description: '',
        pricePerUnit: 0,
        images: []
    };

    // ================================
    // Initialization
    // ================================
    document.addEventListener("DOMContentLoaded", () => {
        confirmationCancel.value = "No Cancel";

        // Persistent hidden inputs for service/subservice IDs
        const sidInput = document.createElement("input");
        sidInput.type = "hidden";
        sidInput.name = "selectedServiceID";
        sidInput.value = lastServiceID || -1;
        confirmationForm.appendChild(sidInput);

        const ssidInput = document.createElement("input");
        ssidInput.type = "hidden";
        ssidInput.name = "selectedSubserviceID";
        ssidInput.value = lastSubserviceID || -1;
        confirmationForm.appendChild(ssidInput);

        // Attach service click handlers
        serviceElements.forEach(el => el.addEventListener('click', () => OnServiceClick(el)));

        // Auto‑select if ID provided
        if (lastServiceID != -1) {
            for (const el of serviceElements) {
                if (el.dataset.id == lastServiceID) {
                    OnServiceClick(el);
                    break;
                }
            }
        }
    });

    // ================================
    // Helper Functions
    // ================================
    function RebuildSubservicesMap() {
        currentService.subservicesMap = {};
        currentService.subservices.forEach(s => {
            currentService.subservicesMap[s.id] = {
                description: s.description,
                pricePerUnit: s.pricePerUnit
            };
        });
    }

    function SetCurrentSubservice(sub) {
        currentSubservice.id = sub.id;
        currentSubservice.name = sub.name;
        currentSubservice.isActive = sub.isActive;
        currentSubservice.orderCount = subserviceOrderCountMap[sub.id] || 0;
        currentSubservice.description = sub.description;
        currentSubservice.pricePerUnit = sub.pricePerUnit;
        currentSubservice.images = [...(subserviceImageMap[sub.id] || [])];
    }

    function ResetSubservicePanel() {
        document.getElementById('selectedSubserviceTitle').textContent = "No Subservice Selected";
        subserviceStatusContainer.innerHTML = '';
        const h2 = subserviceDataContainer.getElementsByTagName('h2')[0];
        const div = subserviceDataContainer.getElementsByTagName('div')[0];
        h2.classList.remove("hidden");
        div.classList.add("hidden");
    }

    // ================================
    // OnServiceClick
    // ================================
    function OnServiceClick(elem) {
        currentService.id = elem.dataset.id;
        currentService.name = elem.dataset.name;
        currentService.status = parseInt(elem.dataset.isActive);
        currentService.orderCount = parseInt(elem.dataset.orderCount);
        currentService.hasDesign = elem.dataset.hasDesign === '1';
        currentService.hasVariableList = elem.dataset.hasVariableList === '1';

        confirmationForm.querySelector('input[name="selectedServiceID"]').value = currentService.id;

        document.getElementById('selectedServiceTitle').textContent = currentService.name + " Service";

        currentService.processes = [...(serviceProcessMap[currentService.id] || [])];
        currentService.subservices = [...(subserviceMap[currentService.id] || [])];
        RebuildSubservicesMap();

        // Reset subservice
        currentSubservice.id = null;
        currentSubservice.name = '';
        confirmationForm.querySelector('input[name="selectedSubserviceID"]').value = -1;

        ShowServiceStatusButtons();
        ShowObjectiveButtons();
        ShowServiceProcess();
        ResetSubservicePanel();
        ShowSubservices();
    }

    // ================================
    // SERVICE STATUS BUTTONS
    // ================================
    function ShowServiceStatusButtons() {
        serviceStatusContainer.innerHTML = '';

        if (currentService.status == 1) {
            const disableButton = MakeButton("Disable", "redBG", "serviceStatusButton");
            serviceStatusContainer.appendChild(disableButton);
        } else {
            const activateButton = MakeButton("Activate", "yellowBG", "serviceStatusButton");
            const canActivate = currentService.processes.length > 0 &&
                currentService.subservices.length > 0 &&
                currentService.subservices[0].isActive == 1;
            if (!canActivate) activateButton.classList.add("faded", "unclickable");
            serviceStatusContainer.appendChild(activateButton);

            // Delete button
            const deleteButton = document.createElement("button");
            deleteButton.type = "button";
            deleteButton.className = "redBG noBorder shadowed centerColumnLayout fullHeight";
            deleteButton.innerHTML = '<img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors unitHeight">';
            deleteButton.id = "deleteServiceButton";
            serviceStatusContainer.appendChild(deleteButton);

            if (currentService.orderCount > 0) {
                deleteButton.classList.add("faded", "unclickable");
            } else {
                deleteButton.addEventListener('click', () => {
                    confirmationTitle.innerHTML = "Delete Service?";
                    confirmationForm.action = "index.php?page=services&action=deleteService";
                    confirmationText.innerHTML = "Are you sure to delete the " + currentService.name + " service?";
                    confirmationSubmit.value = "Yes delete";
                    confirmation.style.display = 'flex';
                });
            }
        }

        // Status toggle action
        const statusButton = document.getElementById('serviceStatusButton');
        if (statusButton) {
            statusButton.addEventListener('click', function() {
                const canActivate = currentService.processes.length > 0 &&
                    currentService.subservices.length > 0 &&
                    currentService.subservices[0].isActive == 1;
                if (canActivate || currentService.status == 1) {
                    confirmationTitle.innerHTML = "Toggle Service Status?";
                    confirmationForm.action = "index.php?page=services&action=toggleServiceStatus";
                    confirmationText.innerHTML = "Are you sure to " + this.textContent + " the " + currentService.name + " service?";
                    confirmationSubmit.value = "Yes " + this.textContent;
                    if (this.textContent == "Activate") confirmationSubmit.classList.add("yellowBG");
                    confirmation.style.display = 'flex';
                }
            });
        }
    }

    // ================================
    // OBJECTIVE BUTTONS (Design / Variable List)
    // ================================
    function ShowObjectiveButtons() {
        objectiveContainer.innerHTML = '';
        const editable = (currentService.orderCount == 0 && currentService.status == 0);

        // Design button
        const designButton = MakeButton(
            currentService.hasDesign ? "Has Design" : "No Design",
            currentService.hasDesign ? "yellowBG" : "redBG",
            "hasDesignButton"
        );
        objectiveContainer.appendChild(designButton);

        // Variable list button
        const listButton = MakeButton(
            currentService.hasVariableList ? "Has Variable List" : "No Variable List",
            currentService.hasVariableList ? "yellowBG" : "redBG",
            "hasVariableListButton"
        );
        objectiveContainer.appendChild(listButton);

        if (!editable) {
            designButton.classList.add("faded", "unclickable");
            listButton.classList.add("faded", "unclickable");
        } else {
            document.getElementById('hasDesignButton').addEventListener('click', function() {
                confirmationTitle.innerHTML = "Toggle Design Objective?";
                confirmationForm.action = "index.php?page=services&action=toggleHasDesign";
                confirmationText.innerHTML = this.textContent == "No Design" ?
                    "Are you sure to activate the design objective?" :
                    "Are you sure to disable the design objective?";
                confirmationSubmit.value = this.textContent == "No Design" ? "Yes Active" : "Yes Disable";
                if (this.textContent == "No Design") confirmationSubmit.classList.add("yellowBG");
                confirmation.style.display = 'flex';
            });
            document.getElementById('hasVariableListButton').addEventListener('click', function() {
                confirmationTitle.innerHTML = "Toggle Variable List Objective?";
                confirmationForm.action = "index.php?page=services&action=toggleHasVariableList";
                confirmationText.innerHTML = this.textContent == "No Variable List" ?
                    "Are you sure to activate the variable list objective?" :
                    "Are you sure to disable the variable list objective?";
                confirmationSubmit.value = this.textContent == "No Variable List" ? "Yes Active" : "Yes Disable";
                if (this.textContent == "No Variable List") confirmationSubmit.classList.add("yellowBG");
                confirmation.style.display = 'flex';
            });
        }
    }

    // ================================
    // SERVICE PROCESS
    // ================================
    function ShowServiceProcess() {
        const editable = (currentService.orderCount == 0 && currentService.status == 0);
        updateServiceProcessButton.classList.toggle("faded", !editable);
        updateServiceProcessButton.classList.toggle("unclickable", !editable);
        updateServiceProcessButton.dataset.interactable = editable ? "1" : "0";
        updateServiceProcessButton.classList.remove("hidden");
        serviceProcess.innerHTML = '';

        if (currentService.processes.length === 0) {
            const emptyDiv = document.createElement("div");
            emptyDiv.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";
            emptyDiv.innerHTML = "<b class='whiteText outlineText'>No Service Process</b>";
            serviceProcess.appendChild(emptyDiv);
            if (editable) {
                const addBtn = CreateAddProcessButton();
                serviceProcess.appendChild(addBtn);
            }
            return;
        }

        // Render each process with arrows
        const processElements = [];
        for (let i = 0; i < currentService.processes.length; i++) {
            const proc = currentService.processes[i];
            const div = document.createElement("div");
            div.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";
            let inner = `<b class='whiteText outlineText'>${proc.name}</b>`;
            if (editable) {
                inner += `<a class="squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove" data-index="${i}">
                            <img src="../../Shared/Img/XIcon.png" alt="X">
                          </a>`;
                if (i > 0 || currentService.processes.length > 1) {
                    if (i < currentService.processes.length - 1) {
                        inner += `<a class="circle squareSize unitHeight souEastAbsolute centerColumnLayout importantInput closeCorner swapRight shadowed" data-index="${i}">
                                    <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors">
                                  </a>`;
                    }
                    if (i > 0) {
                        inner += `<a class="circle squareSize unitHeight souWestAbsolute centerColumnLayout importantInput closeCorner swapLeft shadowed" data-index="${i}">
                                    <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors mirrorX">
                                  </a>`;
                    }
                }
            }
            div.innerHTML = inner;
            processElements.push(div);
        }

        // Add ">" between them
        for (let i = 0; i < processElements.length; i++) {
            serviceProcess.appendChild(processElements[i]);
            if (i < processElements.length - 1) {
                const arrow = document.createElement("h2");
                arrow.textContent = ">";
                serviceProcess.appendChild(arrow);
            }
        }

        // Plus button at the end
        if (editable) {
            serviceProcess.appendChild(CreateAddProcessButton());
        }

        // Event listeners for remove/swap (only when editable)
        if (editable) {
            document.querySelectorAll('.processRemove').forEach(el => {
                el.addEventListener('click', function() {
                    const idx = parseInt(this.dataset.index);
                    currentService.processes.splice(idx, 1);
                    ShowServiceProcess();
                });
            });
            document.querySelectorAll('.swapRight').forEach(el => {
                el.addEventListener('click', function() {
                    const idx = parseInt(this.dataset.index);
                    [currentService.processes[idx], currentService.processes[idx + 1]] = [currentService.processes[idx + 1], currentService.processes[idx]];
                    ShowServiceProcess();
                });
            });
            document.querySelectorAll('.swapLeft').forEach(el => {
                el.addEventListener('click', function() {
                    const idx = parseInt(this.dataset.index);
                    [currentService.processes[idx], currentService.processes[idx - 1]] = [currentService.processes[idx - 1], currentService.processes[idx]];
                    ShowServiceProcess();
                });
            });
        }
    }

    function CreateAddProcessButton() {
        const btn = document.createElement("div");
        btn.className = "circle squareSize duoHeight darkBG roundedMin shadowed centerRowLayout regMinPadding";
        btn.innerHTML = '<img src="../../Shared/Img/CrossIcon.png" alt="Cross" class="invertColors">';
        btn.addEventListener('click', ShowAddProcessesBox);
        return btn;
    }

    function ShowAddProcessesBox() {
        const currentNames = new Set(currentService.processes.map(p => p.name));
        confirmationTitle.innerHTML = "Add Processes";
        confirmationText.innerHTML = "Click on processes that you want to add to the " + currentService.name + " service process.";
        confirmationSubmit.classList.add("hidden");
        confirmationCancel.value = "Return";

        document.querySelectorAll('.tempElement').forEach(el => el.remove());

        const listDiv = document.createElement("div");
        listDiv.className = 'midHeight scrollable columnLayout minGap regMinPadding tempElement';

        processesList.forEach(proc => {
            if (currentNames.has(proc.name)) return;
            const item = document.createElement('div');
            item.className = 'tinHeight noShrink roundedMin centerColumnLayout bordered darkTransBG emphasizedText capitalFirst shadowed clickable';
            item.innerHTML = '<b>' + proc.name + '</b>';
            item.dataset.id = proc.id;
            item.dataset.name = proc.name;
            item.addEventListener('click', () => {
                currentService.processes.push({
                    id: proc.id,
                    name: proc.name
                });
                ShowServiceProcess();
                ShowAddProcessesBox(); // refresh
            });
            listDiv.appendChild(item);
        });

        if (listDiv.children.length === 0) {
            const msg = document.createElement("b");
            msg.className = "centerMarginsSelf";
            msg.textContent = "No Processes To Add";
            listDiv.appendChild(msg);
        }

        confirmationForm.appendChild(listDiv);
        confirmation.style.display = 'flex';
    }

    // ================================
    // SUBSERVICES LIST
    // ================================
    function ShowSubservices() {
        subservicesContainer.innerHTML = '';
        createSubserviceButton.classList.remove("hidden");

        if (currentService.subservices.length === 0) {
            const msg = document.createElement("h2");
            msg.className = "centerMarginsSelf";
            msg.innerHTML = "No Subservices";
            subservicesContainer.appendChild(msg);
            return;
        }

        currentService.subservices.forEach(sub => {
            const card = document.createElement("div");
            card.className = 'roundedMin centerHoriRowLayout flexStatic shadowed clickable fixedScreen noShrink subserviceElement';
            card.dataset.id = sub.id;

            const nameDiv = document.createElement("div");
            nameDiv.className = 'capitalFirst centerText regMinPadding flexMax skewedXNegBG shadowed';
            nameDiv.innerHTML = '<h4 class="whiteText outlineText">' + htmlspecialchars(sub.name) + '</h4>';
            card.appendChild(nameDiv);

            const orderCount = subserviceOrderCountMap[sub.id] || 0;
            const countSpan = document.createElement("h5");
            countSpan.className = 'capitalFirst centerText regMinPadding minWidth';
            countSpan.textContent = "Orders: " + orderCount;
            card.appendChild(countSpan);

            if (sub.isActive == 1) {
                card.classList.add("yellowBorder");
                nameDiv.classList.add("yellowTransBG");
            } else {
                card.classList.add("redBorder");
                nameDiv.classList.add("redTransBG");
            }

            card.addEventListener('click', () => {
                SetCurrentSubservice(sub);
                confirmationForm.querySelector('input[name="selectedSubserviceID"]').value = currentSubservice.id;
                document.getElementById('selectedSubserviceTitle').textContent = currentSubservice.name;
                ShowSubserviceStatusButtons();
                ShowSubserviceData();
                ShowSubserviceImages();
            });

            subservicesContainer.appendChild(card);

            // Persistence selection
            if (lastSubserviceID != -1 && Number(sub.id) == Number(lastSubserviceID)) {
                SetCurrentSubservice(sub);
                confirmationForm.querySelector('input[name="selectedSubserviceID"]').value = currentSubservice.id;
                document.getElementById('selectedSubserviceTitle').textContent = currentSubservice.name;
                ShowSubserviceStatusButtons();
                ShowSubserviceData();
                ShowSubserviceImages();
            }
        });
    }

    // ================================
    // SUBSERVICE STATUS BUTTONS
    // ================================
    function ShowSubserviceStatusButtons() {
        subserviceStatusContainer.innerHTML = '';
        const activeSubserviceCount = currentService.subservices.filter(s => s.isActive == 1).length;

        if (currentSubservice.isActive == 1) {
            const disableButton = MakeButton("Disable", "redBG", "subserviceStatusButton");
            if (activeSubserviceCount === 1) disableButton.classList.add("faded", "unclickable");
            subserviceStatusContainer.appendChild(disableButton);
        } else {
            const activateButton = MakeButton("Activate", "yellowBG", "subserviceStatusButton");
            subserviceStatusContainer.appendChild(activateButton);

            const deleteButton = document.createElement("button");
            deleteButton.type = "button";
            deleteButton.className = "redBG noBorder shadowed centerColumnLayout fullHeight";
            deleteButton.innerHTML = '<img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors unitHeight">';
            deleteButton.id = "deleteSubserviceButton";
            subserviceStatusContainer.appendChild(deleteButton);

            if (currentSubservice.orderCount > 0) {
                deleteButton.classList.add("faded", "unclickable");
            } else {
                deleteButton.addEventListener('click', () => {
                    confirmationTitle.innerHTML = "Delete Subservice?";
                    confirmationForm.action = "index.php?page=services&action=deleteSubservice";
                    confirmationText.innerHTML = "Are you sure to delete the " + currentSubservice.name + " subservice?";
                    confirmationSubmit.value = "Yes delete";
                    confirmation.style.display = 'flex';
                });
            }
        }

        const statusBtn = document.getElementById('subserviceStatusButton');
        if (statusBtn && !(currentSubservice.isActive == 1 && activeSubserviceCount === 1)) {
            statusBtn.addEventListener('click', function() {
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
    // SUBSERVICE DATA (Description, Price)
    // ================================
    function ShowSubserviceData() {
        const h2 = subserviceDataContainer.getElementsByTagName('h2')[0];
        const div = subserviceDataContainer.getElementsByTagName('div')[0];
        h2.classList.add("hidden");
        div.classList.remove("hidden");

        const form = div.getElementsByTagName('form')[0];
        form.querySelector('input[name="selectedServiceID"]').value = currentService.id;
        form.querySelector('input[name="selectedSubserviceID"]').value = currentSubservice.id;
        form.querySelector('textarea[name="description"]').value = currentSubservice.description;
        form.querySelector('textarea[name="description"]').placeholder = currentSubservice.description;
        form.querySelector('input[name="pricePerUnit"]').value = currentSubservice.pricePerUnit;
        form.querySelector('input[name="pricePerUnit"]').placeholder = currentSubservice.pricePerUnit;
    }

    // ================================
    // SUBSERVICE IMAGES
    // ================================
    function ShowSubserviceImages() {
        imagesContainer.innerHTML = '';
        if (currentSubservice.images.length === 0) {
            imagesContainer.innerHTML = '<div class="centerMarginsSelf fullHeight centerColumnLayout fitWidth"><b>No Images</b></div>';
            return;
        }

        currentSubservice.images.forEach(img => {
            const card = document.createElement("div");
            card.className = "squareSize fixedScreen centerColumnLayout relatived shadowed roundedTin";

            const removeLink = document.createElement("a");
            removeLink.className = "circle squareSize unitHeight norEastAbsolute centerColumnLayout importantInput closeCorner swapRight shadowed minZ";
            removeLink.innerHTML = '<img src="../../Shared/Img/XIcon.png" alt="X" class="invertColors">';
            removeLink.dataset.id = img.id;
            removeLink.dataset.imageName = img.name;
            removeLink.addEventListener('click', () => {
                confirmationTitle.innerHTML = "Remove Subservice Image?";
                confirmationForm.action = "index.php?page=services&action=removeSubserviceImage";
                confirmationText.innerHTML = "Are you sure to remove this image from the " + currentSubservice.name + " subservice?";
                confirmationSubmit.value = "Yes Remove";

                const hiddenId = document.createElement("input");
                hiddenId.type = "hidden";
                hiddenId.name = "selectedID";
                hiddenId.value = img.id;
                hiddenId.className = "tempElement";
                confirmationForm.appendChild(hiddenId);

                const previewDiv = document.createElement("div");
                previewDiv.className = "fullWidth tempElement centerHoriRowLayout regMinPadding";
                const previewImg = document.createElement("img");
                previewImg.className = "fullWidth roundedMin shadowed";
                previewImg.src = "../../Storage/SubserviceImages/" + img.name;
                previewDiv.appendChild(previewImg);
                confirmationForm.appendChild(previewDiv);

                confirmation.style.display = 'flex';
            });
            card.appendChild(removeLink);

            const imageEl = document.createElement("img");
            imageEl.className = "fullHeight absoluted clickable";
            imageEl.src = "../../Storage/SubserviceImages/" + img.name;
            imageEl.alt = "Image";
            imageEl.addEventListener('click', () => {
                imageBoxImage.src = imageEl.src;
                imageBox.style.display = 'flex';
            });
            card.appendChild(imageEl);

            imagesContainer.appendChild(card);
        });
    }

    // ================================
    // IMAGE UPLOAD
    // ================================
    addImageButton.addEventListener('click', function() {
        confirmationContent.classList.add("fitWidth");
        confirmationForm.action = "index.php?page=services&action=uploadSubserviceImages";

        const fileRow = document.createElement("div");
        fileRow.className = "tempElement centerHoriRowLayout minGap";
        const label = document.createElement("b");
        label.textContent = "Upload File:";
        fileRow.appendChild(label);

        const fileInput = document.createElement("input");
        fileInput.type = "file";
        fileInput.name = "images[]";
        fileInput.accept = "image/*";
        fileInput.multiple = true;
        fileInput.required = true;
        fileInput.className = "flexMax";
        fileRow.appendChild(fileInput);
        confirmationForm.appendChild(fileRow);

        const previewDiv = document.createElement("div");
        previewDiv.className = "tempElement hidden centerHoriRowLayout minGap regPadding fitWidth scrollableX" +
            " halfScreenMaxWidth fullMinWidth halfScreenHeight";
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
            Array.from(files).forEach(file => {
                const img = document.createElement("img");
                img.className = "fullHeight roundedMin shadowed";
                img.src = URL.createObjectURL(file);
                previewDiv.appendChild(img);
            });
            previewDiv.classList.remove("hidden");
        });
    });

    // ================================
    // CREATE SERVICE MODAL
    // ================================
    createServiceButton.addEventListener('click', () => {
        confirmationTitle.innerHTML = "Create Service";
        confirmationForm.action = "index.php?page=services&action=createService";
        confirmationText.innerHTML = "Please enter a unique service name.";
        confirmationSubmit.value = "Create";
        confirmationSubmit.classList.add("yellowBG", "noBorder");

        const input = document.createElement("input");
        input.type = "text";
        input.name = "name";
        input.placeholder = "Service Name";
        input.className = "tempElement";
        input.required = true;
        confirmationForm.appendChild(input);

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

        const input = document.createElement("input");
        input.type = "text";
        input.name = "name";
        input.placeholder = "Subservice Name";
        input.className = "tempElement";
        input.required = true;
        confirmationForm.appendChild(input);

        confirmation.style.display = 'flex';
    });

    // ================================
    // CLEANUP
    // ================================
    confirmationCancel.addEventListener('click', () => {
        confirmationSubmit.classList.remove("yellowBG", "hidden");
        document.querySelectorAll('.tempElement').forEach(el => el.remove());
    });
    confirmationBG.addEventListener('click', () => {
        confirmationSubmit.classList.remove("yellowBG", "hidden");
        document.querySelectorAll('.tempElement').forEach(el => el.remove());
    });

    // ================================
    // UTILITY
    // ================================
    function MakeButton(text, bgClass, id) {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = `${bgClass} emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight`;
        btn.textContent = text;
        btn.id = id;
        return btn;
    }

    function htmlspecialchars(str) {
        const div = document.createElement("div");
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }
</script>

</html>