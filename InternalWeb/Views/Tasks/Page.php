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
                                        data-id="<?= $task['id'] ?>" data-order-id="<?= $task['orderID'] ?>" data-status="<?= $task['taskStatus'] ?>"
                                        data-design-access="<?= $task['designAccess'] ?>">
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
                <div class="rowLayout roundedMid midGap flexMid noFlexBasis noMinHeight">
                    <section class="box centerColumnLayout tinGap flexMid roundedMid">
                        <div class="columnLayout tinGap fullDimensions">
                            <h3>Assigned to Task:</h3>
                            <div class="columnLayout scrollable flexMax noFlexBasis noMinHeight minGap" id="assigneesContainer">
                                <b class="centerMarginsSelf">No Task Selected</b>
                            </div>
                        </div>
                        <div class="gradientBorderDiag"></div>
                    </section>
                    <section class="box centerColumnLayout tinGap flexMax roundedMid">
                        <div class="columnLayout minGap fullDimensions">
                            <div class="centerHoriRowLayout">
                                <h3 class="flexMax">Tasks Objectives</h3>
                                <a class="midHoriPadding shadowed redBG roundedMin emphasizedText hidden" id="statusButton">Pending</a>
                            </div>
                            <b class="centerMarginsSelf noSelectText">No Task Selected</b>
                            <div class="centerHoriRowLayout minGap duoHeight noSelectHidden hidden">
                                <div class="bordered flexMin fullHeight roundedMin centerHoriRowLayout shadowed fixedScreen clickable" id="designButton">
                                    <b class="flexMax centerText">Unset</b>
                                    <div class="squareSize fullHeight centerColumnLayout darkBG shadowed">
                                        <img src="../../Shared/Img/PhotoIcon.png" alt="Photo" class="invertColors">
                                    </div>
                                </div>
                                <div class="redBorder flexMin fullHeight roundedMin centerHoriRowLayout shadowed fixedScreen">
                                    <b class="flexMax centerText redText">Unapproved</b>
                                    <div class="squareSize fullHeight centerColumnLayout redBG shadowed">
                                        <img src="../../Shared/Img/BarsIcon.png" alt="Bars" class="invertColors">
                                    </div>
                                </div>
                            </div>
                            <div class="flexMax bordered roundedMin centerHoriRowLayout shadowed fixedScreen noSelectHidden hidden">
                                <div class="scrollable fullHeight flexMax gridCenterFlex minGap regMinPadding" id="orderGroupsContainer"></div>
                                <b class="squareSize fullHeight centerColumnLayout darkBG shadowed whiteText regMinPadding">Groups</b>
                            </div>
                        </div>
                        <div class="gradientBorderDiag"></div>
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
    const statusButton = document.getElementById('statusButton');
    const designButton = document.getElementById('designButton');
    const orderGroupsContainer = document.getElementById('orderGroupsContainer');
    const assigneeList = <?php echo json_encode($assigneeList); ?>;
    const designList = <?php echo json_encode($designList); ?>;
    const orderGroupList = <?php echo json_encode($orderGroupList); ?>;

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

    // Due time calculation
    document.querySelectorAll('.dueInText').forEach(function(elem) {
        elem.textContent = elem.dataset.dueDate == '0000-00-00 00:00:00' ? "No due date" : getDueTime(elem.dataset.dueDate) + " (" + formatDate(elem.dataset.dueDate) + ")";
    });

    // Reactive clickable process task data script
    let selectedTaskAssignees;
    let selectedTaskDesign;
    let selectedTaskDesignApproval;
    let selectedTaskGroups;

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

                assigneesContainer.innerHTML = '';
                selectedTaskAssignees.forEach(function(assignee) {
                    tempElement = document.createElement("b");
                    tempElement.textContent = assignee.name;
                    tempElement.className = "centerText regMinPadding shadowed roundedTin";

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

                if (selectedTaskDesignApproval == 0) {
                    designButton.classList.add('redBorder', 'redText');
                    designButton.classList.remove('bordered', 'greenBorder', 'greenText');
                    designButton.querySelector("div").classList.add('redBG');
                    designButton.querySelector("div").classList.remove('darkBG', 'greenBG');

                    designButton.querySelector("b").textContent = 'Unapproved';
                } else if (selectedTaskDesignApproval == 1) {
                    designButton.classList.add('greenBorder', 'greenText');
                    designButton.classList.remove('bordered', 'redBorder', 'redText');
                    designButton.querySelector("div").classList.add('greenBG');
                    designButton.querySelector("div").classList.remove('darkBG', 'redBG');

                    designButton.querySelector("b").textContent = 'Approved';
                } else {
                    designButton.classList.add('bordered');
                    designButton.classList.remove('redBorder', 'redText', 'greenBorder', 'greenText');
                    designButton.querySelector("div").classList.add('darkBG');
                    designButton.querySelector("div").classList.remove('redBG', 'greenBG');

                    designButton.querySelector("b").textContent = 'Unset';
                }

                if (elem.dataset.designAccess == "view & update") {
                    designButton.classList.remove('hidden');

                    if (selectedTaskDesignApproval == 1) {
                        statusButton.classList.remove('unclickable', 'faded');
                    } else {
                        statusButton.classList.add('unclickable', 'faded');
                    }
                } else {
                    designButton.classList.add('hidden');
                    statusButton.classList.remove('unclickable', 'faded');
                }

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
        confirmationForm.action = "index.php?page=tasks&action=updateTaskStatus"

        tempElement = document.createElement("input");
        tempElement.type = "submit";
        tempElement.name = "taskStatus";
        tempElement.className = "tempElement tinHeight redTransBG redBorder centerColumnLayout capitalFirst emphasizedText shadowed";
        tempElement.value = "pending";
        confirmationForm.appendChild(tempElement);

        if (statusButton.dataset.status == tempElement.value) {
            tempElement.classList.add("hidden");
        }

        tempElement = document.createElement("input");
        tempElement.type = "submit";
        tempElement.name = "taskStatus";
        tempElement.className = "tempElement tinHeight yellowTransBG yellowBorder centerColumnLayout capitalFirst emphasizedText shadowed";
        tempElement.value = "partially complete";
        confirmationForm.appendChild(tempElement);

        if (statusButton.dataset.status == tempElement.value) {
            tempElement.classList.add("hidden");
        }

        tempElement = document.createElement("input");
        tempElement.type = "submit";
        tempElement.name = "taskStatus";
        tempElement.className = "tempElement tinHeight greenTransBG greenBorder centerColumnLayout capitalFirst emphasizedText shadowed";
        tempElement.value = "complete";
        confirmationForm.appendChild(tempElement);

        if (statusButton.dataset.status == tempElement.value) {
            tempElement.classList.add("hidden");
        }

        confirmationTitle.innerHTML = "Update Task Status";
        confirmationText.innerHTML = 'Click on the status you want your task to update to.';
        confirmationSubmit.classList.add("hidden");

        confirmation.style.display = 'flex';
    });

    // Design Box logic function
    let uploadedImage;

    designButton.addEventListener('click', function() {
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
        tempDiv.className = "fullWidth tempElement hidden";
        tempDiv.style.maxHeight = "50vh";
        tempDiv.style.overflowY = "scroll";
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

    // Show order groups function logic
    function showOrderGroups() {
        orderGroupsContainer.innerHTML = '';

        selectedTaskGroups.forEach(group => {
            tempElement = document.createElement("b");
            tempElement.className = "noShrink fitHeight roundedMin centerRowLayout minGap darkTransBG regMinPadding bordered";
            tempElement.textContent = group.description + ": " + group.quantity;
            orderGroupsContainer.appendChild(tempElement);
        });
    }

    // Added cancellation events
    confirmationCancel.addEventListener('click', function() {
        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });

        confirmationForm.removeAttribute("enctype");
        confirmationSubmit.classList.remove("hidden");
    });

    confirmationBG.addEventListener('click', function() {
        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });

        confirmationForm.removeAttribute("enctype");
        confirmationSubmit.classList.remove("hidden");
    });
</script>

</html>