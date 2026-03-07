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
                            <div class="midBox minHeight minPadding roundedMin rowLayout minGap flexStatic staffElement <?= $statusClass ?>">
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
                <div class="gradientBorderDiag roundedMid flexMin">
                    <section class="box centerColumnLayout roundedMid">
                        <h3 id="selectedStaffName">No Staff Selected</h3>
                    </section>
                </div>
                <div class="gradientBorderDiag roundedMid flexMid">
                    <section class="box centerColumnLayout roundedMid">
                        <h3>Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>!</h3>
                        <p>You are logged in as: <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></p>
                        <hr>
                        <a href="index.php?page=logout" class="fullWidth">Logout</a>
                    </section>
                </div>
            </section>
        </section>
    </main>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const staffElements = document.querySelectorAll('.staffElement');
        const selectedNameEl = document.getElementById('selectedStaffName');

        staffElements.forEach(function(el) {
            el.addEventListener('click', function() {
                const fullName = el.querySelector('h5').textContent.trim();
                selectedNameEl.textContent = fullName;
            });
        });
    });
</script>

</html>