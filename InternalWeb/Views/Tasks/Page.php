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
    <title>Tasks Panel - Hontoria OMS</title>
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
                min-width: calc(100vw - 3rem);
                max-width: calc(100vw - 3rem);
            }
        }

        @media (max-width: 450px) {
            .asideLayout>main>section>*:nth-child(2)>*:nth-child(2) {
                overflow-y: scroll;
                padding: 0.3rem !important;
            }

            .asideLayout>main>section>*:nth-child(2)>*:nth-child(2)>*:nth-child(1) {
                min-width: 150px !important;
            }

            .asideLayout>main>section>*:nth-child(2)>*:nth-child(2)>*:nth-child(2) {
                min-width: 220px !important;
            }

            .asideLayout>main>h1 {
                font-size: 1.25rem !important;
            }

            .asideLayout>main>h1>img {
                display: block !important;
            }
        }
    </style>
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap relatived">
        <h1 class="titleLogo minGap tinHeight">
            <img src="../../Shared/Img/CheckBoxIcon.png" alt="CheckBox"> Tasks Panel
        </h1>
        <?php include("../Views/.Components/MessageBox.php"); ?>
        <?php
        // Hides the available-tasks section if the user lacks self-assign permission
        $hideClass = in_array('canSelfAssignToTasks', $_SESSION['permissions']) ? '' : 'hidden';
        ?>
        <section class="rowLayout flexMax midGap">
            <!-- Available tasks to self-assign (hidden if no permission) -->
            <section class="flexMid roundedMid centerColumnLayout <?= e($hideClass) ?>">
                <div class="columnLayout minGap box roundedMid fullHeight fullWidth">
                    <h3>Available Tasks</h3>
                    <div class="gridFlex minGrids minGap scrollable flexMax noFlexBasis noMinHeight contentFlexStart regTinPadding">
                        <?php foreach ($availableTasks as $task): ?>
                            <?php if (!$task['isAssigned'] && !$task['isFull']): ?>
                                <div class="darkFadedBG centerHoriColumnLayout regMidPadding roundedMin shadowed bordered">
                                    <h3 class="centerHoriRowLayout whiteText outlineText">
                                        <span class="flexMax"><?= e($task['processName']) ?> Order #<?= e($task['orderID']) ?></span>
                                    </h3>
                                    <!-- POST form to assign self to task, includes CSRF token -->
                                    <form method="POST" action="index.php?page=tasks&action=assignToTask" class="norEastAbsolute closeCorner">
                                        <?php echo CsrfM::getTokenField(); ?>
                                        <input type="hidden" name="orderProcessID" value="<?= e($task['id']) ?>">
                                        <input type="submit" name="submit" value="Assign" class="importantInput shadowed noBorder">
                                    </form>
                                    <h5>Service: <?= e($task['subserviceName']) ?> <?= e($task['serviceName']) ?></h5>
                                    <h5>Customer: <?= e($task['customerName']) ?></h5>
                                    <h5>Due In: <span class="dueInText" data-due-date="<?= e($task['deadlineAt']) ?>">4d 2h (March 31, 2026)</span></h5>
                                    <div class="rowLayout minGap">
                                        <h5 class="centerHoriRowLayout tinGap">
                                            Assigned: <?= e($task['assignedNum']) ?>/<?= e($task['maxAssign']) ?>
                                            <img src="../../Shared/Img/PersonIcon.png" alt="Person" class="unitHeight">
                                        </h5>
                                        <h5 class="centerHoriRowLayout tinGap">
                                            Required: <?= e($task['minAssign']) ?>
                                            <img src="../../Shared/Img/PersonIcon.png" alt="Person" class="unitHeight">
                                        </h5>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="gradientBorderDiag"></div>
            </section>
            <section class="columnLayout midGap flexMid">
                <section class="box centerColumnLayout roundedMid minGap flexMax">
                    <div class="fullDimensions columnLayout minGap">
                        <h3>Assigned Tasks</h3>
                        <div class="gridFlex minGrids minGap scrollable flexMax noFlexBasis noMinHeight contentFlexStart regTinPadding">
                            <?php foreach ($availableTasks as $task): ?>
                                <?php if ($task['isAssigned'] && $task['minAssign'] <= $task['assignedNum']): ?>
                                    <?php
                                    $statusClass = $task['taskStatus'] === 'pending' ? "redTransBG redBorder" : ($task['taskStatus'] === 'complete' ?
                                        "greenTransBG greenBorder" : "yellowTransBG yellowBorder");
                                    // Replace the Messenger domain in the GC link for compatibility
                                    $gcLink = str_replace('https://m.me', 'https://messenger.com', $task['messengerGCLink']);
                                    ?>
                                    <div class="<?= e($statusClass) ?> columnLayout tinGap regMidPadding roundedMin shadowed assignedTaskElement clickable"
                                        data-id="<?= e($task['id']) ?>" data-order-id="<?= e($task['orderID']) ?>" data-status="<?= e($task['taskStatus']) ?>"
                                        data-design-access="<?= e($task['designAccess']) ?>" data-variable-list-access="<?= e($task['variableListAccess']) ?>"
                                        data-design-use="<?= e($task['hasDesign']) ?>" data-variable-list-use="<?= e($task['hasVariableList']) ?>">
                                        <div class="centerHoriRowLayout minGap">
                                            <div class="flexMax">
                                                <h3 class="whiteText outlineText"><?= e($task['processName']) ?> Order #<?= e($task['orderID']) ?></h3>
                                                <h5>Service: <?= e($task['subserviceName']) ?> <?= e($task['serviceName']) ?></h5>
                                                <h5>Customer: <?= e($task['customerName']) ?></h5>
                                                <h5>Due In: <span class="dueInText" data-due-date="<?= e($task['deadlineAt']) ?>">4d 2h (March 31, 2026)</span></h5>
                                                <div class="rowLayout minGap">
                                                    <h5 class="centerHoriRowLayout tinGap">
                                                        Assigned: <?= e($task['assignedNum']) ?>/<?= e($task['maxAssign']) ?>
                                                        <img src="../../Shared/Img/PersonIcon.png" alt="Person" class="unitHeight">
                                                    </h5>
                                                    <h5 class="centerHoriRowLayout tinGap">
                                                        Required: <?= e($task['minAssign']) ?>
                                                        <img src="../../Shared/Img/PersonIcon.png" alt="Person" class="unitHeight">
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($task['hasGCAccess'] == 1): ?>
                                            <a href="<?= e($gcLink) ?>" target="_blank"
                                                class="duoHeight squareSize regMinPadding blueBG roundedMin centerColumnLayout circle shadowed norEastAbsolute closeCorner">
                                                <img src="../../Shared/Img/MessengerIcon.png" alt="Messenger" class="invertColors">
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
                <div class="rowLayout roundedMid midGap flexMid noFlexBasis noMinHeight">
                    <section class="box centerColumnLayout tinGap flexMid roundedMid">
                        <div class="columnLayout tinGap fullDimensions">
                            <h3>Assigned to Task:</h3>
                            <div class="columnLayout scrollable flexMax noFlexBasis noMinHeight minGap regTinPadding" id="assigneesContainer">
                                <b class="centerMarginsSelf">No Task Selected</b>
                            </div>
                        </div>
                        <div class="gradientBorderDiag"></div>
                    </section>
                    <section class="box centerColumnLayout tinGap flexMid roundedMid">
                        <div class="columnLayout minGap fullDimensions">
                            <div class="centerHoriRowLayout">
                                <h3 class="flexMax">Objectives</h3>
                                <h4 class="midHoriPadding shadowed redBG roundedMin emphasizedText hidden outlineText whiteText" id="statusButton">Pending</h4>
                            </div>
                            <b class="centerMarginsSelf noSelectText">No Task Selected</b>
                            <div class="centerHoriRowLayout minGap duoHeight noSelectHidden hidden">
                                <div class="bordered flexMin fullHeight roundedMin centerHoriRowLayout shadowed fixedScreen clickable" id="designButton">
                                    <h4 class="flexMax centerText">Unset</h4>
                                    <div class="squareSize fullHeight centerColumnLayout darkBG shadowed">
                                        <img src="../../Shared/Img/PhotoIcon.png" alt="Photo" class="invertColors">
                                    </div>
                                </div>
                                <div class="redBorder flexMin fullHeight roundedMin centerHoriRowLayout shadowed fixedScreen clickable" id="variableListButton">
                                    <h4 class="flexMax centerText">Unapproved</h4>
                                    <div class="squareSize fullHeight centerColumnLayout redBG shadowed">
                                        <img src="../../Shared/Img/BarsIcon.png" alt="Bars" class="invertColors">
                                    </div>
                                </div>
                            </div>
                            <div class="flexMax bordered roundedMin centerColumnLayout shadowed fixedScreen noSelectHidden hidden">
                                <h4 class="centerColumnLayout darkBG shadowed whiteText fullWidth">Groups</h4>
                                <div class="scrollable fullWidth flexMax gridCenterFlex minGap regMinPadding" id="orderGroupsContainer"></div>
                            </div>
                        </div>
                        <div class="gradientBorderDiag"></div>
                    </section>
                </div>
            </section>
        </section>
        <?php if (!empty($miscTaskAssigned)): ?>
            <!-- Overlay blocking task interaction while a misc task is assigned -->
            <div class="fullDimensions darkTransBG noMargin centerColumnLayout norWestAbsolute edgeCorner">
                <div class="centerColumnLayout roundedMid maxWidth">
                    <div class="box centerColumnLayout roundedMid fullWidth fullHeight minGap midZ">
                        <h1>You cannot do other tasks</h1>
                        <h3 class="centerText">
                            You are currently assigned to a miscellaneous task described as
                            "<?= e($miscTaskAssigned['description']) ?>".
                        </h3>
                    </div>
                    <div class="gradientBorderDiag minZ"></div>
                </div>
            </div>
        <?php endif; ?>
    </main>
    <?php include("../Views/.Components/ConfirmationBox.php"); ?>
    <?php include("../Views/.Components/ImageBox.php"); ?>
</body>
<script src="../.JS/ConfirmationBox.js"></script>
<script src="../.JS/CsrfHandler.js"></script>
<script src="../.JS/ImageBox.js"></script>
<script src="../.JS/TimeHelpers.js"></script>
<script src="../.JS/MiscHelpers.js"></script>
<script>
    const assignedTaskElement = document.querySelectorAll('.assignedTaskElement');
    const selectedIDInput = document.querySelectorAll('.selectedIDInput');
    const assigneesContainer = document.getElementById('assigneesContainer');
    const statusButton = document.getElementById('statusButton');
    const designButton = document.getElementById('designButton');
    const variableListButton = document.getElementById('variableListButton');
    const orderGroupsContainer = document.getElementById('orderGroupsContainer');

    // REVIEW: Full assignee/design/variable‑list data is exposed to JavaScript.
    // Ensure only authorized users can access this page.
    const assigneeList = <?php echo json_encode($assigneeList); ?>;
    const designList = <?php echo json_encode($designList); ?>;
    const variableListMap = <?php echo json_encode($variableListMap); ?>;
    const orderGroupList = <?php echo json_encode($orderGroupList); ?>;

    const assigneeMap = {};

    assigneeList.forEach(item => {
        if (!assigneeMap[item.orderProcessID]) {
            assigneeMap[item.orderProcessID] = [];
        }

        assigneeMap[item.orderProcessID].push({
            name: item.firstName + " " + (item.middleName?.[0] + "." || "") + " " + item.lastName,
            lastName: item.lastName,
            status: item.status
        });
    });

    const designMap = {};

    designList.forEach(item => {
        designMap[item.orderID] = {
            image: item.image,
            approved: item.approved
        };
    });

    const orderGroupMap = {};

    orderGroupList.forEach(item => {
        if (!orderGroupMap[item.orderID]) {
            orderGroupMap[item.orderID] = [];
        }

        orderGroupMap[item.orderID].push({
            description: item.description,
            quantity: item.quantity
        });
    });

    document.addEventListener("DOMContentLoaded", () => {
        confirmationCancel.value = "No Cancel";
    });

    const selectedID = document.createElement("input");
    selectedID.type = "hidden";
    selectedID.name = "selectedID";
    confirmationForm.appendChild(selectedID);

    let tempDiv;
    let tempElement;

    // Due time calculation – safe (textContent)
    document.querySelectorAll('.dueInText').forEach(function(elem) {
        elem.textContent = elem.dataset.dueDate == '0000-00-00 00:00:00' ? "No due date" : getDueTime(elem.dataset.dueDate) + " (" + formatDate(elem.dataset.dueDate) + ")";
    });

    // Reactive clickable process task data script
    let selectedTaskAssignees;
    let selectedTaskDesign;
    let selectedTaskDesignApproval;
    let selectedTaskGroups;
    let selectedTaskDesignAccess = '';
    let selectedTaskVariableListAccess = '';

    document.addEventListener('DOMContentLoaded', function() {
        assignedTaskElement.forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedTaskAssignees = [...(assigneeMap[elem.dataset.id] || [])];
                selectedTaskGroups = [...(orderGroupMap[elem.dataset.orderId] || [])];

                if (designMap[elem.dataset.orderId]) {
                    selectedTaskDesign = designMap[elem.dataset.orderId].image;
                    selectedTaskDesignApproval = designMap[elem.dataset.orderId].approved;
                } else {
                    selectedTaskDesign = '';
                    selectedTaskDesignApproval = -1;
                }

                assigneesContainer.innerHTML = ''; // safe clear
                selectedTaskAssignees.forEach(function(assignee) {
                    tempElement = document.createElement("h5");
                    tempElement.textContent = assignee.name;
                    tempElement.className = "centerText regMinPadding shadowed roundedTin whiteText outlineText";

                    switch (assignee.status) {
                        case 'pending':
                            tempElement.classList.add("redTransBG", "redBorder");
                            break;
                        case 'partially complete':
                            tempElement.classList.add("yellowTransBG", "yellowBorder");
                            break;
                        case 'complete':
                            tempElement.classList.add("greenTransBG", "greenBorder");
                            break;
                    }

                    assigneesContainer.appendChild(tempElement);
                });

                selectedID.value = elem.dataset.id;
                designButton.dataset.id = elem.dataset.orderId;
                statusButton.dataset.status = elem.dataset.status;

                switch (elem.dataset.status) {
                    case 'pending':
                        statusButton.textContent = "Pending";
                        statusButton.classList.add("redBG");
                        statusButton.classList.remove("yellowBG", "greenBG");
                        break;
                    case 'partially complete':
                        statusButton.textContent = "Partially Complete";
                        statusButton.classList.add("yellowBG");
                        statusButton.classList.remove("redBG", "greenBG");
                        break;
                    case 'complete':
                        statusButton.textContent = "Complete";
                        statusButton.classList.add("greenBG");
                        statusButton.classList.remove("redBG", "yellowBG");
                        break;
                }

                // ---- Early flags: does this task actually require a design / variable list? ----
                const designUse = elem.dataset.designUse !== "0"; // true if design is needed
                const variableListUse = elem.dataset.variableListUse !== "0"; // true if list is needed
                selectedTaskDesignAccess = elem.dataset.designAccess;
                selectedTaskVariableListAccess = elem.dataset.variableListAccess;

                // ----- Design button styling and visibility -----
                if (designUse) {
                    if (selectedTaskDesignApproval == 0) {
                        designButton.classList.add('redBorder', 'redText');
                        designButton.classList.remove('bordered', 'greenBorder', 'greenText');
                        designButton.querySelector("div").classList.add('redBG');
                        designButton.querySelector("div").classList.remove('darkBG', 'greenBG');
                        designButton.querySelector("h4").textContent = 'Unapproved';
                    } else if (selectedTaskDesignApproval == 1) {
                        designButton.classList.add('greenBorder', 'greenText');
                        designButton.classList.remove('bordered', 'redBorder', 'redText');
                        designButton.querySelector("div").classList.add('greenBG');
                        designButton.querySelector("div").classList.remove('darkBG', 'redBG');
                        designButton.querySelector("h4").textContent = 'Approved';
                    } else {
                        designButton.classList.add('bordered');
                        designButton.classList.remove('redBorder', 'redText', 'greenBorder', 'greenText');
                        designButton.querySelector("div").classList.add('darkBG');
                        designButton.querySelector("div").classList.remove('redBG', 'greenBG');
                        designButton.querySelector("h4").textContent = 'Unset';
                    }

                    if (elem.dataset.designAccess == "view & update") {
                        designButton.classList.remove('hidden');
                    } else if (elem.dataset.designAccess == "view only") {
                        designButton.classList.remove('hidden');
                        designButton.querySelector("h4").textContent = 'Design';
                    } else {
                        designButton.classList.add('hidden');
                    }
                } else {
                    // No design required — hide completely
                    designButton.classList.add('hidden');
                }

                // ----- Variable list button styling and visibility -----
                if (variableListUse) {
                    const listData = variableListMap[elem.dataset.orderId];
                    console.log(listData);
                    let listStatus = 'incomplete'; // default when data is missing

                    if (listData && listData.list && listData.columns && listData.values) {
                        const groupColumn = listData.columns.find(c => c.columnName.toLowerCase() === 'group');
                        if (groupColumn) {
                            const otherColumns = listData.columns.filter(c => c.id != groupColumn.id);
                            const rowNumbers = [...new Set(listData.values.map(v => v.rowNumber))];

                            let hasEmptyCell = false;
                            for (const col of otherColumns) {
                                for (const row of rowNumbers) {
                                    const cell = listData.values.find(v => v.rowNumber == row && v.columnID == col.id);
                                    if (!cell || !cell.valueText || cell.valueText.trim() === '') {
                                        hasEmptyCell = true;
                                        break;
                                    }
                                }
                                if (hasEmptyCell) break;
                            }

                            let hasUncheckedRow = false;
                            if (listData.rowChecks) {
                                for (const row of rowNumbers) {
                                    if (!listData.rowChecks[row]) {
                                        hasUncheckedRow = true;
                                        break;
                                    }
                                }
                            }

                            const hasIssue = hasEmptyCell || hasUncheckedRow;
                            const isApproved = listData.list.approved == 1;

                            if (hasIssue && !isApproved) {
                                listStatus = 'incomplete'; // dark
                            } else if (!hasIssue && !isApproved) {
                                listStatus = 'unapproved'; // red
                            } else if (hasIssue && isApproved) {
                                listStatus = 'approved'; // yellow
                            } else if (!hasIssue && isApproved) {
                                listStatus = 'complete'; // green
                            }
                        }
                    }

                    // Store for the status‑button handler later
                    window.selectedTaskListStatus = listStatus;

                    // Apply styling
                    switch (listStatus) {
                        case 'complete':
                            variableListButton.classList.add('greenBorder', 'greenText');
                            variableListButton.classList.remove('bordered', 'redBorder', 'redText');
                            variableListButton.querySelector("div").classList.add('greenBG');
                            variableListButton.querySelector("div").classList.remove('darkBG', 'redBG');
                            variableListButton.querySelector("h4").textContent = 'Complete';
                            break;
                        case 'approved':
                            variableListButton.classList.add('yellowBorder', 'yellowText');
                            variableListButton.classList.remove('bordered', 'redBorder', 'redText', 'greenBorder', 'greenText');
                            variableListButton.querySelector("div").classList.add('yellowBG');
                            variableListButton.querySelector("div").classList.remove('darkBG', 'redBG', 'greenBG');
                            variableListButton.querySelector("h4").textContent = 'Approved';
                            break;
                        case 'unapproved':
                            variableListButton.classList.add('redBorder', 'redText');
                            variableListButton.classList.remove('bordered', 'greenBorder', 'greenText');
                            variableListButton.querySelector("div").classList.add('redBG');
                            variableListButton.querySelector("div").classList.remove('darkBG', 'greenBG');
                            variableListButton.querySelector("h4").textContent = 'Unapproved';
                            break;
                        case 'incomplete':
                            variableListButton.classList.add('bordered');
                            variableListButton.classList.remove('greenBorder', 'greenText', 'redBorder', 'redText');
                            variableListButton.querySelector("div").classList.add('darkBG');
                            variableListButton.querySelector("div").classList.remove('greenBG', 'redBG');
                            variableListButton.querySelector("h4").textContent = 'Incomplete';
                            break;
                    }

                    if (elem.dataset.variableListAccess == "view & update") {
                        variableListButton.classList.remove('hidden');
                    } else if (elem.dataset.variableListAccess == "view only") {
                        variableListButton.classList.remove('hidden');
                        variableListButton.querySelector("h4").textContent = 'Variable List';
                    } else {
                        variableListButton.classList.add('hidden');
                    }
                } else {
                    variableListButton.classList.add('hidden');
                    window.selectedTaskListStatus = null;
                }

                // ----- Status button enable/disable based on approvals -----
                const designRequired = designUse && elem.dataset.designAccess == "view & update";
                const variableListRequired = variableListUse && elem.dataset.variableListAccess == "view & update";
                let canChangeStatus = true;

                if (designRequired && selectedTaskDesignApproval != 1) {
                    canChangeStatus = false;
                }
                if (variableListRequired) {
                    const listStatus = window.selectedTaskListStatus;
                    if (!listStatus || (listStatus !== 'approved' && listStatus !== 'complete')) {
                        canChangeStatus = false;
                    }
                }

                if (canChangeStatus) {
                    statusButton.classList.remove('unclickable', 'faded');
                } else {
                    statusButton.classList.add('unclickable', 'faded');
                }

                // ----- Final UI adjustments -----
                document.querySelectorAll('.noSelectText').forEach(function(elem) {
                    elem.remove();
                });

                document.querySelectorAll('.noSelectHidden').forEach(function(elem) {
                    elem.classList.remove('hidden');
                });

                statusButton.classList.remove('hidden');

                showOrderGroups();
            });
        });
    });

    // Process Task status logic functionality
    statusButton.addEventListener('click', function() {
        const oldInputs = confirmationForm.querySelectorAll('.tempElement');
        oldInputs.forEach(el => el.remove());

        confirmationForm.action = "index.php?page=tasks&action=updateTaskStatus";

        const currentStatus = statusButton.dataset.status;
        const statusOrder = ['pending', 'partially complete', 'complete'];
        const currentIndex = statusOrder.indexOf(currentStatus);

        // ---- "pending" button ----
        tempElement = document.createElement("input");
        tempElement.type = "submit";
        tempElement.name = "taskStatus";
        tempElement.className = "tempElement tinHeight redTransBG redBorder centerColumnLayout capitalFirst emphasizedText shadowed";
        tempElement.value = "pending";
        // Hide if current status is pending or higher
        if (currentIndex >= 0) tempElement.classList.add("hidden");
        confirmationForm.appendChild(tempElement);

        // ---- "partially complete" button ----
        tempElement = document.createElement("input");
        tempElement.type = "submit";
        tempElement.name = "taskStatus";
        tempElement.className = "tempElement tinHeight yellowTransBG yellowBorder centerColumnLayout capitalFirst emphasizedText shadowed";
        tempElement.value = "partially complete";
        // Hide if current status is partially complete or higher
        if (currentIndex >= 1) tempElement.classList.add("hidden");
        confirmationForm.appendChild(tempElement);

        // ---- "complete" button ----
        const listRequired = window.selectedTaskListStatus !== undefined && window.selectedTaskListStatus !== null;
        const listNotComplete = listRequired && window.selectedTaskListStatus !== 'complete';

        tempElement = document.createElement("input");
        tempElement.type = "submit";
        tempElement.name = "taskStatus";
        tempElement.className = "tempElement tinHeight greenTransBG greenBorder centerColumnLayout capitalFirst emphasizedText shadowed";
        tempElement.value = "complete";
        // Hide if already complete, or if variable list required but not complete
        if (currentIndex >= 2 || listNotComplete) tempElement.classList.add("hidden");
        confirmationForm.appendChild(tempElement);

        // Safe: hardcoded title/text
        confirmationTitle.textContent = "Update Task Status";
        confirmationText.textContent = 'Click on the status you want your task to update to.';
        confirmationSubmit.classList.add("hidden");

        confirmation.style.display = 'flex';
    });

    // Design Box logic function
    let uploadedImage;

    designButton.addEventListener('click', function() {
        if (selectedTaskDesignAccess === 'view only') {
            // Just show the image in the image box
            if (selectedTaskDesign) {
                imageBoxImage.src = selectedTaskDesign;
                imageBox.style.display = 'flex';
            }
            return;
        }

        // ----- view & update (existing upload code) -----
        confirmationForm.action = "index.php?page=tasks&action=uploadDesign"

        tempDiv = document.createElement("div");
        tempDiv.className = "tempElement centerHoriRowLayout minGap";
        confirmationForm.appendChild(tempDiv);

        tempElement = document.createElement("b");
        tempElement.textContent = "Upload File:";
        tempDiv.appendChild(tempElement);

        tempElement = document.createElement("input");
        tempElement.type = "file";
        tempElement.name = "designImage";
        tempElement.accept = "image/*";
        tempElement.required = "true";
        tempElement.className = "flexMax";
        tempDiv.appendChild(tempElement);

        tempDiv = document.createElement("div");
        tempDiv.className = "fullWidth tempElement hidden scrollable halfScreenMaxHeight";
        confirmationForm.appendChild(tempDiv);

        uploadedImage = document.createElement("img");
        uploadedImage.className = "fullWidth";
        uploadedImage.id = "imageUploaded";
        tempDiv.appendChild(uploadedImage);

        // Safe: hardcoded titles
        confirmationTitle.textContent = "Upload Design Image";
        confirmationText.textContent = "Please upload a photo for this Order's design.";
        confirmationSubmit.value = "Upload";
        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");

        selectedID.value = designButton.dataset.id;
        confirmationForm.enctype = "multipart/form-data";
        confirmation.style.display = 'flex';

        if (selectedTaskDesign) {
            uploadedImage.src = selectedTaskDesign;
            tempDiv.classList.remove("hidden");
        }

        tempElement.addEventListener('change', () => {
            const files = tempElement.files;
            if (files.length === 0) return;
            if (files.length > 1) {
                alert("Only one file allowed");
                tempElement.value = "";
                return;
            }
            const design = files[0];
            if (!design.type.startsWith("image/")) {
                alert("Only images are allowed");
                tempElement.value = "";
                return;
            }
            const file = files[0];
            if (file) {
                uploadedImage.src = URL.createObjectURL(file);
                tempDiv.classList.remove("hidden");
            }
        });
    });

    // Show order groups function logic – safe (textContent)
    function showOrderGroups() {
        orderGroupsContainer.innerHTML = ''; // safe clear

        selectedTaskGroups.forEach(group => {
            tempElement = document.createElement("h5");
            tempElement.className = "noShrink fitHeight roundedMin centerRowLayout minGap darkTransBG regMinPadding bordered capitalFirst whiteText outlineText shadowed";
            tempElement.textContent = group.description + ": " + group.quantity;
            orderGroupsContainer.appendChild(tempElement);
        });
    }

    // Variable list function logic
    variableListButton.addEventListener('click', function() {
        const viewOnly = selectedTaskVariableListAccess === 'view only';

        if (!viewOnly) {
            confirmationContent.classList.remove('maxWidth');
        }

        confirmationForm.action = "index.php?page=tasks&action=updateVariableList";
        selectedID.value = designButton.dataset.id;
        confirmationTitle.textContent = viewOnly ? "View Variable List" : "Edit Variable List";
        confirmationText.textContent = viewOnly ? "" : "Add or remove columns / rows, then save.";
        confirmationSubmit.value = "Save List";
        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");
        if (viewOnly) confirmationSubmit.classList.add("hidden");

        const container = document.createElement("div");
        container.className = "tempElement columnLayout minGap";

        // ---- Add Column button & input (only if editable) ----
        if (!viewOnly) {
            tempDiv = document.createElement("div");
            tempDiv.className = "rowLayout minGap";
            container.appendChild(tempDiv);

            tempElement = document.createElement("button");
            tempElement.type = "button";
            // safe: hardcoded HTML
            tempElement.innerHTML = "<h4>Add Column</h4>";
            tempElement.className = "darkBG whiteText bordered roundedTin minPadding shadowed";
            tempElement.id = "addColumnButton";
            tempDiv.appendChild(tempElement);

            tempElement = document.createElement("input");
            tempElement.type = "text";
            tempElement.placeholder = "Column Name (Unique)";
            tempElement.className = "bordered roundedTin minPadding shadowed flexMid";
            tempElement.id = "columnNameInput";
            tempDiv.appendChild(tempElement);
        }

        const orderID = designButton.dataset.id;
        const raw = variableListMap[orderID] || {
            columns: [],
            values: [],
            rowChecks: {}
        };

        const workingColumns = raw.columns.map(col => ({
            ...col
        }));
        const workingValues = raw.values.map(v => ({
            ...v
        }));

        // Row checks – in view‑only mode, pretend all rows are checked
        const workingRowChecks = {};
        if (viewOnly) {
            const allNums = [...new Set(raw.values.map(v => v.rowNumber))];
            allNums.forEach(rn => {
                workingRowChecks[rn] = true;
            });
        } else if (raw.rowChecks) {
            for (const [rowNum, checked] of Object.entries(raw.rowChecks)) {
                workingRowChecks[parseInt(rowNum)] = !!checked;
            }
        }

        function isColumnIncomplete(col, allRowNums) {
            if (viewOnly) return false; // all columns are "complete" visually
            for (const rn of allRowNums) {
                const cell = findCell(rn, col);
                if (!cell || !cell.valueText || cell.valueText.trim() === '') {
                    return true;
                }
            }
            return false;
        }

        function findCell(rowNum, col) {
            if (col.id) {
                return workingValues.find(v => v.rowNumber == rowNum && v.columnID == col.id);
            } else {
                return workingValues.find(v => v.rowNumber == rowNum && v.tempKey === col.tempKey);
            }
        }

        function renderGrid() {
            const oldScrollDiv = container.querySelector(".scrollable");
            let savedScrollLeft = 0,
                savedScrollTop = 0;
            if (oldScrollDiv) {
                savedScrollLeft = oldScrollDiv.scrollLeft;
                savedScrollTop = oldScrollDiv.scrollTop;
                oldScrollDiv.remove();
            }

            tempDiv = document.createElement("div");
            tempDiv.className = "rowLayout tinGap scrollable scrollableX majorScreenMaxWidth halfScreenMaxHeight regTinPadding contentFlexEven";

            const allRowNums = [...new Set(workingValues.map(v => v.rowNumber))].sort((a, b) => a - b);
            const allChecked = viewOnly || allRowNums.every(rn => workingRowChecks[rn] === true);
            const columnHeaders = [];

            workingColumns.forEach((col, index) => {
                const colIncomplete = (index > 0) ? isColumnIncomplete(col, allRowNums) : false;

                const column = document.createElement("table");
                column.className = "unitWidth tinGap";

                let headerClass;
                if (index === 0) {
                    headerClass = "darkGrayBG";
                } else {
                    headerClass = colIncomplete ? "lightRedBG redBorder" : "lightYellowBG yellowBorder";
                }

                tempElement = document.createElement("th");
                tempElement.className = `${headerClass} bordered shadowed whiteText roundedTin noWrapText midHoriPadding duoHeight stickied topPos`;

                // Build header safely – no innerHTML with user data
                const headerDiv = document.createElement("div");
                headerDiv.className = "centerRowLayout tinGap";
                const colNameH5 = document.createElement("h5");
                colNameH5.className = 'outlineText capitalFirst';
                colNameH5.textContent = col.columnName; // safe: textContent
                headerDiv.appendChild(colNameH5);

                if (!viewOnly && index !== 0) {
                    const xLink = document.createElement("a");
                    xLink.className = "squareSize unitHeight columnRemove";
                    // safe: hardcoded icon
                    xLink.innerHTML = '<img src="../../Shared/Img/XIcon.png" alt="X">';
                    headerDiv.appendChild(xLink);
                }
                tempElement.appendChild(headerDiv);

                column.createTHead().insertRow().appendChild(tempElement);
                columnHeaders[index] = tempElement;

                if (!viewOnly && index !== 0) {
                    const xButton = tempElement.querySelector('.columnRemove');
                    if (xButton) {
                        xButton.addEventListener('click', () => {
                            for (let i = workingValues.length - 1; i >= 0; i--) {
                                const v = workingValues[i];
                                const match = col.id ? (v.columnID === col.id) : (v.tempKey === col.tempKey);
                                if (match) workingValues.splice(i, 1);
                            }
                            workingColumns.splice(index, 1);
                            renderGrid();
                        });
                    }
                }

                const tbody = column.createTBody();

                allRowNums.forEach(rowNum => {
                    let cell = findCell(rowNum, col);
                    const rowCell = tbody.insertRow().insertCell();
                    const rowChecked = workingRowChecks[rowNum] === true;

                    if (index === 0) {
                        const rowBgClass = rowChecked ? "lightYellowBG" : "lightRedBG";
                        const rowBorderClass = rowChecked ? "yellowBorder" : "redBorder";

                        tempElement = document.createElement("div");
                        tempElement.className = `bordered shadowed roundedTin marginTopMin regMinPadding duoHeight centerColumnLayout ${rowBgClass} ${rowBorderClass}`;
                        // Safe: use textContent for cell value
                        const valueH5 = document.createElement("h5");
                        valueH5.className = "capitalFirst whiteText centerText outlineText";
                        valueH5.textContent = cell ? cell.valueText : '';
                        tempElement.appendChild(valueH5);
                    } else {
                        const rowBorderClass = rowChecked ? "yellowBorder" : "redBorder";

                        if (viewOnly) {
                            // Read‑only display, not an input
                            tempElement = document.createElement("div");
                            tempElement.className = `bordered shadowed roundedTin marginTopMin regMinPadding duoHeight centerColumnLayout lightYellowBG ${rowBorderClass}`;
                            const valueH5 = document.createElement("h5");
                            valueH5.className = "capitalFirst whiteText centerText outlineText";
                            valueH5.textContent = cell ? cell.valueText : '';
                            tempElement.appendChild(valueH5);
                        } else {
                            tempElement = document.createElement("input");
                            tempElement.type = "text";
                            tempElement.value = cell ? cell.valueText : '';
                            tempElement.className = `fullWidth bordered shadowed roundedTin marginTopMin capitalFirst duoHeight ${rowBorderClass}`;
                            tempElement.dataset.row = rowNum;
                            tempElement.dataset.col = index;

                            tempElement.addEventListener('input', function() {
                                const newVal = this.value;
                                if (!cell) {
                                    cell = {
                                        id: null,
                                        rowNumber: rowNum,
                                        columnID: col.id || null,
                                        tempKey: col.tempKey || null,
                                        valueText: newVal
                                    };
                                    workingValues.push(cell);
                                } else {
                                    cell.valueText = newVal;
                                }
                                if (index > 0) {
                                    const nowIncomplete = isColumnIncomplete(col, allRowNums);
                                    const header = columnHeaders[index];
                                    if (header) {
                                        header.classList.remove('lightRedBG', 'redBorder', 'lightYellowBG', 'yellowBorder');
                                        header.classList.add(
                                            nowIncomplete ? 'lightRedBG' : 'lightYellowBG',
                                            nowIncomplete ? 'redBorder' : 'yellowBorder'
                                        );
                                    }
                                }
                            });
                        }
                    }

                    rowCell.appendChild(tempElement);
                });

                tempDiv.appendChild(column);
            });

            // ---------- Check column ----------
            // Only show the check column if viewOnly is false
            if (!viewOnly) {
                const checkColumn = document.createElement("table");
                checkColumn.className = "unitWidth tinGap stickied rightPos";

                const headerCheckClass = allRowNums.length > 0 && allChecked ?
                    "lightYellowBG yellowBorder" :
                    "lightRedBG redBorder";

                tempElement = document.createElement("th");
                tempElement.className = `${headerCheckClass} bordered shadowed whiteText roundedTin noWrapText midHoriPadding duoHeight stickied topPos`;
                const masterCheckDiv = document.createElement("div");
                masterCheckDiv.className = "centerHoriRowLayout tinGap";
                const masterCheckbox = document.createElement("input");
                masterCheckbox.type = "checkbox";
                masterCheckbox.checked = allRowNums.length > 0 && allChecked;
                masterCheckbox.className = "unitHeight squareSize";
                masterCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        allRowNums.forEach(rn => {
                            workingRowChecks[rn] = true;
                        });
                    }
                    renderGrid();
                });
                masterCheckDiv.appendChild(masterCheckbox);
                tempElement.appendChild(masterCheckDiv);
                checkColumn.createTHead().insertRow().appendChild(tempElement);

                const checkBody = checkColumn.createTBody();
                allRowNums.forEach(rowNum => {
                    const rowCell = checkBody.insertRow().insertCell();
                    rowCell.className = "centerColumnLayout";
                    const rowChecked = workingRowChecks[rowNum] === true;
                    const rowBgClass = rowChecked ? "lightYellowBG" : "lightRedBG";
                    const rowBorderClass = rowChecked ? "yellowBorder" : "redBorder";

                    const checkbox = document.createElement("input");
                    checkbox.type = "checkbox";
                    checkbox.checked = rowChecked;
                    checkbox.className = "unitHeight squareSize";
                    checkbox.addEventListener('change', function() {
                        workingRowChecks[rowNum] = this.checked;
                        renderGrid();
                    });

                    tempElement = document.createElement("div");
                    tempElement.className = `bordered shadowed roundedTin marginTopMin regMinPadding centerColumnLayout duoHeight fullWidth ${rowBgClass} ${rowBorderClass}`;
                    tempElement.appendChild(checkbox);
                    rowCell.appendChild(tempElement);
                });

                tempDiv.appendChild(checkColumn);
            }

            container.appendChild(tempDiv);

            // Restore scroll position
            tempDiv.scrollLeft = savedScrollLeft;
            tempDiv.scrollTop = savedScrollTop;

            // ---- Arrow key navigation (only when editable) ----
            if (!viewOnly) {
                tempDiv.addEventListener('keydown', function(e) {
                    const target = e.target;
                    if (!target.dataset || target.dataset.row === undefined || target.dataset.col === undefined) return;
                    const currentRow = parseInt(target.dataset.row, 10);
                    const currentCol = parseInt(target.dataset.col, 10);
                    let newRow = currentRow,
                        newCol = currentCol,
                        handled = false;
                    switch (e.key) {
                        case 'ArrowLeft':
                            newCol = currentCol - 1;
                            handled = true;
                            break;
                        case 'ArrowRight':
                            newCol = currentCol + 1;
                            handled = true;
                            break;
                        case 'ArrowUp':
                            newRow = currentRow - 1;
                            handled = true;
                            break;
                        case 'ArrowDown':
                            newRow = currentRow + 1;
                            handled = true;
                            break;
                    }
                    if (handled) {
                        const nextInput = tempDiv.querySelector(`input[data-row="${newRow}"][data-col="${newCol}"]`);
                        if (nextInput) {
                            e.preventDefault();
                            nextInput.focus();
                            nextInput.select();
                        }
                    }
                });
            }
        }

        // ---- Add Column button listener (only if editable) ----
        if (!viewOnly) {
            container.querySelector('#addColumnButton').addEventListener('click', () => {
                const nameInput = document.getElementById('columnNameInput');
                const colName = nameInput.value.trim();
                if (!colName) return;

                const alreadyExists = workingColumns.some(c => c.columnName.toLowerCase() === colName.toLowerCase());
                if (alreadyExists) {
                    nameInput.focus();
                    return;
                }

                const nextDisplayOrder = Math.max(...workingColumns.map(c => c.displayOrder), 0) + 1;
                workingColumns.push({
                    id: null,
                    columnName: colName,
                    displayOrder: nextDisplayOrder,
                    tempKey: 'new_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5)
                });
                nameInput.value = '';
                renderGrid();
            });
        }

        renderGrid();

        confirmationForm.appendChild(container);

        if (!viewOnly) {
            const hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = "variableListData";
            hiddenInput.className = "tempElement";
            confirmationForm.appendChild(hiddenInput);

            confirmationForm.onsubmit = function() {
                const output = {
                    columns: workingColumns.map(c => ({
                        id: c.id || null,
                        columnName: c.columnName,
                        displayOrder: c.displayOrder,
                        tempKey: c.tempKey || null
                    })),
                    values: workingValues.map(v => ({
                        id: v.id || null,
                        rowNumber: v.rowNumber,
                        columnID: v.columnID || null,
                        tempKey: v.tempKey || null,
                        valueText: v.valueText
                    })),
                    rowChecks: Object.entries(workingRowChecks).map(([rowNum, checked]) => ({
                        rowNumber: parseInt(rowNum),
                        isChecked: checked
                    }))
                };
                hiddenInput.value = JSON.stringify(output);
                return true;
            };
        }

        confirmation.style.display = 'flex';
    });

    // Added cancellation events
    confirmationCancel.addEventListener('click', function() {
        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });

        confirmationForm.removeAttribute("enctype");
        confirmationSubmit.classList.remove("hidden");
        confirmationContent.classList.add('maxWidth');
    });

    confirmationBG.addEventListener('click', function() {
        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });

        confirmationForm.removeAttribute("enctype");
        confirmationSubmit.classList.remove("hidden");
        confirmationContent.classList.add('maxWidth');
    });
</script>

</html>