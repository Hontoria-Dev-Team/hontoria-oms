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
                <?php if (in_array('canManageServiceProcesses', $_SESSION['permissions'])): ?>
                    <a href="index.php?page=services&action=manageProcesses" class="roundedMin centerColumnLayout importantInput regPadding emphasizedText shadowed">
                        Manage Processes
                    </a>
                <?php endif; ?>
            </div>
        </h1>
        <?php include("../Views/.Components/MessageBox.php"); ?>
        <section class="rowLayout flexMax midGap">
            <div class="flexMinExtra columnLayout midGap">
                <section class="centerColumnLayout roundedMid flexMid">
                    <div class="box fullHeight fullWidth roundedMid columnLayout tinGap">
                        <div class="centerHoriRowLayout">
                            <h2 class="flexMax">Services:</h2>
                            <?php if (in_array('canCreateServices', $_SESSION['permissions'])): ?>
                                <button type="button" class="darkBG emphasizedText noBorder shadowed whiteText centerColumnLayout fullHeight"
                                    id="createServiceButton">
                                    <b>Create</b>
                                </button>
                            <?php else: ?>
                                <button type="button" class="darkBG emphasizedText noBorder shadowed whiteText centerColumnLayout fullHeight hidden"
                                    id="createServiceButton" style="display:none;">
                                    <b>Create</b>
                                </button>
                            <?php endif; ?>
                        </div>
                        <section class="minGap columnLayout scrollable flexMax noFlexBasis noMinHeight contentFlexStart regMinPadding" id="servicesList">
                            <?php foreach ($servicesList as $service): ?>
                                <?php
                                $serviceName = htmlspecialchars($service['name'], ENT_QUOTES, 'UTF-8');
                                $borderClass = $service['isActive'] ? 'yellowBorder' : 'redBorder';
                                $bgClass = $service['isActive'] ? 'yellowTransBG' : 'redTransBG';
                                $orderCount = $serviceOrderCountMap[$service['id']] ?? 0;
                                ?>
                                <div class="roundedMin centerHoriRowLayout flexStatic serviceElement <?= $borderClass ?> shadowed clickable fixedScreen noShrink"
                                    data-id="<?= htmlspecialchars($service['id'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-name="<?= $serviceName ?>"
                                    data-is-active="<?= htmlspecialchars($service['isActive'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-has-design="<?= htmlspecialchars($service['hasDesign'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-has-variable-list="<?= htmlspecialchars($service['hasVariableList'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-order-count="<?= $orderCount ?>">
                                    <div class="capitalFirst centerText regMinPadding flexMax skewedXNegBG shadowed <?= $bgClass ?>">
                                        <h3 class="whiteText outlineText"><?= $serviceName ?></h3>
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
                                <div class="centerHoriRowLayout minGap" id="serviceProcessActionButtonsContainer">
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
    // ================================================================
    // Permissions injected from server (source-of-truth strings)
    // ================================================================
    const userPermissions = <?php echo json_encode($_SESSION['permissions'] ?? []); ?>;

    // ================================================================
    // DOM Elements (descriptive names, no abbreviations)
    // ================================================================
    const createServiceButton = document.getElementById('createServiceButton');
    const serviceItemElements = document.querySelectorAll('.serviceElement');
    const serviceStatusButtonsContainer = document.getElementById('serviceStatusButtonsContainer');
    const objectiveButtonsContainer = document.getElementById('objectiveButtonsContainer');
    const updateServiceProcessButton = document.getElementById('updateServiceProcessButton');
    const serviceProcessContainer = document.getElementById('serviceProcess');
    const subservicesContainer = document.getElementById('subservicesContainer');
    const createSubserviceButton = document.getElementById('createSubserviceButton');
    const subserviceStatusButtonsContainer = document.getElementById('subserviceStatusButtonsContainer');
    const subserviceDataContainer = document.getElementById('subserviceDataContainer');
    const addSubserviceImageButton = document.getElementById('addSubserviceImageButton');
    const subserviceImagesContainer = document.getElementById('subserviceImagesContainer');

    // ================================================================
    // Server Data (injected via PHP – used to build lookup maps)
    // ================================================================
    const serviceProcessList = <?php echo json_encode($serviceProcessList); ?>; // [{serviceID, id, name}, ...]
    const subserviceList = <?php echo json_encode($subserviceList); ?>; // [{serviceID, id, name, isActive, description, pricePerUnit}, ...]
    const subserviceOrderCountTally = <?php echo json_encode($subserviceOrderCountTally); ?>; // [{subserviceID, orderCount}]
    const processesList = <?php echo json_encode($processesList); ?>; // [{id, name}, ...]
    const subserviceImageList = <?php echo json_encode($subserviceImageList); ?>; // [{subserviceID, id, imageName}, ...]
    const lastServiceIdentifier = <?php echo $serviceID; ?>; // Previously selected service ID (for persistence)
    const lastSubserviceIdentifier = <?php echo $subserviceID; ?>; // Previously selected subservice ID

    // ================================================================
    // Build Lookup Maps (keys use full word 'identifier')
    // ================================================================
    const serviceProcessMap = {}; // serviceIdentifier -> array of {identifier, name}
    serviceProcessList.forEach(item => {
        const key = item.serviceID;
        if (!serviceProcessMap[key]) serviceProcessMap[key] = [];
        serviceProcessMap[key].push({
            identifier: item.id,
            name: item.name
        });
    });

    const subserviceMap = {}; // serviceIdentifier -> array of full subservice objects
    subserviceList.forEach(item => {
        const key = item.serviceID;
        if (!subserviceMap[key]) subserviceMap[key] = [];
        subserviceMap[key].push({
            identifier: item.id,
            name: item.name,
            isActive: item.isActive,
            description: item.description,
            pricePerUnit: item.pricePerUnit
        });
    });

    const subserviceOrderCountMap = {}; // subserviceIdentifier -> orderCount
    subserviceOrderCountTally.forEach(item => {
        subserviceOrderCountMap[item.subserviceID] = item.orderCount;
    });

    const subserviceImageMap = {}; // subserviceIdentifier -> array of {identifier, name}
    subserviceImageList.forEach(item => {
        const key = item.subserviceID;
        if (!subserviceImageMap[key]) subserviceImageMap[key] = [];
        subserviceImageMap[key].push({
            identifier: item.id,
            name: item.imageName
        });
    });

    // ================================================================
    // Central State Objects (properties use full names)
    // ================================================================
    let currentService = {
        identifier: null,
        name: '',
        status: 0, // 1 = active, 0 = inactive
        orderCount: 0,
        hasDesign: false,
        hasVariableList: false,
        processes: [], // array of {identifier, name}
        subservices: [], // array of full subservice objects
        subservicesMap: {} // subserviceIdentifier -> {description, pricePerUnit}
    };

    let currentSubservice = {
        identifier: null,
        name: '',
        isActive: 0,
        orderCount: 0,
        description: '',
        pricePerUnit: 0,
        images: [] // array of {identifier, name}
    };

    // ================================================================
    // Reusable temporary DOM variables (used throughout)
    // ================================================================
    let temporaryElement;
    let temporaryDiv;

    // ================================================================
    // Helper: rebuild subservices map from currentService.subservices
    // ================================================================
    function RebuildSubservicesMap() {
        currentService.subservicesMap = {};
        currentService.subservices.forEach(sub => {
            currentService.subservicesMap[sub.identifier] = {
                description: sub.description,
                pricePerUnit: sub.pricePerUnit
            };
        });
    }

    // ================================================================
    // Helper: transfer a subservice object into currentSubservice
    // ================================================================
    function SetCurrentSubservice(sub) {
        currentSubservice.identifier = sub.identifier;
        currentSubservice.name = sub.name;
        currentSubservice.isActive = sub.isActive;
        currentSubservice.orderCount = subserviceOrderCountMap[sub.identifier] || 0;
        currentSubservice.description = sub.description;
        currentSubservice.pricePerUnit = sub.pricePerUnit;
        currentSubservice.images = [...(subserviceImageMap[sub.identifier] || [])];
    }

    // ================================================================
    // Hidden inputs for confirmation form (service/subservice persistence)
    // ================================================================
    const selectedServiceIdentifierInput = document.createElement("input");
    selectedServiceIdentifierInput.type = "hidden";
    selectedServiceIdentifierInput.name = "selectedServiceID";
    selectedServiceIdentifierInput.value = lastServiceIdentifier || -1;
    confirmationForm.appendChild(selectedServiceIdentifierInput);

    const selectedSubserviceIdentifierInput = document.createElement("input");
    selectedSubserviceIdentifierInput.type = "hidden";
    selectedSubserviceIdentifierInput.name = "selectedSubserviceID";
    selectedSubserviceIdentifierInput.value = lastSubserviceIdentifier || -1;
    confirmationForm.appendChild(selectedSubserviceIdentifierInput);

    // ================================================================
    // SERVICE SELECTION & INITIALIZATION
    // ================================================================
    function OnServiceClick(serviceElement) {
        // Update service state from the clicked element's data attributes
        currentService.identifier = serviceElement.dataset.id;
        currentService.name = serviceElement.dataset.name;
        currentService.status = parseInt(serviceElement.dataset.isActive);
        currentService.orderCount = parseInt(serviceElement.dataset.orderCount);
        currentService.hasDesign = serviceElement.dataset.hasDesign === '1';
        currentService.hasVariableList = serviceElement.dataset.hasVariableList === '1';

        selectedServiceIdentifierInput.value = currentService.identifier;

        document.getElementById('selectedServiceTitle').textContent = currentService.name + " Service";

        // Load processes and subservices from the pre-built maps
        currentService.processes = [...(serviceProcessMap[currentService.identifier] || [])];
        currentService.subservices = [...(subserviceMap[currentService.identifier] || [])];
        RebuildSubservicesMap();

        // Reset subservice state
        currentSubservice.identifier = null;
        currentSubservice.name = '';
        selectedSubserviceIdentifierInput.value = -1;
        document.getElementById('selectedSubserviceTitle').textContent = "No Subservice Selected";

        // Render UI sections
        ShowServiceStatusButtonsContainer();
        ShowObjectiveButtonsContainer();
        ShowServiceProcess();
        ResetSubserviceHeader();
        ShowSubservices();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Attach click handlers to all service elements
        serviceItemElements.forEach(element => {
            element.addEventListener('click', () => OnServiceClick(element));
        });

        // Persistence: if a service identifier was provided in the URL, select it
        if (lastServiceIdentifier != -1) {
            for (const element of serviceItemElements) {
                if (element.dataset.id == lastServiceIdentifier) {
                    OnServiceClick(element);
                    break;
                }
            }
        }

        // Set default text for the cancel button in the confirmation dialog
        confirmationCancel.value = "No Cancel";
    });

    // ================================================================
    // UTILITIES: Reset subservice panel when service changes
    // ================================================================
    function ResetSubserviceHeader() {
        document.getElementById('selectedSubserviceTitle').textContent = "No Subservice Selected";
        subserviceStatusButtonsContainer.innerHTML = '';
        // Hide the subservice data form and show the placeholder heading
        subserviceDataContainer.getElementsByTagName('h2')[0].classList.remove("hidden");
        subserviceDataContainer.getElementsByTagName('div')[0].classList.add("hidden");
    }

    // ================================================================
    // SERVICE STATUS BUTTONS (Activate / Disable + Delete)
    // ================================================================
    function ShowServiceStatusButtonsContainer() {
        serviceStatusButtonsContainer.innerHTML = '';

        if (currentService.status == 1) {
            // Active -> Disable button
            temporaryElement = document.createElement("button");
            temporaryElement.type = "button";
            temporaryElement.className = "redBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
            temporaryElement.textContent = "Disable";
            temporaryElement.id = "serviceStatusButton";
            serviceStatusButtonsContainer.appendChild(temporaryElement);
        } else {
            // Inactive -> Activate button
            temporaryElement = document.createElement("button");
            temporaryElement.type = "button";
            temporaryElement.className = "yellowBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
            temporaryElement.textContent = "Activate";
            temporaryElement.id = "serviceStatusButton";
            serviceStatusButtonsContainer.appendChild(temporaryElement);

            const canActivate = currentService.processes.length > 0 &&
                currentService.subservices.length > 0 &&
                currentService.subservices[0].isActive == 1;
            if (!canActivate) temporaryElement.classList.add("faded", "unclickable");

            // Delete button (only for inactive services)
            temporaryElement = document.createElement("button");
            temporaryElement.type = "button";
            temporaryElement.className = "redBG noBorder shadowed centerColumnLayout fullHeight";
            temporaryElement.innerHTML = '<img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors unitHeight">';
            temporaryElement.id = "deleteServiceButton";
            serviceStatusButtonsContainer.appendChild(temporaryElement);

            if (currentService.orderCount > 0 || !userPermissions.includes('canDeleteServices')) {
                temporaryElement.classList.add("faded", "unclickable");
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

        // Toggle status (if allowed)
        const hasStatusPermission = userPermissions.includes('canAlterServiceStatus');
        const canToggle = currentService.status == 0 ?
            (currentService.processes.length > 0 && currentService.subservices.length > 0 && currentService.subservices[0].isActive == 1) :
            true; // deactivation always allowed if active

        const statusButton = document.getElementById('serviceStatusButton');
        if (!hasStatusPermission || !canToggle) {
            statusButton.classList.add("faded", "unclickable");
        } else {
            statusButton.addEventListener('click', function() {
                confirmationTitle.innerHTML = "Toggle Service Status?";
                confirmationForm.action = "index.php?page=services&action=toggleServiceStatus";
                confirmationText.innerHTML = "Are you sure to " + this.textContent + " the " + currentService.name + " service?";
                confirmationSubmit.value = "Yes " + this.textContent;
                if (this.textContent == "Activate") confirmationSubmit.classList.add("yellowBG");
                confirmation.style.display = 'flex';
            });
        }
    }

    // ================================================================
    // OBJECTIVE BUTTONS (Has Design / Has Variable List)
    // ================================================================
    function ShowObjectiveButtonsContainer() {
        objectiveButtonsContainer.innerHTML = '';

        const hasPermission = userPermissions.includes('canAlterServices');
        const isServiceEditable = (currentService.orderCount == 0 && currentService.status == 0);

        // Has Design button
        temporaryElement = document.createElement("button");
        temporaryElement.type = "button";
        temporaryElement.className = currentService.hasDesign ?
            "yellowBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight" :
            "redBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
        temporaryElement.textContent = currentService.hasDesign ? "Has Design" : "No Design";
        temporaryElement.id = "hasDesignButton";
        objectiveButtonsContainer.appendChild(temporaryElement);

        // Has Variable List button
        temporaryElement = document.createElement("button");
        temporaryElement.type = "button";
        temporaryElement.className = currentService.hasVariableList ?
            "yellowBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight" :
            "redBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
        temporaryElement.textContent = currentService.hasVariableList ? "Has Variable List" : "No Variable List";
        temporaryElement.id = "hasVariableListButton";
        objectiveButtonsContainer.appendChild(temporaryElement);

        // Enable interaction only if user has permission and service is editable
        if (isServiceEditable && hasPermission) {
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
            // Fade buttons and prevent clicks if not editable or no permission
            document.getElementById('hasDesignButton').classList.add("faded", "unclickable");
            document.getElementById('hasVariableListButton').classList.add("faded", "unclickable");
        }
    }

    // ================================================================
    // SERVICE PROCESS MANAGEMENT
    // ================================================================
    function ShowServiceProcess() {
        const hasProcessPermission = userPermissions.includes('canManageServiceProcesses');
        const isServiceEditable = (currentService.orderCount == 0 && currentService.status == 0);
        const isEditable = isServiceEditable && hasProcessPermission;

        // Update the "Save" button state
        if (isEditable) {
            updateServiceProcessButton.classList.remove("faded", "unclickable");
            updateServiceProcessButton.dataset.interactable = "1";
        } else {
            updateServiceProcessButton.classList.add("faded", "unclickable");
            updateServiceProcessButton.dataset.interactable = "0";
        }

        serviceProcessContainer.innerHTML = '';
        updateServiceProcessButton.classList.remove("hidden");

        if (currentService.processes.length === 0) {
            temporaryElement = document.createElement("div");
            temporaryElement.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";
            temporaryElement.innerHTML = "<b class='whiteText outlineText'>No Service Process</b>";
            serviceProcessContainer.appendChild(temporaryElement);

            if (isEditable) {
                temporaryElement = document.createElement("div");
                temporaryElement.className = "circle squareSize duoHeight darkBG roundedMin shadowed centerRowLayout regMinPadding";
                temporaryElement.innerHTML = '<img src="../../Shared/Img/CrossIcon.png" alt="Cross" class="invertColors">';
                temporaryElement.id = "addProcessButton";
                serviceProcessContainer.appendChild(temporaryElement);
                document.getElementById('addProcessButton').addEventListener('click', ShowAddProcessesBox);
            }
            return;
        }

        // Render the process chain
        // First process
        temporaryElement = document.createElement("div");
        temporaryElement.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";
        if (currentService.processes.length === 1) {
            temporaryElement.innerHTML = `
            <b class='whiteText outlineText'>${currentService.processes[0].name}</b>
            <a class="squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove" data-index="0">
                <img src="../../Shared/Img/XIcon.png" alt="X">
            </a>
        `;
        } else {
            temporaryElement.innerHTML = `
            <b class='whiteText outlineText'>${currentService.processes[0].name}</b>
            <a class="squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove" data-index="0">
                <img src="../../Shared/Img/XIcon.png" alt="X">
            </a>
            <a class="circle squareSize unitHeight souEastAbsolute centerColumnLayout importantInput closeCorner swapRight shadowed" data-index="0">
                <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors">
            </a>
        `;
        }
        serviceProcessContainer.appendChild(temporaryElement);

        // Middle processes
        for (let index = 1; index < currentService.processes.length - 1; index++) {
            temporaryElement = document.createElement("h2");
            temporaryElement.textContent = ">";
            serviceProcessContainer.appendChild(temporaryElement);

            temporaryElement = document.createElement("div");
            temporaryElement.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";
            temporaryElement.innerHTML = `
            <b class='whiteText outlineText'>${currentService.processes[index].name}</b>
            <a class="squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove" data-index="${index}">
                <img src="../../Shared/Img/XIcon.png" alt="X">
            </a>
            <a class="circle squareSize unitHeight souWestAbsolute centerColumnLayout importantInput closeCorner swapLeft shadowed" data-index="${index}">
                <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors mirrorX">
            </a>
            <a class="circle squareSize unitHeight souEastAbsolute centerColumnLayout importantInput closeCorner swapRight shadowed" data-index="${index}">
                <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors">
            </a>
        `;
            serviceProcessContainer.appendChild(temporaryElement);
        }

        // Last process (if more than one)
        if (currentService.processes.length > 1) {
            temporaryElement = document.createElement("h2");
            temporaryElement.textContent = ">";
            serviceProcessContainer.appendChild(temporaryElement);

            const lastIndex = currentService.processes.length - 1;
            temporaryElement = document.createElement("div");
            temporaryElement.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";
            temporaryElement.innerHTML = `
            <b class='whiteText outlineText'>${currentService.processes[lastIndex].name}</b>
            <a class="squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove" data-index="${lastIndex}">
                <img src="../../Shared/Img/XIcon.png" alt="X">
            </a>
            <a class="circle squareSize unitHeight souWestAbsolute centerColumnLayout importantInput closeCorner swapLeft shadowed"
                data-index="${lastIndex}">
                <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors mirrorX">
            </a>
        `;
            serviceProcessContainer.appendChild(temporaryElement);
        }

        // Add process button (plus icon) when editable
        if (isEditable) {
            temporaryElement = document.createElement("div");
            temporaryElement.className = "circle squareSize duoHeight darkBG roundedMin shadowed centerRowLayout regMinPadding";
            temporaryElement.innerHTML = '<img src="../../Shared/Img/CrossIcon.png" alt="Cross" class="invertColors">';
            temporaryElement.id = "addProcessButton";
            serviceProcessContainer.appendChild(temporaryElement);
            document.getElementById('addProcessButton').addEventListener('click', ShowAddProcessesBox);
        }

        // Attach remove/swap event handlers only if editable
        document.querySelectorAll('.processRemove').forEach(element => {
            if (isEditable) {
                element.addEventListener('click', function() {
                    const index = parseInt(this.dataset.index);
                    currentService.processes.splice(index, 1);
                    ShowServiceProcess();
                });
            } else {
                element.classList.add("hidden");
            }
        });

        document.querySelectorAll('.swapRight').forEach(element => {
            if (isEditable) {
                element.addEventListener('click', function() {
                    const index = parseInt(this.dataset.index);
                    [currentService.processes[index], currentService.processes[index + 1]] = [currentService.processes[index + 1], currentService.processes[index]];
                    ShowServiceProcess();
                });
            } else {
                element.classList.add("hidden");
            }
        });

        document.querySelectorAll('.swapLeft').forEach(element => {
            if (isEditable) {
                element.addEventListener('click', function() {
                    const index = parseInt(this.dataset.index);
                    [currentService.processes[index], currentService.processes[index - 1]] = [currentService.processes[index - 1], currentService.processes[index]];
                    ShowServiceProcess();
                });
            } else {
                element.classList.add("hidden");
            }
        });
    }

    // ================================================================
    // Modal for adding processes to a service
    // ================================================================
    function ShowAddProcessesBox() {
        // Only proceed if the user has process management rights
        if (!userPermissions.includes('canManageServiceProcesses')) {
            return;
        }

        const currentProcessNames = new Set(currentService.processes.map(process => process.name));
        let hasAddableProcess = false;

        confirmationTitle.innerHTML = "Add Processes";
        confirmationText.innerHTML = "Click on processes that you want to add to the " + currentService.name + " service process.";
        confirmationSubmit.classList.add("hidden");
        confirmationCancel.value = "Return";

        // Remove any previously appended temporary elements
        document.querySelectorAll('.tempElement').forEach(el => el.remove());

        temporaryDiv = document.createElement("div");
        temporaryDiv.className = 'midHeight scrollable columnLayout minGap regMinPadding tempElement';

        processesList.forEach(process => {
            if (currentProcessNames.has(process.name)) return;
            hasAddableProcess = true;
            temporaryElement = document.createElement('div');
            temporaryElement.className = 'tinHeight noShrink roundedMin centerColumnLayout bordered darkTransBG emphasizedText capitalFirst shadowed clickable addProcessElement';
            temporaryElement.innerHTML = '<b>' + process.name + '</b>';
            temporaryElement.dataset.id = process.id;
            temporaryElement.dataset.name = process.name;
            temporaryDiv.appendChild(temporaryElement);
        });

        if (!hasAddableProcess) {
            temporaryElement = document.createElement("b");
            temporaryElement.className = "centerMarginsSelf";
            temporaryElement.textContent = "No Processes To Add";
            temporaryDiv.appendChild(temporaryElement);
        }

        confirmationForm.appendChild(temporaryDiv);

        document.querySelectorAll('.addProcessElement').forEach(element => {
            element.addEventListener('click', () => {
                currentService.processes.push({
                    identifier: element.dataset.id,
                    name: element.dataset.name
                });
                ShowServiceProcess();
                ShowAddProcessesBox(); // refresh modal
            });
        });

        confirmation.style.display = 'flex';
    }

    // ================================================================
    // Update Service Process button (save to server)
    // ================================================================
    updateServiceProcessButton.addEventListener('click', function() {
        if (updateServiceProcessButton.dataset.interactable === "0") return;
        if (!userPermissions.includes('canManageServiceProcesses')) {
            alert("You do not have permission to update the service process.");
            return;
        }

        confirmationTitle.innerHTML = "Update Service Process?";
        confirmationForm.action = "index.php?page=services&action=updateServiceProcess";
        confirmationText.innerHTML = "Are you sure to update the process of the " + currentService.name + " service?";
        confirmationSubmit.value = "Yes Update";
        confirmationSubmit.classList.add("yellowBG");

        document.querySelectorAll('.processListElement').forEach(el => el.remove());
        currentService.processes.forEach(process => {
            temporaryElement = document.createElement('input');
            temporaryElement.type = 'hidden';
            temporaryElement.name = 'processList[]';
            temporaryElement.value = process.identifier;
            temporaryElement.className = "processListElement tempElement";
            confirmationForm.appendChild(temporaryElement);
        });
        confirmation.style.display = 'flex';
    });

    // ================================================================
    // SUB SERVICE LIST AND INTERACTION
    // ================================================================
    function ShowSubservices() {
        subservicesContainer.innerHTML = '';
        createSubserviceButton.classList.remove("hidden");

        // Show create button only if user has permission
        if (!userPermissions.includes('canCreateSubservices')) {
            createSubserviceButton.classList.add("faded", "unclickable");
        } else {
            createSubserviceButton.classList.remove("faded", "unclickable");
        }

        if (currentService.subservices.length === 0) {
            temporaryElement = document.createElement("h2");
            temporaryElement.className = "centerMarginsSelf";
            temporaryElement.innerHTML = "No Subservices";
            subservicesContainer.appendChild(temporaryElement);
            return;
        }

        currentService.subservices.forEach(sub => {
            temporaryDiv = document.createElement("div");
            temporaryDiv.className = 'roundedMin centerHoriRowLayout flexStatic shadowed clickable fixedScreen noShrink subserviceElement';
            temporaryDiv.dataset.id = sub.identifier;
            subservicesContainer.appendChild(temporaryDiv);

            temporaryElement = document.createElement("div");
            temporaryElement.className = 'capitalFirst centerText regMinPadding flexMax skewedXNegBG shadowed';
            temporaryElement.innerHTML = `<h4 class="whiteText outlineText">${sub.name}</h4>`;
            temporaryDiv.appendChild(temporaryElement);

            if (sub.isActive == 1) {
                temporaryDiv.classList.add("yellowBorder");
                temporaryElement.classList.add("yellowTransBG");
            } else {
                temporaryDiv.classList.add("redBorder");
                temporaryElement.classList.add("redTransBG");
            }

            temporaryElement = document.createElement("h5");
            temporaryElement.className = 'capitalFirst centerText regMinPadding minWidth';
            temporaryElement.textContent = "Orders: " + (subserviceOrderCountMap[sub.identifier] || 0);
            temporaryDiv.appendChild(temporaryElement);

            temporaryDiv.addEventListener('click', () => {
                SetCurrentSubservice(sub);
                selectedSubserviceIdentifierInput.value = currentSubservice.identifier;
                document.getElementById('selectedSubserviceTitle').textContent = currentSubservice.name;
                ShowSubserviceStatusButtonsContainer();
                ShowSubserviceDataContainer();
                ShowSubserviceImages();
            });

            // Persistence: auto-select if it matches the URL parameter
            if (lastSubserviceIdentifier != -1 && Number(sub.identifier) == Number(lastSubserviceIdentifier)) {
                SetCurrentSubservice(sub);
                selectedSubserviceIdentifierInput.value = currentSubservice.identifier;
                document.getElementById('selectedSubserviceTitle').textContent = currentSubservice.name;
                ShowSubserviceStatusButtonsContainer();
                ShowSubserviceDataContainer();
                ShowSubserviceImages();
            }
        });
    }

    // ================================================================
    // SUB SERVICE STATUS BUTTONS
    // ================================================================
    function ShowSubserviceStatusButtonsContainer() {
        subserviceStatusButtonsContainer.innerHTML = '';

        if (currentSubservice.isActive == 1) {
            // Disable button
            temporaryElement = document.createElement("button");
            temporaryElement.type = "button";
            temporaryElement.className = "redBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
            temporaryElement.textContent = "Disable";
            temporaryElement.id = "subserviceStatusButton";
            subserviceStatusButtonsContainer.appendChild(temporaryElement);

            // Prevent disabling if this is the only active subservice
            const activeCount = currentService.subservices.filter(s => s.isActive == 1).length;
            if (activeCount === 1 || !userPermissions.includes('canAlterSubserviceStatus')) {
                temporaryElement.classList.add("faded", "unclickable");
            }
        } else {
            // Activate button
            temporaryElement = document.createElement("button");
            temporaryElement.type = "button";
            temporaryElement.className = "yellowBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
            temporaryElement.textContent = "Activate";
            temporaryElement.id = "subserviceStatusButton";
            subserviceStatusButtonsContainer.appendChild(temporaryElement);

            if (!userPermissions.includes('canAlterSubserviceStatus')) {
                temporaryElement.classList.add("faded", "unclickable");
            }

            // Delete button
            temporaryElement = document.createElement("button");
            temporaryElement.type = "button";
            temporaryElement.className = "redBG noBorder shadowed centerColumnLayout fullHeight";
            temporaryElement.innerHTML = '<img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors unitHeight">';
            temporaryElement.id = "deleteSubserviceButton";
            subserviceStatusButtonsContainer.appendChild(temporaryElement);

            if (currentSubservice.orderCount > 0 || !userPermissions.includes('canDeleteSubservices')) {
                temporaryElement.classList.add("faded", "unclickable");
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

        // Toggle status click handler (only if not blocked)
        const isBlocked = (currentSubservice.isActive == 1 && currentService.subservices.filter(s => s.isActive == 1).length === 1) ||
            !userPermissions.includes('canAlterSubserviceStatus');
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

    // ================================================================
    // SUB SERVICE DATA CONTAINER (description & price)
    // ================================================================
    function ShowSubserviceDataContainer() {
        const isEditable = userPermissions.includes('canAlterSubservices');
        subserviceDataContainer.getElementsByTagName('h2')[0].classList.add("hidden");

        const container = subserviceDataContainer.getElementsByTagName('div')[0];
        const formElement = container.getElementsByTagName('form')[0];
        const descriptionInput = formElement.querySelector('textarea');
        const hiddenInputs = formElement.querySelectorAll('input[type="hidden"]');
        const serviceIdInput = hiddenInputs[0];
        const subserviceIdInput = hiddenInputs[1];
        const priceInput = formElement.querySelector('input[type="number"]');
        const submitButton = formElement.querySelector('input[type="submit"]');

        container.classList.remove("hidden");

        serviceIdInput.value = currentService.identifier;
        subserviceIdInput.value = currentSubservice.identifier;
        descriptionInput.value = currentSubservice.description;
        descriptionInput.placeholder = currentSubservice.description;
        priceInput.value = currentSubservice.pricePerUnit;
        priceInput.placeholder = currentSubservice.pricePerUnit;

        // Disable form fields if user lacks permission
        descriptionInput.readOnly = !isEditable;
        priceInput.readOnly = !isEditable;
        if (isEditable) {
            submitButton.classList.remove("faded", "unclickable");
            submitButton.disabled = false;
        } else {
            submitButton.classList.add("faded", "unclickable");
            submitButton.disabled = true;
        }
    }

    // ================================================================
    // SUB SERVICE IMAGES GALLERY
    // ================================================================
    function ShowSubserviceImages() {
        subserviceImagesContainer.innerHTML = '';

        if (currentSubservice.images.length === 0) {
            subserviceImagesContainer.innerHTML = `<div class="centerMarginsSelf fullHeight centerColumnLayout fitWidth"><b>No Images</b></div>`;
            return;
        }

        currentSubservice.images.forEach(img => {
            temporaryDiv = document.createElement("div");
            temporaryDiv.className = "squareSize fixedScreen centerColumnLayout relatived shadowed roundedTin";

            // Remove button (only if user can alter subservices)
            if (userPermissions.includes('canAlterSubservices')) {
                temporaryElement = document.createElement("a");
                temporaryElement.className = "circle squareSize unitHeight norEastAbsolute centerColumnLayout importantInput closeCorner swapRight shadowed minZ removeImageButton";
                temporaryElement.innerHTML = '<img src="../../Shared/Img/XIcon.png" alt="X" class="invertColors">';
                temporaryElement.dataset.id = img.identifier;
                temporaryElement.dataset.imageName = img.name;
                temporaryDiv.appendChild(temporaryElement);
            }

            // Image thumbnail
            temporaryElement = document.createElement("img");
            temporaryElement.className = "fullHeight absoluted clickable subserviceImageElement";
            temporaryElement.src = "../../Storage/SubserviceImages/" + img.name;
            temporaryElement.alt = "Image";
            temporaryDiv.appendChild(temporaryElement);

            subserviceImagesContainer.appendChild(temporaryDiv);
        });

        // Click to enlarge
        document.querySelectorAll('.subserviceImageElement').forEach(element => {
            element.addEventListener('click', () => {
                imageBoxImage.src = element.src;
                imageBox.style.display = 'flex';
            });
        });

        // Remove image logic
        document.querySelectorAll('.removeImageButton').forEach(element => {
            element.addEventListener('click', () => {
                confirmationTitle.innerHTML = "Remove Subservice Image?";
                confirmationForm.action = "index.php?page=services&action=removeSubserviceImage";
                confirmationText.innerHTML = "Are you sure to remove this image from the " + currentSubservice.name + " subservice?";
                confirmationSubmit.value = "Yes Remove";

                const hiddenIdentifier = document.createElement("input");
                hiddenIdentifier.type = "hidden";
                hiddenIdentifier.name = "selectedID";
                hiddenIdentifier.value = element.dataset.id;
                hiddenIdentifier.className = "tempElement";
                confirmationForm.appendChild(hiddenIdentifier);

                const previewDiv = document.createElement("div");
                previewDiv.className = "fullWidth tempElement centerHoriRowLayout regMinPadding";
                confirmationForm.appendChild(previewDiv);

                const previewImg = document.createElement("img");
                previewImg.className = "fullWidth roundedMin shadowed";
                previewImg.src = "../../Storage/SubserviceImages/" + element.dataset.imageName;
                previewDiv.appendChild(previewImg);

                confirmation.style.display = 'flex';
            });
        });

        // Show/hide Add Image button based on permission
        if (userPermissions.includes('canAlterSubservices')) {
            addSubserviceImageButton.classList.remove("faded", "unclickable");
            addSubserviceImageButton.disabled = false;
        } else {
            addSubserviceImageButton.classList.add("faded", "unclickable");
            addSubserviceImageButton.disabled = true;
        }
    }

    // ================================================================
    // SUB SERVICE IMAGE UPLOAD (Multiple)
    // ================================================================
    addSubserviceImageButton.addEventListener('click', function() {
        if (!userPermissions.includes('canAlterSubservices')) {
            alert("You do not have permission to upload images.");
            return;
        }

        confirmationContent.classList.add("fitWidth");
        confirmationForm.action = "index.php?page=services&action=uploadSubserviceImages";

        // File input row
        temporaryDiv = document.createElement("div");
        temporaryDiv.className = "tempElement centerHoriRowLayout minGap";
        confirmationForm.appendChild(temporaryDiv);

        temporaryElement = document.createElement("b");
        temporaryElement.textContent = "Upload File:";
        temporaryDiv.appendChild(temporaryElement);

        const fileInput = document.createElement("input");
        fileInput.type = "file";
        fileInput.name = "images[]";
        fileInput.accept = "image/*";
        fileInput.multiple = true;
        fileInput.required = true;
        fileInput.className = "flexMax";
        temporaryDiv.appendChild(fileInput);

        // Preview container (no forced dimensions, just scrollable and full-width)
        const previewDiv = document.createElement("div");
        previewDiv.className = "tempElement hidden columnLayout minGap regPadding fullWidth scrollableY midHeight";
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

            for (let index = 0; index < files.length; index++) {
                if (!files[index].type.startsWith("image/")) {
                    alert("Only images are allowed. File: " + files[index].name);
                    fileInput.value = "";
                    return;
                }
            }

            // Display previews
            Array.from(files).forEach(file => {
                const imageElement = document.createElement("img");
                imageElement.className = "fullWidth roundedMin shadowed";
                imageElement.src = URL.createObjectURL(file);
                previewDiv.appendChild(imageElement);
            });
            previewDiv.classList.remove("hidden");
        });
    });

    // ================================================================
    // CREATE SERVICE MODAL
    // ================================================================
    createServiceButton.addEventListener('click', () => {
        if (!userPermissions.includes('canCreateServices')) {
            alert("You do not have permission to create services.");
            return;
        }

        confirmationTitle.innerHTML = "Create Service";
        confirmationForm.action = "index.php?page=services&action=createService";
        confirmationText.innerHTML = "Please enter a unique service name.";
        confirmationSubmit.value = "Create";
        confirmationSubmit.classList.add("yellowBG", "noBorder");

        temporaryElement = document.createElement("input");
        temporaryElement.type = "text";
        temporaryElement.name = "name";
        temporaryElement.placeholder = "Service Name";
        temporaryElement.id = "nameInput";
        temporaryElement.className = "tempElement";
        temporaryElement.required = true;
        confirmationForm.appendChild(temporaryElement);

        confirmation.style.display = 'flex';
    });

    // ================================================================
    // CREATE SUBSERVICE MODAL
    // ================================================================
    createSubserviceButton.addEventListener('click', () => {
        if (!currentService.identifier) {
            alert("Please select a service first.");
            return;
        }
        if (!userPermissions.includes('canCreateSubservices')) {
            alert("You do not have permission to create subservices.");
            return;
        }

        confirmationTitle.innerHTML = "Create Subservice";
        confirmationForm.action = "index.php?page=services&action=createSubservice";
        confirmationText.innerHTML = "Please enter a unique subservice name for the " + currentService.name + " service.";
        confirmationSubmit.value = "Create";
        confirmationSubmit.classList.add("yellowBG", "noBorder");

        temporaryElement = document.createElement("input");
        temporaryElement.type = "text";
        temporaryElement.name = "name";
        temporaryElement.placeholder = "Subservice Name";
        temporaryElement.id = "nameInput";
        temporaryElement.className = "tempElement";
        temporaryElement.required = true;
        confirmationForm.appendChild(temporaryElement);

        confirmation.style.display = 'flex';
    });

    // ================================================================
    // CONFIRMATION DIALOG CLEANUP
    // ================================================================
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