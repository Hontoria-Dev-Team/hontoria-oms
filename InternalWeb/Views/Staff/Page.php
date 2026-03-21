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
        <?php include("../Views/.Components/ErrorBox.php"); ?>
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
                    <section class="minGap scrollable gridFlexMid" id="staffList">
                        <?php
                        $userRolesMap = [];
                        foreach ($userRoles as $role) {
                            $userRolesMap[$role['userID']][] = $role['name'];
                        }
                        ?>
                        <?php foreach ($staffList as $staff): ?>
                            <?php
                            $fullName = trim("{$staff['firstName']} " . ($staff['middleName'] ? substr($staff['middleName'], 0, 1) . '.' : '') . " {$staff['lastName']}");
                            $status = $staff['isOnline'] ? ($staff['isActive'] ? 'Active' : 'Idle') : 'Offline';
                            $statusClass = $staff['isOnline'] ? ($staff['isActive'] ? 'active' : 'idle') : '';
                            $roles = $userRolesMap[$staff['id']] ?? [];
                            $rolesText = !empty($roles) ? implode(', ', $roles) : 'Unset Role';
                            ?>
                            <div class="minHeight minPadding roundedMin rowLayout minGap flexStatic staffElement <?= $statusClass ?>"
                                data-id="<?= $staff['id'] ?>" data-name="<?= htmlspecialchars($fullName) ?>" data-roles="<?= $rolesText ?>">
                                <div style="background: var(--gray);" class="flexMid roundedMin centerColumnLayout">
                                    <img src="../../Shared/Img/PersonIcon.png" alt="Person">
                                </div>
                                <div class="flexMax centerHoriColumnLayout">
                                    <h5><?= htmlspecialchars($fullName) ?></h5>
                                    <h6 class="capitalFirst">(<?= $rolesText ?>)</h6>
                                </div>
                                <div class="flexMin status roundedMin minPadding centerColumnLayout">
                                    <h5><?= $status ?></h5>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="tinHeight"></div>
                    </section>
                    <div class="rowLayout minGap souEastAbsolute">
                        <a href="index.php?page=staff&action=create" class="roundedMin centerColumnLayout importantInput regPadding emphasizedText">Create Staff</a>
                    </div>
                </div>
                <div class="gradientBorderDiag"></div>
            </section>
            <section class="columnLayout midGap flexMin">
                <section class="box centerColumnLayout roundedMid minGap flexMin">
                    <h3 id="selectedStaffName">No Staff Selected</h3>
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
                <section class="box centerColumnLayout roundedMid flexMid">
                    <div class="gradientBorderDiag"></div>
                </section>
            </section>
        </section>
    </main>
    <?php include("../Views/.Components/ConfirmationBox.php"); ?>
</body>
<script src="../.JS/ConfirmationBox.js"></script>
<script>
    const staffElements = document.querySelectorAll('.staffElement');
    const nameDisplay = document.getElementById('selectedStaffName');
    const staffActionsstaffActions = document.getElementById('staffActions');
    const rolesText = document.getElementById('rolesText');
    const modifyRolesButton = document.getElementById('modifyRolesButton');
    const roles = <?php echo json_encode($roleList); ?>;
    const userRoles = <?php echo json_encode($userRoles); ?>;

    let name;
    let id;

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

                nameDisplay.textContent = name;
                nameDisplay.style.alignSelf = 'baseline';

                rolesText.parentElement.classList.remove("hidden");
                staffActions.classList.remove("hidden");
                rolesText.textContent = elem.dataset.roles;
            });
        });
    });

    // Delete employee confirmation and logic script
    const deletedID = document.createElement("input");
    deletedID.type = "hidden";
    deletedID.name = "deletedID";
    confirmationForm.appendChild(deletedID);

    document.getElementById('deleteButton').addEventListener('click', function() {
        confirmationForm.action = "index.php?page=staff&action=delete"

        confirmationTitle.innerHTML = "Delete Account?";
        confirmationText.innerHTML = "Are you sure to delete the account of:<br>" + name + "?";
        confirmationSubmit.value = "Yes Delete";
        confirmationSubmit.classList.remove("yellowBG", "whiteText", "noBorder");

        deletedID.value = id;

        confirmation.style.display = 'flex';
    });

    // Change User Role Box Function logic
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

    let currentRolesContainer;
    let choiceRolesContainer;
    let currentRoles;
    let choiceRoles;
    let tempElement;
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
        choiceRolesContainer.className = 'gridCenterHoriFlex minGap tempElement';
        confirmationForm.appendChild(choiceRolesContainer);

        tempElement = document.createElement("b");
        tempElement.textContent = "All Roles:";
        tempElement.classList.add("tempElement");
        confirmationForm.appendChild(tempElement);

        currentRolesContainer = document.createElement("div");
        currentRolesContainer.id = "currentRolesContainer";
        currentRolesContainer.className = 'gridCenterHoriFlex minGap tempElement';
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

            tempRoleXButton = document.createElement("a");
            tempRoleXButton.className = "squareSize unitHeight centerColumnLayout roleRemove";
            tempRoleXButton.innerHTML = '<img src="../../Shared/Img/XIcon.png" alt="X">';
            tempRoleXButton.dataset.index = i;
            tempRoleDiv.appendChild(tempRoleXButton);

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

        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });
    });

    confirmationBG.addEventListener('click', function() {
        confirmationForm.parentElement.classList.add("minGap");

        document.querySelectorAll('.tempElement').forEach(function(elem) {
            elem.remove();
        });
    });
</script>
<script src="../.JS/AutoRefresher.js"></script>

</html>