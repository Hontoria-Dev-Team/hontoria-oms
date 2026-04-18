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
            <div class="rowLayout minGap flexMax contentFlexEnd">
                <?php if (in_array("canCreateUserAccounts", $_SESSION['permissions'])): ?>
                    <a href="index.php?page=staff&action=create" class="roundedMin centerColumnLayout importantInput regPadding emphasizedText shadowed">Create Staff</a>
                <?php endif; ?>
                <?php if (in_array("canAlterAccountRoles", $_SESSION['permissions'])): ?>
                    <a href="index.php?page=staff&action=manageRoles" class="roundedMin centerColumnLayout importantInput regPadding emphasizedText shadowed">Manage Roles</a>
                <?php endif; ?>
            </div>
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
                    <section class="minGap scrollable gridFlexMid regMinPadding flexMax contentFlexStart" id="staffList">
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
                                data-id="<?= $staff['id'] ?>" data-name="<?= htmlspecialchars($fullName) ?>" data-roles="<?= $rolesText ?>"
                                data-phone="<?= $staff['phone'] ?>" data-email="<?= $staff['email'] ?>">
                                <div class="flexMid roundedMin centerColumnLayout grayBG shadowed fixedScreen">
                                    <img src="<?= $userImage ?>" alt="User Photo" class="<?= $userImageStyle ?> squareSize">
                                </div>
                                <div class="flexMax centerHoriColumnLayout">
                                    <h5><?= htmlspecialchars($fullName) ?></h5>
                                    <h6 class="capitalFirst">Last Online: 15 minutes ago</h6>
                                    <h6 class="capitalFirst">(<?= $rolesText ?>)</h6>
                                    <h6 class="capitalFirst">Tasks: <?= $taskCount ?></h6>
                                </div>
                                <div class="flexMin status roundedMin minPadding centerColumnLayout shadowed">
                                    <h5><?= $status ?></h5>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </section>
                </div>
                <div class="gradientBorderDiag"></div>
            </section>
            <section class="columnLayout midGap flexMin">
                <section class="box centerColumnLayout roundedMid minGap flexMin" id="userInfoContainer">
                    <h4>No Staff Selected</h4>
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
<script src="../.JS/TimeHelpers.js"></script>
<script>
    /**
     * Staff Management Page Script
     * Handles staff selection, role assignment, task tracking, performance stats, and activity logs.
     * Depends on global confirmation dialog elements (confirmationForm, confirmationTitle, etc.)
     */

    // ================================
    // DOM Elements (static, can be const)
    // ================================
    const staffElements = document.querySelectorAll('.staffElement');
    const userInfoContainer = document.getElementById('userInfoContainer');
    const taskListContainer = document.getElementById('taskListContainer');

    // ================================
    // Server Data (injected via PHP)
    // ================================
    const roles = <?php echo json_encode($roleList); ?>; // [{id, name}, ...]
    const userRoles = <?php echo json_encode($userRoles); ?>; // [{userID, roleID, name}, ...]
    const roleGovernance = <?php echo json_encode($roleGovernance); ?>; // [{roleSubjectID, roleAgentID, canGrant, canRevoke, canAlter, canDelete}, ...]
    const userProcessTaskList = <?php echo json_encode($userProcessTaskList); ?>; // array of task objects
    const userStatsList = <?php echo json_encode($userStatsList); ?>; // [{userID, tasksCompleted, tasksCompletedDuration}, ...]
    const userActivityLogsList = <?php echo json_encode($userActivityLogsList); ?>; // [{userID, head, log, color, loggedAt}, ...]

    // ================================
    // Build Lookup Maps
    // ================================
    const userRolesMap = {}; // userID -> array of {name, roleID}
    userRoles.forEach(item => {
        if (!userRolesMap[item.userID]) userRolesMap[item.userID] = [];
        userRolesMap[item.userID].push({
            name: item.name,
            roleID: item.roleID
        });
    });

    const userProcessTaskMap = {}; // userID -> array of task objects
    userProcessTaskList.forEach(item => {
        if (!userProcessTaskMap[item.userID]) userProcessTaskMap[item.userID] = [];
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

    const userStatsMap = {}; // userID -> {tasksCompleted, tasksCompletedDuration}
    userStatsList.forEach(item => {
        userStatsMap[item.userID] = {
            tasksCompleted: item.tasksCompleted,
            tasksCompletedDuration: item.tasksCompletedDuration
        };
    });

    const userActivityLogsMap = {}; // userID -> array of activity objects
    userActivityLogsList.forEach(item => {
        if (!userActivityLogsMap[item.userID]) userActivityLogsMap[item.userID] = [];
        userActivityLogsMap[item.userID].push({
            head: item.head,
            log: item.log,
            color: item.color,
            loggedAt: item.loggedAt
        });
    });

    // ================================
    // Helper: Determine which roles cannot be granted (canGrant = 0)
    // ================================
    const noGrants = roleGovernance.filter(g => g.canGrant == 0).map(g => g.roleSubjectID);

    // ================================
    // Global State Variables (current selected user)
    // ================================
    let currentUser = {
        id: null,
        name: '',
        phone: '',
        email: '',
        roles: [],
        tasks: [],
        stats: null,
        activityLogs: []
    };

    let governanceRules = {
        canGrant: 0,
        canRevoke: 0,
        canAlter: 0,
        canDelete: 0
    };

    // ================================
    // Reusable temporary variables (matching the pattern from Services page)
    // ================================
    let tempElement;
    let tempDiv;

    // ================================
    // UI Helpers
    // ================================
    function UpdateUserInfoDisplay(elem) {
        const name = elem.dataset.name;
        const id = elem.dataset.id;
        const phone = elem.dataset.phone;
        const email = elem.dataset.email;
        const rolesText = elem.dataset.roles;

        userInfoContainer.innerHTML = `
        <h5 class="leftStart centerHoriRowLayout minGap">
            ${name} <img src="../../Shared/Img/StatsIcon.png" alt="Stats" class="unitHeight clickable" id="userStatsButton">
        </h5>
        <h6 class="leftStart rowLayout tinGap">Roles:
            <span id="rolesText" class="capitalFirst">${rolesText}</span>
        </h6>
        <span class="leftStart">
            <h6 class="centerHoriRowLayout tinGap">
                <img src="../../Shared/Img/PhoneIcon.png" alt="Phone" class="unitHeight">
                : ${phone}
            </h6>
            <h6 class="centerHoriRowLayout tinGap">
                <img src="../../Shared/Img/MailIcon.png" alt="Mail" class="unitHeight">
                : ${email}
            </h6>
        </span>
        <div class="rowLayout fullWidth minGap" id="staffActions">
            <button type="button" class="importantInput flexMax" id="modifyRolesButton">Modify Roles</button>
            <button type="button" class="criticalInput centerColumnLayout" id="deleteButton">
                <img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors">
            </button>
        </div>
        <div class="gradientBorderDiag"></div>
    `;

        // Store current user data
        currentUser.id = id;
        currentUser.name = name;
        currentUser.phone = phone;
        currentUser.email = email;
        currentUser.roles = [...(userRolesMap[id] || [])];
        currentUser.tasks = [...(userProcessTaskMap[id] || [])];
        currentUser.stats = userStatsMap[id] || {
            tasksCompleted: 0,
            tasksCompletedDuration: 0
        };
        currentUser.activityLogs = [...(userActivityLogsMap[id] || [])];

        // Update governance rules based on current user's roles
        const userRoleIds = currentUser.roles.map(r => r.roleID);
        const relevantGovernance = roleGovernance.filter(gov => userRoleIds.includes(gov.roleSubjectID));
        governanceRules = {
            canGrant: relevantGovernance.every(g => g.canGrant == 1) ? 1 : 0,
            canRevoke: relevantGovernance.every(g => g.canRevoke == 1) ? 1 : 0,
            canAlter: relevantGovernance.every(g => g.canAlter == 1) ? 1 : 0,
            canDelete: relevantGovernance.every(g => g.canDelete == 1) ? 1 : 0
        };

        // Enable/disable action buttons based on permissions
        const modifyBtn = document.getElementById('modifyRolesButton');
        const deleteBtn = document.getElementById('deleteButton');
        if (governanceRules.canGrant || governanceRules.canRevoke) {
            modifyBtn.classList.remove('unclickable', 'faded');
        } else {
            modifyBtn.classList.add('unclickable', 'faded');
        }
        if (governanceRules.canDelete) {
            deleteBtn.classList.remove('unclickable', 'faded');
        } else {
            deleteBtn.classList.add('unclickable', 'faded');
        }

        // Attach event listeners for this user
        document.getElementById('userStatsButton').addEventListener('click', ShowUserStatsModal);
        modifyBtn.addEventListener('click', ShowRoleModificationModal);
        deleteBtn.addEventListener('click', () => ShowDeleteConfirmation(id, name));

        ShowUserTasks();
    }

    // ================================
    // Display User Tasks
    // ================================
    function ShowUserTasks() {
        taskListContainer.innerHTML = '';

        if (currentUser.tasks.length === 0) {
            tempElement = document.createElement('h2');
            tempElement.className = 'centerMarginsSelf';
            tempElement.textContent = 'No Tasks Assigned';
            taskListContainer.appendChild(tempElement);
            return;
        }

        currentUser.tasks.forEach(task => {
            tempDiv = document.createElement('div');
            tempDiv.className = 'centerHoriRowLayout minGap tinGap minPadding roundedMin shadowed';

            let headerClass = '';
            if (task.status === 'pending') {
                tempDiv.classList.add('redTransBG', 'redBorder');
                headerClass = 'redBG';
            } else if (task.status === 'partially complete') {
                tempDiv.classList.add('yellowTransBG', 'yellowBorder');
                headerClass = 'yellowBG';
            } else if (task.status === 'complete') {
                tempDiv.classList.add('greenTransBG', 'greenBorder');
                headerClass = 'greenBG';
            }

            tempDiv.innerHTML = `
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
            taskListContainer.appendChild(tempDiv);
        });
    }

    // ================================
    // Stats Modal (Performance + Activity Logs)
    // ================================
    function ShowUserStatsModal() {
        confirmationTitle.innerHTML = 'Staff Performance Statistics';
        confirmationText.innerHTML = `Here are the performance statistics of ${currentUser.name}.`;
        confirmationSubmit.classList.add('hidden');

        // Remove previous temporary elements
        document.querySelectorAll('.tempElement').forEach(el => el.remove());

        tempDiv = document.createElement('div');
        tempDiv.className = 'tempElement';
        confirmationForm.appendChild(tempDiv);

        const completed = currentUser.stats.tasksCompleted || 0;
        const totalDuration = currentUser.stats.tasksCompletedDuration || 0;
        const avgDuration = completed > 0 ? (totalDuration / completed).toFixed(2) : 0;

        tempElement = document.createElement('h5');
        tempElement.textContent = `Tasks Completed (#): ${completed}`;
        tempDiv.appendChild(tempElement);

        tempElement = document.createElement('h5');
        tempElement.textContent = `Average Task Duration: ${avgDuration} minutes`;
        tempDiv.appendChild(tempElement);

        tempElement = document.createElement('h4');
        tempElement.textContent = 'User Activity Log:';
        tempDiv.appendChild(tempElement);

        const logContainer = document.createElement('div');
        logContainer.className = 'maxMaxHeight scrollable regMinPadding columnLayout minGap';
        tempDiv.appendChild(logContainer);

        if (currentUser.activityLogs.length === 0) {
            tempDiv = document.createElement('div');
            tempDiv.className = 'centerColumnLayout roundedTin regTinPadding shadowed fullWidth darkFadedBG bordered';
            tempDiv.innerHTML = '<h5 class="centerText minHoriPadding">No User Activity</h5>';
            logContainer.appendChild(tempDiv);
        } else {
            currentUser.activityLogs.forEach(activity => {
                tempDiv = document.createElement('div');
                tempDiv.className = 'centerColumnLayout roundedTin regTinPadding shadowed fitHeight fullWidth';
                tempDiv.innerHTML = `
                <h5 class="centerText minHoriPadding">${activity.log}</h5>
                <h6>${formatDateTime(activity.loggedAt)}</h6>
            `;
                // Apply color class
                if (activity.color === 'red') tempDiv.classList.add('redTransBG', 'redBorder');
                else if (activity.color === 'yellow') tempDiv.classList.add('yellowTransBG', 'yellowBorder');
                else if (activity.color === 'green') tempDiv.classList.add('greenTransBG', 'greenBorder');
                else tempDiv.classList.add('darkFadedBG', 'bordered');
                logContainer.appendChild(tempDiv);
            });
        }

        confirmation.style.display = 'flex';
    }

    // ================================
    // Role Modification Modal
    // ================================
    function ShowRoleModificationModal() {
        if (!(governanceRules.canGrant || governanceRules.canRevoke)) return;

        confirmationForm.action = 'index.php?page=staff&action=setRoles';
        confirmationForm.parentElement.classList.remove('minGap');
        confirmationTitle.innerHTML = 'Modify Account Roles';
        confirmationText.innerHTML = '';
        confirmationSubmit.classList.add('yellowBG', 'whiteText', 'noBorder');
        confirmationSubmit.value = 'Confirm Changes';

        // Remove old temp elements
        document.querySelectorAll('.tempElement').forEach(el => el.remove());

        // Create containers
        const choiceRolesContainer = document.createElement('div');
        choiceRolesContainer.id = 'choiceRolesContainer';
        choiceRolesContainer.className = 'gridCenterVertFlex minGap tempElement';
        confirmationForm.appendChild(choiceRolesContainer);

        tempElement = document.createElement('b');
        tempElement.textContent = 'All Roles:';
        tempElement.classList.add('tempElement');
        confirmationForm.appendChild(tempElement);

        const currentRolesContainer = document.createElement('div');
        currentRolesContainer.id = 'currentRolesContainer';
        currentRolesContainer.className = 'gridCenterVertFlex minGap tempElement';
        confirmationForm.appendChild(currentRolesContainer);

        tempElement = document.createElement('b');
        tempElement.textContent = 'Assigned Roles:';
        tempElement.classList.add('tempElement');
        confirmationForm.appendChild(tempElement);

        // Store current roles array (mutable)
        let currentRoles = [...currentUser.roles];

        // Helper to render assigned roles
        function RenderAssignedRoles() {
            currentRolesContainer.innerHTML = '';
            // Remove any hidden inputs with same name (from previous renders)
            document.querySelectorAll('.roleHiddenInput').forEach(el => el.remove());

            if (currentRoles.length === 0) {
                tempElement = document.createElement('h2');
                tempElement.textContent = 'Unset';
                tempElement.className = 'centerText';
                currentRolesContainer.appendChild(tempElement);
            } else {
                currentRoles.forEach((role, idx) => {
                    tempDiv = document.createElement('div');
                    tempDiv.className = 'noShrink roundedMin centerRowLayout minGap yellowTransBG regMinPadding bordered';

                    tempElement = document.createElement('b');
                    tempElement.className = 'flexMax centerText capitalFirst';
                    tempElement.textContent = role.name;
                    tempDiv.appendChild(tempElement);

                    if (governanceRules.canRevoke) {
                        const removeBtn = document.createElement('a');
                        removeBtn.className = 'squareSize unitHeight centerColumnLayout roleRemove';
                        removeBtn.innerHTML = '<img src="../../Shared/Img/XIcon.png" alt="X">';
                        removeBtn.dataset.index = idx;
                        removeBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            currentRoles.splice(idx, 1);
                            RenderAssignedRoles();
                            RenderAvailableRoles();
                        });
                        tempDiv.appendChild(removeBtn);
                    }

                    // Hidden input to submit role ID
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'roleHiddenInput[]';
                    hiddenInput.className = 'roleHiddenInput';
                    hiddenInput.value = role.roleID;
                    confirmationForm.appendChild(hiddenInput);

                    currentRolesContainer.appendChild(tempDiv);
                });
            }
        }

        // Helper to render available roles (not yet assigned)
        function RenderAvailableRoles() {
            choiceRolesContainer.innerHTML = '';
            const assignedIds = new Set(currentRoles.map(r => r.roleID));
            const availableRoles = roles.filter(r => !assignedIds.has(r.id) && !noGrants.includes(r.id));

            if (availableRoles.length === 0) {
                tempElement = document.createElement('h2');
                tempElement.textContent = 'No more available Roles';
                tempElement.className = 'centerText';
                choiceRolesContainer.appendChild(tempElement);
            } else {
                availableRoles.forEach(role => {
                    tempDiv = document.createElement('div');
                    tempDiv.className = 'noShrink roundedMin centerRowLayout minGap darkFadedBG regMinPadding bordered clickable choiceRole';
                    tempDiv.dataset.id = role.id;
                    tempDiv.dataset.name = role.name;

                    tempElement = document.createElement('b');
                    tempElement.className = 'flexMax centerText capitalFirst';
                    tempElement.textContent = role.name;
                    tempDiv.appendChild(tempElement);

                    tempDiv.addEventListener('click', () => {
                        if (governanceRules.canGrant) {
                            currentRoles.push({
                                name: role.name,
                                roleID: role.id
                            });
                            RenderAssignedRoles();
                            RenderAvailableRoles();
                        }
                    });
                    choiceRolesContainer.appendChild(tempDiv);
                });
            }
        }

        RenderAssignedRoles();
        RenderAvailableRoles();
        confirmation.style.display = 'flex';
    }

    // ================================
    // Delete User Confirmation
    // ================================
    function ShowDeleteConfirmation(userId, userName) {
        confirmationForm.action = 'index.php?page=staff&action=delete';
        confirmationTitle.innerHTML = 'Delete Account?';
        confirmationText.innerHTML = `Are you sure to delete the account of:<br>${userName}?`;
        confirmationSubmit.value = 'Yes Delete';
        confirmationSubmit.classList.remove('yellowBG', 'whiteText', 'noBorder');

        // Set hidden input for deleted ID
        let deletedIdInput = document.querySelector('input[name="deletedID"]');
        if (!deletedIdInput) {
            deletedIdInput = document.createElement('input');
            deletedIdInput.type = 'hidden';
            deletedIdInput.name = 'deletedID';
            confirmationForm.appendChild(deletedIdInput);
        }
        deletedIdInput.value = userId;

        confirmation.style.display = 'flex';
    }

    // ================================
    // Event Listeners for Staff Selection
    // ================================
    document.addEventListener('DOMContentLoaded', () => {
        // Set default cancel button text
        confirmationCancel.value = 'No Cancel';

        // Create persistent hidden input for selected ID
        let selectedIdInput = document.querySelector('input[name="selectedID"]');
        if (!selectedIdInput) {
            selectedIdInput = document.createElement('input');
            selectedIdInput.type = 'hidden';
            selectedIdInput.name = 'selectedID';
            confirmationForm.appendChild(selectedIdInput);
        }

        // Attach click handlers to each staff element
        staffElements.forEach(elem => {
            elem.addEventListener('click', () => {
                selectedIdInput.value = elem.dataset.id;
                UpdateUserInfoDisplay(elem);
            });
        });
    });

    // ================================
    // Confirmation Dialog Cleanup (Cancel / Background click)
    // ================================
    confirmationCancel.addEventListener('click', () => {
        confirmationForm.parentElement.classList.add('minGap');
        confirmationSubmit.classList.remove('hidden');
        document.querySelectorAll('.tempElement').forEach(el => el.remove());
    });

    confirmationBG.addEventListener('click', () => {
        confirmationForm.parentElement.classList.add('minGap');
        confirmationSubmit.classList.remove('hidden');
        document.querySelectorAll('.tempElement').forEach(el => el.remove());
    });
</script>
<!-- <script src="../.JS/AutoRefresher.js"></script> -->

</html>