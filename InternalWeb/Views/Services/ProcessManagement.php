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
                                class="centerHoriColumnLayout minGap roundedMin yellowTransBG yellowBorder regMidPadding shadowed">
                                <?php echo CsrfM::getTokenField(); ?>
                                <h2 class="centerText"><?= e($process['name']) ?></h2>
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
                                                value="<?= e($process['minAssignDefault']) ?>" min="1" max="50">
                                        </div>
                                        <div class="centerHoriRowLayout tinGap">
                                            <label for="maxAssign">Max</label>
                                            <input type="number" name="maxAssign" required="true" class="unitHeight unitWidth regTinPadding centerText roundedTin maxAssign"
                                                value="<?= e($process['maxAssignDefault']) ?>" max="50">
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
                                    <input type="hidden" name="id" value="<?= e($process['id']) ?>">
                                    <input type="submit" name="submit" value="Update Process" class="flexMax importantInput shadowed">
                                    <!-- Delete button: data attributes are escaped to prevent XSS -->
                                    <button type="button" class="deleteButton criticalInput centerColumnLayout shadowed"
                                        data-id="<?= e($process['id']) ?>" data-name="<?= e($process['name']) ?>">
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
    const createProcessButton = document.getElementById('createProcessButton');
    const deleteButtons = document.querySelectorAll('.deleteButton');

    let tempDiv;
    let tempElement;

    document.addEventListener("DOMContentLoaded", () => {
        confirmationCancel.value = "No Cancel";
    });

    const selectedID = document.createElement("input");
    selectedID.type = "hidden";
    selectedID.name = "selectedID";
    confirmationForm.appendChild(selectedID);

    // Process creation modal
    createProcessButton.addEventListener('click', function() {
        // All strings are hardcoded – safe to use innerHTML/textContent (textContent used for consistency)
        confirmationTitle.textContent = "Create Process";
        confirmationForm.action = "index.php?page=services&action=createProcess";
        confirmationText.textContent = "Please enter a unique process name.";
        confirmationSubmit.value = "Create";
        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");

        tempElement = document.createElement("input");
        tempElement.type = "text";
        tempElement.name = "name";
        tempElement.placeholder = "Process Name";
        tempElement.id = "processNameInput";
        tempElement.classList.add("tempElement");
        confirmationForm.appendChild(tempElement);

        confirmation.style.display = 'flex';
    });

    // Process delete modal
    deleteButtons.forEach(function(elem) {
        elem.addEventListener('click', function() {
            confirmationTitle.textContent = "Delete Process?";
            confirmationForm.action = "index.php?page=services&action=deleteProcess";

            selectedID.value = elem.dataset.id;
            // The process name is user‑controlled, so we use textContent to avoid XSS
            confirmationText.textContent = "Are you sure to delete the " + elem.dataset.name + " process?";
            confirmationSubmit.value = "Yes Delete";
            confirmationSubmit.classList.remove("yellowBG", "whiteText", "noBorder");

            confirmation.style.display = 'flex';
        });
    });

    // Enforce min ≤ max on assignment inputs
    document.addEventListener('input', function(e) {
        const container = e.target.closest('.assignRange');
        if (!container) return;

        const minInput = container.querySelector('.minAssign');
        const maxInput = container.querySelector('.maxAssign');
        if (!minInput || !maxInput) return;

        const minVal = parseInt(minInput.value) || 1;
        maxInput.min = minVal;
        if (parseInt(maxInput.value) < minVal) {
            maxInput.value = minVal;
        }
    });

    // Cleanup temporary elements on cancel
    confirmationCancel.addEventListener('click', function() {
        document.querySelectorAll('.tempElement').forEach(function(el) {
            el.remove();
        });
    });
    confirmationBG.addEventListener('click', function() {
        document.querySelectorAll('.tempElement').forEach(function(el) {
            el.remove();
        });
    });
</script>

</html>