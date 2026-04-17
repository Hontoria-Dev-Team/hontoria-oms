<!DOCTYPE html>
<html>

<head>
    <title>Staff Panel - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/StaffPage.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <h1 class="titleLogo minGap tinHeight">
            <img src="../../Shared/Img/PeopleIcon.png" alt="People"> Staff Panel
        </h1>
        <?php include("../Views/.Components/MessageBox.php"); ?>
        <section class="rowLayout flexMax midGap">
            <section class="flexMid roundedMid centerColumnLayout">
                <div class="columnLayout minGap box roundedMid fullHeight fullWidth">
                    <form method="GET" action="index.php?page=staff" class="rowLayout fullWidth minGap">
                        <input type="hidden" name="page" value="staff">
                        <input type="hidden" name="action" value="filter">

                        <div class="iconInput flexMax centerHoriRowLayout">
                            <input type="search" name="search" placeholder="Search Staff" class="fullWidth" value="<?= $search ?>">
                            <img src="../../Shared/Img/MagnifierIcon.png" alt="Magnifier">
                        </div>

                        <select name="status">
                            <option value="" <?= $status === '' ? 'selected' : '' ?>>Any Status</option>
                            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="idle" <?= $status === 'idle' ? 'selected' : '' ?>>Idle</option>
                            <option value="offline" <?= $status === 'offline' ? 'selected' : '' ?>>Offline</option>
                        </select>

                        <select name="role">
                            <option value="">Any Role</option>
                            <option value="layoutArtist">Layout Artist</option>
                            <option value="printer">Printer</option>
                            <option value="seamster">Seamster</option>
                        </select>

                        <input type="submit" value="Search" class="importantInput">
                    </form>
                    <section class="minGap scrollable gridFlexMid regMinPadding" id="staffList">
                        <?php
                        $userRolesMap = [];
                        foreach ($userRoles as $role) {
                            $userRolesMap[$role['userID']][] = $role['name'];
                        }
                        ?>
                        <?php foreach ($staffList as $staff): ?>
                            <?php
                            $fullName = trim("{$staff['firstName']} " . ($staff['middleName'] ? substr($staff['middleName'], 0, 1) . '.' : '') . " {$staff['lastName']}");
                            $taskCount = $userTaskCountMap[$staff['id']] ?? 0;
                            $status = $staff['isOnline'] ? ($taskCount > 0 ? 'Active' : 'Idle') : 'Offline';
                            $statusClass = $staff['isOnline'] ? ($taskCount > 0 ? 'active' : 'idle') : '';
                            $roles = $userRolesMap[$staff['id']] ?? [];
                            $rolesText = !empty($roles) ? implode(', ', $roles) : 'Unset Role';
                            $bgClass = $staff['isOnline'] ? ($taskCount > 0 ? 'yellowTransBG' : 'darkFadedBG') : 'redTransBG';
                            $userImage = isset($accountImageMap[$staff['id']]) ?
                                "../../Storage/AccountImages/" . $accountImageMap[$staff['id']] : "../../Shared/Img/PersonIcon.png";
                            $userImageStyle = isset($accountImageMap[$staff['id']]) ?
                                "imageCoverFull" : "";
                            ?>
                            <div class="minHeight minPadding roundedMin rowLayout minGap flexStatic staffElement shadowed <?= $statusClass ?> <?= $bgClass ?>"
                                data-id="<?= $staff['id'] ?>" data-name="<?= htmlspecialchars($fullName) ?>" data-roles="<?= $rolesText ?>">
                                <div class="flexMid roundedMin centerColumnLayout grayBG shadowed fixedScreen">
                                    <img src="<?= $userImage ?>" alt="User Photo" class="<?= $userImageStyle ?> squareSize">
                                </div>
                                <div class="flexMax centerHoriColumnLayout">
                                    <h5><?= htmlspecialchars($fullName) ?></h5>
                                    <h6 class="capitalFirst">(<?= $rolesText ?>)</h6>
                                    <h6 class="capitalFirst">Tasks: <?= $taskCount ?></h6>

                                </div>
                                <div class="flexMin status roundedMin minPadding centerColumnLayout shadowed">
                                    <h5><?= $status ?></h5>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="tinHeight"></div>
                    </section>
                    <div class="rowLayout minGap souEastAbsolute">
                        <?php if (in_array("canCreateUserAccounts", $_SESSION['permissions'])): ?>
                            <a href="index.php?page=staff&action=create" class="roundedMin centerColumnLayout importantInput regPadding emphasizedText">Create Staff</a>
                        <?php endif; ?>
                        <?php if (in_array("canAlterAccountRoles", $_SESSION['permissions'])): ?>
                            <a href="index.php?page=staff&action=manageRoles" class="roundedMin centerColumnLayout importantInput regPadding emphasizedText">Manage Roles</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="gradientBorderDiag"></div>
            </section>
            <section class="columnLayout midGap flexMin">
                <section class="box centerColumnLayout roundedMid minGap flexMin">
                    <h4 id="selectedStaffName" class="centerHoriRowLayout minGap">No Staff Selected</h4>
                    <b class="leftStart rowLayout tinGap hidden">Roles:
                        <span id="rolesText" class="capitalFirst">Admin, Artist</span>
                    </b>
                    <div class="rowLayout fullWidth minGap hidden" id="staffActions">
                        <button type="button" class="importantInput flexMax" id="modifyRolesButton">Modify Roles</button>
                        <button type="button" class="criticalInput centerColumnLayout" id="deleteButton">
                            <img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors">
                        </button>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
                <section class="box centerColumnLayout roundedMid flexMid noBasis noMinHeight minGap">
                    <h5 class="leftStart">Assigned Tasks:</h5>
                    <div id="taskListContainer" class="scrollable fullDimensions columnLayout minGap">
                        <h2 class="centerMarginsSelf">No Staff Selected</h2>
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
    const staffElements = document.querySelectorAll('.staffElement');
    const nameDisplay = document.getElementById('selectedStaffName');
    const staffActionsstaffActions = document.getElementById('staffActions');
    const rolesText = document.getElementById('rolesText');
    const modifyRolesButton = document.getElementById('modifyRolesButton');
    const deleteButton = document.getElementById('deleteButton');
    const taskListContainer = document.getElementById('taskListContainer');
    const roles = <?php echo json_encode($roleList); ?>;
    const userRoles = <?php echo json_encode($userRoles); ?>;
    const roleGovernance = <?php echo json_encode($roleGovernance); ?>;
    const userProcessTaskList = <?php echo json_encode($userProcessTaskList); ?>;
    const userStatsList = <?php echo json_encode($userStatsList); ?>;
    const userActivityLogsList = <?php echo json_encode($userActivityLogsList); ?>;

    const userRolesMap = {};

    userRoles.forEach(item => {
        if (!userRolesMap[item.userID]) {
            userRolesMap[item.userID] = [];
        }

        userRolesMap[item.userID].push({
            name: item.name,
            roleID: item.roleID
        });
    });

    const userProcessTaskMap = {};

    userProcessTaskList.forEach(item => {
        if (!userProcessTaskMap[item.userID]) {
            userProcessTaskMap[item.userID] = [];
        }

        userProcessTaskMap[item.userID].push({
            status: item.status,
            assignedAt: item.assignedAt,
            orderID: item.orderID,
            customerName: item.customerName,
            subserviceName: item.subserviceName,
            serviceName: item.serviceName,
            processName: item.processName
        });
    });

    const userStatsMap = {};

    userStatsList.forEach(item => {
        userStatsMap[item.userID] = {
            tasksCompleted: item.tasksCompleted,
            tasksCompletedDuration: item.tasksCompletedDuration
        };
    });

    const userActivityLogsMap = {};

    userActivityLogsList.forEach(item => {
        if (!userActivityLogsMap[item.userID]) {
            userActivityLogsMap[item.userID] = [];
        }

        userActivityLogsMap[item.userID].push({
            head: item.head,
            log: item.log,
            color: item.color,
            loggedAt: item.loggedAt
        });
    });

    const noGrants = [];

    roleGovernance.forEach(item => {
        if (item.canGrant == 0) {
            noGrants.push(item.roleSubjectID);
        }
    });

    let name;
    let id;
    let selectedUserRoles;
    let selectedUserTasks;
    let selectedUserStats;
    let selectedUserActivityLogs;
    let governances;
    let governanceRules;
    let tempDiv;
    let tempElement;

    document.addEventListener("DOMContentLoaded", () => {
        confirmationCancel.value = "No Cancel";
    });

    const selectedID = document.createElement("input");
    selectedID.type = "hidden";
    selectedID.name = "selectedID";
    confirmationForm.appendChild(selectedID);

    // Reactive clickable employee data script
    document.addEventListener('DOMContentLoaded', function() {
        staffElements.forEach(function(elem) {
            elem.addEventListener('click', function() {
                name = elem.dataset.name;
                id = elem.dataset.id;

                selectedID.value = elem.dataset.id;

                nameDisplay.innerHTML = name + '<img src="../../Shared/Img/StatsIcon.png" alt="Stats" class="unitHeight clickable" id="userStatsButton">';
                nameDisplay.style.alignSelf = 'baseline';

                selectedUserRoles = [...(userRolesMap[id] || [])];
                selectedUserTasks = [...(userProcessTaskMap[id] || [])];
                selectedUserStats = userStatsMap[id];
                selectedUserActivityLogs = [...(userActivityLogsMap[id] || [])];

                document.getElementById('userStatsButton').addEventListener('click', function() {
                    confirmationTitle.innerHTML = "Staff Performance Statistics";
                    confirmationText.innerHTML = "Here the performance statistics of " + name + ".";
                    confirmationSubmit.classList.add("hidden");

                    tempDiv = document.createElement("div");
                    tempDiv.className = "tempElement";
                    confirmationForm.appendChild(tempDiv);

                    tempElement = document.createElement("h5");
                    tempElement.textContent = 'Tasks Completed (#): ' + selectedUserStats.tasksCompleted;
                    tempDiv.appendChild(tempElement);

                    const avgTaskDuration = selectedUserStats.tasksCompleted != 0 ? (selectedUserStats.tasksCompletedDuration / selectedUserStats.tasksCompleted).toFixed(2) : 0;

                    tempElement = document.createElement("h5");
                    tempElement.textContent = 'Average Task Duration: ' + avgTaskDuration + ' minutes';
                    tempDiv.appendChild(tempElement);

                    tempElement = document.createElement("h4");
                    tempElement.textContent = 'User Activity Log: ';
                    tempDiv.appendChild(tempElement);

                    const taskHistoryContainer = document.createElement("div");
                    taskHistoryContainer.className = "maxMaxHeight scrollable regMinPadding columnLayout minGap";
                    tempDiv.appendChild(taskHistoryContainer);

                    selectedUserActivityLogs.forEach((activity) => {
                        tempElement = document.createElement("div");
                        tempElement.className = "centerColumnLayout roundedTin regTinPadding shadowed fitHeight fullWidth";
                        tempElement.innerHTML = `
                            <h5 class="centerText minHoriPadding">${activity.log}</h5>
                            <h6>${formatDateTime(activity.loggedAt)}</h6>
                        `;
                        taskHistoryContainer.appendChild(tempElement);

                        switch (activity.color) {
                            case 'red':
                                tempElement.classList.add("redTransBG", "redBorder");
                                break;
                            case 'yellow':
                                tempElement.classList.add("yellowTransBG", "yellowBorder");
                                break;
                            case 'green':
                                tempElement.classList.add("greenTransBG", "greenBorder");
                                break;
                            default:
                                tempElement.classList.add("darkFadedBG", "bordered");
                                break;
                        }
                    });

                    if (selectedUserActivityLogs.length == 0) {
                        tempElement = document.createElement("div");
                        tempElement.className = "centerColumnLayout roundedTin regTinPadding shadowed fullWidth darkFadedBG bordered";
                        tempElement.innerHTML = `<h5 class="centerText minHoriPadding">No User Activity</h5>`;
                        taskHistoryContainer.appendChild(tempElement);
                    }

                    confirmation.style.display = 'flex';
                });

                governances = roleGovernance.filter(gov =>
                    selectedUserRoles.some(role => role.roleID === gov.roleSubjectID)
                );

                governanceRules = {
                    canGrant: governances.every(role => role.canGrant == 1) ? 1 : 0,
                    canRevoke: governances.every(role => role.canRevoke == 1) ? 1 : 0,
                    canAlter: governances.every(role => role.canAlter == 1) ? 1 : 0,
                    canDelete: governances.every(role => role.canDelete == 1) ? 1 : 0
                };

                rolesText.parentElement.classList.remove("hidden");
                staffActions.classList.remove("hidden");

                if (governanceRules.canGrant || governanceRules.canRevoke) {
                    modifyRolesButton.classList.remove("unclickable", "faded");
                } else {
                    modifyRolesButton.classList.add("unclickable", "faded");
                }

                if (governanceRules.canDelete) {
                    deleteButton.classList.remove("unclickable", "faded");
                } else {
                    deleteButton.classList.add("unclickable", "faded");
                }

                rolesText.textContent = elem.dataset.roles;

                showTasks();
            });
        });
    });

    // Task List Display logic function
    function showTasks() {
        taskListContainer.innerHTML = '';

        selectedUserTasks.forEach((task) => {
            tempElement = document.createElement("div");
            tempElement.className = "centerHoriRowLayout minGap tinGap minPadding roundedMin shadowed";

            let headerClass;

            switch (task.status) {
                case 'pending':
                    tempElement.classList.add('redTransBG', 'redBorder');
                    headerClass = 'redBG';
                    break;
                case 'partially complete':
                    tempElement.classList.add('yellowTransBG', 'yellowBorder');
                    headerClass = 'yellowBG';
                    break;
                case 'complete':
                    tempElement.classList.add('greenTransBG', 'greenBorder');
                    headerClass = 'greenBG';
                    break;
            }

            tempElement.innerHTML = `
                <h5 class="${headerClass} whiteText roundedMin minPadding shadowed tinWidth centerText">Order #${task.orderID}</h5>
                <div class="columnLayout">
                    <h6 class="capitalFirst">Status: ${task.status}</h6>
                    <h6>Service: ${task.serviceName}</h6>
                    <h6>Subservice: ${task.subserviceName}</h6>
                    <h6>Task: ${task.processName}</h6>
                    <h6>Customer: ${task.customerName}</h6>
                    <h6>Assigned At: ${formatDateTime(task.assignedAt)}</h6>
                </div>
            `;
            taskListContainer.appendChild(tempElement);
        });

        if (selectedUserTasks.length == 0) {
            tempElement = document.createElement("h2");
            tempElement.className = "centerMarginsSelf";
            tempElement.textContent = 'No Tasks Assigned';
            taskListContainer.appendChild(tempElement);
        }
    }

    // Delete employee confirmation and logic script
    const deletedID = document.createElement("input");
    deletedID.type = "hidden";
    deletedID.name = "deletedID";
    confirmationForm.appendChild(deletedID);

    deleteButton.addEventListener('click', function() {
        confirmationForm.action = "index.php?page=staff&action=delete"

        confirmationTitle.innerHTML = "Delete Account?";
        confirmationText.innerHTML = "Are you sure to delete the account of:<br>" + name + "?";
        confirmationSubmit.value = "Yes Delete";
        confirmationSubmit.classList.remove("yellowBG", "whiteText", "noBorder");

        deletedID.value = id;

        confirmation.style.display = 'flex';
    });

    // Change User Role Box Function logic
    let currentRolesContainer;
    let choiceRolesContainer;
    let currentRoles;
    let choiceRoles;
    let tempRoleDiv;
    let tempRoleTitle;
    let tempRoleXButton;

    modifyRolesButton.addEventListener('click', function() {
        confirmationForm.action = "index.php?page=staff&action=setRoles"
        confirmationForm.parentElement.classList.remove("minGap");

        confirmationTitle.innerHTML = "Modify Account Roles";
        confirmationText.innerHTML = "";

        currentRoles = [...(userRolesMap[id] || [])];

        choiceRolesContainer = document.createElement("div");
        choiceRolesContainer.id = "choiceRolesContainer";
        choiceRolesContainer.className = 'gridCenterVertFlex minGap tempElement';
        confirmationForm.appendChild(choiceRolesContainer);

        tempElement = document.createElement("b");
        tempElement.textContent = "All Roles:";
        tempElement.classList.add("tempElement");
        confirmationForm.appendChild(tempElement);

        currentRolesContainer = document.createElement("div");
        currentRolesContainer.id = "currentRolesContainer";
        currentRolesContainer.className = 'gridCenterVertFlex minGap tempElement';
        confirmationForm.appendChild(currentRolesContainer);

        setAssignedRoles();
        setChoiceRoles();

        tempElement = document.createElement("b");
        tempElement.textContent = "Assigned Roles:";
        tempElement.classList.add("tempElement");
        confirmationForm.appendChild(tempElement);

        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");
        confirmationSubmit.value = "Confirm Changes";

        confirmation.style.display = 'flex';
    });

    function setAssignedRoles() {
        currentRolesContainer.innerHTML = '';

        document.querySelectorAll('.roleHiddenInput').forEach(function(elem) {
            elem.remove();
        });

        if (currentRoles.length == 0) {
            tempElement = document.createElement("h2");
            tempElement.textContent = "Unset";
            tempElement.className = "centerText";
            currentRolesContainer.appendChild(tempElement);
        }

        for (let i = 0; i < currentRoles.length; i++) {
            tempRoleDiv = document.createElement("div");
            tempRoleDiv.className = "noShrink roundedMin centerRowLayout minGap yellowTransBG regMinPadding bordered";

            tempRoleTitle = document.createElement("b");
            tempRoleTitle.className = "flexMax centerText capitalFirst";
            tempRoleTitle.textContent = currentRoles[i].name;
            tempRoleDiv.appendChild(tempRoleTitle);

            if (governanceRules.canRevoke) {
                tempRoleXButton = document.createElement("a");
                tempRoleXButton.className = "squareSize unitHeight centerColumnLayout roleRemove";
                tempRoleXButton.innerHTML = '<img src="../../Shared/Img/XIcon.png" alt="X">';
                tempRoleXButton.dataset.index = i;
                tempRoleDiv.appendChild(tempRoleXButton);
            }

            roleHiddenInput = document.createElement("input");
            roleHiddenInput.type = "hidden";
            roleHiddenInput.name = "roleHiddenInput[]";
            roleHiddenInput.className = "roleHiddenInput";
            roleHiddenInput.value = currentRoles[i].roleID;
            confirmationForm.appendChild(roleHiddenInput);

            currentRolesContainer.appendChild(tempRoleDiv);
        };

        document.querySelectorAll('.roleRemove').forEach(function(elem) {
            elem.addEventListener('click', function() {
                currentRoles.splice(elem.dataset.index, 1);
                setAssignedRoles();
                setChoiceRoles();
            });
        });
    }

    function setChoiceRoles() {
        choiceRolesContainer.innerHTML = '';
        choiceRoles = [];

        roles.forEach((item) => {
            if (currentRoles.some(role => role.roleID === item.id)) return;

            choiceRoles.push(item);
        });

        if (choiceRoles.length == 0) {
            tempElement = document.createElement("h2");
            tempElement.textContent = "No more available Roles";
            tempElement.className = "centerText";
            choiceRolesContainer.appendChild(tempElement);
        }

        for (let i = 0; i < choiceRoles.length; i++) {
            if (noGrants.includes(choiceRoles[i].id)) continue;

            tempRoleDiv = document.createElement("div");
            tempRoleDiv.className = "noShrink roundedMin centerRowLayout minGap darkFadedBG regMinPadding bordered clickable choiceRole";
            tempRoleDiv.dataset.index = i;
            tempRoleDiv.dataset.name = choiceRoles[i].name;
            tempRoleDiv.dataset.id = choiceRoles[i].id;

            tempRoleTitle = document.createElement("b");
            tempRoleTitle.className = "flexMax centerText capitalFirst";
            tempRoleTitle.textContent = choiceRoles[i].name;
            tempRoleDiv.appendChild(tempRoleTitle);

            choiceRolesContainer.appendChild(tempRoleDiv);
        };

        document.querySelectorAll('.choiceRole').forEach(function(elem) {
            elem.addEventListener('click', function() {
                currentRoles.push({
                    name: elem.dataset.name,
                    roleID: elem.dataset.id
                });
                setAssignedRoles();
                setChoiceRoles();
            });
        });
    }

    // Added cancellation events
    confirmationCancel.addEventListener('click', function() {
        confirmationForm.parentElement.classList.add("minGap");
        confirmationSubmit.classList.remove("hidden");

        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });
    });

    confirmationBG.addEventListener('click', function() {
        confirmationForm.parentElement.classList.add("minGap");
        confirmationSubmit.classList.remove("hidden");

        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });
    });
</script>
<!-- <script src="../.JS/AutoRefresher.js"></script> -->

</html>