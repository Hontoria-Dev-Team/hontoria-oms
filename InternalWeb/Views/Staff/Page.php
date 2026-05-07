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
    <title>Staff Panel - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/StaffPage.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        #staffList img {
            width: 80%;
            height: unset;
        }

        @media (max-width: 1250px) {
            form[action="index.php?page=staff"] {
                overflow-y: scroll;
                padding: 0.1rem !important;
            }

            form[action="index.php?page=staff"] input[type="search"] {
                width: 250px !important;
            }
        }

        @media (max-width: 1050px) {
            form[action="index.php?page=staff"] {
                overflow-y: scroll;
                padding: 0.1rem !important;
            }

            form[action="index.php?page=staff"] input[type="search"] {
                width: 150px !important;
            }
        }

        @media (max-width: 800px) {
            .asideLayout>main>section {
                min-width: fit-content;
                flex-direction: row-reverse;
            }

            .asideLayout>main>section>*:nth-child(1) {
                min-width: 230px;
                max-width: 230px;
            }

            .asideLayout>main>section>*:nth-child(2) {
                min-width: calc(100vw - 3rem);
                max-width: calc(100vw - 3rem);
            }
        }
    </style>
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
                <?php if (in_array("canAlterRoles", $_SESSION['permissions'])): ?>
                    <a href="index.php?page=staff&action=manageRoles" class="roundedMin centerColumnLayout importantInput regPadding emphasizedText shadowed">Manage Roles</a>
                <?php endif; ?>
            </div>
        </h1>
        <?php include("../Views/.Components/MessageBox.php"); ?>
        <section class="rowLayout flexMax midGap">
            <section class="columnLayout midGap flexMinExtra">
                <section class="box centerColumnLayout roundedMid minGap flexMin" id="userInfoContainer">
                    <h4>No Staff Selected</h4>
                    <div class="gradientBorderDiag"></div>
                </section>
                <section class="box centerColumnLayout roundedMid flexMid noBasis noMinHeight minGap">
                    <h5 class="leftStart">Assigned Tasks:</h5>
                    <div id="taskListContainer" class="scrollable fullDimensions columnLayout minGap regTinPadding">
                        <h2 class="centerMarginsSelf">No Staff Selected</h2>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
            </section>
            <section class="flexMax roundedMid centerColumnLayout noFlexBasis noMinWidth">
                <div class="columnLayout minGap box roundedMid fullHeight fullWidth">
                    <!-- GET search form – read‑only filtering, no CSRF needed -->
                    <form method="GET" action="index.php?page=staff" class="rowLayout fullWidth minGap">
                        <input type="hidden" name="page" value="staff">

                        <div class="iconInput flexMax centerHoriRowLayout">
                            <input type="search" name="search" placeholder="Search Staff" class="fullWidth" value="<?= e($search ?? '') ?>">
                            <img src="../../Shared/Img/MagnifierIcon.png" alt="Magnifier">
                        </div>

                        <select name="onlineStatus">
                            <option value="">Any Online Status</option>
                            <option value="active" <?= ($onlineStatus ?? '') === 'active' ? 'selected' : '' ?>>Active Now</option>
                            <option value="offline" <?= ($onlineStatus ?? '') === 'offline' ? 'selected' : '' ?>>Offline</option>
                        </select>

                        <select name="activityStatus">
                            <option value="">Any Activity Status</option>
                            <option value="busy" <?= ($activityStatus ?? '') === 'busy' ? 'selected' : '' ?>>Busy</option>
                            <option value="idle" <?= ($activityStatus ?? '') === 'idle' ? 'selected' : '' ?>>Idle</option>
                        </select>

                        <select name="roleId" class="capitalFirst">
                            <option value="">Any Role</option>
                            <?php foreach ($roleList as $role): ?>
                                <option class="capitalFirst" value="<?= (int)$role['id'] ?>" <?= ($roleId ?? '') == $role['id'] ? 'selected' : '' ?>>
                                    <?= e($role['name']) ?>
                                </option>
                            <?php endforeach; ?>
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
                            $taskCount = ($userTaskCountMap[$staff['id']] ?? 0) + (isset($userMiscTaskMap[$staff['id']]) ? 1 : 0);
                            $status = $taskCount > 0 ? 'Busy' : 'Idle';
                            $statusColor = $taskCount > 0 ? '--yellowTrans' : '--redTrans';
                            $staffElementBorder = $taskCount > 0 ? 'yellowBorder' : 'redBorder';
                            $statusBG = $taskCount > 0 ? 'yellowBG' : 'redBG';
                            $roles = $userRolesMap[$staff['id']] ?? [];
                            $rolesText = !empty($roles) ? implode(', ', $roles) : 'Unset Role';
                            $userImage = isset($accountImageMap[$staff['id']]) ?
                                "../../Storage/AccountImages/" . e($accountImageMap[$staff['id']]) : "../../Shared/Img/PersonIcon.png";
                            $userImageStyle = isset($accountImageMap[$staff['id']]) ?
                                "imageCoverFull" : "";

                            // activity status calculation (unchanged logic)
                            $activityStatus = "Active now";
                            $activityStatusBG = "yellowBG";
                            $activityStatusColor = '--yellowTrans';

                            if ($staff['lastActivityAt'] === null || empty($staff['lastActivityAt'])) {
                                $activityStatus = "No Activity";
                                $activityStatusBG = "redBG";
                                $activityStatusColor = '--redTrans';
                            } else {
                                $manila = new DateTimeZone('Asia/Manila');
                                $lastActivityDate = DateTime::createFromFormat(
                                    'Y-m-d H:i:s',
                                    $staff['lastActivityAt'],
                                    $manila
                                );

                                if ($lastActivityDate === false) {
                                    $activityStatus = "No Activity";
                                    $activityStatusBG = "redBG";
                                    $activityStatusColor = '--redTrans';
                                } else {
                                    $lastActivity = $lastActivityDate->getTimestamp();
                                    $now = (new DateTime('now', $manila))->getTimestamp();
                                    $fifteenMinutes = 15 * 60;
                                    $diff = $now - $lastActivity;

                                    if ($diff < $fifteenMinutes) {
                                        if ($diff < 60) {
                                            $activityStatus = "Active now";
                                        } else {
                                            $minutes = floor($diff / 60);
                                            $activityStatus = "Active {$minutes} min ago";
                                        }
                                        $activityStatusBG = "yellowBG";
                                        $activityStatusColor = '--yellowTrans';
                                    } else {
                                        $activityStatus = "Offline";
                                        $activityStatusBG = "redBG";
                                        $activityStatusColor = '--redTrans';
                                    }
                                }
                            }

                            $staffElementBG = "background: linear-gradient(to top, var(" . $statusColor . "), var(" . $activityStatusColor . ")) !important;";
                            ?>
                            <div class="minHeight minPadding roundedMin rowLayout minGap flexStatic staffElement shadowed <?= e($staffElementBorder) ?>"
                                style="<?= e($staffElementBG) ?>"
                                data-id="<?= e($staff['id']) ?>" data-name="<?= e($fullName) ?>" data-last-name="<?= e($staff['lastName']) ?>"
                                data-roles="<?= e($rolesText) ?>" data-phone="<?= e($staff['phone']) ?>" data-email="<?= e($staff['email']) ?>">
                                <div class="flexMin roundedMin centerColumnLayout grayBG shadowed fixedScreen">
                                    <img src="<?= e($userImage) ?>" alt="User Photo" class="<?= e($userImageStyle) ?> squareSize">
                                </div>
                                <div class="flexMid centerHoriColumnLayout">
                                    <h5 class="whiteText outlineText"><?= e($fullName) ?></h5>
                                    <?php if (!empty($staff['note'])): ?>
                                        <h6 class="capitalFirst faded"><?= e($staff['note']) ?></h6>
                                    <?php endif; ?>
                                    <h6 class="capitalFirst">(<?= e($rolesText) ?>)</h6>
                                    <h6 class="capitalFirst">Tasks: <?= e($taskCount) ?></h6>
                                </div>
                                <div class="souEastAbsolute closeCorner rowLayout tinGap">
                                    <h5 class="status roundedTin fitDimensions minHoriPadding shadowed whiteText outlineText <?= e($statusBG) ?>">
                                        <?= e($status) ?>
                                    </h5>
                                    <h5 class="roundedTin whiteText fitDimensions minHoriPadding shadowed outlineText <?= e($activityStatusBG) ?>">
                                        <?= e($activityStatus) ?>
                                    </h5>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </section>
                </div>
                <div class="gradientBorderDiag"></div>
            </section>
        </section>
    </main>
    <?php include("../Views/.Components/ConfirmationBox.php"); ?>
</body>
<script src="../.JS/ConfirmationBox.js"></script>
<script src="../.JS/CsrfHandler.js"></script>
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
    const userMiscTaskList = <?php echo json_encode($userMiscTaskList); ?>;
    const userPermissions = <?php echo json_encode($_SESSION['permissions'] ?? []); ?>;

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

    const userMiscTaskMap = {};
    userMiscTaskList.forEach(item => {
        userMiscTaskMap[item.userID] = {
            description: item.description,
            assignedAt: item.assignedAt
        };
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
        activityLogs: [],
        miscTask: null
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
        let name = elem.dataset.name;
        if (name.length > 30) {
            name = elem.dataset.lastName + ', ' + name;
            if (name.length > 28) {
                name = name.substring(0, 28) + '...';
            }
        }

        const id = elem.dataset.id;
        const phone = elem.dataset.phone;
        const email = elem.dataset.email;
        const rolesText = elem.dataset.roles;

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
        currentUser.miscTask = userMiscTaskMap[id] || null;

        // Rebuild the entire user info container safely (no innerHTML)
        userInfoContainer.innerHTML = ''; // safe clear

        // Name and stats icon
        const h5Name = document.createElement('h5');
        h5Name.className = 'leftStart centerHoriRowLayout minGap';
        h5Name.appendChild(document.createTextNode(name + ' '));
        const statsIcon = document.createElement('img');
        statsIcon.src = '../../Shared/Img/StatsIcon.png';
        statsIcon.alt = 'Stats';
        statsIcon.className = 'unitHeight clickable';
        statsIcon.id = 'userStatsButton';
        h5Name.appendChild(statsIcon);
        userInfoContainer.appendChild(h5Name);

        // Roles
        const h6Roles = document.createElement('h6');
        h6Roles.className = 'leftStart rowLayout tinGap';
        h6Roles.innerHTML = 'Roles: <span id="rolesText" class="capitalFirst"></span>'; // safe: hardcoded
        h6Roles.querySelector('span').textContent = rolesText; // set user data safely
        userInfoContainer.appendChild(h6Roles);

        // Contact info
        const spanContact = document.createElement('span');
        spanContact.className = 'leftStart';
        const phoneH6 = document.createElement('h6');
        phoneH6.className = 'centerHoriRowLayout tinGap';
        phoneH6.innerHTML = '<img src="../../Shared/Img/PhoneIcon.png" alt="Phone" class="unitHeight"> : '; // safe
        phoneH6.appendChild(document.createTextNode(phone));
        spanContact.appendChild(phoneH6);
        const emailH6 = document.createElement('h6');
        emailH6.className = 'centerHoriRowLayout tinGap';
        emailH6.innerHTML = '<img src="../../Shared/Img/MailIcon.png" alt="Mail" class="unitHeight"> : '; // safe
        emailH6.appendChild(document.createTextNode(email));
        spanContact.appendChild(emailH6);
        userInfoContainer.appendChild(spanContact);

        // Action buttons
        const actionsDiv = document.createElement('div');
        actionsDiv.className = 'rowLayout fullWidth minGap';
        actionsDiv.id = 'staffActions';

        const modifyBtn = document.createElement('button');
        modifyBtn.type = 'button';
        modifyBtn.className = 'importantInput flexMax shadowed noBorder';
        modifyBtn.id = 'modifyRolesButton';
        modifyBtn.textContent = 'Modify Roles';
        actionsDiv.appendChild(modifyBtn);

        if (currentUser.miscTask === null) {
            const assignBtn = document.createElement('button');
            assignBtn.type = 'button';
            assignBtn.className = 'importantInput flexMax shadowed noBorder';
            assignBtn.id = 'assignMiscTaskButton';
            assignBtn.textContent = 'Assign Misc Task';
            actionsDiv.appendChild(assignBtn);
        } else {
            const updateBtn = document.createElement('button');
            updateBtn.type = 'button';
            updateBtn.className = 'importantInput yellowBG outlineText flexMax shadowed noBorder';
            updateBtn.id = 'updateMiscTaskButton';
            updateBtn.textContent = 'Update Misc Task';
            actionsDiv.appendChild(updateBtn);
        }
        userInfoContainer.appendChild(actionsDiv);

        // Delete button
        const delBtn = document.createElement('button');
        delBtn.type = 'button';
        delBtn.className = 'criticalInput centerColumnLayout norEastAbsolute';
        delBtn.id = 'deleteButton';
        delBtn.innerHTML = '<img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors">'; // safe: hardcoded
        userInfoContainer.appendChild(delBtn);

        // Gradient border
        const gradDiv = document.createElement('div');
        gradDiv.className = 'gradientBorderDiag';
        userInfoContainer.appendChild(gradDiv);

        // Update governance rules based on current user's roles
        const userRoleIds = currentUser.roles.map(r => r.roleID);
        const relevantGovernance = roleGovernance.filter(gov => userRoleIds.includes(gov.roleSubjectID));
        governanceRules = {
            canGrant: relevantGovernance.every(g => g.canGrant == 1) ? 1 : 0,
            canRevoke: relevantGovernance.every(g => g.canRevoke == 1) ? 1 : 0,
            canAlter: relevantGovernance.every(g => g.canAlter == 1) ? 1 : 0,
            canDelete: relevantGovernance.every(g => g.canDelete == 1) ? 1 : 0
        };

        // Helper to apply classes based on a condition
        function SetButtonState(button, enabled) {
            if (!button) return;
            if (enabled) {
                button.classList.remove('unclickable');
                button.classList.remove('faded');
            } else {
                button.classList.add('unclickable');
                button.classList.add('faded');
            }
        }

        // Combined permission + governance checks
        const canModifyRoles = userPermissions.includes('canAlterAccountRoles') && (governanceRules.canGrant || governanceRules.canRevoke);
        SetButtonState(modifyBtn, canModifyRoles);

        const canDeleteUser = userPermissions.includes('canDeleteUserAccounts') && governanceRules.canDelete;
        SetButtonState(delBtn, canDeleteUser);

        const assignMiscBtn = document.getElementById('assignMiscTaskButton');
        const updateMiscBtn = document.getElementById('updateMiscTaskButton');
        const canAssignMisc = userPermissions.includes('canAssignMiscTasksToStaff') && governanceRules.canGrant;
        SetButtonState(assignMiscBtn, canAssignMisc);
        const canUpdateMisc = userPermissions.includes('canFinalizeMiscTasksToStaff') && governanceRules.canGrant;
        SetButtonState(updateMiscBtn, canUpdateMisc);

        // Attach event listeners for this user
        document.getElementById('userStatsButton').addEventListener('click', ShowUserStatsModal);
        modifyBtn.addEventListener('click', ShowRoleModificationModal);
        delBtn.addEventListener('click', () => ShowDeleteConfirmation(id, name));
        if (assignMiscBtn) assignMiscBtn.addEventListener('click', ShowMiscTaskAssignmentModal);
        if (updateMiscBtn) updateMiscBtn.addEventListener('click', ShowMiscTaskUpdateModal);

        ShowUserTasks();
    }

    // ================================
    // Display User Tasks
    // ================================
    function ShowUserTasks() {
        taskListContainer.innerHTML = ''; // safe clear

        if (currentUser.miscTask === null && currentUser.tasks.length === 0) {
            tempElement = document.createElement('h2');
            tempElement.className = 'centerMarginsSelf';
            tempElement.textContent = 'No Tasks Assigned';
            taskListContainer.appendChild(tempElement);
            return;
        }

        // Show misc task first if it exists
        if (currentUser.miscTask !== null) {
            tempDiv = document.createElement('div');
            tempDiv.className = 'centerHoriRowLayout minGap tinGap minPadding roundedMin shadowed yellowTransBG yellowBorder';

            const innerDiv = document.createElement('div');
            innerDiv.className = 'columnLayout';
            const titleH5 = document.createElement('h5');
            titleH5.className = 'whiteText outlineText';
            titleH5.textContent = 'Miscellaneous Task';
            innerDiv.appendChild(titleH5);
            const descH6 = document.createElement('h6');
            descH6.className = 'capitalFirst';
            descH6.textContent = 'Task: ' + currentUser.miscTask.description;
            innerDiv.appendChild(descH6);
            const dateH6 = document.createElement('h6');
            dateH6.textContent = 'Assigned At: ' + formatDateTime(currentUser.miscTask.assignedAt);
            innerDiv.appendChild(dateH6);
            tempDiv.appendChild(innerDiv);
            taskListContainer.appendChild(tempDiv);
        }

        currentUser.tasks.forEach(task => {
            tempDiv = document.createElement('div');
            tempDiv.className = 'centerHoriRowLayout minGap tinGap minPadding roundedMin shadowed';

            if (task.status === 'pending') {
                tempDiv.classList.add('redTransBG', 'redBorder');
            } else if (task.status === 'partially complete') {
                tempDiv.classList.add('yellowTransBG', 'yellowBorder');
            } else if (task.status === 'complete') {
                tempDiv.classList.add('greenTransBG', 'greenBorder');
            }

            const innerDiv = document.createElement('div');
            innerDiv.className = 'columnLayout';
            const processH5 = document.createElement('h5');
            processH5.className = 'whiteText outlineText';
            processH5.textContent = task.processName + ' Order #' + task.orderID;
            innerDiv.appendChild(processH5);
            const serviceH6 = document.createElement('h6');
            serviceH6.textContent = 'Service: ' + task.subserviceName + ' ' + task.serviceName;
            innerDiv.appendChild(serviceH6);
            const customerH6 = document.createElement('h6');
            customerH6.textContent = 'Customer: ' + task.customerName;
            innerDiv.appendChild(customerH6);
            const assignedH6 = document.createElement('h6');
            assignedH6.textContent = 'Assigned At: ' + formatDateTime(task.assignedAt);
            innerDiv.appendChild(assignedH6);
            tempDiv.appendChild(innerDiv);
            taskListContainer.appendChild(tempDiv);
        });
    }

    // ================================
    // Stats Modal (Performance + Activity Logs)
    // ================================
    function ShowUserStatsModal() {
        confirmationTitle.textContent = 'Staff Performance Statistics';
        confirmationText.textContent = 'Here are the performance statistics of ' + currentUser.name + '.';
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
        tempElement.textContent = 'Tasks Completed (#): ' + completed;
        tempDiv.appendChild(tempElement);

        tempElement = document.createElement('h5');
        tempElement.textContent = 'Average Task Duration: ' + avgDuration + ' minutes';
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
            const emptyH5 = document.createElement('h5');
            emptyH5.className = 'centerText minHoriPadding whiteText outlineText';
            emptyH5.textContent = 'No User Activity';
            tempDiv.appendChild(emptyH5);
            logContainer.appendChild(tempDiv);
        } else {
            currentUser.activityLogs.forEach(activity => {
                tempDiv = document.createElement('div');
                tempDiv.className = 'centerColumnLayout roundedTin regTinPadding shadowed fitHeight fullWidth';
                const logH5 = document.createElement('h5');
                logH5.className = 'centerText minHoriPadding whiteText outlineText';
                logH5.textContent = activity.log; // safe: textContent
                const timeH6 = document.createElement('h6');
                timeH6.textContent = formatDateTime(activity.loggedAt);
                tempDiv.appendChild(logH5);
                tempDiv.appendChild(timeH6);
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
        confirmationTitle.textContent = 'Modify Account Roles';
        confirmationText.textContent = '';
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
            currentRolesContainer.innerHTML = ''; // safe clear
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
                        // safe: hardcoded icon
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
            choiceRolesContainer.innerHTML = ''; // safe clear
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
        confirmationTitle.textContent = 'Delete Account?';
        confirmationText.textContent = 'Are you sure to delete the account of:\n' + userName + '?';
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
    // Misc Task Assignment Modal
    // ================================
    function ShowMiscTaskAssignmentModal() {
        if (!governanceRules.canGrant) return;

        confirmationForm.action = 'index.php?page=staff&action=assignMiscTask';
        confirmationTitle.textContent = 'Assign Miscellaneous Task';
        confirmationText.textContent = 'Input the description of the miscellaneous task or work that this account will be assigned to.';
        confirmationSubmit.classList.add('yellowBG', 'whiteText', 'noBorder');
        confirmationSubmit.value = 'Assign';

        tempElement = document.createElement("input");
        tempElement.type = "text";
        tempElement.name = "description";
        tempElement.placeholder = "Short Task Description";
        tempElement.className = "tempElement";
        tempElement.required = true;
        confirmationForm.appendChild(tempElement);

        confirmation.style.display = 'flex';
    }

    // ================================
    // Misc Task Update Modal
    // ================================
    function ShowMiscTaskUpdateModal() {
        if (!governanceRules.canGrant) return;

        confirmationForm.action = 'index.php?page=staff&action=updateMiscTask';
        confirmationTitle.textContent = 'Update Miscellaneous Task';
        confirmationText.textContent = 'Select an action for this miscellaneous task.';
        confirmationSubmit.classList.add('hidden');

        tempDiv = document.createElement("div");
        tempDiv.className = "tempElement rowLayout minGap";
        confirmationForm.appendChild(tempDiv);

        tempElement = document.createElement("input");
        tempElement.type = "submit";
        tempElement.name = "miscTaskAction";
        tempElement.value = "complete";
        tempElement.className = "tempElement importantInput greenBG shadowed noBorder capitalFirst flexMax";
        tempDiv.appendChild(tempElement);

        tempElement = document.createElement("input");
        tempElement.type = "submit";
        tempElement.name = "miscTaskAction";
        tempElement.value = "unassign";
        tempElement.className = "tempElement importantInput redBG shadowed noBorder capitalFirst flexMax";
        tempDiv.appendChild(tempElement);

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

</html>