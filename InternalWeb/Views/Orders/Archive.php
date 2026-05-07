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
    <title>Order Archive - Hontoria OMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/StaffPage.css">
    <style>
        .asideLayout>main>section {
            min-width: fit-content !important;
        }

        .asideLayout>main>section>div>:nth-child(2)>* {
            min-height: 255px !important;
        }

        @media (max-height: 650px) {
            .asideLayout>main>section>div>:nth-child(2) {
                overflow-y: scroll;
                padding: 0.3rem !important;
            }
        }

        @media (max-width: 800px) {
            .asideLayout>main>section>div>:nth-child(2) {
                min-width: 500px !important;
            }

            .asideLayout>main>section>div>:nth-child(1) {
                min-width: 210px !important;
            }
        }

        @media (max-width: 500px) {
            .asideLayout>main>section>div>:nth-child(1) {
                min-width: calc(100vw - 3rem) !important;
                max-width: calc(100vw - 3rem) !important;
            }
        }

        @media (max-width: 450px) {
            .asideLayout>main>span>h1 {
                font-size: 1.25rem !important;
            }

            .asideLayout>main>span>h1>img {
                display: block !important;
            }
        }
    </style>
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
                                // Style based on completion status
                                $elemStyle = $order['isCompleted'] ? "yellowBorder yellowTransBG" : "redBorder redTransBG";
                                $idBG = $order['isCompleted'] ? "yellowBG" : "redBG";
                                ?>
                                <div class="tinHeight noShrink roundedMin centerHoriRowLayout clickable shadowed fixedScreen orderElement <?= e($elemStyle) ?>"
                                    data-id="<?= e($order['id']) ?>">
                                    <h3 class="gradientDiagBG flexMid centerColumnLayout fullHeight whiteText skewedXNegBG shadowed capitalFirst outlineText <?= e($idBG) ?>">
                                        <span>Order #<?= e($order['id']) ?></span>
                                    </h3>
                                    <b class="flexMax fullHeight centerColumnLayout whiteText outlineText"><?= e($order['subserviceName']) ?> <?= e($order['serviceName']) ?></b>
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

    // REVIEW: Entire order archive data is exposed to JavaScript. Ensure only authorized users can access this page.
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

    // Show Order details using safe DOM methods (no innerHTML with variables)
    function ShowDetails() {
        // Clear previous content safely
        while (detailsContainer.firstChild) detailsContainer.removeChild(detailsContainer.firstChild);

        // Order header
        var headerBold = document.createElement('b');
        headerBold.appendChild(document.createTextNode('Order #1 '));
        var statusSpan = document.createElement('span');
        statusSpan.textContent = '(' + (selectedOrder.isCompleted == 1 ? 'Completed' : 'Not Completed') + ')';
        statusSpan.className = selectedOrder.isCompleted == 1 ? 'yellowText' : 'redText';
        headerBold.appendChild(statusSpan);
        detailsContainer.appendChild(headerBold);

        // Grid container for details
        var gridDiv = document.createElement('div');
        gridDiv.className = 'gridFlex tinGap';
        detailsContainer.appendChild(gridDiv);

        // Helper to create a detail row
        function addDetail(label, value) {
            var row = document.createElement('div');
            row.className = 'centerHoriRowLayout tinGap marginRightMin';
            var b = document.createElement('b');
            b.appendChild(document.createTextNode(label + ': '));
            var span = document.createElement('span');
            span.className = 'fontWeightNormal';
            span.textContent = value;
            b.appendChild(span);
            row.appendChild(b);
            gridDiv.appendChild(row);
        }

        addDetail('Service', selectedOrder.serviceName + ',');
        addDetail('Subservice', selectedOrder.subserviceName + ',');
        addDetail('Customer', selectedOrder.customerName + ',');
        addDetail('Total Price', '₱' + selectedOrder.priceTotal + ',');
        addDetail('Created At', formatDate(selectedOrder.createdAt) + ',');
        var deadlineText = selectedOrder.deadlineAt == '0000-00-00 00:00:00' ? 'No Deadline' : formatDate(selectedOrder.deadlineAt);
        addDetail('Deadline At', deadlineText + ',');
        addDetail('Archived At', formatDate(selectedOrder.archivedAt));

        // Groups container
        var groupSection = document.createElement('div');
        groupSection.className = 'flexMax bordered roundedMin centerColumnLayout shadowed fixedScreen noSelectHidden';
        detailsContainer.appendChild(groupSection);

        var groupHeader = document.createElement('h4');
        groupHeader.className = 'centerColumnLayout darkBG shadowed whiteText fullWidth';
        groupHeader.textContent = 'Groups';
        groupSection.appendChild(groupHeader);

        var groupListDiv = document.createElement('div');
        groupListDiv.className = 'scrollable fullWidth flexMax gridCenterFlex minGap regMinPadding';
        groupListDiv.id = 'orderGroupsContainer';
        groupSection.appendChild(groupListDiv);

        selectedOrderGroups.forEach(function(group) {
            var groupItem = document.createElement('h5');
            groupItem.className = 'noShrink fitHeight roundedMin centerRowLayout minGap darkTransBG regMinPadding bordered capitalFirst whiteText outlineText shadowed';
            groupItem.textContent = group.description + ' : ' + group.units;
            groupListDiv.appendChild(groupItem);
        });
    }

    // Show Order design
    function ShowDesign() {
        while (orderDesignContainer.firstChild) orderDesignContainer.removeChild(orderDesignContainer.firstChild);

        if (!selectedOrderDesign) {
            var noDesignMsg = document.createElement('h2');
            noDesignMsg.className = 'centerMarginsSelf';
            noDesignMsg.textContent = 'No Design Found';
            orderDesignContainer.appendChild(noDesignMsg);
            return;
        }

        var img = document.createElement('img');
        img.id = 'orderDesign';
        img.className = 'roundedMid shadowed clickable';
        img.style.maxHeight = '100%';
        img.style.maxWidth = '100%';
        img.style.height = 'unset';
        img.style.width = 'unset';
        img.src = '../../Storage/Designs/' + selectedOrderDesign;
        orderDesignContainer.appendChild(img);

        // View order design focus logic
        img.addEventListener('click', function() {
            imageBoxImage.src = '../../Storage/Designs/' + selectedOrderDesign;
            imageBox.style.display = 'flex';
        });
    }

    // Show Order Process
    function ShowProcess() {
        while (orderProcessContainer.firstChild) orderProcessContainer.removeChild(orderProcessContainer.firstChild);
        var hasFirstProcess = false;

        if (Object.keys(selectedOrderProcess).length == 0) {
            var msg = document.createElement('h2');
            msg.className = 'centerMarginsSelf';
            msg.textContent = 'No Process Archived';
            orderProcessContainer.appendChild(msg);
            return;
        }

        selectedOrderProcessNames = Object.keys(orderAssignmentMap[selectedOrderID]);

        selectedOrderProcessNames.forEach(function(processName, index) {
            if (hasFirstProcess) {
                var arrow = document.createElement('h1');
                arrow.textContent = '>';
                orderProcessContainer.appendChild(arrow);
            }

            var procDiv = document.createElement('div');
            procDiv.className = 'flexMin minHeight bordered darkFadedBG roundedMin centerColumnLayout tinGap clickable processElement';
            procDiv.dataset.name = processName;
            orderProcessContainer.appendChild(procDiv);

            var procNameH3 = document.createElement('h3');
            procNameH3.className = 'whiteText outlineText';
            procNameH3.textContent = processName;
            procDiv.appendChild(procNameH3);

            var assigneeInfo = document.createElement('div');
            assigneeInfo.className = 'centerHoriRowLayout tinGap unitHeight';
            procDiv.appendChild(assigneeInfo);

            // People icon
            var peopleIcon = document.createElement('img');
            peopleIcon.src = '../../Shared/Img/PeopleIcon.png';
            peopleIcon.alt = 'People';
            peopleIcon.className = 'unitHeight';
            assigneeInfo.appendChild(peopleIcon);

            var assigneeCountDiv = document.createElement('div');
            assigneeCountDiv.className = 'centerHoriRowLayout tinGap';
            assigneeInfo.appendChild(assigneeCountDiv);

            var p = document.createElement('p');
            p.textContent = 'Assigned: ' + (selectedOrderProcess[processName] || []).length;
            assigneeCountDiv.appendChild(p);

            hasFirstProcess = true;
        });

        // Selecting process element shows its archived assignees
        document.querySelectorAll('.processElement').forEach(function(elem) {
            elem.addEventListener('click', function() {
                while (orderProcessAssigneesContainer.firstChild) orderProcessAssigneesContainer.removeChild(orderProcessAssigneesContainer.firstChild);

                var assignees = selectedOrderProcess[elem.dataset.name] || [];
                assignees.forEach(function(assignee) {
                    var assigneeDiv = document.createElement('div');
                    assigneeDiv.className = 'noShrink roundedMin centerHoriRowLayout clickable shadowed fixedScreen bordered';
                    orderProcessAssigneesContainer.appendChild(assigneeDiv);

                    var nameBold = document.createElement('b');
                    nameBold.className = 'centerColumnLayout fullHeight skewedXNegBG shadowed capitalFirst darkFadedBG whiteText outlineText';
                    assigneeDiv.appendChild(nameBold);

                    var nameSpan = document.createElement('span');
                    nameSpan.className = 'regMinPadding';
                    nameSpan.textContent = assignee.assigneeName;
                    nameBold.appendChild(nameSpan);

                    var dateBold = document.createElement('b');
                    dateBold.className = 'centerText capitalFirst regMinPadding';
                    dateBold.textContent = 'Assigned: ' + formatDate(assignee.assignedAt);
                    assigneeDiv.appendChild(dateBold);
                });
            });
        });
    }

    // Selecting on other order reset the assignees container
    function ResetAssignees() {
        while (orderProcessAssigneesContainer.firstChild) orderProcessAssigneesContainer.removeChild(orderProcessAssigneesContainer.firstChild);
        var msg = document.createElement('h2');
        msg.className = 'centerMarginsSelf';
        msg.textContent = 'No Process Selected';
        orderProcessAssigneesContainer.appendChild(msg);
    }
</script>

</html>