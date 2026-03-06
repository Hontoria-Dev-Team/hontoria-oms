<!DOCTYPE html>
<html>

<head>
    <title>Dashboard - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/StaffPage.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout leftAlign midGap midPadding flexStretch">
        <h1 class="titleLogo minGap">
            <img src="../../Shared/Img/PeopleIcon.png" alt="People"> Staff Panel
        </h1>
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