<!DOCTYPE html>
<html>

<head>
    <title>Orders Panel - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
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
        <section class="columnLayout flexMax midGap">
            <section class="centerColumnLayout roundedMid flexMid minHeight">
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
                    <section class="minGap gridFlexMid scrollable flexMax regMinPadding" id="orderList">
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
                            $divBgClass = $order['status'] === "Active" ?
                                "yellowTransBG yellowBorder" : ($order['status'] === "Idle" ? "redTransBG redBorder" : "greenTransBG greenBorder");
                            $statusStyleClass = $order['status'] === "Active" ?
                                "yellowBG" : ($order['status'] === "Idle" ? "redBG" : "greenBG clickable");
                            ?>
                            <div class="midHeight regPadding roundedMin centerHoriColumnLayout minGap flexStatic orderElement shadowed clickable <?= $divBgClass ?>"
                                data-id="<?= $order['id'] ?>" data-due="<?= $order['deadlineAt'] ?>" data-customer="<?= $order['customerName'] ?>">
                                <p class="norWestAbsolute closeCorner transText">Order #<?= $order['id'] ?></p>
                                <div class="orderStatusElement souEastAbsolute minPadding roundedMin shadowed emphasizedText whiteText <?= $statusStyleClass ?>"
                                    data-status="<?= $order['status'] ?>" data-id="<?= $order['id'] ?>">
                                    <?= $order['status'] ?>
                                </div>
                                <h2 class="centerHoriRowLayout tinGap"><?= $order['subserviceName'] ?> <?= $order['serviceName'] ?> <b>(<?= $order['customerName'] ?>)</b></h2>
                                <div class="columnLayout">
                                    <b>Due In: <span class="dueInText" data-due-date="<?= $order['deadlineAt'] ?>"></span></b>
                                    <b>Value: ₱<?= $order['priceTotal'] ?></b>
                                    <b>Current Process: <?= $activeProcesses ?></b>
                                    <b class="centerHoriRowLayout tinGap"><img src="../../Shared/Img/PeopleIcon.png" alt="People" class="unitHeight">
                                        Assigned: <?= $assigneeCount ?></b>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="tinHeight"></div>
                    </section>
                </div>
                <div class="gradientBorderDiag"></div>
            </section>
            <section class="centerRowLayout midGap flexMin roundedMid">
                <section class="centerColumnLayout roundedMid flexMid fullHeight">
                    <section class="box roundedMid fullDimensions centerHoriColumnLayout minGap">
                        <h3 class="centerRowLayout tinGap centerText fullWidth" id="selectedText">No Service Selected</h3>
                        <form method="POST" action="index.php?page=orders&action=changeDeadline" class="centerHoriRowLayout minGap hidden" id="deadlineForm">
                            <label for="deadlineAt" class="minWidth">Due Date</label>
                            <input type="date" name="deadlineAt" class="fullWidth" id="deadlineAt">
                            <button type="button" id="changeDeadlineButton" class="importantInput flexMax">Change</button>
                        </form>
                        <div class="rowLayout minGap hidden" id="additionalSelectedInputs">
                            <button type="button" id="assignEmployeesButton" class="importantInput flexMax">Assign Employees</button>
                            <button type="button" class="criticalInput centerColumnLayout" id="deleteOrderButton">
                                <img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors">
                            </button>
                        </div>
                    </section>
                    <div class="gradientBorderDiag"></div>
                </section>
                <section class="centerColumnLayout roundedMid flexMax fullHeight">
                    <section class="box columnLayout roundedMid minGap fullDimensions">
                        <h5>Order Task Process</h5>
                        <div class="centerHoriRowLayout tinGap flexMax" id="orderProcess">
                            <h3 class="flexMin centerText">No Service Selected</h3>
                        </div>
                    </section>
                    <div class="gradientBorderDiag"></div>
                </section>
            </section>
        </section>
    </main>
    <?php include("../Views/.Components/ConfirmationBox.php"); ?>
</body>
<script src="../.JS/ConfirmationBox.js"></script>
<script src="../.JS/DueTimeCalculator.js"></script>
<script>
    const orderProcess = document.getElementById('orderProcess');
    const selectedText = document.getElementById('selectedText');
    const deadlineForm = document.getElementById('deadlineForm');
    const deadlineAt = document.getElementById('deadlineAt');
    const changeDeadlineButton = document.getElementById('changeDeadlineButton');
    const additionalSelectedInputs = document.getElementById('additionalSelectedInputs');
    const assignEmployeesButton = document.getElementById('assignEmployeesButton');
    const deleteOrderButton = document.getElementById('deleteOrderButton');
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
            assignedNum: item.assignedNum
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
            processDiv.className = 'flexMin minHeight bordered roundedMin centerColumnLayout tinGap clickable processElement';
            processDiv.dataset.orderProcessID = orderProcesses[i].id;
            processDiv.dataset.name = orderProcesses[i].processName;
            processDiv.dataset.status = orderProcesses[i].status;

            processHead = document.createElement('h3');
            processHead.textContent = orderProcesses[i].processName;
            processParagraph = document.createElement('p');
            processParagraph.className = "norWestAbsolute closeCorner";

            switch (orderProcesses[i].status) {
                case 'complete':
                    processDiv.classList.add('greenTransBG');
                    processParagraph.textContent = '(Complete)';
                    break;
                case 'partially complete':
                    processDiv.classList.add('yellowGreenTransBG');
                    processParagraph.textContent = '(Partially Complete)';
                    break;
                case 'active':
                    processDiv.classList.add('yellowTransBG');
                    processParagraph.textContent = '(Active)';
                    break;
                case 'pending':
                    processDiv.classList.add('redTransBG');
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
                        <p>Assigned: ${orderProcesses[i].assignedNum}/${orderProcesses[i].maxAssign}</p>
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

    // Editable deadline and delete order function logic
    function setMinToToday() {
        const today = new Date().toISOString().split('T')[0];
        deadlineAt.min = today;
    }

    setMinToToday();

    setInterval(setMinToToday, 60000);

    document.querySelectorAll('.orderElement').forEach(function(elem) {
        elem.addEventListener('click', function() {
            selectedText.classList.remove("centerRowLayout");
            selectedText.classList.add("centerHoriRowLayout");
            selectedText.innerHTML = "Order #" + elem.dataset.id + " <b>(" + elem.dataset.customer + ")</b>";

            deadlineAt.value = elem.dataset.due.split(' ')[0];
            deadlineForm.classList.remove("hidden");

            deleteOrderButton.dataset.selectedId = elem.dataset.id;
            additionalSelectedInputs.classList.remove("hidden");
        });
    });

    changeDeadlineButton.addEventListener('click', function() {
        confirmationTitle.innerHTML = "Change Order Deadline?";
        confirmationForm.action = "index.php?page=orders&action=changeDeadline"

        confirmationText.innerHTML = "Are you sure to change the deadline of Order #" + selectedID.value + "?";
        confirmationSubmit.value = "Yes Change";
        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");

        confirmation.style.display = 'flex';
    });

    deadlineAt.addEventListener('change', function() {
        newDeadline.value = deadlineAt.value;
    });

    deleteOrderButton.addEventListener('click', function() {
        confirmationTitle.innerHTML = "Delete Order?";
        confirmationForm.action = "index.php?page=orders&action=delete";

        confirmationText.innerHTML = "Are you sure to delete Order #" + selectedID.value + "?";
        confirmationSubmit.value = "Yes Delete";
        confirmationSubmit.classList.remove("yellowBG", "whiteText", "noBorder");

        confirmation.style.display = 'flex';
    });

    // Employee Assignment Box Function logic
    let hasAvailableProcess;

    assignEmployeesButton.addEventListener('click', function() {
        hasAvailableProcess = false;

        tempDiv = document.createElement('div');
        tempDiv.className = "columnLayout minGap tempElement maxHeight scrollable regMinPadding";
        tempDiv.id = "assignableEmployeesContainer";
        confirmationForm.appendChild(tempDiv);

        tempElement = document.createElement("div");
        tempElement.innerHTML = "<b>No process selected.</b>";
        tempElement.className = "centerColumnLayout regMinPadding shadowed roundedTin flexMax darkFadedBG bordered";
        tempDiv.appendChild(tempElement);

        tempElement = document.createElement('h3');
        tempElement.textContent = "Assignable Employees:";
        tempElement.className = "tempElement";
        confirmationForm.appendChild(tempElement);

        tempDiv = document.createElement('div');
        tempDiv.className = "centerHoriRowLayout tinGap tempElement regMinPadding";
        confirmationForm.appendChild(tempDiv);

        selectedOrderProcesses.forEach(function(process) {
            if (process.status == 'complete' || process.status == 'pending' || Number(process.maxAssign) <= Number(process.assignedNum)) return;

            tempElement = document.createElement("b");
            tempElement.textContent = process.name;
            tempElement.className = "centerText regMinPadding shadowed roundedTin flexMax yellowTransBG yellowBorder selectedProcessTaskAssignment clickable";
            tempElement.dataset.orderProcessID = process.id;
            tempElement.dataset.processID = process.processID;
            tempDiv.appendChild(tempElement);

            hasAvailableProcess = true;
        });

        if (!hasAvailableProcess) {
            tempElement = document.createElement("b");
            tempElement.textContent = "No available process to assign.";
            tempElement.className = "centerText regMinPadding shadowed roundedTin flexMax darkFadedBG bordered";
            tempDiv.appendChild(tempElement);
        }

        tempElement = document.createElement('h3');
        tempElement.textContent = "Available Processes:";
        tempElement.className = "tempElement";
        confirmationForm.appendChild(tempElement);

        confirmationTitle.innerHTML = "Assign Employees";
        confirmationForm.action = "index.php?page=orders&action=assignEmployee"

        confirmationText.innerHTML = "To assign employees to tasks, first click on a task that is available for assignment; then select an applicable employee to the task.";
        confirmationSubmit.classList.add("hidden");

        confirmation.style.display = 'flex';

        document.querySelectorAll('.selectedProcessTaskAssignment').forEach(function(elem) {
            elem.addEventListener('click', function() {
                const container = document.getElementById('assignableEmployeesContainer');

                container.innerHTML = '';

                const assignableEmployees = [...(userProcessMap[elem.dataset.processID] || [])];
                const taskAssignees = [...(taskAssigneeMap[elem.dataset.orderProcessID] || [])];

                let hasAssignableEmployee = false;

                assignableEmployees.forEach(function(employee) {
                    if (taskAssignees.includes(employee.userID)) return;

                    tempElement = document.createElement("form");
                    tempElement.method = "POST";
                    tempElement.innerHTML = `
                        <input type="hidden" name="userID" value="${employee.userID}">
                        <input type="hidden" name="orderProcessID" value="${elem.dataset.orderProcessID}">
                        <div class="rowLayout duoHeight">
                            <b class="flexMax yellowBG whiteText fullHeight centerColumnLayout skewedXNegBG">
                                <span>${employee.name}</span>
                            </b>
                            <b class="midHoriPadding fullHeight centerColumnLayout">Tasks: ${userTaskCountMap[employee.userID] || 0}</b>
                        </div>
                        <div class="capitalFirst yellowTransBG centerColumnLayout">
                            <b>${employee.roles}</b>
                        </div>
                        <input type="submit" name="submit" class="fullDimensions invisible absoluted">
                    `;
                    tempElement.className = "centerText relatived centerHoriColumnLayout shadowed roundedTin yellowBorder selectedEmployeeAssign fixedScreen";
                    tempElement.action = "index.php?page=orders&action=assignEmployeeToTask";
                    container.appendChild(tempElement);

                    hasAssignableEmployee = true;
                });

                if (!hasAssignableEmployee) {
                    tempElement = document.createElement("b");
                    tempElement.innerHTML = "<b>No assignable employee for this process to assign.</b>";
                    tempElement.className = "centerColumnLayout centerText regMinPadding shadowed roundedTin flexMax darkFadedBG bordered";
                    container.appendChild(tempElement);
                }
            });
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

        confirmationSubmit.classList.remove("hidden", "yellowBG", "whiteText", "noBorder");
    });

    confirmationBG.addEventListener('click', function() {
        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });

        confirmationSubmit.classList.remove("hidden", "yellowBG", "whiteText", "noBorder");
    });
</script>

</html>