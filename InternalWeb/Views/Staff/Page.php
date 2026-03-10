<!DOCTYPE html>
<html>

<head>
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/StaffPage.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout leftAlign midGap midPadding flexStretch">
        <h1 class="titleLogo minGap">
            <img src="../../Shared/Img/PeopleIcon.png" alt="People"> Staff Panel
        </h1>

        <section class="rowLayout flexMax midGap">
            <div class="gradientBorderDiag roundedMid flexMid">
                <section class="box columnLayout roundedMid minGap">
                    <form method="GET" action="index.php?page=staff" class="rowLayout fullWidth minGap">
                        <input type="hidden" name="page" value="staff">
                        <input type="hidden" name="action" value="filter">

                        <div class="iconInput flexMax centerRowLayout">
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
                    <section class="minHeight minGap scrollable gridFlexMid" id="staffList">
                        <?php foreach ($staffList as $staff): ?>
                            <?php
                            $fullName = trim("{$staff['firstName']} " . ($staff['middleName'] ? substr($staff['middleName'], 0, 1) . '.' : '') . " {$staff['lastName']}");
                            $status = $staff['isOnline'] ? ($staff['isActive'] ? 'Active' : 'Idle') : 'Offline';
                            $statusClass = $staff['isOnline'] ? ($staff['isActive'] ? 'active' : 'idle') : '';
                            ?>
                            <div class="minHeight minPadding roundedMin rowLayout minGap flexStatic staffElement <?= $statusClass ?>"
                                data-id="<?= $staff['id'] ?>" data-name="<?= htmlspecialchars($fullName) ?>" data-can-manage-staff="<?= $staff['canManageStaff'] ?>">
                                <div style="background: var(--gray);" class="flexMid roundedMin centerColumnLayout">
                                    <img src="../../Shared/Img/PersonIcon.png" alt="Person">
                                </div>
                                <div class="flexMax centerHoriColumnLayout">
                                    <h5><?= htmlspecialchars($fullName) ?></h5>
                                </div>
                                <div class="flexMin status roundedMin minPadding centerColumnLayout">
                                    <h5><?= $status ?></h5>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </section>
                    <a href="index.php?page=staff&action=create" id="createButton" class="roundedMin centerColumnLayout">Create Staff</a>
                </section>
            </div>
            <section class="columnLayout midGap flexMin">
                <div class="gradientBorderDiag roundedMid flexMid">
                    <section class="box centerColumnLayout roundedMid minGap" id="detailsPanel">
                        <h3 id="selectedStaffName">No Staff Selected</h3>
                        <p style="display: none;">Permissions: </p>
                        <form id="permissionForm" action="index.php?page=staff&action=updatePermissions" method="POST" class="columnLayout fullWidth minGap" style="display:none;">
                            <input type="hidden" name="selectedID" id="selectedStaffId">
                            <div class="rowLayout centerRowLayout minGap">
                                <input type="checkbox" name="canManageStaff" id="permCheckbox" value="1">
                                <label for="permCheckbox">Can Manage Staff</label>
                            </div>

                            <div class="rowLayout fullWidth minGap">
                                <input type="submit" class="importantInput flexMax" value="Update Permissions">
                                <input type="button" class="criticalInput flexMin" id="deleteButton" value="Delete">
                            </div>
                        </form>
                    </section>
                </div>
                <div class="gradientBorderDiag roundedMid flexMax">
                    <section class="box centerColumnLayout roundedMid">
                    </section>
                </div>
            </section>
        </section>
    </main>
    <section id="deleteConfirmation" class="centerColumnLayout" style="display: none;">
        <div class="gradientBorderDiag roundedMid maxWidth midHeight">
            <section class="box centerColumnLayout roundedMid minGap">
                <h1>Delete Account?</h1>
                <h4 id="confirmationText">Are you sure to delete Victor's Account?</h4>
                <form id="permissionForm" action="index.php?page=staff&action=delete" method="POST" class="rowLayout fullWidth minGap">
                    <input type="hidden" name="deletedID" id="deletedStaffId">
                    <input type="submit" class="criticalInput flexMax" value="Yes Delete">
                    <input type="button" class="importantInput flexMax" id="cancelDeleteButton" value="No Cancel">
                </form>
            </section>
        </div>
    </section>
    <div id="confirmationBackground" style="display: none;"></div>
</body>
<script>
    const staffElements = document.querySelectorAll('.staffElement');
    const nameDisplay = document.getElementById('selectedStaffName');
    const form = document.getElementById('permissionForm');
    const idSelected = document.getElementById('selectedStaffId');
    const idDeleted = document.getElementById('deletedStaffId');
    const checkbox = document.getElementById('permCheckbox');
    const detailsPanel = document.getElementById('detailsPanel');
    const permissionsParagraph = detailsPanel.querySelector('p');
    const confirmation = document.getElementById('deleteConfirmation');
    const confirmationBG = document.getElementById('confirmationBackground');
    const text = document.getElementById('confirmationText');

    let name;
    let id;
    let hasPerm;

    // Reactive clickable employee data script
    document.addEventListener('DOMContentLoaded', function() {
        staffElements.forEach(function(elem) {
            elem.addEventListener('click', function() {
                name = elem.dataset.name;
                id = elem.dataset.id;
                hasPerm = elem.dataset.canManageStaff == '1';

                nameDisplay.textContent = name;
                nameDisplay.style.alignSelf = 'baseline';

                idSelected.value = id;
                checkbox.checked = hasPerm;

                form.style.display = 'flex';

                permissionsParagraph.style.display = 'unset';
                permissionsParagraph.style.alignSelf = 'baseline';
            });
        });
    });

    // Delete employee confirmation and logic script
    document.getElementById('deleteButton').addEventListener('click', function() {
        text.innerHTML = "Are you sure to delete the account of:<br>" + name + "?";

        idDeleted.value = id;

        confirmation.style.display = 'flex';
        confirmationBG.style.display = 'unset';
    });

    document.getElementById('cancelDeleteButton').addEventListener('click', function() {
        confirmation.style.display = 'none';
        confirmationBG.style.display = 'none';
    });
    document.getElementById('confirmationBackground').addEventListener('click', function() {
        confirmation.style.display = 'none';
        confirmationBG.style.display = 'none';
    });
</script>
<script src="../.JS/AutoRefresher.js"></script>

</html>
