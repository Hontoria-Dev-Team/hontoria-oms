<!DOCTYPE html>
<html>

<head>
    <title>Orders Panel - Hontoria OMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <style>
        @media (max-width: 500px) {
            .asideLayout>main>section {
                min-width: fit-content;
                flex-direction: row-reverse;
            }

            .asideLayout>main>section>*:nth-child(2) {
                min-width: calc(100vw - 3rem) !important;
                max-width: calc(100vw - 3rem) !important;
            }

            .asideLayout>main>section>*:nth-child(1) {
                min-width: calc(100vw - 3rem) !important;
                max-width: calc(100vw - 3rem) !important;
            }
        }

        @media (max-width: 450px) {
            #orderProcess {
                min-width: 450px !important;
            }

            #orderProcess>h3 {
                text-align: left;
            }

            :has(> #orderProcess) {
                overflow-y: scroll;
            }

            :has(> #orderProcess)>*:nth-child(1) {
                position: sticky;
                left: 0;
            }

            :has(> #orderProcess)>*:nth-child(1) {
                position: sticky;
                left: 0;
            }

            form[action="index.php?page=staff"] {
                overflow-y: scroll;
                padding: 0.1rem !important;
            }

            form[action="index.php?page=staff"] input[type="search"] {
                width: 60vw !important;
            }
        }
    </style>
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <h1 class="titleLogo minGap tinHeight">
            <img src="../../Shared/Img/ListIcon.png" alt="List"> Orders Panel
            <div class="rowLayout minGap flexMax contentFlexEnd">
                <a href="index.php?page=orders&action=create" class="roundedMin centerColumnLayout importantInput regPadding emphasizedText shadowed">
                    Create Order
                </a>
                <a href="index.php?page=orders&action=viewArchive" class="roundedMin centerColumnLayout importantInput regPadding emphasizedText shadowed">
                    Order Archive
                </a>
            </div>
        </h1>
        <?php include("../Views/.Components/MessageBox.php"); ?>
        <section class="rowLayout flexMax midGap">
            <section class="centerColumnLayout roundedMid flexMinExtra fullHeight noMinWidth">
                <section class="box roundedMid fullDimensions columnLayout minGap" id="selectedOrderSection">
                    <h4 class="centerRowLayout tinGap centerText centerMarginsSelf fullWidth">No Service Selected</h4>
                </section>
                <div class="gradientBorderDiag"></div>
            </section>
            <div class="columnLayout midGap flexMax roundedMid">
                <section class="centerColumnLayout roundedMid flexMax minHeight">
                    <div class="box fullHeight fullWidth roundedMid columnLayout minGap">
                        <form method="GET" action="index.php?page=staff" class="rowLayout fullWidth minGap">
                            <input type="hidden" name="page" value="orders">
                            <input type="hidden" name="action" value="filter">

                            <div class="iconInput flexMax centerHoriRowLayout">
                                <input type="search" name="search" placeholder="Search Order" class="fullWidth" value="<?= $search ?>">
                                <img src="../../Shared/Img/MagnifierIcon.png" alt="Magnifier">
                            </div>

                            <select name="status">
                                <option value="" <?= $status === '' ? 'selected' : '' ?>>Any Status</option>
                                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="idle" <?= $status === 'idle' ? 'selected' : '' ?>>Idle</option>
                            </select>

                            <input type="submit" value="Search" class="importantInput">
                        </form>
                        <section class="contentFlexStart minGap gridFlexMid scrollable flexMax regMinPadding" id="orderList">
                            <?php foreach ($orderList as $order): ?>
                                <?php
                                $activeProcesses = "";

                                foreach ($orderProcessList as $process) {
                                    if ($process['orderID'] != $order['id'] || !in_array($process['status'], ['active', 'partially complete'])) {
                                        continue;
                                    }

                                    $activeProcesses .= $process['processName'] . ", ";
                                }
                                $activeProcesses = rtrim($activeProcesses, ", ");

                                $assigneeCount = $orderAssigneeCountMap[$order['id']] ?? 0;
                                $divBgClass = $order['status'] === "Unpaid"
                                    ? "bordered darkFadedBG" : ($order['status'] === "Active"
                                        ? "yellowTransBG yellowBorder" : ($order['status'] === "Idle"
                                            ? "redTransBG redBorder"      : "greenTransBG greenBorder"));
                                $statusStyleClass = $order['status'] === "Unpaid"
                                    ? "darkBG" : ($order['status'] === "Active"
                                        ? "yellowBG" : ($order['status'] === "Idle"
                                            ? "redBG"   : "greenBG clickable"));
                                ?>
                                <div class="fitHeight regMidPadding roundedMin centerHoriColumnLayout tinGap flexStatic orderElement shadowed clickable <?= $divBgClass ?>"
                                    data-id="<?= $order['id'] ?>" data-due="<?= $order['deadlineAt'] ?>" data-customer="<?= $order['customerName'] ?>"
                                    data-service="<?= $order['subserviceName'] . ' ' . $order['serviceName'] ?>"
                                    data-code="<?= $order['orderCode'] ?>">
                                    <h5 class="norEastAbsolute closeCorner transText">Order #<?= $order['id'] ?></h5>
                                    <div class="orderStatusElement souEastAbsolute closeCorner minPadding roundedMin shadowed whiteText <?= $statusStyleClass ?>"
                                        data-status="<?= $order['status'] ?>" data-id="<?= $order['id'] ?>">
                                        <h4 class="outlineText"><?= $order['status'] ?></h4>
                                    </div>
                                    <h4 class="whiteText outlineText"><?= $order['subserviceName'] ?> <?= $order['serviceName'] ?></h4>
                                    <div class="columnLayout">
                                        <h5>Customer: <?= $order['customerName'] ?></span></h5>
                                        <h5>Due In: <span class="dueInText" data-due-date="<?= $order['deadlineAt'] ?>"></span></h5>
                                        <h5>Value: ₱<?= $order['priceTotal'] ?></h5>
                                        <h5>Current Process: <?= $activeProcesses ?></h5>
                                        <h5 class="centerHoriRowLayout tinGap"><img src="../../Shared/Img/PeopleIcon.png" alt="People" class="unitHeight">
                                            Assigned: <?= $assigneeCount ?></h5>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </section>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
                <section class="centerColumnLayout roundedMid flexMinExtra fullHeight">
                    <section class="box columnLayout roundedMid minGap fullDimensions">
                        <h5>Order Task Process</h5>
                        <div class="centerHoriRowLayout tinGap flexMax" id="orderProcess">
                            <h3 class="flexMin centerText">No Service Selected</h3>
                        </div>
                    </section>
                    <div class="gradientBorderDiag"></div>
                </section>
            </div>
        </section>
    </main>
    <?php include("../Views/.Components/ConfirmationBox.php"); ?>
</body>
<script src="../.JS/ConfirmationBox.js"></script>
<script src="../.JS/TimeHelpers.js"></script>
<script>
    const orderProcess = document.getElementById('orderProcess');
    const selectedOrderSection = document.getElementById('selectedOrderSection');
    const orders = <?php echo json_encode($orderList); ?>;
    const orderProcesses = <?php echo json_encode($orderProcessList); ?>;
    const userProcessList = <?php echo json_encode($userProcessList); ?>;
    const taskAssigneeList = <?php echo json_encode($taskAssigneeList); ?>;
    const userTaskCountTally = <?php echo json_encode($userTaskCountTally); ?>;
    const userProcessTasksList = <?php echo json_encode($userProcessTasksList); ?>;

    const orderProcessesMap = {};

    orderProcesses.forEach(item => {
        if (!orderProcessesMap[item.orderID]) {
            orderProcessesMap[item.orderID] = [];
        }

        orderProcessesMap[item.orderID].push({
            id: item.id,
            processID: item.processID,
            name: item.processName,
            status: item.status,
            maxAssign: item.maxAssign,
            assignedNum: item.assignedNum,
            hasDesign: item.hasDesign,
            hasVariableList: item.hasVariableList,
            designAccess: item.designAccess,
            variableListAccess: item.variableListAccess
        });
    });

    const userProcessMap = {};

    userProcessList.forEach(item => {
        if (!userProcessMap[item.processID]) {
            userProcessMap[item.processID] = [];
        }

        userProcessMap[item.processID].push({
            userID: item.userID,
            name: item.firstName + " " + item.middleName[0] + ". " + item.lastName,
            roles: item.roles
        });
    });

    const taskAssigneeMap = {};

    taskAssigneeList.forEach(item => {
        if (!taskAssigneeMap[item.orderProcessID]) {
            taskAssigneeMap[item.orderProcessID] = [];
        }

        taskAssigneeMap[item.orderProcessID].push(item.userID);
    });

    const userTaskCountMap = {};

    userTaskCountTally.forEach(item => {
        userTaskCountMap[item.userID] = (item.taskCount);
    });

    const userProcessTasksMap = {};
    let designButton = null;
    let variableListButton = null;
    const designList = <?php echo json_encode($designList); ?>;
    const variableListMap = <?php echo json_encode($variableListMap); ?>;
    const designMap = {};

    designList.forEach(item => {
        designMap[item.orderID] = {
            image: item.image,
            approved: item.approved
        };
    });

    let selectedOrderDesign = '';
    let selectedOrderDesignApproval = -1;
    let selectedOrderDesignAccess = 'no access';
    let selectedOrderVariableListAccess = 'no access';

    userProcessTasksList.forEach(item => {
        if (!userProcessTasksMap[item.orderProcessID]) {
            userProcessTasksMap[item.orderProcessID] = [];
        }

        userProcessTasksMap[item.orderProcessID].push({
            userID: item.userID,
            name: item.firstName + " " + item.middleName[0] + ". " + item.lastName,
            status: item.status,
            assignedAt: item.assignedAt,
            roles: item.roles
        });
    });

    document.addEventListener("DOMContentLoaded", () => {
        confirmationCancel.value = "No Cancel";
    });

    const selectedID = document.createElement("input");
    selectedID.type = "hidden";
    selectedID.name = "selectedID";
    confirmationForm.appendChild(selectedID);

    const newDeadline = document.createElement("input");
    newDeadline.type = "hidden";
    newDeadline.name = "newDeadline";
    confirmationForm.appendChild(newDeadline);

    // Due time calculation
    document.querySelectorAll('.dueInText').forEach(function(elem) {
        elem.textContent = elem.dataset.dueDate == '0000-00-00 00:00:00' ? "No due date" : getDueTime(elem.dataset.dueDate) + " (" + formatDate(elem.dataset.dueDate) + ")";
    });

    // Order Process Graph Show Function
    let selectedOrderProcesses;
    let arrow;
    let processDiv;
    let hasFirstProcess;
    let processHead;
    let processParagraph;
    let tempDiv;
    let tempElement;

    document.querySelectorAll('.orderElement').forEach(function(elem) {
        elem.addEventListener('click', function() {
            selectedID.value = elem.dataset.id;

            showProcess(elem.dataset.id);

            selectedOrderProcesses = [...(orderProcessesMap[elem.dataset.id] || [])];
        });
    });

    function showProcess(orderID) {
        orderProcess.innerHTML = '';
        hasFirstProcess = false;

        for (let i = 0; i < orderProcesses.length; i++) {
            if (orderProcesses[i].orderID != orderID) {
                continue;
            }

            if (hasFirstProcess) {
                arrow = document.createElement('h1');
                arrow.textContent = '>';
                orderProcess.appendChild(arrow);
            }

            processDiv = document.createElement('div');
            processDiv.className = 'flexMin minHeight shadowed roundedMin centerColumnLayout tinGap clickable processElement';
            processDiv.dataset.orderProcessID = orderProcesses[i].id;
            processDiv.dataset.name = orderProcesses[i].processName;
            processDiv.dataset.status = orderProcesses[i].status;

            processHead = document.createElement('h4');
            processHead.className = "whiteText outlineText";
            processHead.textContent = orderProcesses[i].processName;
            processParagraph = document.createElement('h5');
            processParagraph.className = "norWestAbsolute closeCorner transText";

            switch (orderProcesses[i].status) {
                case 'complete':
                    processDiv.classList.add('greenTransBG', 'greenBorder');
                    processParagraph.textContent = '(Complete)';
                    break;
                case 'partially complete':
                    processDiv.classList.add('yellowGreenTransBG', 'yellowGreenBorder');
                    processParagraph.textContent = '(Partially Complete)';
                    break;
                case 'active':
                    processDiv.classList.add('yellowTransBG', 'yellowBorder');
                    processParagraph.textContent = '(Active)';
                    break;
                case 'pending':
                    processDiv.classList.add('redTransBG', 'redBorder');
                    processParagraph.textContent = '(Pending)';
                    break;
            }

            processDiv.appendChild(processHead);
            processDiv.appendChild(processParagraph);

            if (!(orderProcesses[i].status == 'complete' || orderProcesses[i].status == 'pending')) {
                tempElement = document.createElement('div');
                tempElement.className = "centerHoriRowLayout tinGap unitHeight assignRange"
                tempElement.innerHTML = `
                    <img src="../../Shared/Img/PeopleIcon.png" alt="People" class="unitHeight">
                    <div class="centerHoriRowLayout tinGap">
                        <h5>Assigned: ${orderProcesses[i].assignedNum}/${orderProcesses[i].maxAssign}</h5>
                    </div>
                `;

                processDiv.appendChild(tempElement);
            }

            orderProcess.appendChild(processDiv);

            hasFirstProcess = true;
        }

        document.querySelectorAll('.processElement').forEach(function(elem) {
            elem.addEventListener('click', function() {
                confirmationTitle.innerHTML = "Order Process Assignees";

                if (elem.dataset.status == 'complete') {
                    confirmationText.innerHTML = "The process has already been completed.";
                } else {
                    const assignedEmployees = [...(userProcessTasksMap[elem.dataset.orderProcessID] || [])];
                    let hasAssignees = false;

                    confirmationText.innerHTML = "Here are the assignees for this order's " + elem.dataset.name +
                        " process. You can unnassign employees by clicking on the X button next to them.";

                    tempDiv = document.createElement('div');
                    tempDiv.className = "columnLayout minGap tempElement maxHeight scrollable regMinPadding";
                    tempDiv.id = "assignableEmployeesContainer";
                    confirmationForm.appendChild(tempDiv);

                    assignedEmployees.forEach(function(employee) {
                        tempElement = document.createElement("div");
                        tempElement.innerHTML = `
                            <div class="flexMax columnLayout tinGap regMinPadding">
                                <b>${employee.name}</b>
                                <b class="capitalFirst">${employee.roles}</b>
                                <b>Assigned At: ${formatDate(employee.assignedAt)}</b>
                            </div>
                            <form class="squareSize unitHeight norEastAbsolute centerColumnLayout closeCorner clickable"
                                method="POST" action="index.php?page=orders&action=removeAssignment">
                                <input type="hidden" name="userID" value="${employee.userID}">
                                <input type="hidden" name="orderProcessID" value="${elem.dataset.orderProcessID}">
                                <input type="submit" name="submit" class="absoluted fullDimensions invisible">
                                <img src="../../Shared/Img/XIcon.png" alt="X">
                            </form>
                        `;
                        tempElement.className = "centerText noShrink centerHoriColumnLayout shadowed roundedTin yellowBorder yellowTransBG selectedEmployeeAssign fixedScreen";
                        tempDiv.appendChild(tempElement);

                        hasAssignees = true;
                    });

                    if (!hasAssignees) {
                        tempDiv.innerHTML = `
                            <b class="centerColumnLayout centerText regMinPadding shadowed roundedTin flexMax darkFadedBG bordered">
                                <b>No employees assigned for this process.</b>
                            </b>
                        `;
                    }
                }

                confirmationSubmit.classList.add("hidden");

                confirmation.style.display = 'flex';
            });
        });
    }

    function getVariableListStatus(orderID) {
        const listData = variableListMap[orderID];
        let listStatus = 'incomplete';

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
                    listStatus = 'incomplete';
                } else if (!hasIssue && !isApproved) {
                    listStatus = 'unapproved';
                } else if (hasIssue && isApproved) {
                    listStatus = 'approved';
                } else if (!hasIssue && isApproved) {
                    listStatus = 'complete';
                }
            }
        }

        return listStatus;
    }

    function updateOrderObjectives() {
        if (!designButton || !variableListButton || !selectedOrderProcesses) return;

        const orderID = selectedID.value;
        const hasDesign = selectedOrderProcesses.some(p => p.hasDesign == 1);
        const hasVariableList = selectedOrderProcesses.some(p => p.hasVariableList == 1);
        selectedOrderDesignAccess = hasDesign ? 'view & update' : 'no access';
        selectedOrderVariableListAccess = hasVariableList ? 'view & update' : 'no access';

        if (hasDesign && selectedOrderDesignAccess !== 'no access') {
            designButton.classList.remove('hidden');

            if (selectedOrderDesignApproval == 0) {
                designButton.className = 'redBorder flexMin duoHeight roundedMin centerHoriRowLayout shadowed fixedScreen clickable';
                designButton.querySelector('div').className = 'squareSize duoHeight centerColumnLayout redBG shadowed';
                designButton.querySelector('h4').textContent = 'Unapproved';
                designButton.querySelector('h4').style.color = 'var(--red)';
            } else if (selectedOrderDesignApproval == 1) {
                designButton.className = 'greenBorder flexMin duoHeight roundedMin centerHoriRowLayout shadowed fixedScreen clickable';
                designButton.querySelector('div').className = 'squareSize duoHeight centerColumnLayout greenBG shadowed';
                designButton.querySelector('h4').textContent = 'Approved';
                designButton.querySelector('h4').style.color = 'var(--green)';
            } else {
                designButton.className = 'bordered flexMin duoHeight roundedMin centerHoriRowLayout shadowed fixedScreen clickable';
                designButton.querySelector('div').className = 'squareSize duoHeight centerColumnLayout darkBG shadowed';
                designButton.querySelector('h4').textContent = 'Unset';
                designButton.querySelector('h4').style.color = 'var(--dark)';
            }

            if (selectedOrderDesignAccess === 'view only') {
                designButton.querySelector('h4').textContent = 'Design';
                designButton.querySelector('h4').style.color = '';
            }
        } else {
            designButton.classList.add('hidden');
        }

        if (hasVariableList && selectedOrderVariableListAccess !== 'no access') {
            variableListButton.classList.remove('hidden');
            const listStatus = getVariableListStatus(orderID);

            switch (listStatus) {
                case 'complete':
                    variableListButton.className = 'greenBorder flexMin duoHeight roundedMin centerHoriRowLayout shadowed fixedScreen clickable';
                    variableListButton.querySelector('div').className = 'squareSize duoHeight centerColumnLayout greenBG shadowed';
                    variableListButton.querySelector('h4').textContent = 'Complete';
                    variableListButton.querySelector('h4').style.color = 'var(--green)';
                    break;
                case 'approved':
                    variableListButton.className = 'yellowBorder flexMin duoHeight roundedMin centerHoriRowLayout shadowed fixedScreen clickable';
                    variableListButton.querySelector('div').className = 'squareSize duoHeight centerColumnLayout yellowBG shadowed';
                    variableListButton.querySelector('h4').textContent = 'Approved';
                    variableListButton.querySelector('h4').style.color = 'var(--yellow)';
                    break;
                case 'unapproved':
                    variableListButton.className = 'redBorder flexMin duoHeight roundedMin centerHoriRowLayout shadowed fixedScreen clickable';
                    variableListButton.querySelector('div').className = 'squareSize duoHeight centerColumnLayout redBG shadowed';
                    variableListButton.querySelector('h4').textContent = 'Unapproved';
                    variableListButton.querySelector('h4').style.color = 'var(--red)';
                    break;
                default:
                    variableListButton.className = 'bordered flexMin duoHeight roundedMin centerHoriRowLayout shadowed fixedScreen clickable';
                    variableListButton.querySelector('div').className = 'squareSize duoHeight centerColumnLayout darkBG shadowed';
                    variableListButton.querySelector('h4').textContent = 'Incomplete';
                    variableListButton.querySelector('h4').style.color = 'var(--dark)';
                    break;
            }

            if (selectedOrderVariableListAccess === 'view only') {
                variableListButton.querySelector('h4').textContent = 'Variable List';
                variableListButton.querySelector('h4').style.color = '';
            }
        } else {
            variableListButton.classList.add('hidden');
        }
    }

    function setupOrderObjectiveListeners() {
        if (!designButton || !variableListButton) return;

        designButton.onclick = function() {
            if (selectedOrderDesignAccess === 'view only') {
                if (selectedOrderDesign) {
                    imageBoxImage.src = selectedOrderDesign;
                    imageBox.style.display = 'flex';
                }
                return;
            }

            confirmationForm.action = "index.php?page=orders&action=uploadDesign";
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

            confirmationTitle.innerHTML = "Upload Design Image";
            confirmationText.innerHTML = "Please upload a photo for this Order's design.";
            confirmationSubmit.value = "Upload";
            confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");

            selectedID.value = designButton.dataset.id;
            confirmationForm.enctype = "multipart/form-data";
            confirmation.style.display = 'flex';

            if (selectedOrderDesign) {
                uploadedImage.src = selectedOrderDesign;
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
                const file = files[0];
                if (!file.type.startsWith("image/")) {
                    alert("Only images are allowed");
                    tempElement.value = "";
                    return;
                }
                uploadedImage.src = URL.createObjectURL(file);
                tempDiv.classList.remove("hidden");
            });
        };

        variableListButton.onclick = function() {
            const viewOnly = selectedOrderVariableListAccess === 'view only';

            if (!viewOnly) {
                confirmationContent.classList.remove('maxWidth');
            }

            confirmationForm.action = "index.php?page=orders&action=updateVariableList";
            selectedID.value = designButton.dataset.id;
            confirmationTitle.textContent = viewOnly ? "View Variable List" : "Edit Variable List";
            confirmationText.textContent = viewOnly ? "" : "Add or remove columns / rows, then save.";
            confirmationSubmit.value = "Save List";
            confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");
            if (viewOnly) confirmationSubmit.classList.add("hidden");

            const container = document.createElement("div");
            container.className = "tempElement columnLayout minGap";

            if (!viewOnly) {
                tempDiv = document.createElement("div");
                tempDiv.className = "rowLayout minGap";
                container.appendChild(tempDiv);

                tempElement = document.createElement("button");
                tempElement.type = "button";
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
                if (viewOnly) return false;
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
                    const removeBtnHtml = (!viewOnly && index !== 0) ?
                        `<a class="squareSize unitHeight columnRemove"><img src="../../Shared/Img/XIcon.png" alt="X"></a>` :
                        '';
                    tempElement.innerHTML = `
                    <div class="centerRowLayout tinGap">
                        <h5 class='outlineText capitalFirst'>${col.columnName}</h5>
                        ${removeBtnHtml}
                    </div>
                `;
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
                            tempElement.innerHTML = `<h5 class="capitalFirst whiteText centerText outlineText">${cell ? cell.valueText : ''}</h5>`;
                        } else {
                            const rowBorderClass = rowChecked ? "yellowBorder" : "redBorder";

                            if (viewOnly) {
                                tempElement = document.createElement("div");
                                tempElement.className = `bordered shadowed roundedTin marginTopMin regMinPadding duoHeight centerColumnLayout lightYellowBG ${rowBorderClass}`;
                                tempElement.innerHTML = `<h5 class="capitalFirst whiteText centerText outlineText">${cell ? cell.valueText : ''}</h5>`;
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
                tempDiv.scrollLeft = savedScrollLeft;
                tempDiv.scrollTop = savedScrollTop;

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
                    confirmationContent.classList.add('maxWidth');
                    return true;
                };
            }

            confirmation.style.display = 'flex';
        };
    }

    document.querySelectorAll('.orderElement').forEach(function(elem) {
        elem.addEventListener('click', function() {
            const today = new Date().toISOString().split('T')[0];

            // ----- Build the section (now includes the assign panel) -----
            selectedOrderSection.innerHTML = `
            <h4 class="centerHoriRowLayout tinGap centerText fullWidth">Order #${elem.dataset.id}</h4>
            <div>
                <h4 class="centerHoriRowLayout tinGap centerText fullWidth">Customer: ${elem.dataset.customer}</h4>
                <h4 class="centerHoriRowLayout tinGap centerText fullWidth">Service: ${elem.dataset.service}</h4>
                <a class="centerHoriRowLayout tinGap centerText fullWidth darkText underlineText boldenText"
                    href="http://localhost/hontoria-oms/PublicWeb/Public/?page=order&code=${elem.dataset.code}">Order Page: ${elem.dataset.code}</a>
            </div>
            <button type="button" class="criticalInput centerColumnLayout shadowed noBorder norEastAbsolute deleteOrderButton" data-selected-id="${elem.dataset.id}">
                <img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors">
            </button>
            <form method="POST" action="index.php?page=orders&action=changeDeadline" class="centerHoriRowLayout minGap">
                <h6>Due Date</h6>
                <input type="date" name="deadlineAt" class="flexMax deadlineAt" value="${elem.dataset.due.split(' ')[0]}" min="${today}">
                <button type="button" class="importantInput shadowed noBorder changeDeadlineButton">Change</button>
            </form>
            <div class="centerHoriRowLayout minGap orderObjectivesContainer">
                <div id="designButton" class="bordered flexMin duoHeight roundedMin centerHoriRowLayout shadowed fixedScreen clickable hidden">
                    <h4 class="flexMax centerText">Design</h4>
                    <div class="squareSize fullHeight centerColumnLayout darkBG shadowed">
                        <img src="../../Shared/Img/PhotoIcon.png" alt="Photo" class="invertColors">
                    </div>
                </div>
                <div id="variableListButton" class="redBorder flexMin duoHeight roundedMin centerHoriRowLayout shadowed fixedScreen clickable hidden">
                    <h4 class="flexMax centerText">Variable List</h4>
                    <div class="squareSize fullHeight centerColumnLayout redBG shadowed">
                        <img src="../../Shared/Img/BarsIcon.png" alt="Bars" class="invertColors">
                    </div>
                </div>
            </div>
            <div class="centerColumnLayout roundedMin flexMax noFlexBasis noMinHeight">
                <div class="assignEmployeesPanel columnLayout minGap fullDimensions whiteBG midZ roundedMin regMidPadding"></div>
                <div class="gradientBorderDiag minZ"></div>
            <div>
        `;

            // ----- Get fresh references -----
            const deleteOrderButton = selectedOrderSection.querySelector('.deleteOrderButton');
            const changeDeadlineButton = selectedOrderSection.querySelector('.changeDeadlineButton');
            const deadlineAt = selectedOrderSection.querySelector('.deadlineAt');
            const assignPanel = selectedOrderSection.querySelector('.assignEmployeesPanel');
            designButton = selectedOrderSection.querySelector('#designButton');
            variableListButton = selectedOrderSection.querySelector('#variableListButton');
            selectedID.value = elem.dataset.id;
            selectedOrderProcesses = [...(orderProcessesMap[elem.dataset.id] || [])];
            selectedOrderDesign = designMap[elem.dataset.id] ? designMap[elem.dataset.id].image : '';
            selectedOrderDesignApproval = designMap[elem.dataset.id] ? designMap[elem.dataset.id].approved : -1;
            if (designButton) {
                designButton.dataset.id = elem.dataset.id;
            }
            if (variableListButton) {
                variableListButton.dataset.id = elem.dataset.id;
            }
            updateOrderObjectives();
            setupOrderObjectiveListeners();

            // ----- Delete order listener -----
            deleteOrderButton.addEventListener('click', function() {
                confirmationTitle.innerHTML = "Delete Order?";
                confirmationForm.action = "index.php?page=orders&action=delete";

                confirmationText.innerHTML = "Are you sure to delete Order #" + selectedID.value + "?";
                confirmationSubmit.value = "Yes Delete";
                confirmationSubmit.classList.remove("yellowBG", "whiteText", "noBorder");

                confirmation.style.display = 'flex';
            });

            // ----- Change deadline listener -----
            changeDeadlineButton.addEventListener('click', function() {
                confirmationTitle.innerHTML = "Change Order Deadline?";
                confirmationForm.action = "index.php?page=orders&action=changeDeadline";

                confirmationText.innerHTML = "Are you sure to change the deadline of Order #" + selectedID.value + "?";
                confirmationSubmit.value = "Yes Change";
                confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");

                confirmation.style.display = 'flex';
            });

            deadlineAt.addEventListener('change', function() {
                newDeadline.value = deadlineAt.value;
            });

            // ----- Build Assign Employees UI inline -----
            function buildAssignPanel() {
                assignPanel.innerHTML = '<h3>Assign Employees To Order</h3>'; // clear

                tempDiv = document.createElement('div');
                assignPanel.appendChild(tempDiv);

                // Available Processes title
                const procTitle = document.createElement('h4');
                procTitle.textContent = "Available Processes:";
                tempDiv.appendChild(procTitle);

                // Container for process badges
                const processListDiv = document.createElement('div');
                processListDiv.className = "centerHoriRowLayout minGap regMinPadding scrollableX contentFlexEven";
                let hasAvailableProcess = false;

                selectedOrderProcesses.forEach(function(process) {
                    if (process.status == 'complete' || process.status == 'pending' ||
                        Number(process.maxAssign) <= Number(process.assignedNum)) return;

                    const procBadge = document.createElement("h5");
                    procBadge.textContent = process.name;
                    procBadge.className = "centerText whiteText outlineText regMinPadding shadowed roundedTin yellowTransBG yellowBorder selectedProcessTaskAssignment clickable";
                    procBadge.dataset.orderProcessID = process.id;
                    procBadge.dataset.processID = process.processID;
                    processListDiv.appendChild(procBadge);
                    hasAvailableProcess = true;
                });

                if (!hasAvailableProcess) {
                    const noProc = document.createElement("h5");
                    noProc.textContent = "No available process to assign.";
                    noProc.className = "centerText whiteText outlineText regMinPadding shadowed roundedTin flexMax darkFadedBG bordered";
                    processListDiv.appendChild(noProc);
                }

                tempDiv.appendChild(processListDiv);

                tempDiv = document.createElement('div');
                tempDiv.className = "columnLayout flexMax";
                assignPanel.appendChild(tempDiv);

                // Assignable Employees area (will be filled when a process is clicked)
                const empTitle = document.createElement('h4');
                empTitle.textContent = "Assignable Employees:";
                tempDiv.appendChild(empTitle);

                const employeeContainer = document.createElement('div');
                employeeContainer.className = "columnLayout minGap maxHeight scrollable regMinPadding flexMax noFlexBasis noMinHeight";
                employeeContainer.id = "assignableEmployeesContainer";
                // initial message
                employeeContainer.innerHTML =
                    '<div class="centerColumnLayout regMinPadding whiteText outlineText shadowed roundedTin flexMax darkFadedBG bordered"><b>No process selected.</b></div>';
                tempDiv.appendChild(employeeContainer);

                // Attach click events to process badges (after they are in the DOM)
                document.querySelectorAll('.selectedProcessTaskAssignment').forEach(function(procElem) {
                    procElem.addEventListener('click', function() {
                        const container = document.getElementById('assignableEmployeesContainer');
                        container.innerHTML = '';

                        const assignableEmployees = [...(userProcessMap[procElem.dataset.processID] || [])];
                        const taskAssignees = [...(taskAssigneeMap[procElem.dataset.orderProcessID] || [])];

                        let hasAssignableEmployee = false;

                        assignableEmployees.forEach(function(employee) {
                            if (taskAssignees.includes(employee.userID)) return;

                            const form = document.createElement("form");
                            form.method = "POST";
                            form.innerHTML = `
                            <input type="hidden" name="userID" value="${employee.userID}">
                            <input type="hidden" name="orderProcessID" value="${procElem.dataset.orderProcessID}">
                            <div class="rowLayout unitHeight">
                                <h5 class="flexMax yellowBG whiteText outlineText fullHeight centerColumnLayout skewedXNegBG">
                                    <span>${employee.name}</span>
                                </h5>
                                <h5 class="midHoriPadding fullHeight centerColumnLayout">Tasks: ${userTaskCountMap[employee.userID] || 0}</h5>
                            </div>
                            <div class="capitalFirst yellowTransBG centerColumnLayout">
                                <h5 class="whiteText outlineText">${employee.roles}</h5>
                            </div>
                            <input type="submit" name="submit" class="fullDimensions invisible absoluted">
                        `;
                            form.className = "centerText relatived centerHoriColumnLayout shadowed roundedTin yellowBorder selectedEmployeeAssign fixedScreen noShrink";
                            form.action = "index.php?page=orders&action=assignEmployeeToTask";
                            container.appendChild(form);
                            hasAssignableEmployee = true;
                        });

                        if (!hasAssignableEmployee) {
                            const msg = document.createElement("b");
                            msg.innerHTML = "<b>No assignable employee for this process to assign.</b>";
                            msg.className = "centerColumnLayout centerText regMinPadding shadowed roundedTin flexMax darkFadedBG bordered";
                            container.appendChild(msg);
                        }
                    });
                });
            }

            // Build the assign panel immediately (no button press needed)
            buildAssignPanel();
        });
    });

    // Order Verifying Function Logic
    document.querySelectorAll('.orderStatusElement.clickable').forEach(function(elem) {
        elem.addEventListener('click', function() {
            ShowVerificationBox(elem.dataset.id);
        });
    });

    function ShowVerificationBox(id) {
        confirmationTitle.innerHTML = "Verify Order Completion?";
        confirmationForm.action = "index.php?page=orders&action=verifyComplete";

        confirmationText.innerHTML = "Are you sure to verify the completion of Order #" + id + "? You are fully held responsible for false reporting.";
        confirmationSubmit.value = "Yes Verify";
        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");

        confirmation.style.display = 'flex';
    }

    // Added cancellation events
    confirmationCancel.addEventListener('click', function() {
        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });

        confirmationForm.removeAttribute("enctype");
        confirmationContent.classList.add("maxWidth");
        confirmationSubmit.classList.remove("hidden", "yellowBG", "whiteText", "noBorder");
    });

    confirmationBG.addEventListener('click', function() {
        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });

        confirmationForm.removeAttribute("enctype");
        confirmationContent.classList.add("maxWidth");
        confirmationSubmit.classList.remove("hidden", "yellowBG", "whiteText", "noBorder");
    });
</script>

</html>