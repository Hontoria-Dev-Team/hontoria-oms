<?php
// XSS escape helper – define once across the application
if (!function_exists('e')) {
    function e($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}
?>
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
                            <button type="button" class="darkBG emphasizedText noBorder shadowed whiteText centerColumnLayout fullHeight" id="createServiceButton">
                                <b>Create</b>
                            </button>
                        </div>
                        <section class="minGap columnLayout scrollable flexMax noFlexBasis noMinHeight contentFlexStart regMinPadding" id="servicesList">
                            <?php foreach ($servicesList as $service): ?>
                                <?php
                                $name = e($service['name']);
                                $borderClass = $service['isActive'] ? 'yellowBorder' : 'redBorder';
                                $bgClass = $service['isActive'] ? 'yellowTransBG' : 'redTransBG';
                                $orderCount = (int)($serviceOrderCountMap[$service['id']] ?? 0);
                                ?>
                                <div class="roundedMin centerHoriRowLayout flexStatic serviceElement <?= e($borderClass) ?> shadowed clickable fixedScreen noShrink"
                                    data-id="<?= e($service['id']) ?>" data-name="<?= $name ?>" data-is-active="<?= e($service['isActive']) ?>"
                                    data-has-design="<?= e($service['hasDesign']) ?>" data-has-variable-list="<?= e($service['hasVariableList']) ?>"
                                    data-order-count="<?= $orderCount ?>">
                                    <div class="capitalFirst centerText regMinPadding flexMax skewedXNegBG shadowed <?= e($bgClass) ?>">
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
                                    <button type="button" class="darkBG emphasizedText noBorder shadowed whiteText centerColumnLayout fullHeight hidden" id="createSubserviceButton">
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
                                            <?php echo CsrfM::getTokenField(); ?>
                                            <input type="hidden" name="selectedServiceID">
                                            <input type="hidden" name="selectedSubserviceID">
                                            <div class="flexMax columnLayout tinGap">
                                                <b>Description</b>
                                                <textarea name="description" class="scrollableTextarea minHeight fullWidth flexMax minPadding justifiedText unresizeable" id="descriptionText"></textarea>
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
                                                <button type="button" class="darkBG noBorder shadowed whiteText centerColumnLayout fullHeight roundedTin" id="addSubserviceImageButton">
                                                    <h5>Add Image</h5>
                                                </button>
                                            </div>
                                            <div class="gridFlex minGap midGrids flexMax contentFlexStart noFlexBasis noMinHeight scrollable regMinPadding" id="subserviceImagesContainer"></div>
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
<script src="../.JS/CsrfHandler.js"></script>
<script src="../.JS/ImageBox.js"></script>
<script>
    // DOM elements
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

    // Server data
    const serviceProcessList = <?php echo json_encode($serviceProcessList); ?>;
    const subserviceList = <?php echo json_encode($subserviceList); ?>;
    const subserviceOrderCountTally = <?php echo json_encode($subserviceOrderCountTally); ?>;
    const processesList = <?php echo json_encode($processesList); ?>;
    const subserviceImageList = <?php echo json_encode($subserviceImageList); ?>;
    const lastServiceID = <?php echo (int)$serviceID; ?>;
    const lastSubserviceID = <?php echo (int)$subserviceID; ?>;

    // Build lookup maps
    const serviceProcessMap = {};
    serviceProcessList.forEach(item => {
        if (!serviceProcessMap[item.serviceID]) serviceProcessMap[item.serviceID] = [];
        serviceProcessMap[item.serviceID].push({
            id: item.id,
            name: item.name
        });
    });

    const subserviceMap = {};
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

    const subserviceOrderCountMap = {};
    subserviceOrderCountTally.forEach(item => {
        subserviceOrderCountMap[item.subserviceID] = item.orderCount;
    });

    const subserviceImageMap = {};
    subserviceImageList.forEach(item => {
        if (!subserviceImageMap[item.subserviceID]) subserviceImageMap[item.subserviceID] = [];
        subserviceImageMap[item.subserviceID].push({
            id: item.id,
            name: item.imageName
        });
    });

    document.addEventListener("DOMContentLoaded", () => {
        confirmationCancel.value = "No Cancel";
    });

    // CSRF helper – refresh token from the page's hidden token field (if present)
    function refreshCsrfToken() {
        const pageToken = document.querySelector('input[name="_csrf_token"]');
        if (pageToken) {
            const old = confirmationForm.querySelector('input[name="_csrf_token"]');
            if (old) old.remove();
            confirmationForm.insertBefore(pageToken.cloneNode(true), confirmationForm.firstChild);
        }
    }

    // Hidden inputs for confirmation form
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

    let tempElement, tempDiv;

    let currentService = {
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
    let currentSubservice = {
        id: null,
        name: '',
        isActive: 0,
        orderCount: 0,
        description: '',
        pricePerUnit: 0,
        images: []
    };

    function RebuildSubservicesMap() {
        currentService.subservicesMap = {};
        currentService.subservices.forEach(sub => {
            currentService.subservicesMap[sub.id] = {
                description: sub.description,
                pricePerUnit: sub.pricePerUnit
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

    function OnServiceClick(elem) {
        currentService.id = elem.dataset.id;
        currentService.name = elem.dataset.name;
        currentService.status = parseInt(elem.dataset.isActive);
        currentService.orderCount = parseInt(elem.dataset.orderCount);
        currentService.hasDesign = elem.dataset.hasDesign === '1';
        currentService.hasVariableList = elem.dataset.hasVariableList === '1';
        selectedServiceIdInput.value = currentService.id;
        document.getElementById('selectedServiceTitle').textContent = currentService.name + " Service";
        currentService.processes = [...(serviceProcessMap[currentService.id] || [])];
        currentService.subservices = [...(subserviceMap[currentService.id] || [])];
        RebuildSubservicesMap();
        currentSubservice.id = null;
        currentSubservice.name = '';
        selectedSubserviceIdInput.value = -1;
        document.getElementById('selectedSubserviceTitle').textContent = "No Subservice Selected";
        ShowServiceStatusButtonsContainer();
        ShowObjectiveButtonsContainer();
        ShowServiceProcess();
        ResetSubserviceHeader();
        ShowSubservices();
    }

    document.addEventListener('DOMContentLoaded', function() {
        serviceElements.forEach(elem => elem.addEventListener('click', () => OnServiceClick(elem)));
        if (lastServiceID != -1) {
            for (const elem of serviceElements) {
                if (elem.dataset.id == lastServiceID) {
                    OnServiceClick(elem);
                    break;
                }
            }
        }
    });

    function ShowServiceStatusButtonsContainer() {
        serviceStatusButtonsContainer.innerHTML = '';
        if (currentService.status == 1) {
            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "redBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
            tempElement.textContent = "Disable";
            tempElement.id = "serviceStatusButton";
            serviceStatusButtonsContainer.appendChild(tempElement);
        } else {
            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "yellowBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
            tempElement.textContent = "Activate";
            tempElement.id = "serviceStatusButton";
            serviceStatusButtonsContainer.appendChild(tempElement);
            const canActivate = currentService.processes.length > 0 && currentService.subservices.length > 0 && currentService.subservices[0].isActive == 1;
            if (!canActivate) tempElement.classList.add("faded", "unclickable");
            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "redBG noBorder shadowed centerColumnLayout fullHeight";
            // InnerHTML for the icon is hardcoded, safe
            tempElement.innerHTML = '<img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors unitHeight">';
            tempElement.id = "deleteServiceButton";
            serviceStatusButtonsContainer.appendChild(tempElement);
            if (currentService.orderCount > 0) tempElement.classList.add("faded", "unclickable");
            else {
                document.getElementById('deleteServiceButton').addEventListener('click', () => {
                    confirmationTitle.textContent = "Delete Service?";
                    confirmationForm.action = "index.php?page=services&action=deleteService";
                    confirmationText.textContent = "Are you sure to delete the " + currentService.name + " service?";
                    confirmationSubmit.value = "Yes delete";
                    confirmation.style.display = 'flex';
                });
            }
        }
        document.getElementById('serviceStatusButton').addEventListener('click', function() {
            const canActivate = currentService.processes.length > 0 && currentService.subservices.length > 0 && currentService.subservices[0].isActive == 1;
            if (canActivate) {
                confirmationTitle.textContent = "Toggle Service Status?";
                confirmationForm.action = "index.php?page=services&action=toggleServiceStatus";
                confirmationText.textContent = "Are you sure to " + this.textContent + " the " + currentService.name + " service?";
                confirmationSubmit.value = "Yes " + this.textContent;
                if (this.textContent == "Activate") confirmationSubmit.classList.add("yellowBG");
                confirmation.style.display = 'flex';
            }
        });
    }

    function ShowObjectiveButtonsContainer() {
        objectiveButtonsContainer.innerHTML = '';
        tempElement = document.createElement("button");
        tempElement.type = "button";
        tempElement.className = currentService.hasDesign ? "yellowBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight" : "redBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
        tempElement.textContent = currentService.hasDesign ? "Has Design" : "No Design";
        tempElement.id = "hasDesignButton";
        objectiveButtonsContainer.appendChild(tempElement);
        tempElement = document.createElement("button");
        tempElement.type = "button";
        tempElement.className = currentService.hasVariableList ? "yellowBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight" : "redBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
        tempElement.textContent = currentService.hasVariableList ? "Has Variable List" : "No Variable List";
        tempElement.id = "hasVariableListButton";
        objectiveButtonsContainer.appendChild(tempElement);
        const editable = (currentService.orderCount == 0 && currentService.status == 0);
        if (editable) {
            document.getElementById('hasDesignButton').addEventListener('click', function() {
                confirmationTitle.textContent = "Toggle Design Objective?";
                confirmationForm.action = "index.php?page=services&action=toggleHasDesign";
                if (this.textContent == "No Design") {
                    confirmationSubmit.classList.add("yellowBG");
                    confirmationText.textContent = "Are you sure to activate the design objective?";
                    confirmationSubmit.value = "Yes Active";
                } else {
                    confirmationText.textContent = "Are you sure to disable the design objective?";
                    confirmationSubmit.value = "Yes Disable";
                }
                confirmation.style.display = 'flex';
            });
            document.getElementById('hasVariableListButton').addEventListener('click', function() {
                confirmationTitle.textContent = "Toggle Variable List Objective?";
                confirmationForm.action = "index.php?page=services&action=toggleHasVariableList";
                if (this.textContent == "No Variable List") {
                    confirmationSubmit.classList.add("yellowBG");
                    confirmationText.textContent = "Are you sure to activate the variable list objective?";
                    confirmationSubmit.value = "Yes Active";
                } else {
                    confirmationText.textContent = "Are you sure to disable the variable list objective?";
                    confirmationSubmit.value = "Yes Disable";
                }
                confirmation.style.display = 'flex';
            });
        } else {
            document.getElementById('hasDesignButton').classList.add("faded", "unclickable");
            document.getElementById('hasVariableListButton').classList.add("faded", "unclickable");
        }
    }

    // Service process – rebuild with safe DOM, no innerHTML for variable data
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
            const noProcessDiv = document.createElement("div");
            noProcessDiv.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";
            const b = document.createElement("b");
            b.className = "whiteText outlineText";
            b.textContent = "No Service Process";
            noProcessDiv.appendChild(b);
            serviceProcess.appendChild(noProcessDiv);
            if (editable) {
                const addBtn = document.createElement("div");
                addBtn.className = "circle squareSize duoHeight darkBG roundedMin shadowed centerRowLayout regMinPadding";
                addBtn.innerHTML = '<img src="../../Shared/Img/CrossIcon.png" alt="Cross" class="invertColors">'; // safe
                addBtn.id = "addProcessButton";
                serviceProcess.appendChild(addBtn);
                document.getElementById('addProcessButton').addEventListener('click', ShowAddProcessesBox);
            }
            return;
        }

        // First process
        const firstDiv = document.createElement("div");
        firstDiv.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";
        const bFirstName = document.createElement("b");
        bFirstName.className = "whiteText outlineText";
        bFirstName.textContent = currentService.processes[0].name;
        firstDiv.appendChild(bFirstName);
        const removeA = document.createElement("a");
        removeA.className = "squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove";
        removeA.dataset.index = "0";
        removeA.innerHTML = '<img src="../../Shared/Img/XIcon.png" alt="X">'; // safe
        firstDiv.appendChild(removeA);
        if (currentService.processes.length > 1) {
            const swapA = document.createElement("a");
            swapA.className = "circle squareSize unitHeight souEastAbsolute centerColumnLayout importantInput closeCorner swapRight shadowed";
            swapA.dataset.index = "0";
            swapA.innerHTML = '<img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors">'; // safe
            firstDiv.appendChild(swapA);
        }
        serviceProcess.appendChild(firstDiv);

        // Middle processes
        for (let i = 1; i < currentService.processes.length - 1; i++) {
            const arrow = document.createElement("h2");
            arrow.textContent = ">";
            serviceProcess.appendChild(arrow);
            const midDiv = document.createElement("div");
            midDiv.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";
            const bName = document.createElement("b");
            bName.className = "whiteText outlineText";
            bName.textContent = currentService.processes[i].name;
            midDiv.appendChild(bName);
            const remA = document.createElement("a");
            remA.className = "squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove";
            remA.dataset.index = i.toString();
            remA.innerHTML = '<img src="../../Shared/Img/XIcon.png" alt="X">';
            midDiv.appendChild(remA);
            const swapL = document.createElement("a");
            swapL.className = "circle squareSize unitHeight souWestAbsolute centerColumnLayout importantInput closeCorner swapLeft shadowed";
            swapL.dataset.index = i.toString();
            swapL.innerHTML = '<img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors mirrorX">';
            midDiv.appendChild(swapL);
            const swapR = document.createElement("a");
            swapR.className = "circle squareSize unitHeight souEastAbsolute centerColumnLayout importantInput closeCorner swapRight shadowed";
            swapR.dataset.index = i.toString();
            swapR.innerHTML = '<img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors">';
            midDiv.appendChild(swapR);
            serviceProcess.appendChild(midDiv);
        }

        // Last process (if more than one)
        if (currentService.processes.length > 1) {
            const arrow = document.createElement("h2");
            arrow.textContent = ">";
            serviceProcess.appendChild(arrow);
            const lastDiv = document.createElement("div");
            lastDiv.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";
            const bLastName = document.createElement("b");
            bLastName.className = "whiteText outlineText";
            bLastName.textContent = currentService.processes[currentService.processes.length - 1].name;
            lastDiv.appendChild(bLastName);
            const remA = document.createElement("a");
            remA.className = "squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove";
            remA.dataset.index = (currentService.processes.length - 1).toString();
            remA.innerHTML = '<img src="../../Shared/Img/XIcon.png" alt="X">';
            lastDiv.appendChild(remA);
            const swapL = document.createElement("a");
            swapL.className = "circle squareSize unitHeight souWestAbsolute centerColumnLayout importantInput closeCorner swapLeft shadowed";
            swapL.dataset.index = (currentService.processes.length - 1).toString();
            swapL.innerHTML = '<img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors mirrorX">';
            lastDiv.appendChild(swapL);
            serviceProcess.appendChild(lastDiv);
        }

        // Add process button
        if (editable) {
            const addBtn = document.createElement("div");
            addBtn.className = "circle squareSize duoHeight darkBG roundedMin shadowed centerRowLayout regMinPadding";
            addBtn.innerHTML = '<img src="../../Shared/Img/CrossIcon.png" alt="Cross" class="invertColors">'; // safe
            addBtn.id = "addProcessButton";
            serviceProcess.appendChild(addBtn);
            document.getElementById('addProcessButton').addEventListener('click', ShowAddProcessesBox);
        }

        // Event handlers (logic unchanged)
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
        confirmationTitle.textContent = "Add Processes";
        confirmationText.textContent = "Click on processes that you want to add to the " + currentService.name + " service process.";
        confirmationSubmit.classList.add("hidden");
        confirmationCancel.value = "Return";
        document.querySelectorAll('.tempElement').forEach(el => el.remove());
        tempDiv = document.createElement("div");
        tempDiv.className = 'midHeight scrollable columnLayout minGap regMinPadding tempElement';
        processesList.forEach(proc => {
            if (currentNames.has(proc.name)) return;
            tempElement = document.createElement('div');
            tempElement.className = 'tinHeight noShrink roundedMin centerColumnLayout bordered darkTransBG emphasizedText capitalFirst shadowed clickable addProcessElement';
            // Use textContent to safely display process name
            tempElement.textContent = proc.name;
            tempElement.dataset.id = proc.id;
            tempElement.dataset.name = proc.name;
            tempDiv.appendChild(tempElement);
        });
        if (tempDiv.children.length === 0) {
            const noProcess = document.createElement("b");
            noProcess.className = "centerMarginsSelf";
            noProcess.textContent = "No Processes To Add";
            tempDiv.appendChild(noProcess);
        }
        confirmationForm.appendChild(tempDiv);
        document.querySelectorAll('.addProcessElement').forEach(el => {
            el.addEventListener('click', () => {
                currentService.processes.push({
                    id: el.dataset.id,
                    name: el.dataset.name
                });
                ShowServiceProcess();
                ShowAddProcessesBox();
            });
        });
        confirmation.style.display = 'flex';
    }

    updateServiceProcessButton.addEventListener('click', function() {
        if (updateServiceProcessButton.dataset.interactable === "0") return;
        confirmationTitle.textContent = "Update Service Process?";
        confirmationForm.action = "index.php?page=services&action=updateServiceProcess";
        confirmationText.textContent = "Are you sure to update the process of the " + currentService.name + " service?";
        confirmationSubmit.value = "Yes Update";
        confirmationSubmit.classList.add("yellowBG");
        document.querySelectorAll('.processListElement').forEach(el => el.remove());
        currentService.processes.forEach(proc => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'processList[]';
            input.value = proc.id;
            input.className = "processListElement tempElement";
            confirmationForm.appendChild(input);
        });
        confirmation.style.display = 'flex';
    });

    // Subservices – safe DOM
    function ShowSubservices() {
        subservicesContainer.innerHTML = '';
        createSubserviceButton.classList.remove("hidden");
        if (currentService.subservices.length === 0) {
            const msg = document.createElement("h2");
            msg.className = "centerMarginsSelf";
            msg.textContent = "No Subservices";
            subservicesContainer.appendChild(msg);
            return;
        }
        currentService.subservices.forEach(sub => {
            tempDiv = document.createElement("div");
            tempDiv.className = 'roundedMin centerHoriRowLayout flexStatic shadowed clickable fixedScreen noShrink subserviceElement';
            tempDiv.dataset.id = sub.id;
            tempElement = document.createElement("div");
            tempElement.className = 'capitalFirst centerText regMinPadding flexMax skewedXNegBG shadowed';
            const h4 = document.createElement("h4");
            h4.className = "whiteText outlineText";
            h4.textContent = sub.name;
            tempElement.appendChild(h4);
            tempDiv.appendChild(tempElement);
            if (sub.isActive == 1) {
                tempDiv.classList.add("yellowBorder");
                tempElement.classList.add("yellowTransBG");
            } else {
                tempDiv.classList.add("redBorder");
                tempElement.classList.add("redTransBG");
            }
            const orderCountH5 = document.createElement("h5");
            orderCountH5.className = 'capitalFirst centerText regMinPadding minWidth';
            orderCountH5.textContent = "Orders: " + (subserviceOrderCountMap[sub.id] || 0);
            tempDiv.appendChild(orderCountH5);
            subservicesContainer.appendChild(tempDiv);
            tempDiv.addEventListener('click', () => {
                SetCurrentSubservice(sub);
                selectedSubserviceIdInput.value = currentSubservice.id;
                document.getElementById('selectedSubserviceTitle').textContent = currentSubservice.name;
                ShowSubserviceStatusButtonsContainer();
                ShowSubserviceDataContainer();
                ShowSubserviceImages();
            });
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

    function ShowSubserviceStatusButtonsContainer() {
        subserviceStatusButtonsContainer.innerHTML = '';
        if (currentSubservice.isActive == 1) {
            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "redBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
            tempElement.textContent = "Disable";
            tempElement.id = "subserviceStatusButton";
            subserviceStatusButtonsContainer.appendChild(tempElement);
            const activeCount = currentService.subservices.filter(s => s.isActive == 1).length;
            if (activeCount === 1) tempElement.classList.add("faded", "unclickable");
        } else {
            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "yellowBG emphasizedText noBorder shadowed whiteText outlineText centerColumnLayout flexMax noPadding fullHeight";
            tempElement.textContent = "Activate";
            tempElement.id = "subserviceStatusButton";
            subserviceStatusButtonsContainer.appendChild(tempElement);
            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "redBG noBorder shadowed centerColumnLayout fullHeight";
            tempElement.innerHTML = '<img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors unitHeight">'; // safe
            tempElement.id = "deleteSubserviceButton";
            subserviceStatusButtonsContainer.appendChild(tempElement);
            if (currentSubservice.orderCount > 0) tempElement.classList.add("faded", "unclickable");
            else {
                document.getElementById('deleteSubserviceButton').addEventListener('click', () => {
                    document.querySelectorAll('.tempElement').forEach(el => el.remove());
                    refreshCsrfToken();
                    confirmationTitle.textContent = "Delete Subservice?";
                    confirmationForm.action = "index.php?page=services&action=deleteSubservice";
                    confirmationText.textContent = "Are you sure to delete the " + currentSubservice.name + " subservice?";
                    confirmationSubmit.value = "Yes delete";
                    confirmation.style.display = 'flex';
                });
            }
        }
        const isBlocked = (currentSubservice.isActive == 1 && currentService.subservices.filter(s => s.isActive == 1).length === 1);
        if (!isBlocked) {
            document.getElementById('subserviceStatusButton').addEventListener('click', function() {
                document.querySelectorAll('.tempElement').forEach(el => el.remove());
                refreshCsrfToken();
                confirmationTitle.textContent = "Toggle Subservice Status?";
                confirmationForm.action = "index.php?page=services&action=toggleSubserviceStatus";
                confirmationText.textContent = "Are you sure to " + this.textContent + " the " + currentSubservice.name + " subservice?";
                confirmationSubmit.value = "Yes " + this.textContent;
                if (this.textContent == "Activate") confirmationSubmit.classList.add("yellowBG");
                confirmation.style.display = 'flex';
            });
        }
    }

    // Subservice data container – set form values, no innerHTML
    function ShowSubserviceDataContainer() {
        subserviceDataContainer.getElementsByTagName('h2')[0].classList.add("hidden");
        const container = subserviceDataContainer.getElementsByTagName('div')[0];
        const form = container.getElementsByTagName('form')[0];
        const descInput = form.querySelector('textarea[name="description"]');
        const serviceIdInput = form.querySelector('input[name="selectedServiceID"]');
        const subserviceIdInput = form.querySelector('input[name="selectedSubserviceID"]');
        const priceInput = form.querySelector('input[name="pricePerUnit"]');
        container.classList.remove("hidden");
        // Refresh CSRF token in this form
        const pageToken = document.querySelector('input[name="_csrf_token"]');
        if (pageToken) {
            const old = form.querySelector('input[name="_csrf_token"]');
            if (old) old.remove();
            form.insertBefore(pageToken.cloneNode(true), form.firstChild);
        }
        serviceIdInput.value = currentService.id;
        subserviceIdInput.value = currentSubservice.id;
        descInput.value = currentSubservice.description;
        descInput.placeholder = currentSubservice.description;
        priceInput.value = currentSubservice.pricePerUnit;
        priceInput.placeholder = currentSubservice.pricePerUnit;
    }

    function ShowSubserviceImages() {
        subserviceImagesContainer.innerHTML = '';
        if (currentSubservice.images.length === 0) {
            const noImages = document.createElement("div");
            noImages.className = "centerMarginsSelf fullHeight centerColumnLayout fitWidth";
            const b = document.createElement("b");
            b.textContent = "No Images";
            noImages.appendChild(b);
            subserviceImagesContainer.appendChild(noImages);
            return;
        }
        currentSubservice.images.forEach(img => {
            tempDiv = document.createElement("div");
            tempDiv.className = "squareSize fixedScreen centerColumnLayout relatived shadowed roundedTin";
            // Remove button
            const removeA = document.createElement("a");
            removeA.className = "circle squareSize unitHeight norEastAbsolute centerColumnLayout importantInput closeCorner swapRight shadowed minZ removeImageButton";
            removeA.innerHTML = '<img src="../../Shared/Img/XIcon.png" alt="X" class="invertColors">'; // safe
            removeA.dataset.id = img.id;
            removeA.dataset.imageName = img.name;
            tempDiv.appendChild(removeA);
            // Image
            const imgEl = document.createElement("img");
            imgEl.className = "fullHeight absoluted clickable subserviceImageElement";
            imgEl.src = "../../Storage/SubserviceImages/" + img.name;
            imgEl.alt = "Image";
            tempDiv.appendChild(imgEl);
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
                document.querySelectorAll('.tempElement').forEach(elem => elem.remove());
                refreshCsrfToken();
                confirmationTitle.textContent = "Remove Subservice Image?";
                confirmationForm.action = "index.php?page=services&action=removeSubserviceImage";
                confirmationText.textContent = "Are you sure to remove this image from the " + currentSubservice.name + " subservice?";
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

    addSubserviceImageButton.addEventListener('click', function() {
        document.querySelectorAll('.tempElement').forEach(el => el.remove());
        refreshCsrfToken();
        confirmationContent.classList.add("fitWidth");
        confirmationForm.action = "index.php?page=services&action=uploadSubserviceImages";
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
        const previewDiv = document.createElement("div");
        previewDiv.className = "tempElement hidden centerHoriRowLayout minGap regPadding fitWidth scrollableX halfScreenMaxWidth fullMinWidth halfScreenHeight";
        confirmationForm.appendChild(previewDiv);
        confirmationTitle.textContent = "Upload Design Image";
        confirmationText.textContent = "Please upload images for this subservice.";
        confirmationSubmit.value = "Upload";
        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");
        confirmationForm.enctype = "multipart/form-data";
        confirmation.style.display = 'flex';
        fileInput.addEventListener('change', () => {
            previewDiv.innerHTML = ''; // safe clear
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
                const img = document.createElement("img");
                img.className = "fullHeight roundedMin shadowed centerMarginsSelf";
                img.src = URL.createObjectURL(files[0]);
                previewDiv.appendChild(img);
            } else {
                Array.from(files).forEach(file => {
                    const img = document.createElement("img");
                    img.className = "fullHeight roundedMin shadowed";
                    img.src = URL.createObjectURL(file);
                    previewDiv.appendChild(img);
                });
            }
            previewDiv.classList.remove("hidden");
        });
    });

    function ResetSubserviceHeader() {
        document.getElementById('selectedSubserviceTitle').textContent = "No Subservice Selected";
        subserviceStatusButtonsContainer.innerHTML = '';
        subserviceDataContainer.getElementsByTagName('h2')[0].classList.remove("hidden");
        subserviceDataContainer.getElementsByTagName('div')[0].classList.add("hidden");
    }

    createServiceButton.addEventListener('click', () => {
        confirmationTitle.textContent = "Create Service";
        confirmationForm.action = "index.php?page=services&action=createService";
        confirmationText.textContent = "Please enter a unique service name.";
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

    createSubserviceButton.addEventListener('click', () => {
        if (!currentService.id) {
            alert("Please select a service first.");
            return;
        }
        confirmationTitle.textContent = "Create Subservice";
        confirmationForm.action = "index.php?page=services&action=createSubservice";
        confirmationText.textContent = "Please enter a unique subservice name for the " + currentService.name + " service.";
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