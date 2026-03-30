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
                                        data-id="<?= $task['id'] ?>" data-order-id="<?= $task['orderID'] ?>" data-status="<?= $task['taskStatus'] ?>" data-design-access="<?= $task['designAccess'] ?>">
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
                <div class="rowLayout roundedMid midGap flexMid">
                    <section class="box centerColumnLayout tinGap flexMid roundedMid">
                        <div class="columnLayout tinGap fullDimensions">
                            <h3>Assigned to Task:</h3>
                            <b class="columnLayout scrollable flexMax noFlexBasis noMinHeight" id="assigneesContainer"></b>
                        </div>
                        <div class="gradientBorderDiag"></div>
                    </section>
                    <section class="box centerColumnLayout tinGap flexMax roundedMid">
                        <div class="columnLayout tinGap fullDimensions">
                            <h3>Tasks Objectives</h3>
                            <div class="centerHoriRowLayout minGap" id="designInputContainer">
                                <b>Design: </b>
                                <form method="POST" action="index.php?page=tasks&action=uploadDesign" enctype="multipart/form-data" class="centerHoriRowLayout minGap flexMax">
                                    <input type="hidden" name="selectedID" class="selectedIDInput">
                                    <input type="file" id="designInput" name="designImage" accept="image/*" class="flexMax" required>
                                    <input type="submit" name="submit" value="Submit" class="importantInput">
                                </form>
                            </div>
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
                        <div class="gradientBorderDiag"></div>
                        <div class="souEastAbsolute rowLayout minGap">
                            <a class="squareSize duoHeight centerColumnLayout importantInput roundedMin" id="designShowButton">
                                <img src="../../Shared/Img/PhotoIcon.png" alt="Photo" class="invertColors">
                            </a>
                            <a class="squareSize duoHeight centerColumnLayout importantInput roundedMin">
                                <img src="../../Shared/Img/BarsIcon.png" alt="Bars" class="invertColors">
                            </a>
                        </div>
                    </section>
                </div>
            </section>
        </section>
    </main>
    <?php include("../Views/.Components/ConfirmationBox.php"); ?>
    <?php include("../Views/.Components/ImageBox.php"); ?>
</body>
<script src="../.JS/ConfirmationBox.js"></script>
<script src="../.JS/ImageBox.js"></script>
<script src="../.JS/DueTimeCalculator.js"></script>
<script src="../.JS/MiscHelpers.js"></script>
<script>
    const assignedTaskElement = document.querySelectorAll('.assignedTaskElement');
    const selectedIDInput = document.querySelectorAll('.selectedIDInput');
    const assigneesContainer = document.getElementById('assigneesContainer');
    const taskStatusSelect = document.getElementById('taskStatusSelect');
    const updateStatusButton = document.getElementById('updateStatusButton');
    const designShowButton = document.getElementById('designShowButton');
    const assigneeList = <?php echo json_encode($assigneeList); ?>;
    const designList = <?php echo json_encode($designList); ?>;

    const assigneeMap = {};

    assigneeList.forEach(item => {
        if (!assigneeMap[item.orderProcessID]) {
            assigneeMap[item.orderProcessID] = [];
        }

        assigneeMap[item.orderProcessID].push({
            name: item.firstName + " " + (item.middleName?.[0] + "." || "") + " " + item.lastName,
            status: item.status
        });
    });

    const designMap = {};

    designList.forEach(item => {
        designMap[item.orderID] = item.image;
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
    let selectedTaskDesign;

    document.addEventListener('DOMContentLoaded', function() {
        assignedTaskElement.forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedTaskAssignees = [...(assigneeMap[elem.dataset.id] || [])];
                selectedTaskDesign = designMap[elem.dataset.orderId];

                assigneesContainer.innerHTML = '';
                selectedTaskAssignees.forEach(function(assignee) {
                    tempElement = document.createElement("span");

                    switch (assignee.status) {
                        case 'pending':
                            tempElement.textContent = assignee.name + " - X";
                            tempElement.classList.add("redText");
                            break;
                        case 'partially complete':
                            tempElement.textContent = assignee.name + " - 〇";
                            tempElement.classList.add("yellowText");
                            break;
                        case 'complete':
                            tempElement.textContent = assignee.name + " - ✓";
                            tempElement.classList.add("greenText");
                            break;
                    }

                    assigneesContainer.appendChild(tempElement);
                });

                selectedIDInput.forEach(function(selected) {
                    selected.value = elem.dataset.orderId;
                });

                selectedID.value = elem.dataset.id;
                taskStatusSelect.value = elem.dataset.status;

                if (elem.dataset.designAccess == "view & update") {
                    designInputContainer.classList.remove('hidden');
                } else {
                    designInputContainer.classList.add('hidden');
                }
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

    // Show Design logic functionality
    designShowButton.addEventListener('click', function() {
        if (selectedTaskDesign == null) return;

        imageBoxImage.src = selectedTaskDesign;
        imageBox.style.display = 'flex';
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

    // UI Enforcement of file upload
    designInput.addEventListener('change', () => {
        const files = designInput.files;

        if (files.length === 0) return;

        if (files.length > 1) {
            alert("Only one file allowed");
            designInput.value = "";
            return;
        }

        const design = files[0];

        if (!design.type.startsWith("image/")) {
            alert("Only images are allowed");
            designInput.value = "";
            return;
        }
    });
</script>

</html>