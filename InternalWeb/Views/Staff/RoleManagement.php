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
                        <?php foreach ($roleList as $role): ?>
                            <div class="tinHeight noShrink roundedMin centerHoriRowLayout clickable shadowed fixedScreen roleElement"
                                data-id="<?= $role['id'] ?>" data-name="<?= $role['name'] ?>">
                                <h3 class="gradientDiagBG flexMid centerColumnLayout fullHeight whiteText skewedXNegBG shadowed capitalFirst"><span><?= $role['name'] ?></span></h3>
                                <b class="flexMin whiteBG fullHeight centerColumnLayout"><?= $role['count'] ?> Users</b>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="rowLayout minGap souEastAbsolute">
                        <?php if (in_array("canCreateRoles", $_SESSION['permissions'])): ?>
                            <a href="#" class="roundedMin centerColumnLayout importantInput regPadding emphasizedText" id="createRoleButton">Create Role</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="gradientBorderDiag"></div>
            </section>
            <section class="columnLayout midGap flexMid">
                <section class="centerColumnLayout roundedMid minGap">
                    <div class="centerHoriRowLayout minGap box roundedMid fullHeight fullWidth">
                        <h1 id="selectedRoleTitle" class="capitalFirst">No Role Selected</h1>
                        <?php if (in_array("canDeleteRoles", $_SESSION['permissions'])): ?>
                            <button type="button" class="criticalInput centerColumnLayout eastAbsolute hidden" id="deleteButton">
                                <img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors">
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
                <section class="centerColumnLayout roundedMid minGap flexMid">
                    <div class="columnLayout minGap box roundedMid fullHeight fullWidth">
                        <h2>Permissions:</h2>
                        <div class="flexMax columnLayout minGap">
                            <div class="fullWidth columnLayout tinGap noMinHeight flexMin noFlexBasis">
                                <h3>Assigned:</h3>
                                <div class="gridCenterFlex minGap scrollable flexMax" id="assignedPermsContainer">
                                    <h2 class="selfCenter">No Role Selected</h2>
                                </div>
                            </div>
                            <div class="fullWidth columnLayout tinGap noMinHeight flexMin noFlexBasis">
                                <h3>Available:</h3>
                                <div class="gridCenterFlex minGap scrollable flexMax" id="availablePermsContainer">
                                    <h2 class="selfCenter">No Role Selected</h2>
                                </div>
                            </div>
                            <div>
                                <button type="button" class="importantInput fullWidth hidden" id="submitRolePermissionsButton">Confirm Changes</button>
                            </div>
                        </div>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
                <section class="centerColumnLayout roundedMid flexMin hidden">
                    <div class="columnLayout minGap box roundedMid fullHeight fullWidth">
                        <h2>Management Governance Rules:</h2>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
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
    const createRoleButton = document.getElementById('createRoleButton');
    const deleteButton = document.getElementById('deleteButton');
    const rolePermissionsList = <?php echo json_encode($rolePermissionsList); ?>;
    const userPermissionsList = <?php echo json_encode($userPermissionsList); ?>;

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

    let selectedRolePermissions;
    let selectedRoleName;
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
                selectedID.value = elem.dataset.id;

                submitRolePermissionsButton.classList.remove("hidden");
                deleteButton.classList.remove("hidden");

                setAvailablePerms();
                setAssignedPerms();
            });
        });
    });

    let tempPermDiv;

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
            tempPermDiv = document.createElement("div");
            tempPermDiv.className = "noShrink fitHeight roundedMin centerRowLayout minGap yellowTransBG regMinPadding bordered";

            tempElement = document.createElement("b");
            tempElement.className = "flexMax centerText capitalFirst";
            tempElement.textContent = formatCamelCase(selectedRolePermissions[i].name);
            tempPermDiv.appendChild(tempElement);

            if (!unrevokablePerms.includes(selectedRolePermissions[i].id)) {
                tempElement = document.createElement("a");
                tempElement.className = "squareSize unitHeight centerColumnLayout permissionRemove";
                tempElement.innerHTML = '<img src="../../Shared/Img/XIcon.png" alt="X">';
                tempElement.dataset.index = i;
                tempPermDiv.appendChild(tempElement);
            }

            tempElement = document.createElement("input");
            tempElement.type = "hidden";
            tempElement.name = "newPermissions[]";
            tempElement.className = "newPermissions";
            tempElement.value = selectedRolePermissions[i].id;
            confirmationForm.appendChild(tempElement);

            assignedPermsContainer.appendChild(tempPermDiv);
        };

        document.querySelectorAll('.permissionRemove').forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedRolePermissions.splice(elem.dataset.index, 1);
                setAvailablePerms();
                setAssignedPerms();
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
            tempPermDiv = document.createElement("div");
            tempPermDiv.className = "noShrink fitHeight roundedMin centerRowLayout minGap darkFadedBG regMinPadding bordered choicePermission";
            tempPermDiv.dataset.id = selectedRoleAvailablePerms[i].id;
            tempPermDiv.dataset.name = selectedRoleAvailablePerms[i].name;

            tempElement = document.createElement("b");
            tempElement.className = "flexMax centerText capitalFirst";
            tempElement.textContent = formatCamelCase(selectedRoleAvailablePerms[i].name);
            tempPermDiv.appendChild(tempElement);

            availablePermsContainer.appendChild(tempPermDiv);
        };

        document.querySelectorAll('.choicePermission').forEach(function(elem) {
            elem.addEventListener('click', function() {
                selectedRolePermissions.push({
                    id: elem.dataset.id,
                    name: elem.dataset.name
                });
                setAvailablePerms();
                setAssignedPerms();
            });
        });
    }

    //Confirmation Box for permission change functionality
    submitRolePermissionsButton.addEventListener('click', function() {
        confirmationForm.action = "index.php?page=staff&action=changeRolePermissions"

        confirmationTitle.innerHTML = "Change Role's Permissions?";
        confirmationText.innerHTML = 'Are you sure to change the permissions of the <span class="capitalFirst">' + selectedRoleName + '</span> role?';
        confirmationSubmit.value = "Yes Change";
        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");

        confirmation.style.display = 'flex';
    });

    //Role creation logic functionality
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

    //Role deletion logic functionality
    deleteButton.addEventListener('click', function() {
        confirmationForm.action = "index.php?page=staff&action=deleteRole"

        confirmationTitle.innerHTML = "Delete Role";
        confirmationText.innerHTML = 'Are you sure to delete the <span class="capitalFirst">' + selectedRoleName + '</span> role?';
        confirmationSubmit.value = "Yes Delete";
        confirmationSubmit.classList.remove("yellowBG", "whiteText", "noBorder");

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