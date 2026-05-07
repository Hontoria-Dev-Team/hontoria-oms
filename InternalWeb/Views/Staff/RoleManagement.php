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
    <title>Role Management - Hontoria OMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/StaffPage.css">
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
                min-width: 500px;
                max-width: 500px;
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

        @media (max-width: 400px) {
            .asideLayout>main>section>*:nth-child(2) {
                min-width: 400px;
                max-width: 400px;
            }

            .asideLayout>main>section>*:nth-child(2)>*:nth-child(2)>*:nth-child(2) {
                overflow-x: scroll !important;
                overflow-y: hidden !important;
                padding: 0.3rem !important;
            }

            .asideLayout>main>section>*:nth-child(2)>*:nth-child(2)>*:nth-child(2)>*:nth-child(1) {
                min-width: 250px !important;
            }

            .asideLayout>main>section>*:nth-child(2)>*:nth-child(2)>*:nth-child(2)>*:nth-child(2) {
                min-width: 200px !important;
            }
        }
    </style>
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <span class="centerHoriRowLayout midGap">
            <?php include("../Views/.Components/BackLink.php"); ?>
            <h1 class="titleLogo minGap tinHeight flexMax">
                <img src="../../Shared/Img/PeopleIcon.png" alt="People"> Role Management
            </h1>
        </span>
        <?php include("../Views/.Components/MessageBox.php"); ?>
        <section class="rowLayout flexMax midGap">
            <section class="flexMid roundedMid centerColumnLayout">
                <div class="columnLayout minGap box roundedMid fullHeight fullWidth">
                    <div class="centerHoriRowLayout">
                        <h2 class="flexMax">Roles:</h2>
                        <?php if (in_array("canCreateRoles", $_SESSION['permissions'])): ?>
                            <button type="button" class="darkBG emphasizedText noBorder shadowed whiteText centerColumnLayout fullHeight" id="createRoleButton">
                                <b>Create</b>
                            </button>
                        <?php endif; ?>
                    </div>
                    <div id="processesContainer" class="scrollable columnLayout minGap regMinPadding flexMax">
                        <?php foreach ($roleTally as $role): ?>
                            <div class="noShrink roundedMin centerHoriRowLayout clickable shadowed fixedScreen roleElement"
                                data-id="<?= e($role['id']) ?>" data-name="<?= e($role['name']) ?>">
                                <h3 class="regMinPadding gradientDiagBG flexMid centerColumnLayout fullHeight whiteText skewedXNegBG shadowed capitalFirst">
                                    <span class="outlineText"><?= e($role['name']) ?></span>
                                </h3>
                                <h5 class="flexMin whiteBG fullHeight centerColumnLayout"><?= e($role['count']) ?> Users</h5>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="gradientBorderDiag"></div>
            </section>
            <section class="columnLayout midGap flexMax">
                <section class="centerColumnLayout roundedMid minGap">
                    <div class="centerHoriRowLayout minGap box roundedMid fullHeight fullWidth">
                        <h2 id="selectedRoleTitle" class="capitalFirst">No Role Selected</h2>
                        <?php if (in_array("canDeleteRoles", $_SESSION['permissions'])): ?>
                            <button type="button" class="criticalInput centerColumnLayout eastAbsolute hidden" id="deleteButton">
                                <img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors">
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
                <div class="columnLayout flexMax midGap">
                    <section class="centerColumnLayout roundedMid minGap flexMax">
                        <div class="columnLayout minGap box roundedMid fullHeight fullWidth">
                            <div class="flexMax rowLayout minGap noMinHeight noFlexBasis">
                                <div class="fullWidth columnLayout tinGap noMinHeight flexMin noFlexBasis">
                                    <h3>Assigned Permissions:</h3>
                                    <div class="gridCenterFlex tinGap scrollable flexMax regMinPadding" id="assignedPermsContainer">
                                        <h2 class="selfCenter">No Role Selected</h2>
                                    </div>
                                </div>
                                <div class="fullWidth columnLayout tinGap noMinHeight flexMin noFlexBasis">
                                    <h3>Available Permissions:</h3>
                                    <div class="gridCenterFlex tinGap scrollable flexMax regMinPadding" id="availablePermsContainer">
                                        <h2 class="selfCenter">No Role Selected</h2>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="importantInput fullWidth hidden" id="submitRolePermissionsButton">Confirm Changes</button>
                        </div>
                        <div class="gradientBorderDiag"></div>
                    </section>
                    <div class="rowLayout flexMax midGap noMinHeight noFlexBasis">
                        <section class="centerColumnLayout roundedMid minGap flexMid hidden" id="managementRulesContainer">
                            <div class="columnLayout minGap box roundedMid fullHeight fullWidth">
                                <h3 class="centerHoriRowLayout">Management Rules:
                                    <button type="button" id="addRuleButton" class="importantInput eastAbsolute edgeCorner">Add Rule</button>
                                </h3>
                                <div class="flexMax columnLayout tinGap scrollable container regMinPadding"></div>
                                <button type="button" class="importantInput fullWidth" id="confirmRuleChangesButton">Confirm Changes</button>
                            </div>
                            <div class="gradientBorderDiag"></div>
                        </section>
                        <section class="centerColumnLayout roundedMid minGap flexMid" id="processTaskContainer">
                            <div class="columnLayout minGap box roundedMid fullHeight fullWidth">
                                <h3 class="centerHoriRowLayout">Process Task Access:
                                    <button type="button" id="addTaskButton" class="importantInput eastAbsolute edgeCorner hidden">Add Task</button>
                                </h3>
                                <div class="flexMax columnLayout tinGap scrollable container regMinPadding">
                                    <h2 class="selfCenter centerMarginsSelf">No Role Selected</h2>
                                </div>
                                <button type="button" class="importantInput fullWidth hidden" id="confirmTaskChangesButton">Confirm Changes</button>
                            </div>
                            <div class="gradientBorderDiag"></div>
                        </section>
                    </div>
                </div>
            </section>
        </section>
    </main>
    <?php include("../Views/.Components/ConfirmationBox.php"); ?>
</body>
<script src="../.JS/ConfirmationBox.js"></script>
<script src="../.JS/CsrfHandler.js"></script>
<script src="../.JS/MiscHelpers.js"></script>
<script>
    const selectedRoleTitle = document.getElementById('selectedRoleTitle');
    const assignedPermsContainer = document.getElementById('assignedPermsContainer');
    const availablePermsContainer = document.getElementById('availablePermsContainer');
    const submitRolePermissionsButton = document.getElementById('submitRolePermissionsButton');
    const managementRulesContainer = document.getElementById('managementRulesContainer');
    const processTaskContainer = document.getElementById('processTaskContainer');
    const createRoleButton = document.getElementById('createRoleButton');
    const deleteButton = document.getElementById('deleteButton');
    const addRuleButton = document.getElementById('addRuleButton');
    const confirmRuleChangesButton = document.getElementById('confirmRuleChangesButton');
    const confirmTaskChangesButton = document.getElementById('confirmTaskChangesButton');
    const addTaskButton = document.getElementById('addTaskButton');
    const roleList = <?php echo json_encode($roleList); ?>;
    const roleTally = <?php echo json_encode($roleTally); ?>;
    const rolePermissionsList = <?php echo json_encode($rolePermissionsList); ?>;
    const userPermissionsList = <?php echo json_encode($userPermissionsList); ?>;
    const roleGovernanceList = <?php echo json_encode($roleGovernanceList); ?>;
    const processTaskList = <?php echo json_encode($processTaskList); ?>;
    const processList = <?php echo json_encode($processList); ?>;

    const rolesName = {};
    roleList.forEach(item => {
        rolesName[item.id] = item.name;
    });

    const rolePermissionsMap = {};
    rolePermissionsList.forEach(item => {
        if (!rolePermissionsMap[item.roleID]) {
            rolePermissionsMap[item.roleID] = [];
        }
        rolePermissionsMap[item.roleID].push({
            id: item.permissionID,
            name: item.name
        });
    });

    const roleGovernanceMap = {};
    roleGovernanceList.forEach(item => {
        if (!roleGovernanceMap[item.roleAgentID]) {
            roleGovernanceMap[item.roleAgentID] = [];
        }
        roleGovernanceMap[item.roleAgentID].push({
            roleSubjectID: item.roleSubjectID,
            canGrant: item.canGrant,
            canRevoke: item.canRevoke,
            canAlter: item.canAlter,
            canDelete: item.canDelete
        });
    });

    const processTaskMap = {};
    processTaskList.forEach(item => {
        if (!processTaskMap[item.roleID]) {
            processTaskMap[item.roleID] = [];
        }
        processTaskMap[item.roleID].push({
            id: item.processID,
            name: item.name
        });
    });

    const alterableRoles = roleTally.map(item => item.id);

    let selectedRolePermissions;
    let selectedRoleName;
    let selectedRoleGovernance;
    let selectedRoleProcessTasks;
    let currentUserRoleGovernance;
    let tempElement;

    document.addEventListener("DOMContentLoaded", () => {
        confirmationCancel.value = "No Cancel";
    });

    const selectedID = document.createElement("input");
    selectedID.type = "hidden";
    selectedID.name = "selectedID";
    confirmationForm.appendChild(selectedID);

    // Reactive clickable role data script
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.roleElement').forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedRoleName = elem.dataset.name;
                selectedRoleTitle.textContent = selectedRoleName;

                selectedRolePermissions = [...(rolePermissionsMap[elem.dataset.id] || [])];
                selectedRoleGovernance = [...(roleGovernanceMap[elem.dataset.id] || [])];
                selectedRoleProcessTasks = [...(processTaskMap[elem.dataset.id] || [])];

                selectedID.value = elem.dataset.id;

                submitRolePermissionsButton.classList.remove("hidden");
                confirmTaskChangesButton.classList.remove("hidden");
                if (deleteButton) deleteButton.classList.remove("hidden");
                addTaskButton.classList.remove("hidden");

                updateSelection();
            });
        });
    });

    function updateSelection() {
        if (selectedRolePermissions.some(p => p.name === 'canAlterRoles') || selectedRolePermissions.some(p => p.name === 'canAlterAccountRoles')) {
            managementRulesContainer.classList.remove("hidden");
        } else {
            managementRulesContainer.classList.add("hidden");
        }

        setAvailablePerms();
        setAssignedPerms();
        setGovernanceRules();
        setProcessTasks();
    }

    let tempDiv;

    // Show the assigned permissions of the role function
    let unrevokablePerms;

    function setAssignedPerms() {
        assignedPermsContainer.innerHTML = ''; // safe clear

        document.querySelectorAll('.newPermissions').forEach(function(elem) {
            elem.remove();
        });

        unrevokablePerms = getDirectionalArrayDiff(selectedRolePermissions, userPermissionsList, 'id').map(item => item.id);

        if (selectedRolePermissions.length == 0) {
            tempElement = document.createElement("h2");
            tempElement.textContent = "No Permissions Assigned";
            tempElement.className = "selfCenter";
            assignedPermsContainer.appendChild(tempElement);
        }

        for (let i = 0; i < selectedRolePermissions.length; i++) {
            tempDiv = document.createElement("div");
            tempDiv.className = "noShrink fitHeight roundedMin centerRowLayout minGap yellowTransBG regMinPadding bordered shadowed";

            tempElement = document.createElement("h5");
            tempElement.className = "flexMax centerText capitalFirst whiteText outlineText";
            tempElement.textContent = formatCamelCase(selectedRolePermissions[i].name);
            tempDiv.appendChild(tempElement);

            if (!unrevokablePerms.includes(selectedRolePermissions[i].id)) {
                tempElement = document.createElement("a");
                tempElement.className = "squareSize unitHeight centerColumnLayout permissionRemove";
                // safe: hardcoded icon
                tempElement.innerHTML = '<img src="../../Shared/Img/XIcon.png" alt="X">';
                tempElement.dataset.index = i;
                tempDiv.appendChild(tempElement);
            }

            tempElement = document.createElement("input");
            tempElement.type = "hidden";
            tempElement.name = "newPermissions[]";
            tempElement.className = "newPermissions";
            tempElement.value = selectedRolePermissions[i].id;
            confirmationForm.appendChild(tempElement);

            assignedPermsContainer.appendChild(tempDiv);
        };

        document.querySelectorAll('.permissionRemove').forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedRolePermissions.splice(elem.dataset.index, 1);
                updateSelection();
            });
        });
    }

    // Show the available permissions of the role function
    let selectedRoleAvailablePerms;

    function setAvailablePerms() {
        availablePermsContainer.innerHTML = ''; // safe clear

        selectedRoleAvailablePerms = getDirectionalArrayDiff(userPermissionsList, selectedRolePermissions, 'id');

        if (selectedRoleAvailablePerms.length == 0) {
            tempElement = document.createElement("h2");
            tempElement.textContent = "No More Permissions Available To Assign";
            tempElement.className = "selfCenter minHoriPadding centerText";
            availablePermsContainer.appendChild(tempElement);
        }

        for (let i = 0; i < selectedRoleAvailablePerms.length; i++) {
            tempDiv = document.createElement("div");
            tempDiv.className = "noShrink fitHeight roundedMin centerRowLayout minGap darkFadedBG regMinPadding bordered choicePermission shadowed";
            tempDiv.dataset.id = selectedRoleAvailablePerms[i].id;
            tempDiv.dataset.name = selectedRoleAvailablePerms[i].name;

            tempElement = document.createElement("h5");
            tempElement.className = "flexMax centerText capitalFirst whiteText outlineText";
            tempElement.textContent = formatCamelCase(selectedRoleAvailablePerms[i].name);
            tempDiv.appendChild(tempElement);

            availablePermsContainer.appendChild(tempDiv);
        };

        document.querySelectorAll('.choicePermission').forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedRolePermissions.push({
                    id: elem.dataset.id,
                    name: elem.dataset.name
                });
                updateSelection();
            });
        });
    }

    // Show governance rules of the role function
    function setGovernanceRules() {
        const container = managementRulesContainer.querySelector('.container');
        container.innerHTML = ''; // safe clear

        document.querySelectorAll('.roleSubjects').forEach(function(elem) {
            elem.remove();
        });

        document.querySelectorAll('.canGrants').forEach(function(elem) {
            elem.remove();
        });

        document.querySelectorAll('.canRevokes').forEach(function(elem) {
            elem.remove();
        });

        document.querySelectorAll('.canAlters').forEach(function(elem) {
            elem.remove();
        });

        document.querySelectorAll('.canDeletes').forEach(function(elem) {
            elem.remove();
        });

        if (selectedRoleGovernance.length == 0) {
            tempElement = document.createElement("h2");
            tempElement.textContent = "No Management Rules";
            tempElement.className = "selfCenter centerMarginsSelf minHoriPadding centerText";
            container.appendChild(tempElement);
        }

        for (let i = 0; i < selectedRoleGovernance.length; i++) {
            const rule = selectedRoleGovernance[i];
            tempDiv = document.createElement("div");
            tempDiv.className = "yellowTransBG roundedMin bordered centerHoriRowLayout regMinPadding shadowed";

            if (!alterableRoles.includes(rule.roleSubjectID)) {
                tempDiv.classList.add("unclickable", "faded");
            }

            const nameEl = document.createElement("h5");
            nameEl.className = "flexMax centerColumnLayout capitalFirst whiteText outlineText";
            nameEl.textContent = rolesName[rule.roleSubjectID] || 'Unknown Role';
            tempDiv.appendChild(nameEl);

            // Build the toggle row safely (no innerHTML)
            const toggleRow = document.createElement("div");
            toggleRow.className = "centerRowLayout minGap minHoriPadding";

            // Grant
            const grantDiv = document.createElement("div");
            grantDiv.className = "centerRowLayout tinGap canGrantCheck";
            grantDiv.dataset.index = i;
            const grantCheckbox = document.createElement("input");
            grantCheckbox.type = "checkbox";
            grantCheckbox.checked = rule.canGrant == 1;
            grantDiv.appendChild(grantCheckbox);
            const grantLabel = document.createElement("h6");
            grantLabel.textContent = "Grant";
            grantDiv.appendChild(grantLabel);
            toggleRow.appendChild(grantDiv);

            // Revoke
            const revokeDiv = document.createElement("div");
            revokeDiv.className = "centerRowLayout tinGap canRevokeCheck";
            revokeDiv.dataset.index = i;
            const revokeCheckbox = document.createElement("input");
            revokeCheckbox.type = "checkbox";
            revokeCheckbox.checked = rule.canRevoke == 1;
            revokeDiv.appendChild(revokeCheckbox);
            const revokeLabel = document.createElement("h6");
            revokeLabel.textContent = "Revoke";
            revokeDiv.appendChild(revokeLabel);
            toggleRow.appendChild(revokeDiv);

            // Alter
            const alterDiv = document.createElement("div");
            alterDiv.className = "centerRowLayout tinGap canAlterCheck";
            alterDiv.dataset.index = i;
            const alterCheckbox = document.createElement("input");
            alterCheckbox.type = "checkbox";
            alterCheckbox.checked = rule.canAlter == 1;
            alterDiv.appendChild(alterCheckbox);
            const alterLabel = document.createElement("h6");
            alterLabel.textContent = "Alter";
            alterDiv.appendChild(alterLabel);
            toggleRow.appendChild(alterDiv);

            // Delete
            const deleteDiv = document.createElement("div");
            deleteDiv.className = "centerRowLayout tinGap canDeleteCheck";
            deleteDiv.dataset.index = i;
            const deleteCheckbox = document.createElement("input");
            deleteCheckbox.type = "checkbox";
            deleteCheckbox.checked = rule.canDelete == 1;
            deleteDiv.appendChild(deleteCheckbox);
            const deleteLabel = document.createElement("h6");
            deleteLabel.textContent = "Delete";
            deleteDiv.appendChild(deleteLabel);
            toggleRow.appendChild(deleteDiv);

            tempDiv.appendChild(toggleRow);

            // Remove rule button
            const removeA = document.createElement("a");
            removeA.className = "squareSize unitHeight centerColumnLayout norWestAbsolute closeCorner removeRule";
            removeA.innerHTML = '<img src="../../Shared/Img/XIcon.png" alt="X">'; // safe
            removeA.dataset.index = i;
            tempDiv.appendChild(removeA);

            container.appendChild(tempDiv);
        }

        // Attach event listeners after DOM is built
        document.querySelectorAll('.removeRule').forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedRoleGovernance.splice(elem.dataset.index, 1);
                updateSelection();
            });
        });

        // Attach event listeners to the actual checkboxes for all rules
        document.querySelectorAll('.canGrantCheck').forEach(function(div) {
            const idx = div.dataset.index;
            const checkbox = div.querySelector('input[type="checkbox"]');
            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    selectedRoleGovernance[idx].canGrant = checkbox.checked ? 1 : 0;
                });
            }
        });
        document.querySelectorAll('.canRevokeCheck').forEach(function(div) {
            const idx = div.dataset.index;
            const checkbox = div.querySelector('input[type="checkbox"]');
            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    selectedRoleGovernance[idx].canRevoke = checkbox.checked ? 1 : 0;
                });
            }
        });
        document.querySelectorAll('.canAlterCheck').forEach(function(div) {
            const idx = div.dataset.index;
            const checkbox = div.querySelector('input[type="checkbox"]');
            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    selectedRoleGovernance[idx].canAlter = checkbox.checked ? 1 : 0;
                });
            }
        });
        document.querySelectorAll('.canDeleteCheck').forEach(function(div) {
            const idx = div.dataset.index;
            const checkbox = div.querySelector('input[type="checkbox"]');
            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    selectedRoleGovernance[idx].canDelete = checkbox.checked ? 1 : 0;
                });
            }
        });
    }

    // Helper to build confirmation text with role name safely
    function buildRoleConfirmationMessage(messageBefore, messageAfter) {
        // Clear previous content
        while (confirmationText.firstChild) confirmationText.removeChild(confirmationText.firstChild);
        if (messageBefore) {
            confirmationText.appendChild(document.createTextNode(messageBefore));
        }
        const span = document.createElement("span");
        span.className = "capitalFirst";
        span.textContent = selectedRoleName; // safe, no HTML injection
        confirmationText.appendChild(span);
        if (messageAfter) {
            confirmationText.appendChild(document.createTextNode(messageAfter));
        }
    }

    // Confirmation Box for permission change functionality
    submitRolePermissionsButton.addEventListener('click', function() {
        confirmationForm.action = "index.php?page=staff&action=changeRolePermissions"
        confirmationTitle.textContent = "Change Role's Permissions?"; // plain text, safe
        buildRoleConfirmationMessage("Are you sure to change the permissions of the ", " role?");
        confirmationSubmit.value = "Yes Change";
        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");
        confirmation.style.display = 'flex';
    });

    // Role creation logic functionality
    if (createRoleButton) {
        createRoleButton.addEventListener('click', function() {
            confirmationTitle.textContent = "Create Service"; // Note: original had "Create Service", keeping as-is
            confirmationForm.action = "index.php?page=staff&action=createRole";
            confirmationText.textContent = "Please enter a unique role name."; // hardcoded, safe
            confirmationSubmit.value = "Create";
            confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");

            tempElement = document.createElement("input");
            tempElement.type = "text";
            tempElement.name = "name";
            tempElement.placeholder = "Role Name";
            tempElement.id = "nameInput";
            tempElement.classList.add("tempElement");
            confirmationForm.appendChild(tempElement);

            confirmation.style.display = 'flex';
        });
    }

    // Role deletion logic functionality
    if (deleteButton) {
        deleteButton.addEventListener('click', function() {
            confirmationForm.action = "index.php?page=staff&action=deleteRole"
            confirmationTitle.textContent = "Delete Role?";
            buildRoleConfirmationMessage("Are you sure to delete the ", " role?");
            confirmationSubmit.value = "Yes Delete";
            confirmationSubmit.classList.remove("yellowBG", "whiteText", "noBorder");
            confirmation.style.display = 'flex';
        });
    }

    // Rule addition logic functionality
    addRuleButton.addEventListener('click', function() {
        confirmationTitle.textContent = "Add Rule"; // hardcoded
        confirmationText.textContent = "Please select the roles you want to add to the role's rule list.";
        confirmationSubmit.classList.add("hidden");
        showRoleRuleAdditionBox();
    });

    function showRoleRuleAdditionBox() {
        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });

        tempDiv = document.createElement("div");
        tempDiv.className = 'midHeight scrollable columnLayout minGap tempElement';

        const currentRules = new Set(selectedRoleGovernance.map(g => g.roleSubjectID));
        let hasSelection = false;

        alterableRoles.forEach((item) => {
            if (currentRules.has(item)) return;

            tempElement = document.createElement('div');
            tempElement.className = 'tinHeight noShrink roundedMin centerColumnLayout bordered darkFadedBG emphasizedText capitalFirst addRuleElement';
            tempElement.textContent = rolesName[item];
            tempElement.dataset.id = item;
            tempDiv.appendChild(tempElement);

            hasSelection = true;
        });

        if (!hasSelection) {
            tempElement = document.createElement("h2");
            tempElement.textContent = "No Roles to Add";
            tempElement.className = "selfCenter centerMarginsSelf";
            tempDiv.appendChild(tempElement);
        }

        confirmationForm.appendChild(tempDiv);

        confirmation.style.display = 'flex';

        document.querySelectorAll('.addRuleElement').forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedRoleGovernance.push({
                    canAlter: "0",
                    canDelete: "0",
                    canGrant: "0",
                    canRevoke: "0",
                    roleSubjectID: elem.dataset.id
                });
                updateSelection();
                showRoleRuleAdditionBox();
            });
        });
    }

    // Helper to sync hidden rule inputs with current selectedRoleGovernance state
    function syncRuleHiddenInputs() {
        // Remove all old hidden rule inputs
        confirmationForm.querySelectorAll('.roleSubjects, .canGrants, .canRevokes, .canAlters, .canDeletes').forEach(function(elem) {
            elem.remove();
        });
        // Re-create hidden inputs for each rule (current state)
        selectedRoleGovernance.forEach(function(rule) {
            function addHidden(name, value, className) {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = name;
                input.value = value;
                input.className = className;
                confirmationForm.appendChild(input);
            }
            addHidden("roleSubjects[]", rule.roleSubjectID, "roleSubjects");
            addHidden("canGrants[]", rule.canGrant, "canGrants");
            addHidden("canRevokes[]", rule.canRevoke, "canRevokes");
            addHidden("canAlters[]", rule.canAlter, "canAlters");
            addHidden("canDeletes[]", rule.canDelete, "canDeletes");
        });
    }

    // Rule submission logic functionality
    confirmRuleChangesButton.addEventListener('click', function() {
        syncRuleHiddenInputs(); // Ensure hidden inputs match current checkbox state
        confirmationForm.action = "index.php?page=staff&action=changeManagementRules"
        confirmationTitle.textContent = "Change Rules?";
        buildRoleConfirmationMessage("Are you sure to change the management rules of the ", " role?");
        confirmationSubmit.value = "Yes Change";
        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");
        confirmation.style.display = 'flex';
    });

    // Show process tasks of the role function
    function setProcessTasks() {
        const container = processTaskContainer.querySelector('.container');
        container.innerHTML = ''; // safe clear

        document.querySelectorAll('.processTasks').forEach(function(elem) {
            elem.remove();
        });

        if (selectedRoleProcessTasks.length == 0) {
            tempElement = document.createElement("h2");
            tempElement.textContent = "No Processes Assigned";
            tempElement.className = "selfCenter centerMarginsSelf";
            container.appendChild(tempElement);
        }

        for (let i = 0; i < selectedRoleProcessTasks.length; i++) {
            tempDiv = document.createElement("div");
            tempDiv.className = "yellowTransBG roundedMin bordered centerColumnLayout regMinPadding noShrink shadowed";

            tempElement = document.createElement("h5");
            tempElement.className = "flexMax centerColumnLayout capitalFirst whiteText outlineText";
            tempElement.textContent = selectedRoleProcessTasks[i].name;
            tempDiv.appendChild(tempElement);

            tempElement = document.createElement("input");
            tempElement.type = "hidden";
            tempElement.name = "processTasks[]";
            tempElement.className = "processTasks";
            tempElement.value = selectedRoleProcessTasks[i].id;
            confirmationForm.appendChild(tempElement);

            tempElement = document.createElement("a");
            tempElement.className = "squareSize unitHeight centerColumnLayout norWestAbsolute closeCorner removeTask";
            tempElement.innerHTML = '<img src="../../Shared/Img/XIcon.png" alt="X">'; // safe
            tempElement.dataset.index = i;
            tempDiv.appendChild(tempElement);

            container.appendChild(tempDiv);
        };

        document.querySelectorAll('.removeTask').forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedRoleProcessTasks.splice(elem.dataset.index, 1);
                updateSelection();
            });
        });
    }

    // Task addition logic functionality
    addTaskButton.addEventListener('click', function() {
        confirmationTitle.textContent = "Add Process Task"; // hardcoded
        confirmationText.textContent = "Please select the processes you want to add to the role's process task list.";
        confirmationSubmit.classList.add("hidden");
        showRoleTaskAdditionBox();
    });

    function showRoleTaskAdditionBox() {
        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });

        tempDiv = document.createElement("div");
        tempDiv.className = 'midHeight scrollable columnLayout minGap tempElement';

        const currentTasks = new Set(selectedRoleProcessTasks.map(g => g.id));
        let hasSelection = false;

        processList.forEach((item) => {
            if (currentTasks.has(item.id)) return;

            tempElement = document.createElement('div');
            tempElement.className = 'tinHeight noShrink roundedMin centerColumnLayout bordered darkFadedBG emphasizedText capitalFirst tempElement addTaskElement';
            tempElement.textContent = item.name;
            tempElement.dataset.name = item.name;
            tempElement.dataset.id = item.id;
            tempDiv.appendChild(tempElement);

            hasSelection = true;
        });

        if (!hasSelection) {
            tempElement = document.createElement("h2");
            tempElement.textContent = "No Roles to Add"; // original text, keeping
            tempElement.className = "selfCenter centerMarginsSelf";
            tempDiv.appendChild(tempElement);
        }

        confirmationForm.appendChild(tempDiv);

        confirmation.style.display = 'flex';

        document.querySelectorAll('.addTaskElement').forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedRoleProcessTasks.push({
                    name: elem.dataset.name,
                    id: elem.dataset.id
                });
                updateSelection();
                showRoleTaskAdditionBox();
            });
        });
    }

    // Process Task submission logic functionality
    confirmTaskChangesButton.addEventListener('click', function() {
        confirmationForm.action = "index.php?page=staff&action=changeProcessTasks"
        confirmationTitle.textContent = "Change Process Tasks?";
        buildRoleConfirmationMessage("Are you sure to change the process tasks of the ", " role?");
        confirmationSubmit.value = "Yes Change";
        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");
        confirmation.style.display = 'flex';
    });

    // Added cancellation events
    confirmationCancel.addEventListener('click', function() {
        confirmationSubmit.classList.remove("hidden");
        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });
    });

    confirmationBG.addEventListener('click', function() {
        confirmationSubmit.classList.remove("hidden");
        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });
    });
</script>

</html>