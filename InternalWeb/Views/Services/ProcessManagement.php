<!DOCTYPE html>
<html>

<head>
    <title>Process Management - Hontoria OMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/StaffPage.css">
    <style>
        @media (max-width: 800px) {
            .asideLayout>main>section {
                min-width: unset !important;
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
    </style>
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <span class="centerHoriRowLayout midGap">
            <?php include("../Views/.Components/BackLink.php"); ?>
            <h1 class="titleLogo minGap tinHeight">
                <img src="../../Shared/Img/PeopleIcon.png" alt="People"> Process Management
            </h1>
        </span>
        <?php include("../Views/.Components/MessageBox.php"); ?>
        <section class="rowLayout flexMax midGap">
            <section class="flexMax roundedMid centerColumnLayout">
                <div class="box roundedMid fullHeight fullWidth columnLayout">
                    <div class="gridFlex midGrids minGap noFlexBasis noMinHeight scrollable contentFlexStart fixedScreen">
                        <?php foreach ($processList as $process): ?>
                            <form method="POST" action="index.php?page=services&action=updateProcess"
                                class="centerHoriColumnLayout minGap roundedMin yellowTransBG yellowBorder regMidPadding shadowed processUpdateForm">
                                <h2 class="centerText"><?= htmlspecialchars($process['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                                <div class="centerRowLayout minGap">
                                    <div class="centerRowLayout tinGap canGrantCheck" data-index="0">
                                        <input type="checkbox" name="hasGCAccess" value="1" <?= $process['hasGCAccess'] ? 'checked' : '' ?>>
                                        <p>Group Chat Access</p>
                                    </div>
                                    <div class="centerRowLayout tinGap unitHeight assignRange flexMax scaledUpMin">
                                        <img src="../../Shared/Img/PeopleIcon.png" alt="People" class="unitHeight">
                                        <div class="centerHoriRowLayout tinGap">
                                            <label for="minAssign">Min</label>
                                            <input type="number" name="minAssign" required="true" class="unitHeight unitWidth regTinPadding centerText roundedTin minAssign"
                                                value="<?= htmlspecialchars($process['minAssignDefault'], ENT_QUOTES, 'UTF-8') ?>" min="1" max="50">
                                        </div>
                                        <div class="centerHoriRowLayout tinGap">
                                            <label for="maxAssign">Max</label>
                                            <input type="number" name="maxAssign" required="true" class="unitHeight unitWidth regTinPadding centerText roundedTin maxAssign"
                                                value="<?= htmlspecialchars($process['maxAssignDefault'], ENT_QUOTES, 'UTF-8') ?>" max="50">
                                        </div>
                                    </div>
                                </div>
                                <div class="rowLayout minGap canGrantCheck" data-index="0">
                                    <b>Design Access</b>
                                    <select name="designAccess" class="flexMax">
                                        <option value="no access" <?= $process['designAccess'] === 'no access' ? 'selected' : '' ?>>No Access</option>
                                        <option value="view only" <?= $process['designAccess'] === 'view only' ? 'selected' : '' ?>>View Only</option>
                                        <option value="view & update" <?= $process['designAccess'] === 'view & update' ? 'selected' : '' ?>>View & Update</option>
                                    </select>
                                </div>
                                <div class="rowLayout minGap canGrantCheck" data-index="0">
                                    <b>Variable List Access</b>
                                    <select name="variableListAccess" class="flexMax">
                                        <option value="no access" <?= $process['variableListAccess'] === 'no access' ? 'selected' : '' ?>>No Access</option>
                                        <option value="view only" <?= $process['variableListAccess'] === 'view only' ? 'selected' : '' ?>>View Only</option>
                                        <option value="view & update" <?= $process['variableListAccess'] === 'view & update' ? 'selected' : '' ?>>View & Update</option>
                                    </select>
                                </div>
                                <div class="rowLayout minGap">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($process['id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="submit" name="submit" value="Update Process" class="flexMax importantInput shadowed updateSubmitButton">
                                    <button type="button" class="deleteButton criticalInput centerColumnLayout shadowed deleteProcessButton"
                                        data-id="<?= htmlspecialchars($process['id'], ENT_QUOTES, 'UTF-8') ?>" data-name="<?= htmlspecialchars($process['name'], ENT_QUOTES, 'UTF-8') ?>">
                                        <img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors">
                                    </button>
                                </div>
                            </form>
                        <?php endforeach; ?>
                    </div>
                    <div class="rowLayout minGap souEastAbsolute">
                        <a id="createProcessButton" class="roundedMin centerColumnLayout importantInput regPadding emphasizedText">Create Process</a>
                    </div>
                </div>
                <div class="gradientBorderDiag"></div>
            </section>
        </section>
    </main>
    <?php include("../Views/.Components/ConfirmationBox.php"); ?>
</body>
<script src="../.JS/ConfirmationBox.js"></script>
<script src="../.JS/MiscHelpers.js"></script>
<script>
    const userPermissions = <?php echo json_encode($_SESSION['permissions'] ?? []); ?>;
    const lockedProcessIdentifiers = <?php echo json_encode($lockedProcessIdentifiers ?? []); ?>;

    const createProcessButton = document.getElementById('createProcessButton');
    const deleteProcessButtons = document.querySelectorAll('.deleteProcessButton');
    const processUpdateForms = document.querySelectorAll('.processUpdateForm');

    let temporaryDiv;
    let temporaryElement;

    // Build a Set of locked identifiers (as integers) for fast lookup
    const lockedSet = new Set(lockedProcessIdentifiers.map(Number));

    // Check if user can manage processes at all
    const hasManagePermission = userPermissions.includes('canManageServiceProcesses');

    // Disable creation button if no permission
    if (!hasManagePermission && createProcessButton) {
        createProcessButton.classList.add('faded', 'unclickable');
        createProcessButton.style.pointerEvents = 'none';
    }

    // For each process form, disable inputs & submit if locked or no permission
    processUpdateForms.forEach(form => {
        const processIdInput = form.querySelector('input[name="id"]');
        if (!processIdInput) return;
        const processIdentifier = Number(processIdInput.value);

        const isLocked = lockedSet.has(processIdentifier);
        const shouldDisable = !hasManagePermission || isLocked;

        if (shouldDisable) {
            const submitButton = form.querySelector('.updateSubmitButton');
            if (submitButton) {
                submitButton.classList.add('faded', 'unclickable');
                submitButton.disabled = true;
            }
            const inputs = form.querySelectorAll('input:not([type="hidden"]), select');
            inputs.forEach(input => {
                input.disabled = true;
            });
        }
    });

    // For delete buttons, disable if locked or no permission
    deleteProcessButtons.forEach(button => {
        const processIdentifier = Number(button.dataset.id);
        const isLocked = lockedSet.has(processIdentifier);
        const shouldDisable = !hasManagePermission || isLocked;

        if (shouldDisable) {
            button.classList.add('faded', 'unclickable');
            button.disabled = true;
        }
    });

    document.addEventListener("DOMContentLoaded", () => {
        confirmationCancel.value = "No Cancel";
    });

    // Hidden input for process identifier in confirmation form
    const selectedProcessIdentifierInput = document.createElement("input");
    selectedProcessIdentifierInput.type = "hidden";
    selectedProcessIdentifierInput.name = "selectedID";
    confirmationForm.appendChild(selectedProcessIdentifierInput);

    // Process Creation
    createProcessButton.addEventListener('click', function() {
        if (!hasManagePermission) {
            alert("You do not have permission to create processes.");
            return;
        }

        confirmationTitle.innerHTML = "Create Process";
        confirmationForm.action = "index.php?page=services&action=createProcess";
        confirmationText.innerHTML = "Please enter a unique process name.";
        confirmationSubmit.value = "Create";
        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");

        temporaryElement = document.createElement("input");
        temporaryElement.type = "text";
        temporaryElement.name = "name";
        temporaryElement.placeholder = "Process Name";
        temporaryElement.id = "processNameInput";
        temporaryElement.classList.add("tempElement");
        confirmationForm.appendChild(temporaryElement);

        confirmation.style.display = 'flex';
    });

    // Process Delete
    deleteProcessButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            if (!hasManagePermission) {
                alert("You do not have permission to delete processes.");
                return;
            }

            const processIdentifier = Number(button.dataset.id);
            if (lockedSet.has(processIdentifier)) {
                alert("This process cannot be deleted because it is used by a service with active orders.");
                return;
            }

            confirmationTitle.innerHTML = "Delete Process?";
            confirmationForm.action = "index.php?page=services&action=deleteProcess";
            selectedProcessIdentifierInput.value = button.dataset.id;
            confirmationText.innerHTML = "Are you sure to delete the " + button.dataset.name + " process?";
            confirmationSubmit.value = "Yes Delete";
            confirmationSubmit.classList.remove("yellowBG", "whiteText", "noBorder");

            confirmation.style.display = 'flex';
        });
    });

    // Ensure maxAssign >= minAssign dynamically
    document.addEventListener('input', function(event) {
        const container = event.target.closest('.assignRange');
        if (!container) return;

        const minInput = container.querySelector('.minAssign');
        const maxInput = container.querySelector('.maxAssign');
        if (!minInput || !maxInput) return;

        const minValue = parseInt(minInput.value) || 1;
        maxInput.min = minValue;
        if (parseInt(maxInput.value) < minValue) {
            maxInput.value = minValue;
        }
    });

    // Cleanup temporary elements on dialog close
    confirmationCancel.addEventListener('click', function() {
        document.querySelectorAll('.tempElement').forEach(function(element) {
            element.remove();
        });
    });

    confirmationBG.addEventListener('click', function() {
        document.querySelectorAll('.tempElement').forEach(function(element) {
            element.remove();
        });
    });
</script>

</html>