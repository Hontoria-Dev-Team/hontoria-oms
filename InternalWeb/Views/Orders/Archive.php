<!DOCTYPE html>
<html>

<head>
    <title>Order Archive - Hontoria OMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/StaffPage.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <span class="centerHoriRowLayout midGap">
            <?php include("../Views/.Components/BackLink.php"); ?>
            <h1 class="titleLogo minGap tinHeight">
                <img src="../../Shared/Img/ArchiveIcon.png" alt="Archive"> Order Archive
            </h1>
        </span>
        <?php include("../Views/.Components/MessageBox.php"); ?>
        <section class="flexMax">
            <div class="rowLayout midGap fullDimensions">
                <section class="roundedMid centerColumnLayout flexMid">
                    <div class="box fullDimensions roundedMid columnLayout minGap">
                        <h2>Archived Orders:</h2>
                        <div id="ordersContainer" class="scrollable columnLayout minGap regMinPadding flexMax noFlexBasis noMinHeight">
                            <?php foreach ($orderList as $order): ?>
                                <?php
                                $elemStyle = $order['isCompleted'] ? "yellowBorder yellowTransBG" : "redBorder redTransBG";
                                $idBG = $order['isCompleted'] ? "yellowBG" : "redBG";
                                ?>
                                <div class="tinHeight noShrink roundedMin centerHoriRowLayout clickable shadowed fixedScreen orderElement <?= $elemStyle ?>"
                                    data-id="<?= $order['id'] ?>">
                                    <h3 class="gradientDiagBG flexMid centerColumnLayout fullHeight whiteText skewedXNegBG shadowed capitalFirst <?= $idBG ?>">
                                        <span>Order #<?= $order['id'] ?></span>
                                    </h3>
                                    <b class="flexMax fullHeight centerColumnLayout"><?= $order['subserviceName'] ?> <?= $order['serviceName'] ?></b>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
                <div class="flexMax columnLayout midGap">
                    <div class="flexMax rowLayout midGap">
                        <section class="roundedMid centerColumnLayout flexMax">
                            <div class="box fullDimensions roundedMid columnLayout minGap">
                                <h2>Order Details:</h2>
                                <div id="detailsContainer" class="columnLayout tinGap flexMax">
                                    <h2 class="centerMarginsSelf">No Order Selected</h2>
                                </div>
                            </div>
                            <div class="gradientBorderDiag"></div>
                        </section>
                        <section class="roundedMid centerColumnLayout flexMax">
                            <div class="box fullDimensions roundedMid columnLayout minGap">
                                <h2>Order Image:</h2>
                                <div class="flexMax centerColumnLayout regMinPadding noFlexBasis noMinHeight" id="orderDesignContainer">
                                    <h2 class="centerMarginsSelf">No Order Selected</h2>
                                </div>
                            </div>
                            <div class="gradientBorderDiag"></div>
                        </section>
                    </div>
                    <section class="roundedMid centerColumnLayout flexMax noFlexBasis noMinHeight">
                        <div class="box fullDimensions roundedMid columnLayout minGap">
                            <h2>Order Process:</h2>
                            <div class="centerHoriRowLayout tinGap flexMax" id="orderProcessContainer">
                                <h2 class="centerMarginsSelf">No Order Selected</h2>
                            </div>
                            <h2>Order Process Assignees:</h2>
                            <div class="gridCenterFlex minGap scrollable flexMax" id="orderProcessAssigneesContainer">
                                <h2 class="centerMarginsSelf">No Process Selected</h2>
                            </div>
                        </div>
                        <div class="gradientBorderDiag"></div>
                    </section>
                </div>
            </div>
        </section>
    </main>
    <?php include("../Views/.Components/ImageBox.php"); ?>
</body>
<script src="../.JS/TimeHelpers.js"></script>
<script src="../.JS/ImageBox.js"></script>
<script>
    const detailsContainer = document.getElementById('detailsContainer');
    const orderDesignContainer = document.getElementById('orderDesignContainer');
    const orderProcessContainer = document.getElementById('orderProcessContainer');
    const orderProcessAssigneesContainer = document.getElementById('orderProcessAssigneesContainer');
    const orderList = <?php echo json_encode($orderList); ?>;
    const orderGroupList = <?php echo json_encode($orderGroupList); ?>;
    const orderDesignList = <?php echo json_encode($orderDesignList); ?>;
    const orderAssignmentList = <?php echo json_encode($orderAssignmentList); ?>;

    const orderMap = {};

    orderList.forEach(item => {
        orderMap[item.id] = {
            serviceName: item.serviceName,
            subserviceName: item.subserviceName,
            customerName: item.customerName,
            messengerGCLink: item.messengerGCLink,
            priceTotal: item.priceTotal,
            createdAt: item.createdAt,
            deadlineAt: item.deadlineAt,
            archivedAt: item.archivedAt,
            isCompleted: item.isCompleted
        };
    });

    const orderGroupMap = {};

    orderGroupList.forEach(item => {
        if (!orderGroupMap[item.orderArchiveID]) {
            orderGroupMap[item.orderArchiveID] = [];
        }

        orderGroupMap[item.orderArchiveID].push({
            description: item.description,
            units: item.units
        });
    });

    const orderDesignMap = {};

    orderDesignList.forEach(item => {
        orderDesignMap[item.orderArchiveID] = item.imageName;
    });

    const orderAssignmentMap = {};

    orderAssignmentList.forEach(item => {
        if (!orderAssignmentMap[item.orderArchiveID]) {
            orderAssignmentMap[item.orderArchiveID] = {};
        }

        if (!orderAssignmentMap[item.orderArchiveID][item.processName]) {
            orderAssignmentMap[item.orderArchiveID][item.processName] = [];
        }

        orderAssignmentMap[item.orderArchiveID][item.processName].push({
            assigneeName: item.userFirstName + " " + (item.userMiddleName ? item.userMiddleName.charAt(0) + ". " : "") + item.userLastName,
            assignedAt: item.assignedAt
        });
    });

    let selectedOrderID;
    let selectedOrder;
    let selectedOrderGroups;
    let selectedOrderDesign;
    let selectedOrderProcess;
    let selectedOrderProcessNames;
    let selectedOrderProcessee;
    let tempDiv;
    let tempElement;

    // Clicking an order logic
    document.querySelectorAll('.orderElement').forEach(function(elem) {
        elem.addEventListener('click', function() {
            selectedOrderID = elem.dataset.id;

            selectedOrder = orderMap[selectedOrderID];
            selectedOrderGroups = [...(orderGroupMap[selectedOrderID] || [])];
            selectedOrderDesign = orderDesignMap[selectedOrderID];
            selectedOrderProcess = orderAssignmentMap[selectedOrderID] || {};

            ResetAssignees();
            ShowDetails();
            ShowDesign();
            ShowProcess();
        });
    });

    // Show Order details function logic
    function ShowDetails() {
        detailsContainer.innerHTML = `
            <b>Order #1 <span class="${selectedOrder.isCompleted == 1 ? "yellowText" : "redText"}">(${selectedOrder.isCompleted == 1 ? "Completed" : "Not Completed"})</span></b>
            <div class="gridFlex tinGap">
                <div class="centerHoriRowLayout tinGap marginRightMin">
                    <b>Service: <span class="fontWeightNormal">${selectedOrder.serviceName},</span></b>
                </div>
                <div class="centerHoriRowLayout tinGap marginRightMin">
                    <b>Subservice: <span class="fontWeightNormal">${selectedOrder.subserviceName},</span></b>
                </div>
                <div class="centerHoriRowLayout tinGap marginRightMin">
                    <b>Customer: <span class="fontWeightNormal">${selectedOrder.customerName},</span></b>
                </div>
                <div class="centerHoriRowLayout tinGap marginRightMin">
                    <b>Total Price: <span class="fontWeightNormal">₱${selectedOrder.priceTotal},</span></b>
                </div>
                <div class="centerHoriRowLayout tinGap marginRightMin">
                    <b>Created At: <span class="fontWeightNormal">${formatDate(selectedOrder.createdAt)},</span></b>
                </div>
                <div class="centerHoriRowLayout tinGap marginRightMin">
                    <b>Deadline At:
                        <span class="fontWeightNormal">
                            ${selectedOrder.deadlineAt == '0000-00-00 00:00:00' ? "No Deadline" : formatDate(selectedOrder.deadlineAt)},
                        </span>
                    </b>
                </div>
                <div class="centerHoriRowLayout tinGap marginRightMin">
                    <b>Archived At: <span class="fontWeightNormal">${formatDate(selectedOrder.archivedAt)}</span></b>
                </div>
            </div>
        `;

        tempElement = document.createElement('div');
        tempElement.className = 'flexMax bordered roundedMin centerHoriRowLayout shadowed fixedScreen noSelectHidden';
        detailsContainer.appendChild(tempElement);

        tempDiv = document.createElement('div');
        tempDiv.className = 'scrollable fullHeight flexMax gridCenterFlex minGap regMinPadding';
        tempElement.appendChild(tempDiv);

        selectedOrderGroups.forEach(group => {
            tempElement = document.createElement("b");
            tempElement.className = "noShrink fitHeight roundedMin centerRowLayout minGap darkTransBG regMinPadding bordered";
            tempElement.textContent = group.description + ": " + group.units;
            tempDiv.appendChild(tempElement);
        });

        tempElement = document.createElement("b");
        tempElement.className = "squareSize fullHeight centerColumnLayout darkBG shadowed whiteText regMinPadding";
        tempElement.textContent = "Groups";
        tempDiv.parentElement.appendChild(tempElement);
    }

    // Show Order design function logic
    function ShowDesign() {
        orderDesignContainer.innerHTML = '';

        if (!selectedOrderDesign) {
            orderDesignContainer.innerHTML = '<h2 class="centerMarginsSelf">No Design Found</h2>';
            return;
        }

        tempElement = document.createElement("img");
        tempElement.id = "orderDesign";
        tempElement.className = "roundedMid shadowed clickable";
        tempElement.style = "max-height: 100%; max-width: 100%; height: unset; width: unset;";
        tempElement.src = "../../Storage/Designs/" + selectedOrderDesign;
        orderDesignContainer.appendChild(tempElement);

        // View order design focus logic
        document.getElementById('orderDesign').addEventListener('click', function() {
            imageBoxImage.src = "../../Storage/Designs/" + selectedOrderDesign;
            imageBox.style.display = 'flex';
        });
    }

    // Show Order Process Function Logic
    function ShowProcess() {
        orderProcessContainer.innerHTML = '';
        let hasFirstProcess = false;

        if (Object.keys(selectedOrderProcess).length == 0) {
            orderProcessContainer.innerHTML = '<h2 class="centerMarginsSelf">No Process Archived</h2>';
            return;
        }

        selectedOrderProcessNames = Object.keys(orderAssignmentMap[selectedOrderID]);

        selectedOrderProcessNames.forEach((processName, index) => {
            if (hasFirstProcess) {
                tempElement = document.createElement('h1');
                tempElement.textContent = '>';
                orderProcessContainer.appendChild(tempElement);
            }

            tempDiv = document.createElement('div');
            tempDiv.className = 'flexMin minHeight bordered darkFadedBG roundedMin centerColumnLayout tinGap clickable processElement';
            tempDiv.dataset.name = processName;
            orderProcessContainer.appendChild(tempDiv);

            tempElement = document.createElement('h3');
            tempElement.textContent = processName;
            tempDiv.appendChild(tempElement);

            tempElement = document.createElement('div');
            tempElement.className = "centerHoriRowLayout tinGap unitHeight"

            const assignees = selectedOrderProcess[processName] || [];

            tempElement.innerHTML = `
                <img src="../../Shared/Img/PeopleIcon.png" alt="People" class="unitHeight">
                <div class="centerHoriRowLayout tinGap">
                    <p>Assigned: ${assignees.length}</p>
                </div>
            `;
            tempDiv.appendChild(tempElement);

            hasFirstProcess = true;
        });

        // Selecting process element show its archived assignees function logic
        document.querySelectorAll('.processElement').forEach(function(elem) {
            elem.addEventListener('click', function() {
                orderProcessAssigneesContainer.innerHTML = '';

                selectedOrderProcess[elem.dataset.name].forEach((assignee) => {
                    tempDiv = document.createElement('div');
                    tempDiv.className = 'noShrink roundedMin centerHoriRowLayout clickable shadowed fixedScreen bordered';
                    orderProcessAssigneesContainer.appendChild(tempDiv);

                    tempElement = document.createElement('b');
                    tempElement.className = 'centerColumnLayout fullHeight skewedXNegBG shadowed capitalFirst darkFadedBG';
                    tempElement.innerHTML = `<span class="regMinPadding">${assignee.assigneeName}</span>`;
                    tempDiv.appendChild(tempElement);

                    tempElement = document.createElement('b');
                    tempElement.className = 'centerText capitalFirst regMinPadding';
                    tempElement.textContent = "Assigned: " + formatDate(assignee.assignedAt);
                    tempDiv.appendChild(tempElement);
                });
            });
        });
    }

    // Selecting on other order reset the assignees container
    function ResetAssignees() {
        orderProcessAssigneesContainer.innerHTML = '<h2 class="centerMarginsSelf">No Process Selected</h2>';
    }
</script>

</html>
