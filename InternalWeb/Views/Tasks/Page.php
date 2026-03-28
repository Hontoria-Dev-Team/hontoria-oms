<!DOCTYPE html>
<html>

<head>
    <title>Tasks Panel - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <h1 class="titleLogo minGap tinHeight">
            <img src="../../Shared/Img/CheckBoxIcon.png" alt="CheckBox"> Tasks Panel
        </h1>
        <?php include("../Views/.Components/ErrorBox.php"); ?>
        <section class="rowLayout flexMax midGap">
            <section class="flexMid roundedMid centerColumnLayout">
                <div class="columnLayout minGap box roundedMid fullHeight fullWidth">
                    <h3>Available Tasks</h3>
                    <div class="gridFlex minGrids minGap scrollable flexMax noFlexBasis noMinHeight contentFlexStart">
                        <?php foreach ($availableTasks as $task): ?>
                            <?php if (!$task['isAssigned'] && !$task['isFull']): ?>
                                <div class="darkFadedBG centerHoriColumnLayout tinGap regPadding roundedMin shadowed bordered">
                                    <h2 class="centerHoriRowLayout">
                                        <span class="flexMax">Order #<?= $task['orderID'] ?></span>
                                        <form method="POST" action="index.php?page=tasks&action=assignToTask">
                                            <input type="hidden" name="orderProcessID" value="<?= $task['id'] ?>">
                                            <input type="submit" name="submit" value="Assign" class="importantInput shadowed">
                                        </form>
                                    </h2>
                                    <b>Service: <?= $task['serviceName'] ?> <?= $task['subserviceName'] ?></b>
                                    <b>Task: <?= $task['processName'] ?></b>
                                    <b>Customer: <?= $task['customerName'] ?></b>
                                    <b>Due In: <span class="dueInText" data-due-date="<?= $task['deadlineAt'] ?>">4d 2h (March 31, 2026)</span></b>
                                    <div class="rowLayout minGap">
                                        <b class="centerHoriRowLayout tinGap">
                                            Assigned: <?= $task['assignedNum'] ?>/<?= $task['maxAssign'] ?>
                                            <img src="../../Shared/Img/PersonIcon.png" alt="Person" class="unitHeight">
                                        </b>
                                        <b class="centerHoriRowLayout tinGap">
                                            Required: <?= $task['minAssign'] ?>
                                            <img src="../../Shared/Img/PersonIcon.png" alt="Person" class="unitHeight">
                                        </b>
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
                        <div class="columnLayout minGap scrollable flexMax noFlexBasis noMinHeight contentFlexStart">
                            <?php foreach ($availableTasks as $task): ?>
                                <?php if ($task['isAssigned']): ?>
                                    <?php
                                    $statusClass = $task['taskStatus'] === 'pending' ? "redTransBG redBorder" : ($task['taskStatus'] === 'complete' ?
                                        "greenTransBG greenBorder" : "yellowTransBG yellowBorder");
                                    ?>
                                    <div class="<?= $statusClass ?> columnLayout tinGap regPadding roundedMin shadowed assignedTaskElement clickable"
                                        data-id="<?= $task['id'] ?>" data-status="<?= $task['taskStatus'] ?>">
                                        <div class="centerHoriRowLayout minGap">
                                            <div class="flexMax">
                                                <h2>Order #<?= $task['orderID'] ?></h2>
                                                <div class="centerHoriRowLayout minGap">
                                                    <div class="flexMax columnLayout">
                                                        <b>Service: <?= $task['serviceName'] ?> <?= $task['subserviceName'] ?></b>
                                                        <b>Task: <?= $task['processName'] ?></b>
                                                        <b>Customer: <?= $task['customerName'] ?></b>
                                                    </div>
                                                    <div class="flexMax columnLayout">
                                                        <b>Due In: <span class="dueInText" data-due-date="<?= $task['deadlineAt'] ?>">4d 2h (March 31, 2026)</span></b>
                                                        <b class="centerHoriRowLayout tinGap">
                                                            Assigned: <?= $task['assignedNum'] ?>/<?= $task['maxAssign'] ?>
                                                            <img src="../../Shared/Img/PersonIcon.png" alt="Person" class="unitHeight">
                                                        </b>
                                                        <b class="centerHoriRowLayout tinGap">
                                                            Required: <?= $task['minAssign'] ?>
                                                            <img src="../../Shared/Img/PersonIcon.png" alt="Person" class="unitHeight">
                                                        </b>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="<?= $task['messengerGCLink'] ?>" class="tinHeight squareSize regMinPadding blueBG roundedMin centerColumnLayout circle shadowed">
                                                <img src="../../Shared/Img/MessengerIcon.png" alt="Messenger" class="invertColors">
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
                <section class="box centerColumnLayout roundedMid minGap flexMid">
                    <div class="fullDimensions rowLayout minGap">
                        <div class="columnLayout tinGap flexMid">
                            <h3>Assigned to Task:</h3>
                            <b class="columnLayout scrollable flexMax noFlexBasis noMinHeight" id="assigneesContainer">
                                <span class="indentText greenText">Josh Rabia - ✓</span>
                                <span class="indentText yellowText">John Hempon - 〇</span>
                                <span class="indentText redText">Ace Galves - X</span>
                            </b>
                        </div>
                        <div class="columnLayout tinGap flexMax">
                            <h3>Tasks Objectives</h3>
                            <div class="centerHoriRowLayout minGap">
                                <b>Task Status: </b>
                                <select class="flexMax" id="taskStatusSelect">
                                    <option value="pending" selected>Pending</option>
                                    <option value="partially complete">Partially Complete</option>
                                    <option value="complete">Complete</option>
                                </select>
                                <button type="button" class="importantInput" id="updateStatusButton">Update</button>
                            </div>
                        </div>
                    </div>
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
    const assignedTaskElement = document.querySelectorAll('.assignedTaskElement');
    const assigneesContainer = document.getElementById('assigneesContainer');
    const taskStatusSelect = document.getElementById('taskStatusSelect');
    const updateStatusButton = document.getElementById('updateStatusButton');
    const assigneeList = <?php echo json_encode($assigneeList); ?>;

    const assigneeMap = {};

    assigneeList.forEach(item => {
        if (!assigneeMap[item.orderProcessID]) {
            assigneeMap[item.orderProcessID] = [];
        }

        assigneeMap[item.orderProcessID].push({
            name: item.firstName + " " + (item.middleName?.[0] + "." || "") + " " + item.lastName
        });
    });

    document.addEventListener("DOMContentLoaded", () => {
        confirmationCancel.value = "No Cancel";
    });

    const selectedID = document.createElement("input");
    selectedID.type = "hidden";
    selectedID.name = "selectedID";
    confirmationForm.appendChild(selectedID);

    let tempElement;

    // Due time calculation
    document.querySelectorAll('.dueInText').forEach(function(elem) {
        elem.textContent = elem.dataset.dueDate == '0000-00-00 00:00:00' ? "No due date" : getDueTime(elem.dataset.dueDate) + " (" + formatDate(elem.dataset.dueDate) + ")";
    });

    // Reactive clickable process task data script
    let selectedTaskAssignees;

    document.addEventListener('DOMContentLoaded', function() {
        assignedTaskElement.forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedTaskAssignees = [...(assigneeMap[elem.dataset.id] || [])];

                assigneesContainer.innerHTML = '';
                selectedTaskAssignees.forEach(function(assignee) {
                    tempElement = document.createElement("span");
                    tempElement.textContent = assignee.name + " - X";
                    tempElement.classList.add("indentText", "redText");
                    assigneesContainer.appendChild(tempElement);
                });

                selectedID.value = elem.dataset.id;
                taskStatusSelect.value = elem.dataset.status;
            });
        });
    });

    // Process Task submission logic functionality
    updateStatusButton.addEventListener('click', function() {
        confirmationForm.action = "index.php?page=tasks&action=updateTaskStatus"

        tempElement = document.createElement("input");
        tempElement.type = "hidden";
        tempElement.name = "taskStatus";
        tempElement.className = "tempElement";
        tempElement.value = taskStatusSelect.value;
        confirmationForm.appendChild(tempElement);

        confirmationTitle.innerHTML = "Change Task's Status?";
        confirmationText.innerHTML = 'Are you sure to change the status of this task to <b class="capitalFirst">' + taskStatusSelect.value + '</b>?';
        confirmationSubmit.value = "Yes Change";
        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");

        confirmation.style.display = 'flex';
    });


    // Added cancellation events
    confirmationCancel.addEventListener('click', function() {
        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });
    });

    confirmationBG.addEventListener('click', function() {
        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });
    });
</script>

</html>