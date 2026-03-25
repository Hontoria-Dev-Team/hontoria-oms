<!DOCTYPE html>
<html>

<head>
    <title>Role Management - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/StaffPage.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <span class="centerHoriRowLayout midGap">
            <?php include("../Views/.Components/BackLink.php"); ?>
            <h1 class="titleLogo minGap tinHeight">
                <img src="../../Shared/Img/PeopleIcon.png" alt="People"> Role Management
            </h1>
        </span>
        <?php include("../Views/.Components/ErrorBox.php"); ?>
        <section class="rowLayout flexMax midGap">
            <section class="flexMid roundedMid centerColumnLayout">
                <div class="columnLayout minGap box roundedMid fullHeight fullWidth">
                    <h2>Roles:</h2>
                    <div id="processesContainer" class="scrollable columnLayout minGap regMinPadding">
                        <?php foreach ($roleTally as $role): ?>
                            <div class="tinHeight noShrink roundedMin centerHoriRowLayout clickable shadowed fixedScreen roleElement"
                                data-id="<?= $role['id'] ?>" data-name="<?= $role['name'] ?>">
                                <h3 class="gradientDiagBG flexMid centerColumnLayout fullHeight whiteText skewedXNegBG shadowed capitalFirst"><span><?= $role['name'] ?></span></h3>
                                <b class="flexMin whiteBG fullHeight centerColumnLayout"><?= $role['count'] ?> Users</b>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="rowLayout minGap souEastAbsolute">
                        <?php if (in_array("canCreateRoles", $_SESSION['permissions'])): ?>
                            <a class="roundedMin centerColumnLayout importantInput regPadding emphasizedText clickable" id="createRoleButton">Create Role</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="gradientBorderDiag"></div>
            </section>
            <section class="columnLayout midGap flexMax">
                <section class="centerColumnLayout roundedMid minGap">
                    <div class="centerHoriRowLayout minGap box roundedMid fullHeight fullWidth">
                        <h2 id="selectedRoleTitle" class="capitalFirst">No Role Selected</h2>
                        <?php if (in_array("canDeleteRoles", $_SESSION['permissions'])): ?>
                            <!-- SOMEHOW MAKE THIS UNCLICKABLE FRONTEND IF WE CANT DELETE IT -->
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
                                    <div class="gridCenterFlex minGap scrollable flexMax" id="assignedPermsContainer">
                                        <h2 class="selfCenter">No Role Selected</h2>
                                    </div>
                                </div>
                                <div class="fullWidth columnLayout tinGap noMinHeight flexMin noFlexBasis">
                                    <h3>Available Permissions:</h3>
                                    <div class="gridCenterFlex minGap scrollable flexMax" id="availablePermsContainer">
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
                                <div class="flexMax columnLayout minGap scrollable container"></div>
                                <button type="button" class="importantInput fullWidth" id="confirmRuleChangesButton">Confirm Changes</button>
                            </div>
                            <div class="gradientBorderDiag"></div>
                        </section>
                        <section class="centerColumnLayout roundedMid minGap flexMid" id="processTaskContainer">
                            <div class="columnLayout minGap box roundedMid fullHeight fullWidth">
                                <h3 class="centerHoriRowLayout">Process Task Access:
                                    <button type="button" id="addTaskButton" class="importantInput eastAbsolute edgeCorner">Add Task</button>
                                </h3>
                                <div class="flexMax" style="background-color: red;"></div>
                                <button type="button" class="importantInput fullWidth">Confirm Changes</button>
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
    const roleList = <?php echo json_encode($roleList); ?>;
    const roleTally = <?php echo json_encode($roleTally); ?>;
    const rolePermissionsList = <?php echo json_encode($rolePermissionsList); ?>;
    const userPermissionsList = <?php echo json_encode($userPermissionsList); ?>;
    const roleGovernanceList = <?php echo json_encode($roleGovernanceList); ?>;

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

    const alterableRoles = roleTally.map(item => item.id);

    let selectedRolePermissions;
    let selectedRoleName;
    let selectedRoleGovernance;
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

                selectedID.value = elem.dataset.id;

                submitRolePermissionsButton.classList.remove("hidden");
                deleteButton.classList.remove("hidden");

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
    }

    let tempDiv;

    // Show the assigned permissions of the role function
    let unrevokablePerms;

    function setAssignedPerms() {
        assignedPermsContainer.innerHTML = '';

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
            tempDiv.className = "noShrink fitHeight roundedMin centerRowLayout minGap yellowTransBG regMinPadding bordered";

            tempElement = document.createElement("b");
            tempElement.className = "flexMax centerText capitalFirst";
            tempElement.textContent = formatCamelCase(selectedRolePermissions[i].name);
            tempDiv.appendChild(tempElement);

            if (!unrevokablePerms.includes(selectedRolePermissions[i].id)) {
                tempElement = document.createElement("a");
                tempElement.className = "squareSize unitHeight centerColumnLayout permissionRemove";
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
        availablePermsContainer.innerHTML = '';

        selectedRoleAvailablePerms = getDirectionalArrayDiff(userPermissionsList, selectedRolePermissions, 'id');

        if (selectedRoleAvailablePerms.length == 0) {
            tempElement = document.createElement("h2");
            tempElement.textContent = "No More Permissions Available To Assign";
            tempElement.className = "selfCenter";
            availablePermsContainer.appendChild(tempElement);
        }

        for (let i = 0; i < selectedRoleAvailablePerms.length; i++) {
            tempDiv = document.createElement("div");
            tempDiv.className = "noShrink fitHeight roundedMin centerRowLayout minGap darkFadedBG regMinPadding bordered choicePermission";
            tempDiv.dataset.id = selectedRoleAvailablePerms[i].id;
            tempDiv.dataset.name = selectedRoleAvailablePerms[i].name;

            tempElement = document.createElement("b");
            tempElement.className = "flexMax centerText capitalFirst";
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
        managementRulesContainer.querySelector('.container').innerHTML = '';

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
            tempElement.className = "selfCenter centerMarginsSelf";
            managementRulesContainer.querySelector('.container').appendChild(tempElement);
        }

        for (let i = 0; i < selectedRoleGovernance.length; i++) {
            tempDiv = document.createElement("div");
            tempDiv.className = "yellowTransBG roundedMin bordered centerColumnLayout regMinPadding";

            if (!alterableRoles.includes(selectedRoleGovernance[i].roleSubjectID)) {
                tempDiv.classList.add("unclickable", "faded");
            }

            tempElement = document.createElement("b");
            tempElement.className = "flexMax centerColumnLayout capitalFirst";
            tempElement.textContent = rolesName[selectedRoleGovernance[i].roleSubjectID];
            tempDiv.appendChild(tempElement);

            tempElement = document.createElement("hr");
            tempDiv.appendChild(tempElement);

            tempElement = document.createElement("div");
            tempElement.className = "centerRowLayout minGap";
            tempElement.innerHTML = `
                <div class="centerRowLayout tinGap canGrantCheck" data-index="${i}">
                    <input type="checkbox" name="canGrant" ${selectedRoleGovernance[i].canGrant == 1 ? 'checked' : ''}>
                    <p>Grant</p>
                </div>

                <div class="centerRowLayout tinGap canRevokeCheck" data-index="${i}">
                    <input type="checkbox" name="canRevoke" ${selectedRoleGovernance[i].canRevoke == 1 ? 'checked' : ''}>
                    <p>Revoke</p>
                </div>

                <div class="centerRowLayout tinGap canAlterCheck" data-index="${i}">
                    <input type="checkbox" name="canAlter" ${selectedRoleGovernance[i].canAlter == 1 ? 'checked' : ''}>
                    <p>Alter</p>
                </div>

                <div class="centerRowLayout tinGap canDeleteCheck" data-index="${i}">
                    <input type="checkbox" name="canDelete" ${selectedRoleGovernance[i].canDelete == 1 ? 'checked' : ''}>
                    <p>Delete</p>
                </div>
            `;
            tempDiv.appendChild(tempElement);

            tempElement = document.createElement("input");
            tempElement.type = "hidden";
            tempElement.name = "roleSubjects[]";
            tempElement.className = "roleSubjects";
            tempElement.value = selectedRoleGovernance[i].roleSubjectID;
            confirmationForm.appendChild(tempElement);

            tempElement = document.createElement("input");
            tempElement.type = "hidden";
            tempElement.name = "canGrants[]";
            tempElement.className = "canGrants";
            tempElement.value = selectedRoleGovernance[i].canGrant;
            confirmationForm.appendChild(tempElement);

            tempElement = document.createElement("input");
            tempElement.type = "hidden";
            tempElement.name = "canRevokes[]";
            tempElement.className = "canRevokes";
            tempElement.value = selectedRoleGovernance[i].canRevoke;
            confirmationForm.appendChild(tempElement);

            tempElement = document.createElement("input");
            tempElement.type = "hidden";
            tempElement.name = "canAlters[]";
            tempElement.className = "canAlters";
            tempElement.value = selectedRoleGovernance[i].canAlter;
            confirmationForm.appendChild(tempElement);

            tempElement = document.createElement("input");
            tempElement.type = "hidden";
            tempElement.name = "canDeletes[]";
            tempElement.className = "canDeletes";
            tempElement.value = selectedRoleGovernance[i].canDelete;
            confirmationForm.appendChild(tempElement);

            tempElement = document.createElement("a");
            tempElement.className = "squareSize unitHeight centerColumnLayout norWestAbsolute closeCorner removeRule";
            tempElement.innerHTML = '<img src="../../Shared/Img/XIcon.png" alt="X">';
            tempElement.dataset.index = i;
            tempDiv.appendChild(tempElement);

            managementRulesContainer.querySelector('.container').appendChild(tempDiv);
        };

        document.querySelectorAll('.removeRule').forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedRoleGovernance.splice(elem.dataset.index, 1);
                updateSelection();
            });
        });

        document.querySelectorAll('.canGrantCheck').forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedRoleGovernance[elem.dataset.index].canGrant = selectedRoleGovernance[elem.dataset.index].canGrant ? 0 : 1;
                updateSelection();
            });
        });

        document.querySelectorAll('.canRevokeCheck').forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedRoleGovernance[elem.dataset.index].canRevoke = selectedRoleGovernance[elem.dataset.index].canRevoke ? 0 : 1;
                updateSelection();
            });
        });

        document.querySelectorAll('.canAlterCheck').forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedRoleGovernance[elem.dataset.index].canAlter = selectedRoleGovernance[elem.dataset.index].canAlter ? 0 : 1;
                updateSelection();
            });
        });

        document.querySelectorAll('.canDeleteCheck').forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedRoleGovernance[elem.dataset.index].canDelete = selectedRoleGovernance[elem.dataset.index].canDelete ? 0 : 1;
                updateSelection();
            });
        });
    }

    // Confirmation Box for permission change functionality
    submitRolePermissionsButton.addEventListener('click', function() {
        confirmationForm.action = "index.php?page=staff&action=changeRolePermissions"

        confirmationTitle.innerHTML = "Change Role's Permissions?";
        confirmationText.innerHTML = 'Are you sure to change the permissions of the <span class="capitalFirst">' + selectedRoleName + '</span> role?';
        confirmationSubmit.value = "Yes Change";
        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");

        confirmation.style.display = 'flex';
    });

    // Role creation logic functionality
    createRoleButton.addEventListener('click', function() {
        confirmationTitle.innerHTML = "Create Service";
        confirmationForm.action = "index.php?page=staff&action=createRole";

        confirmationText.innerHTML = "Please enter a unique role name.";
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

    // Role deletion logic functionality
    deleteButton.addEventListener('click', function() {
        confirmationForm.action = "index.php?page=staff&action=deleteRole"

        confirmationTitle.innerHTML = "Delete Role?";
        confirmationText.innerHTML = 'Are you sure to delete the <span class="capitalFirst">' + selectedRoleName + '</span> role?';
        confirmationSubmit.value = "Yes Delete";
        confirmationSubmit.classList.remove("yellowBG", "whiteText", "noBorder");

        confirmation.style.display = 'flex';
    });

    // Rule addition logic functionality
    addRuleButton.addEventListener('click', function() {
        confirmationTitle.innerHTML = "Add Rule";

        confirmationText.innerHTML = "Please select the roles you want to add to the role's rule list.";
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

    // Rule submission logic functionality
    confirmRuleChangesButton.addEventListener('click', function() {
        confirmationForm.action = "index.php?page=staff&action=changeManagementRules"

        confirmationTitle.innerHTML = "Change Rules?";
        confirmationText.innerHTML = 'Are you sure to change the management rules of the <span class="capitalFirst">' + selectedRoleName + '</span> role?';
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