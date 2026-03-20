<!DOCTYPE html>
<html>

<head>
    <title>Orders Panel - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <h1 class="titleLogo minGap tinHeight">
            <img src="../../Shared/Img/ListIcon.png" alt="List"> Orders Panel
        </h1>
        <section class="columnLayout flexMax midGap">
            <section class="centerColumnLayout roundedMid flexMid minHeight">
                <div class="box fullHeight fullWidth roundedMid columnLayout minGap">
                    <form method="GET" action="index.php?page=staff" class="rowLayout fullWidth minGap">
                        <input type="hidden" name="page" value="orders">
                        <input type="hidden" name="action" value="filter">

                        <div class="iconInput flexMax centerHoriRowLayout">
                            <input type="search" name="search" placeholder="Search Order" class="fullWidth" value="<?= $search ?>">
                            <img src="../../Shared/Img/MagnifierIcon.png" alt="Magnifier">
                        </div>

                        <select name="status">
                            <option value="" <?= $status === '' ? 'selected' : '' ?>>Any Status</option>
                            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="idle" <?= $status === 'idle' ? 'selected' : '' ?>>Idle</option>
                        </select>

                        <input type="submit" value="Search" class="importantInput">
                    </form>
                    <section class="minGap gridFlexMid scrollable" id="orderList">
                        <?php foreach ($orderList as $order): ?>
                            <?php
                            $activeProcesses = "";

                            foreach ($orderProcessList as $process) {
                                if ($process['orderID'] != $order['id'] || !in_array($process['status'], ['active', 'partially complete'])) {
                                    continue;
                                }

                                $activeProcesses .= $process['processName'] . ", ";
                            }
                            $activeProcesses = rtrim($activeProcesses, ", ");
                            ?>
                            <div class="midHeight regPadding roundedMin centerHoriColumnLayout minGap flexStatic orderElement bordered shadowed clickable"
                                data-id="<?= $order['id'] ?>" data-due="<?= $order['deadlineAt'] ?>" data-customer="<?= $order['customerName'] ?>">
                                <p class="norWestAbsolute closeCorner transText">Order #<?= $order['id'] ?></p>
                                <div class="souEastAbsolute closeCorner minPadding bordered roundedMin">Status</div>
                                <h2 class="centerHoriRowLayout tinGap"><?= $order['subserviceName'] ?> <?= $order['serviceName'] ?> <b>(<?= $order['customerName'] ?>)</b></h2>
                                <div>
                                    <p>Due In: <span class="dueInText" data-due-date="<?= $order['deadlineAt'] ?>"></span></p>
                                    <p>Value: ₱<?= $order['priceTotal'] ?></p>
                                    <p>Current Process: <?= $activeProcesses ?></p>
                                    <p>Workers: Aljun, Jhonna Mae, Kim</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="tinHeight"></div>
                    </section>
                </div>
                <div class="rowLayout minGap souEastAbsolute">
                    <a href="index.php?page=orders&action=create" class="roundedMin centerColumnLayout importantInput regPadding emphasizedText">Create Order</a>
                </div>
                <div class="gradientBorderDiag"></div>
            </section>
            <section class="centerRowLayout midGap flexMin roundedMid">
                <section class="centerColumnLayout roundedMid flexMid fullHeight">
                    <section class="box roundedMid fullDimensions centerHoriColumnLayout minGap">
                        <h3 class="centerRowLayout tinGap centerText fullWidth" id="selectedText">No Service Selected</h3>
                        <form method="POST" action="index.php?page=orders&action=changeDeadline" class="centerHoriRowLayout minGap hidden" id="deadlineForm">
                            <label for="deadlineAt" class="minWidth">Due Date</label>
                            <input type="date" name="deadlineAt" class="fullWidth" id="deadlineAt">
                            <button type="button" id="changeDeadlineButton" class="importantInput flexMax">Change</button>
                        </form>
                        <div class="rowLayout minGap hidden" id="additionalSelectedInputs">
                            <button type="button" id="assignEmployeesButton" class="importantInput flexMax">Assign Employees</button>
                            <button type="button" class="criticalInput centerColumnLayout" id="deleteOrderButton">
                                <img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors">
                            </button>
                        </div>
                    </section>
                    <div class="gradientBorderDiag"></div>
                </section>
                <section class="centerColumnLayout roundedMid flexMax fullHeight">
                    <section class="box columnLayout roundedMid minGap fullDimensions">
                        <h5>Order Process</h5>
                        <div class="centerHoriRowLayout minGap flexMax" id="orderProcess">
                            <h3 class="flexMin centerText">No Service Selected</h3>
                        </div>
                    </section>
                    <div class="gradientBorderDiag"></div>
                </section>
            </section>
        </section>
    </main>
    <?php include("../Views/.Components/ConfirmationBox.php"); ?>
</body>
<script src="../.JS/ConfirmationBox.js"></script>
<script src="../.JS/DueTimeCalculator.js"></script>
<script>
    const orderProcess = document.getElementById('orderProcess');
    const selectedText = document.getElementById('selectedText');
    const deadlineForm = document.getElementById('deadlineForm');
    const deadlineAt = document.getElementById('deadlineAt');
    const changeDeadlineButton = document.getElementById('changeDeadlineButton');
    const additionalSelectedInputs = document.getElementById('additionalSelectedInputs');
    const deleteOrderButton = document.getElementById('deleteOrderButton');
    const orders = <?php echo json_encode($orderList); ?>;
    const orderProcesses = <?php echo json_encode($orderProcessList); ?>;

    document.addEventListener("DOMContentLoaded", () => {
        confirmationCancel.value = "No Cancel";
    });

    const selectedID = document.createElement("input");
    selectedID.type = "hidden";
    selectedID.name = "selectedID";
    confirmationForm.appendChild(selectedID);

    const newDeadline = document.createElement("input");
    newDeadline.type = "hidden";
    newDeadline.name = "newDeadline";
    confirmationForm.appendChild(newDeadline);

    // Due time calculation
    document.querySelectorAll('.dueInText').forEach(function(elem) {
        elem.textContent = elem.dataset.dueDate == '' ? "No due date" : getDueTime(elem.dataset.dueDate) + " (" + formatDate(elem.dataset.dueDate) + ")";
    });

    // Order Process Graph Show Function
    let arrow;
    let processDiv;
    let hasFirstProcess;
    let processHead;
    let processParagraph;

    document.querySelectorAll('.orderElement').forEach(function(elem) {
        elem.addEventListener('click', function() {
            showProcess(elem.dataset.id);
        });
    });

    function showProcess(orderID) {
        orderProcess.innerHTML = '';
        hasFirstProcess = false;

        for (let i = 0; i < orderProcesses.length; i++) {
            if (orderProcesses[i].orderID != orderID) {
                continue;
            }

            if (hasFirstProcess) {
                arrow = document.createElement('h1');
                arrow.textContent = '>';
                orderProcess.appendChild(arrow);
            }

            processDiv = document.createElement('div');
            processDiv.className = 'flexMin minHeight bordered roundedMin centerColumnLayout';

            processHead = document.createElement('h3');
            processHead.textContent = orderProcesses[i].processName;
            processParagraph = document.createElement('p');

            switch (orderProcesses[i].status) {
                case 'complete':
                    processDiv.classList.add('greenTransBG');
                    processParagraph.textContent = '(Complete)';
                    break;
                case 'partially complete':
                    processDiv.classList.add('yellowGreenTransBG');
                    processParagraph.textContent = '(Partially Complete)';
                    break;
                case 'active':
                    processDiv.classList.add('yellowTransBG');
                    processParagraph.textContent = '(Active)';
                    break;
                case 'pending':
                    processDiv.classList.add('redTransBG');
                    processParagraph.textContent = '(Pending)';
                    break;
            }

            processDiv.appendChild(processHead);
            processDiv.appendChild(processParagraph);

            orderProcess.appendChild(processDiv);

            hasFirstProcess = true;
        }
    }

    // Editable deadline and delete order function logic
    function setMinToToday() {
        const today = new Date().toISOString().split('T')[0];
        deadlineAt.min = today;
    }

    setMinToToday();

    setInterval(setMinToToday, 60000);

    document.querySelectorAll('.orderElement').forEach(function(elem) {
        elem.addEventListener('click', function() {
            selectedText.classList.remove("centerRowLayout");
            selectedText.classList.add("centerHoriRowLayout");
            selectedText.innerHTML = "Order #" + elem.dataset.id + " <b>(" + elem.dataset.customer + ")</b>";

            deadlineAt.value = elem.dataset.due.split(' ')[0];
            selectedID.value = elem.dataset.id;
            deadlineForm.classList.remove("hidden");

            deleteOrderButton.dataset.selectedId = elem.dataset.id;
            additionalSelectedInputs.classList.remove("hidden");
        });
    });

    changeDeadlineButton.addEventListener('click', function() {
        confirmationTitle.innerHTML = "Change Order Deadline?";
        confirmationForm.action = "index.php?page=orders&action=changeDeadline"

        confirmationText.innerHTML = "Are you sure to change the deadline of Order #" + selectedID.value + "?";
        confirmationSubmit.value = "Yes Change";
        confirmationSubmit.classList.add("yellowBG", "whiteText", "noBorder");

        confirmation.style.display = 'flex';
    });

    deadlineAt.addEventListener('change', function() {
        newDeadline.value = deadlineAt.value;
    });

    deleteOrderButton.addEventListener('click', function() {
        confirmationTitle.innerHTML = "Delete Order?";
        confirmationForm.action = "index.php?page=orders&action=delete"

        confirmationText.innerHTML = "Are you sure to delete Order #" + selectedID.value + "?";
        confirmationSubmit.value = "Yes Delete";
        confirmationSubmit.classList.remove("yellowBG", "whiteText", "noBorder");

        confirmation.style.display = 'flex';
    });
</script>

</html>