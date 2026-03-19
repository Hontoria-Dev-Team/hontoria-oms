<!DOCTYPE html>
<html>

<head>
    <title>Order Creation - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/StaffPage.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <span class="centerHoriRowLayout midGap">
            <?php include("../Views/.Components/BackLink.php"); ?>
            <h1 class="titleLogo minGap tinHeight">
                <img src="../../Shared/Img/ListIcon.png" alt="List"> Order Creation
            </h1>
        </span>
        <?php include("../Views/.Components/ErrorBox.php"); ?>
        <section>
            <form method="POST" action="index.php?page=orders&action=createFinal" class="centerHoriRowLayout midGap fullHeight">
                <div class="centerColumnLayout flexMax fullHeight midGap">
                    <section class="flexMin fullWidth roundedMid centerColumnLayout">
                        <div class="box fullDimensions roundedMid columnLayout minGap">
                            <h3>Service Details</h3>
                            <div class="centerHoriRowLayout minGap">
                                <div class="flexMin columnLayout">
                                    <label for="serviceType">Service</label>
                                    <select name="serviceType" id="serviceType" required>
                                        <?php foreach ($serviceList as $service): ?>
                                            <option value="<?= $service['id'] ?>"><?= $service['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="flexMin columnLayout">
                                    <label for="subserviceType">Subservice</label>
                                    <select name="subserviceType" id="subserviceType" required>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="gradientBorderDiag"></div>
                    </section>
                    <section class="flexMin fullWidth roundedMid centerColumnLayout">
                        <div class="box fullDimensions roundedMid columnLayout minGap">
                            <h3>Order Identifiers</h3>
                            <div>
                                <label for="customerName">Customer Name</label>
                                <input type="text" name="customerName" required="true" class="fullWidth" value="<?php echo htmlspecialchars($customerName ?? ''); ?>">
                            </div>
                            <div>
                                <label for="messengerGCLink">Messenger Group Chat Invite Link</label>
                                <input type="url" name="messengerGCLink" required="true" class="fullWidth" pattern="https://m\.me/.*" placeholder="https://m.me/..."
                                    value="<?php echo htmlspecialchars($messengerGCLink ?? ''); ?>">
                            </div>
                        </div>
                        <div class="gradientBorderDiag"></div>
                    </section>
                    <section class="flexMin fullWidth roundedMid centerColumnLayout">
                        <div class="box fullDimensions roundedMid columnLayout minGap">
                            <h3>Time Information</h3>
                            <div>
                                <label for="deadlineAt">Due Date</label>
                                <input type="date" name="deadlineAt" class="fullWidth" id="deadlineAt">
                            </div>
                            <p class="capitalFirst" id="dueAtText">No Due Date</p>
                        </div>
                        <div class="gradientBorderDiag"></div>
                    </section>
                    <section class="flexMax fullWidth roundedMid centerColumnLayout">
                        <div class="box fullDimensions roundedMid columnLayout minGap">
                            <h3>Service Process</h3>
                            <div class="centerHoriRowLayout minGap flexMax" id="serviceProcess"></div>
                        </div>
                        <div class="gradientBorderDiag"></div>
                    </section>
                </div>
                <div class="centerColumnLayout flexMid roundedMid fullHeight midGap">
                    <section class="flexMin fullWidth roundedMid centerColumnLayout">
                        <div class="box fullDimensions roundedMid columnLayout minGap">
                            <h3>Order Pricing</h3>
                            <p class="flexMin">Total Price: ₱<span id="priceTotalText"></span></p>
                            <p class="flexMin">Price Per Unit: ₱<span id="pricePerUnitText"></span></p>
                            <input type="hidden" name="priceTotal" id="priceTotal">
                            <div>
                                <label for="priceDiscount">Price Discount</label>
                                <input type="number" name="priceDiscount" class="fullWidth" id="priceDiscount" min="0" value="0">
                            </div>
                        </div>
                        <div class="gradientBorderDiag"></div>
                    </section>
                    <section class="flexMax minHeight fullWidth roundedMid centerColumnLayout">
                        <div class="box fullDimensions roundedMid columnLayout minGap">
                            <div class="centerHoriRowLayout">
                                <h3 class="flexMax">Order Groups</h3>
                                <button type="button" id="addOrderGroupButton" class="importantInput">Add Order Group</button>
                            </div>
                            <div class="scrollable reverseColumnLayout" id="orderGroups">
                                <div class="minHeight noShrink centerHoriRowLayout minGap">
                                    <div class="flexMid">
                                        <label for="groupDescriptions[]">Description</label>
                                        <input type="text" name="groupDescriptions[]" required="true" class="fullWidth">
                                    </div>
                                    <div class="flexMin">
                                        <label for="groupQuantities[]">Quantity</label>
                                        <input type="number" name="groupQuantities[]" required="true" class="fullWidth orderGroupPrice" min="1" value="1">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="gradientBorderDiag"></div>
                    </section>
                    <section class="flexMin minHeight fullWidth roundedMid centerColumnLayout">
                        <div class="box fullDimensions roundedMid centerColumnLayout minGap">
                            <input type="submit" name="submit" value="Create Order" class="fullWidth importantInput">
                        </div>
                        <div class="gradientBorderDiag"></div>
                    </section>
                </div>
            </form>
        </section>
    </main>
</body>
<script src="../.JS/DueTimeCalculator.js"></script>
<script>
    const serviceType = document.getElementById('serviceType');
    const subserviceType = document.getElementById('subserviceType');
    const deadlineAt = document.getElementById('deadlineAt');
    const dueAtText = document.getElementById('dueAtText');
    const serviceProcess = document.getElementById('serviceProcess');
    const addOrderGroupButton = document.getElementById('addOrderGroupButton');
    const orderGroups = document.getElementById('orderGroups');
    const priceTotalText = document.getElementById('priceTotalText');
    const pricePerUnitText = document.getElementById('pricePerUnitText');
    const priceTotal = document.getElementById('priceTotal');
    const priceDiscount = document.getElementById('priceDiscount');
    const subservices = <?php echo json_encode($subserviceList); ?>;
    const serviceProcesses = <?php echo json_encode($serviceProcessList); ?>;

    // Service and subservice selection functionality
    let option;

    function setSubservices(serviceID) {
        subserviceType.innerHTML = '';
        for (let i = 0; i < subservices.length; i++) {
            if (subservices[i].serviceID != serviceID) {
                continue;
            }

            option = document.createElement('option');
            option.value = subservices[i].id;
            option.innerHTML = subservices[i].name;
            subserviceType.appendChild(option);
        }
    }

    setSubservices(serviceType.value);

    serviceType.addEventListener('change', function() {
        setSubservices(serviceType.value);
    });

    // Due in calculation logic
    function setMinToToday() {
        const today = new Date().toISOString().split('T')[0];
        deadlineAt.min = today;
    }

    setMinToToday();

    setInterval(setMinToToday, 60000);

    deadlineAt.addEventListener('change', () => {
        dueAtText.textContent = deadlineAt.value == null ? dueAtText.textContent = 'No due date' : 'Due In: ' + getDueTime(deadlineAt.value);
    });

    // Service Process Graph logic
    let arrow;
    let processDiv;
    let hasFirstProcess;
    let currentServiceProcess;
    let currentProcessIndex;
    let tempStatusInput;
    let processHead;
    let processParagraph;

    function setProcess(serviceID) {
        serviceProcess.innerHTML = '';
        currentServiceProcess = [];
        currentProcessIndex = 0;
        hasFirstProcess = false;

        for (let i = 0; i < serviceProcesses.length; i++) {
            if (serviceProcesses[i].serviceID != serviceID) {
                continue;
            }

            if (hasFirstProcess) {
                arrow = document.createElement('h1');
                arrow.textContent = '>';
                serviceProcess.appendChild(arrow);
            }

            processDiv = document.createElement('div');
            processDiv.className = 'flexMin minHeight bordered roundedMin centerColumnLayout processElement clickable unselectable';
            processDiv.classList.add(hasFirstProcess ? 'redTransBG' : 'yellowTransBG');
            processDiv.dataset.status = hasFirstProcess ? 'pending' : 'active';
            processDiv.dataset.name = serviceProcesses[i].name;
            processDiv.dataset.index = currentProcessIndex++;

            processHead = document.createElement('h3');
            processHead.textContent = serviceProcesses[i].name;
            processParagraph = document.createElement('p');
            processParagraph.textContent = hasFirstProcess ? '(Pending)' : '(Active)';

            processDiv.appendChild(processHead);
            processDiv.appendChild(processParagraph);

            serviceProcess.appendChild(processDiv);
            currentServiceProcess.push(processDiv);

            tempStatusInput = document.createElement('input');
            tempStatusInput.type = "hidden";
            tempStatusInput.name = "orderProcess[]";
            tempStatusInput.value = hasFirstProcess ? 'pending' : 'active';
            processDiv.appendChild(tempStatusInput);

            hasFirstProcess = true;
        }

        document.querySelectorAll('.processElement').forEach(function(elem) {
            elem.addEventListener('click', function() {
                if (elem.dataset.status == 'active') {
                    return;
                }

                elem.classList.remove("redTransBG", "greenTransBG");
                elem.classList.add("yellowTransBG");
                elem.dataset.status = 'active';

                elem.querySelector('h3').textContent = elem.dataset.name;
                elem.querySelector('p').textContent = '(Active)';
                elem.querySelector('input').value = "active";

                for (let i = elem.dataset.index - 1; i >= 0; i--) {
                    currentServiceProcess[i].classList.remove("redTransBG", "yellowTransBG");
                    currentServiceProcess[i].classList.add("greenTransBG");
                    currentServiceProcess[i].dataset.status = 'complete';

                    currentServiceProcess[i].querySelector('h3').textContent = currentServiceProcess[i].dataset.name;
                    currentServiceProcess[i].querySelector('p').textContent = '(Complete)';
                    currentServiceProcess[i].querySelector('input').value = "complete";
                }

                for (let i = Number(elem.dataset.index) + 1; i < currentServiceProcess.length; i++) {
                    currentServiceProcess[i].classList.remove("greenTransBG", "yellowTransBG");
                    currentServiceProcess[i].classList.add("redTransBG");
                    currentServiceProcess[i].dataset.status = 'pending';

                    currentServiceProcess[i].querySelector('h3').textContent = currentServiceProcess[i].dataset.name;
                    currentServiceProcess[i].querySelector('p').textContent = '(Pending)';
                    currentServiceProcess[i].querySelector('input').value = "pending";
                }
            });
        });
    }

    setProcess(serviceType.value);

    serviceType.addEventListener('change', function() {
        setProcess(serviceType.value);
    });

    // Order Group Function Logic
    let currentOrderGroupIndex = 0;
    let tempGroup;

    addOrderGroupButton.addEventListener('click', function() {
        tempGroup = document.createElement('div');
        tempGroup.className = 'minHeight noShrink centerHoriRowLayout minGap botBordered';
        tempGroup.id = "orderGroup" + currentOrderGroupIndex++;
        tempGroup.innerHTML = `
            <a class="squareSize unitHeight norEastAbsolute centerColumnLayout closeCorner removeOrderGroup" data-group-id="${tempGroup.id}">
                <img src="../../Shared/Img/XIcon.png" alt="X">
            </a>
            <div class="flexMid">
                <label for="groupDescriptions[]">Description</label>
                <input type="text" name="groupDescriptions[]" required="true" class="fullWidth">
            </div>
            <div class="flexMin">
                <label for="groupQuantities[]">Quantity</label>
                <input type="number" name="groupQuantities[]" required="true" class="fullWidth orderGroupPrice" min="1" value="1">
            </div>
        `;

        orderGroups.appendChild(tempGroup);

        const xButton = tempGroup.querySelector('.removeOrderGroup');
        xButton.addEventListener('click', function() {
            const groupId = this.dataset.groupId;

            const groupElement = document.getElementById(groupId);
            if (groupElement) {
                groupElement.remove();
            }
        });

        document.querySelectorAll('.orderGroupPrice').forEach(function(elem) {
            elem.addEventListener('change', function() {
                showPrice();
            });
        });
    });

    //Pricing Logic
    let quantities = 0;
    let currentPricePerUnit;
    let subserviceMatch;

    serviceType.addEventListener('change', function() {
        showPrice();
    });

    subserviceType.addEventListener('change', function() {
        showPrice();
    });

    priceDiscount.addEventListener('change', function() {
        showPrice();
    });

    function showPrice() {
        subserviceMatch = subservices.find(
            subservice => subservice.id === subserviceType.value
        );

        if (subserviceMatch) {
            currentPricePerUnit = subserviceMatch.pricePerUnit;
            pricePerUnitText.textContent = currentPricePerUnit;
        }

        quantities = 0;
        document.querySelectorAll('.orderGroupPrice').forEach(function(input) {
            quantities += parseFloat(input.value) || 0;
        });

        priceTotal.value = (quantities * currentPricePerUnit) - priceDiscount.value;
        priceTotalText.textContent = priceDiscount.value != 0 ?
            (quantities * currentPricePerUnit) + " - " + priceDiscount.value + " = ₱" + ((quantities * currentPricePerUnit) - priceDiscount.value) :
            quantities * currentPricePerUnit;
    }

    showPrice();
</script>

</html>