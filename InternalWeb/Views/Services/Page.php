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
            <div class="flexMid columnLayout midGap">
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
                                        <h3><?= $name ?></h3>
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
                                            <input type="submit" name="submit" value="Update" class="importantInput">
                                        </form>
                                        <div class="flexMid fullHeight columnLayout minGap">
                                            <div class="centerHoriRowLayout">
                                                <b class="flexMax">Images</b>
                                                <button type="button" class="darkBG noBorder shadowed whiteText centerColumnLayout fullHeight roundedTin"
                                                    id="addSubserviceImageButton">
                                                    <b>Add Image</b>
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
    const serviceProcessList = <?php echo json_encode($serviceProcessList); ?>;
    const subserviceList = <?php echo json_encode($subserviceList); ?>;
    const subserviceOrderCountTally = <?php echo json_encode($subserviceOrderCountTally); ?>;
    const processesList = <?php echo json_encode($processesList); ?>;
    const subserviceImageList = <?php echo json_encode($subserviceImageList); ?>;
    const lastServiceID = <?php echo $serviceID; ?>;
    const lastSubserviceID = <?php echo $subserviceID; ?>;

    const serviceProcessMap = {};

    serviceProcessList.forEach(item => {
        if (!serviceProcessMap[item.serviceID]) {
            serviceProcessMap[item.serviceID] = [];
        }

        serviceProcessMap[item.serviceID].push({
            id: item.id,
            name: item.name
        });
    });

    const subserviceMap = {};

    subserviceList.forEach(item => {
        if (!subserviceMap[item.serviceID]) {
            subserviceMap[item.serviceID] = [];
        }

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
        if (!subserviceImageMap[item.subserviceID]) {
            subserviceImageMap[item.subserviceID] = [];
        }

        subserviceImageMap[item.subserviceID].push({
            id: item.id,
            name: item.imageName
        });
    });

    document.addEventListener("DOMContentLoaded", () => {
        confirmationCancel.value = "No Cancel";
    });

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

    let tempElement;
    let tempDiv;
    let selectedServiceProcess;
    let selectedServiceSubservices;
    let selectedServiceSubservicesMap;
    let selectedSubserviceImages;
    let selectedServiceID;
    let selectedServiceName;
    let selectedServiceStatus;
    let selectedServiceOrderCount;
    let selectedSubserviceID;
    let selectedSubserviceName;

    // Logic when clicked on service element
    document.addEventListener('DOMContentLoaded', function() {
        serviceElements.forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedServiceID = elem.dataset.id;
                selectedServiceName = elem.dataset.name;
                selectedServiceStatus = elem.dataset.isActive;
                selectedServiceOrderCount = elem.dataset.orderCount;

                selectedServiceIdInput.value = selectedServiceID;

                document.getElementById('selectedServiceTitle').textContent = selectedServiceName + " Service";

                selectedServiceProcess = [...(serviceProcessMap[selectedServiceID] || [])];
                selectedServiceSubservices = [...(subserviceMap[selectedServiceID] || [])];

                selectedServiceSubservicesMap = {};

                selectedServiceSubservices.forEach(item => {
                    selectedServiceSubservicesMap[item.id] = {
                        description: item.description,
                        pricePerUnit: item.pricePerUnit
                    };
                });

                ShowServiceStatusButtonsContainer();
                ShowObjectiveButtonsContainer(elem.dataset.hasDesign, elem.dataset.hasVariableList);
                ShowServiceProcess();
                ResetSubserviceHeader();
                ShowSubservices();
            });
        });

        // Service Persistance Logic
        if (lastServiceID != -1) {
            for (const elem of serviceElements) {
                if (elem.dataset.id == lastServiceID) {
                    elem.click();
                    break;
                }
            }
        }
    });

    // Showing the service status buttons and delete button
    function ShowServiceStatusButtonsContainer() {
        serviceStatusButtonsContainer.innerHTML = '';

        if (selectedServiceStatus == 1) {
            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "redBG emphasizedText noBorder shadowed whiteText centerColumnLayout flexMax noPadding fullHeight";
            tempElement.textContent = "Disable";
            tempElement.id = "serviceStatusButton";
            serviceStatusButtonsContainer.appendChild(tempElement);
        } else {
            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "yellowBG emphasizedText noBorder shadowed whiteText centerColumnLayout flexMax noPadding fullHeight";
            tempElement.textContent = "Activate";
            tempElement.id = "serviceStatusButton";
            serviceStatusButtonsContainer.appendChild(tempElement);

            // If a service has no process and no active subservice then dont let be active
            if (!(selectedServiceProcess.length > 0 && selectedServiceSubservices.length > 0 && selectedServiceSubservices[0].isActive == 1)) {
                tempElement.classList.add("faded", "unclickable");
            }

            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "redBG noBorder shadowed centerColumnLayout fullHeight";
            tempElement.innerHTML = '<img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors unitHeight">';
            tempElement.id = "deleteServiceButton";
            serviceStatusButtonsContainer.appendChild(tempElement);

            // If a service has active orders, then dont let it be deleted
            if (selectedServiceOrderCount > 0) {
                tempElement.classList.add("faded", "unclickable");
            } else {
                // Delete Service Button Logic
                document.getElementById('deleteServiceButton').addEventListener('click', function() {
                    confirmationTitle.innerHTML = "Delete Service?";
                    confirmationForm.action = "index.php?page=services&action=deleteService"

                    confirmationText.innerHTML = "Are you sure to delete the " + selectedServiceName + " service?";
                    confirmationSubmit.value = "Yes delete";

                    confirmation.style.display = 'flex';
                });
            }
        }

        // Service Status Button Toggle Logic
        document.getElementById('serviceStatusButton').addEventListener('click', function() {
            // If a service has no process and no active subservice then dont let be active
            if (selectedServiceProcess.length > 0 && selectedServiceSubservices.length > 0 && selectedServiceSubservices[0].isActive == 1) {
                confirmationTitle.innerHTML = "Toggle Service Status?";
                confirmationForm.action = "index.php?page=services&action=toggleServiceStatus"

                confirmationText.innerHTML = "Are you sure to " + this.textContent + " the " + selectedServiceName + " service?";
                confirmationSubmit.value = "Yes " + this.textContent;

                if (this.textContent == "Activate") {
                    confirmationSubmit.classList.add("yellowBG");
                }

                confirmation.style.display = 'flex';
            }
        });
    }

    // Showing the objective buttons
    function ShowObjectiveButtonsContainer(hasDesign, hasVariableList) {
        objectiveButtonsContainer.innerHTML = '';

        tempElement = document.createElement("button");
        tempElement.type = "button";

        if (hasDesign == 1) {
            tempElement.className = "yellowBG emphasizedText noBorder shadowed whiteText centerColumnLayout flexMax noPadding fullHeight";
            tempElement.textContent = "Has Design";
        } else {
            tempElement.className = "redBG emphasizedText noBorder shadowed whiteText centerColumnLayout flexMax noPadding fullHeight";
            tempElement.textContent = "No Design";
        }

        tempElement.id = "hasDesignButton";
        objectiveButtonsContainer.appendChild(tempElement);

        tempElement = document.createElement("button");
        tempElement.type = "button";

        if (hasVariableList == 1) {
            tempElement.className = "yellowBG emphasizedText noBorder shadowed whiteText centerColumnLayout flexMax noPadding fullHeight";
            tempElement.textContent = "Has Variable List";
        } else {
            tempElement.className = "redBG emphasizedText noBorder shadowed whiteText centerColumnLayout flexMax noPadding fullHeight";
            tempElement.textContent = "No Variable List";
        }

        tempElement.id = "hasVariableListButton";
        objectiveButtonsContainer.appendChild(tempElement);

        // If the service has active orders or is active then dont let service edits
        if (selectedServiceOrderCount == 0 && selectedServiceStatus == 0) {
            // Has Design Button Toggle Logic
            document.getElementById('hasDesignButton').addEventListener('click', function() {
                confirmationTitle.innerHTML = "Toggle Design Objective?";
                confirmationForm.action = "index.php?page=services&action=toggleHasDesign"

                if (this.textContent == "No Design") {
                    confirmationSubmit.classList.add("yellowBG");
                    confirmationText.innerHTML = "Are you sure to active the design objective?";
                    confirmationSubmit.value = "Yes Active";
                } else {
                    confirmationText.innerHTML = "Are you sure to disable the design objective?";
                    confirmationSubmit.value = "Yes Disable";
                }

                confirmation.style.display = 'flex';
            });

            // Has Variable List Button Toggle Logic
            document.getElementById('hasVariableListButton').addEventListener('click', function() {
                confirmationTitle.innerHTML = "Toggle Design Objective?";
                confirmationForm.action = "index.php?page=services&action=toggleHasVariableList"

                if (this.textContent == "No Variable List") {
                    confirmationSubmit.classList.add("yellowBG");
                    confirmationText.innerHTML = "Are you sure to active the variable list objective?";
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

    // Show the service process function
    function ShowServiceProcess() {
        // Dont let users edit the service process when the service is active and has active orders
        if (selectedServiceOrderCount == 0 && selectedServiceStatus == 0) {
            updateServiceProcessButton.classList.remove("faded", "unclickable");
            updateServiceProcessButton.dataset.interactable = 1;
        } else {
            updateServiceProcessButton.classList.add("faded", "unclickable");
            updateServiceProcessButton.dataset.interactable = 0;
        }

        serviceProcess.innerHTML = '';
        updateServiceProcessButton.classList.remove("hidden");

        if (selectedServiceProcess.length == 0) {
            tempElement = document.createElement("div");
            tempElement.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";
            tempElement.innerHTML = "<b>No Service Process</b>"
            serviceProcess.appendChild(tempElement);

            // Dont let users edit the service process when the service is active and has active orders
            if (selectedServiceOrderCount == 0 && selectedServiceStatus == 0) {
                tempElement = document.createElement("div");
                tempElement.className = "circle squareSize duoHeight darkBG roundedMin shadowed centerRowLayout regMinPadding";
                tempElement.innerHTML = '<img src="../../Shared/Img/CrossIcon.png" alt="Cross" class="invertColors">'
                tempElement.id = "addProcessButton";
                serviceProcess.appendChild(tempElement);

                document.getElementById('addProcessButton').addEventListener('click', function() {
                    ShowAddProcessesBox();
                });
            }

            return;
        }

        tempElement = document.createElement("div");
        tempElement.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";

        tempElement.innerHTML = selectedServiceProcess.length == 1 ? `
            <b>${selectedServiceProcess[0].name}</b>
            <a class="squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove" data-index="0">
                <img src="../../Shared/Img/XIcon.png" alt="X">
            </a>
        ` : `
            <b>${selectedServiceProcess[0].name}</b>
            <a class="squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove" data-index="0">
                <img src="../../Shared/Img/XIcon.png" alt="X">
            </a>
            <a class="circle squareSize unitHeight souEastAbsolute centerColumnLayout importantInput closeCorner swapRight shadowed" data-index="0">
                <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors">
            </a>
        `;

        serviceProcess.appendChild(tempElement);

        for (let i = 1; i < selectedServiceProcess.length - 1; i++) {
            tempElement = document.createElement("h2");
            tempElement.textContent = ">";
            serviceProcess.appendChild(tempElement);

            tempElement = document.createElement("div");
            tempElement.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";
            tempElement.innerHTML = `
                <b>${selectedServiceProcess[i].name}</b>
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

        if (selectedServiceProcess.length > 1) {
            tempElement = document.createElement("h2");
            tempElement.textContent = ">";
            serviceProcess.appendChild(tempElement);

            tempElement = document.createElement("div");
            tempElement.className = "flexMin minHeight darkFadedBG bordered roundedMin centerRowLayout minGap shadowed";
            tempElement.innerHTML = `
                <b>${selectedServiceProcess[selectedServiceProcess.length - 1].name}</b>
                <a class="squareSize unitHeight norWestAbsolute centerColumnLayout closeCorner processRemove" data-index="${selectedServiceProcess.length - 1}">
                    <img src="../../Shared/Img/XIcon.png" alt="X">
                </a>
                <a class="circle squareSize unitHeight souWestAbsolute centerColumnLayout importantInput closeCorner swapLeft shadowed"
                    data-index="${selectedServiceProcess.length - 1}">
                    <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors mirrorX">
                </a>
            `;
            serviceProcess.appendChild(tempElement);
        }

        // Dont let users edit the service process when the service is active and has active orders
        if (selectedServiceOrderCount == 0 && selectedServiceStatus == 0) {
            tempElement = document.createElement("div");
            tempElement.className = "circle squareSize duoHeight darkBG roundedMin shadowed centerRowLayout regMinPadding";
            tempElement.innerHTML = '<img src="../../Shared/Img/CrossIcon.png" alt="Cross" class="invertColors">';
            tempElement.id = "addProcessButton";
            serviceProcess.appendChild(tempElement);

            document.getElementById('addProcessButton').addEventListener('click', function() {
                ShowAddProcessesBox();
            });
        }

        document.querySelectorAll('.processRemove').forEach(function(elem) {
            // Dont let users edit the service process when the service is active and has active orders
            if (selectedServiceOrderCount == 0 && selectedServiceStatus == 0) {
                elem.addEventListener('click', function() {
                    selectedServiceProcess.splice(elem.dataset.index, 1);
                    ShowServiceProcess();
                });
            } else {
                elem.classList.add("hidden");
            }
        });

        document.querySelectorAll('.swapRight').forEach(function(elem) {
            // Dont let users edit the service process when the service is active and has active orders
            if (selectedServiceOrderCount == 0 && selectedServiceStatus == 0) {
                elem.addEventListener('click', function() {
                    const index = Number(elem.dataset.index);
                    [selectedServiceProcess[index], selectedServiceProcess[index + 1]] = [selectedServiceProcess[index + 1], selectedServiceProcess[index]];
                    ShowServiceProcess();
                });
            } else {
                elem.classList.add("hidden");
            }
        });

        document.querySelectorAll('.swapLeft').forEach(function(elem) {
            // Dont let users edit the service process when the service is active and has active orders
            if (selectedServiceOrderCount == 0 && selectedServiceStatus == 0) {
                elem.addEventListener('click', function() {
                    const index = Number(elem.dataset.index);
                    [selectedServiceProcess[index], selectedServiceProcess[index - 1]] = [selectedServiceProcess[index - 1], selectedServiceProcess[index]];
                    ShowServiceProcess();
                });
            } else {
                elem.classList.add("hidden");
            }
        });
    }

    // Show add processes to service process box
    function ShowAddProcessesBox() {
        const currentProcesses = new Set(selectedServiceProcess.map(p => p.name));
        let hasAddableProcesses = false;

        confirmationTitle.innerHTML = "Add Processes";

        confirmationText.innerHTML = "Click on processes that you want to add to the " + selectedServiceName + " service process.";
        confirmationSubmit.classList.add("hidden");

        confirmationCancel.value = "Return";

        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });

        tempDiv = document.createElement("div");
        tempDiv.className = 'midHeight scrollable columnLayout minGap regMinPadding tempElement';

        processesList.forEach((item) => {
            if (currentProcesses.has(item.name)) return;

            tempElement = document.createElement('div');
            tempElement.className = 'tinHeight noShrink roundedMin centerColumnLayout bordered darkTransBG emphasizedText capitalFirst shadowed clickable addProcessElement';
            tempElement.innerHTML = '<b>' + item.name + '</b>';
            tempElement.dataset.name = item.name;
            tempElement.dataset.id = item.id;
            tempDiv.appendChild(tempElement);

            hasAddableProcesses = true;
        });

        if (!hasAddableProcesses) {
            tempElement = document.createElement("b");
            tempElement.className = "centerMarginsSelf";
            tempElement.textContent = "No Processes To Add";
            tempDiv.appendChild(tempElement);
        }

        confirmationForm.appendChild(tempDiv);

        document.querySelectorAll('.addProcessElement').forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedServiceProcess.push({
                    id: elem.dataset.id,
                    name: elem.dataset.name
                });

                ShowServiceProcess();
                ShowAddProcessesBox();
            });
        });

        confirmation.style.display = 'flex';
    }

    // Update Process Function Logic
    updateServiceProcessButton.addEventListener('click', function() {
        if (updateServiceProcessButton.dataset.interactable == 0) return;

        confirmationTitle.innerHTML = "Update Service Process?";
        confirmationForm.action = "index.php?page=services&action=updateServiceProcess";

        confirmationText.innerHTML = "Are you sure to update the process of the " + selectedServiceName + " service?";
        confirmationSubmit.value = "Yes Update";
        confirmationSubmit.classList.add("yellowBG");

        selectedServiceProcess.forEach(function(process, i) {
            tempElement = document.createElement('input');
            tempElement.type = 'hidden';
            tempElement.name = 'processList[]';
            tempElement.value = process.id;
            tempElement.className = "processListElement tempElement";
            confirmationForm.appendChild(tempElement);
        });

        confirmation.style.display = 'flex';
    });

    // Show subservices function
    function ShowSubservices() {
        subservicesContainer.innerHTML = '';
        createSubserviceButton.classList.remove("hidden");

        if (selectedServiceSubservices.length == 0) {
            tempElement = document.createElement("h2");
            tempElement.className = "centerMarginsSelf";
            tempElement.innerHTML = "No Subservices"
            subservicesContainer.appendChild(tempElement);
            return;
        }

        selectedServiceSubservices.forEach((item) => {
            tempDiv = document.createElement("div");
            tempDiv.className = 'roundedMin centerHoriRowLayout flexStatic shadowed clickable fixedScreen noShrink subserviceElement';
            tempDiv.dataset.id = item.id;
            subservicesContainer.appendChild(tempDiv);

            tempElement = document.createElement("div");
            tempElement.className = 'capitalFirst centerText regMinPadding flexMax skewedXNegBG shadowed';
            tempElement.innerHTML = `<h4>${item.name}</h4>`;
            tempDiv.appendChild(tempElement);

            if (item.isActive == 1) {
                tempDiv.classList.add("yellowBorder");
                tempElement.classList.add("yellowTransBG");
            } else {
                tempDiv.classList.add("redBorder");
                tempElement.classList.add("redTransBG");
            }

            tempElement = document.createElement("h5");
            tempElement.className = 'capitalFirst centerText regMinPadding minWidth';
            tempElement.textContent = "Orders: " + (subserviceOrderCountMap[item.id] || 0);
            tempDiv.appendChild(tempElement);

            // Logic when clicked on subservice element
            tempDiv.addEventListener('click', function() {
                selectedSubserviceID = item.id;
                selectedSubserviceName = item.name;

                selectedSubserviceIdInput.value = selectedSubserviceID;

                document.getElementById('selectedSubserviceTitle').textContent = selectedSubserviceName;

                ShowSubserviceStatusButtonsContainer(item.isActive, subserviceOrderCountMap[item.id] || 0);
                ShowSubserviceDataContainer();
                ShowSubserviceImages();
            });

            // Subservice Persistance Logic
            if (lastSubserviceID != -1 && Number(item.id) == Number(lastSubserviceID)) {
                selectedSubserviceID = item.id;
                selectedSubserviceName = item.name;

                selectedSubserviceIdInput.value = selectedSubserviceID;

                document.getElementById('selectedSubserviceTitle').textContent = selectedSubserviceName;

                ShowSubserviceStatusButtonsContainer(item.isActive, subserviceOrderCountMap[item.id] || 0);
                ShowSubserviceDataContainer();
                ShowSubserviceImages();
            }
        });
    }

    // Showing the subservice status buttons and delete button
    function ShowSubserviceStatusButtonsContainer(status, orderCount) {
        subserviceStatusButtonsContainer.innerHTML = '';

        if (status == 1) {
            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "redBG emphasizedText noBorder shadowed whiteText centerColumnLayout flexMax noPadding fullHeight";
            tempElement.textContent = "Disable";
            tempElement.id = "subserviceStatusButton";
            subserviceStatusButtonsContainer.appendChild(tempElement);

            // If the subservice is the last active one in the service dont let it be disabled
            if (selectedServiceSubservices.length == 1 || selectedServiceSubservices[1].isActive == 0) {
                tempElement.classList.add("faded", "unclickable");
            }
        } else {
            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "yellowBG emphasizedText noBorder shadowed whiteText centerColumnLayout flexMax noPadding fullHeight";
            tempElement.textContent = "Activate";
            tempElement.id = "subserviceStatusButton";
            subserviceStatusButtonsContainer.appendChild(tempElement);

            tempElement = document.createElement("button");
            tempElement.type = "button";
            tempElement.className = "redBG noBorder shadowed centerColumnLayout fullHeight";
            tempElement.innerHTML = '<img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors unitHeight">';
            tempElement.id = "deleteSubserviceButton";
            subserviceStatusButtonsContainer.appendChild(tempElement);

            // If the subservice has active orders dont let it be deleted
            if (orderCount > 0) {
                tempElement.classList.add("faded", "unclickable");
            } else {
                // Delete Subservice Button Logic
                document.getElementById('deleteSubserviceButton').addEventListener('click', function() {
                    confirmationTitle.innerHTML = "Delete Subservice?";
                    confirmationForm.action = "index.php?page=services&action=deleteSubservice"

                    confirmationText.innerHTML = "Are you sure to delete the " + selectedSubserviceName + " subservice?";
                    confirmationSubmit.value = "Yes delete";

                    confirmation.style.display = 'flex';
                });
            }
        }

        // If the subservice status is active and is the last active one in the service then dont let be disabled
        if (!(status == 1 && (selectedServiceSubservices.length == 1 || selectedServiceSubservices[1].isActive == 0))) {
            // Subservice Status Button Toggle Logic
            document.getElementById('subserviceStatusButton').addEventListener('click', function() {
                confirmationTitle.innerHTML = "Toggle Subservice Status?";
                confirmationForm.action = "index.php?page=services&action=toggleSubserviceStatus"

                confirmationText.innerHTML = "Are you sure to " + this.textContent + " the " + selectedSubserviceName + " subservice?";
                confirmationSubmit.value = "Yes " + this.textContent;

                if (this.textContent == "Activate") {
                    confirmationSubmit.classList.add("yellowBG");
                }

                confirmation.style.display = 'flex';
            });
        }
    }

    // Showing subservice data container function logic
    function ShowSubserviceDataContainer() {
        subserviceDataContainer.getElementsByTagName('h2')[0].classList.add("hidden");

        const container = subserviceDataContainer.getElementsByTagName('div')[0];
        const formElement = container.getElementsByTagName('form')[0];
        const descriptionInput = formElement.getElementsByTagName('textarea')[0];
        const selectedServiceIDInput = formElement.getElementsByTagName('input')[0];
        const selectedSubserviceIDInput = formElement.getElementsByTagName('input')[1];
        const pricePerUnitInput = formElement.getElementsByTagName('input')[2];

        container.classList.remove("hidden");

        selectedServiceIDInput.value = selectedServiceID;
        selectedSubserviceIDInput.value = selectedSubserviceID;
        descriptionInput.value = selectedServiceSubservicesMap[selectedSubserviceID].description;
        descriptionInput.placeholder = descriptionInput.value;
        pricePerUnitInput.value = selectedServiceSubservicesMap[selectedSubserviceID].pricePerUnit;
        pricePerUnitInput.placeholder = pricePerUnitInput.value;
    }

    // Showing subservice images function logic
    function ShowSubserviceImages() {
        subserviceImagesContainer.innerHTML = '';
        selectedSubserviceImages = [...(subserviceImageMap[selectedSubserviceID] || [])];

        if (selectedSubserviceImages.length == 0) {
            subserviceImagesContainer.innerHTML = `
                <div class="centerMarginsSelf fullHeight centerColumnLayout fitWidth">
                    <b>No Images</b>
                </div>
            `;
            return;
        }

        selectedSubserviceImages.forEach(item => {
            tempDiv = document.createElement("div");
            tempDiv.className = "squareSize fixedScreen centerColumnLayout relatived shadowed roundedTin";

            tempElement = document.createElement("a");
            tempElement.className = "circle squareSize unitHeight norEastAbsolute centerColumnLayout importantInput closeCorner swapRight shadowed minZ removeImageButton";
            tempElement.innerHTML = '<img src="../../Shared/Img/XIcon.png" alt="X" class="invertColors">';
            tempElement.dataset.id = item.id;
            tempElement.dataset.imageName = item.name;
            tempDiv.appendChild(tempElement);

            tempElement = document.createElement("img");
            tempElement.className = "fullHeight absoluted clickable subserviceImageElement";
            tempElement.src = "../../Storage/SubserviceImages/" + item.name;
            tempElement.alt = "Image";
            tempDiv.appendChild(tempElement);

            subserviceImagesContainer.appendChild(tempDiv);
        });

        // View Subservice Image Focus Logic
        document.querySelectorAll('.subserviceImageElement').forEach(function(elem) {
            elem.addEventListener('click', function() {
                imageBoxImage.src = elem.src;
                imageBox.style.display = 'flex';
            });
        });

        // Remove Subservice Image Logic
        document.querySelectorAll('.removeImageButton').forEach(function(elem) {
            elem.addEventListener('click', function() {
                confirmationTitle.innerHTML = "Remove Subservice Image?";
                confirmationForm.action = "index.php?page=services&action=removeSubserviceImage";

                confirmationText.innerHTML = "Are you sure to remove this image from the " + selectedSubserviceName + " subservice?";
                confirmationSubmit.value = "Yes Remove";

                tempElement = document.createElement("input");
                tempElement.type = "hidden";
                tempElement.name = "selectedID";
                tempElement.value = elem.dataset.id;
                tempElement.className = "tempElement";
                confirmationForm.appendChild(tempElement);

                tempDiv = document.createElement("div");
                tempDiv.className = "fullWidth tempElement centerHoriRowLayout regMinPadding";
                confirmationForm.appendChild(tempDiv);

                const uploadedImage = document.createElement("img");
                uploadedImage.className = "fullWidth roundedMin shadowed";
                uploadedImage.src = "../../Storage/SubserviceImages/" + elem.dataset.imageName;
                tempDiv.appendChild(uploadedImage);

                confirmation.style.display = 'flex';
            });
        });
    }

    // Subservice Image Upload Logic
    addSubserviceImageButton.addEventListener('click', function() {
        confirmationContent.classList.add("fitWidth");
        confirmationForm.action = "index.php?page=services&action=uploadSubserviceImages"

        tempDiv = document.createElement("div");
        tempDiv.className = "tempElement centerHoriRowLayout minGap";
        confirmationForm.appendChild(tempDiv);

        tempElement = document.createElement("b");
        tempElement.textContent = "Upload File:";
        tempDiv.appendChild(tempElement);

        tempElement = document.createElement("input");
        tempElement.type = "file";
        tempElement.name = "images[]";
        tempElement.accept = "image/*";
        tempElement.multiple = true;
        tempElement.required = "true";
        tempElement.className = "flexMax";
        tempDiv.appendChild(tempElement);

        tempDiv = document.createElement("div");
        tempDiv.className = "tempElement hidden centerHoriRowLayout minGap regPadding fitWidth scrollableX halfScreenMaxWidth fullMinWidth halfScreenHeight";
        confirmationForm.appendChild(tempDiv);

        confirmationTitle.innerHTML = "Upload Design Image";
        confirmationText.innerHTML = "Please upload images for this subservice.";
        confirmationSubmit.value = "Upload";
        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");

        confirmationForm.enctype = "multipart/form-data";
        confirmation.style.display = 'flex';

        tempElement.addEventListener('change', () => {
            tempDiv.innerHTML = '';

            const files = tempElement.files;

            if (files.length === 0) {
                tempDiv.classList.add("hidden");
                return;
            }

            for (let i = 0; i < files.length; i++) {
                const design = files[i];

                if (!design.type.startsWith("image/")) {
                    alert("Only images are allowed. File: " + design.name);
                    tempElement.value = "";
                    return;
                }
            }

            if (Array.from(files).length == 1) {
                const uploadedImage = document.createElement("img");
                uploadedImage.className = "fullHeight roundedMin shadowed centerMarginsSelf";
                uploadedImage.src = URL.createObjectURL(files[0]);
                tempDiv.appendChild(uploadedImage);
            } else {
                Array.from(files).forEach(item => {
                    const uploadedImage = document.createElement("img");
                    uploadedImage.className = "fullHeight roundedMin shadowed";
                    uploadedImage.src = URL.createObjectURL(item);
                    tempDiv.appendChild(uploadedImage);
                });
            }

            tempDiv.classList.remove("hidden");
        });
    });

    // Reset the subservices part of the page when clicking on another service
    function ResetSubserviceHeader(status) {
        document.getElementById('selectedSubserviceTitle').textContent = "No Subservice Selected";
        subserviceStatusButtonsContainer.innerHTML = '';

        subserviceDataContainer.getElementsByTagName('h2')[0].classList.remove("hidden");
        subserviceDataContainer.getElementsByTagName('div')[0].classList.add("hidden");
    }

    // Service creation logic
    createServiceButton.addEventListener('click', function() {
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

    // Subservice creation logic
    createSubserviceButton.addEventListener('click', function() {
        confirmationTitle.innerHTML = "Create Subservice";
        confirmationForm.action = "index.php?page=services&action=createSubservice";

        confirmationText.innerHTML = "Please enter a unique subservice name for the " + selectedServiceName + " service.";
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

    // Added cancellation events
    confirmationCancel.addEventListener('click', function() {
        confirmationSubmit.classList.remove("yellowBG", "hidden");

        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });
    });

    confirmationBG.addEventListener('click', function() {
        confirmationSubmit.classList.remove("yellowBG", "hidden");

        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });
    });
</script>

</html>