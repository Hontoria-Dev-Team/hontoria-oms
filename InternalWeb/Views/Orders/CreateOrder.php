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
    <title>Order Creation - Hontoria OMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <link rel="stylesheet" href="../.CSS/StaffPage.css">
    <style>
        .asideLayout>main>section {
            min-width: fit-content !important;
        }

        @media (max-height: 925px) {
            .asideLayout>main>section>form>:nth-child(1) {
                justify-content: flex-start;
                overflow-y: scroll;
                padding: 0.3rem !important;
            }

            .asideLayout>main>section>form>:nth-child(2) {
                justify-content: flex-start;
                overflow-y: scroll;
                padding: 0.3rem !important;
            }

            .asideLayout>main>section>form>:nth-child(2)>:nth-child(2) {
                min-height: 400px !important;
            }

            .asideLayout>main>section>form>:nth-child(2)>:last-child {
                position: sticky;
                bottom: 0;
            }
        }

        @media (max-height: 600px) {
            .asideLayout>main>section>form>:nth-child(2)>:nth-child(2) {
                min-height: 300px !important;
            }
        }

        @media (max-height: 400px) {
            .asideLayout>main>section>form>:nth-child(2)>:nth-child(2) {
                min-height: 200px !important;
            }
        }

        @media (max-width: 800px) {
            .asideLayout>main>section>form>:nth-child(1) {
                min-width: calc(100vw - 3rem);
                max-width: calc(100vw - 3rem);
            }

            .asideLayout>main>section>form>:nth-child(2) {
                min-width: 200px;
                max-width: 200px;
            }
        }

        @media (max-width: 600px) {
            #serviceProcess {
                min-width: 520px;
                max-width: 520px;
            }

            :has(>#serviceProcess) {
                overflow-x: scroll;
                overflow-y: hidden;
            }

            :has(>#serviceProcess)>:nth-child(1) {
                position: sticky;
                left: 0;
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
                <img src="../../Shared/Img/ListIcon.png" alt="List"> Order Creation
            </h1>
        </span>
        <?php include("../Views/.Components/MessageBox.php"); ?>
        <section class="flexMax">
            <form method="POST" action="index.php?page=orders&action=createFinal" class="centerHoriRowLayout midGap fullHeight">
                <div class="centerColumnLayout flexMid fullHeight midGap">
                    <section class="flexMin fullWidth roundedMid centerColumnLayout">
                        <div class="box fullDimensions roundedMid columnLayout minGap">
                            <h3>Service Details</h3>
                            <div class="centerHoriRowLayout minGap">
                                <div class="flexMin columnLayout">
                                    <label for="serviceType">Service</label>
                                    <select name="serviceType" id="serviceType" required>
                                        <?php foreach ($serviceList as $service): ?>
                                            <option value="<?= e($service['id']) ?>"><?= e($service['name']) ?></option>
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
                                <input type="text" name="customerName" required="true" class="fullWidth" value="<?= e($customerName ?? '') ?>">
                            </div>
                            <div>
                                <label for="messengerGCLink">Messenger Group Chat Invite Link</label>
                                <input type="url" name="messengerGCLink" required="true" class="fullWidth" pattern="https://m\.me/.*" placeholder="https://m.me/..."
                                    value="<?= e($messengerGCLink ?? '') ?>">
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
                            <h3>Order Process</h3>
                            <div class="centerHoriRowLayout tinGap flexMax" id="serviceProcess"></div>
                        </div>
                        <div class="gradientBorderDiag"></div>
                    </section>
                </div>
                <div class="centerColumnLayout flexMin roundedMid fullHeight midGap">
                    <section class="flexMin fullWidth roundedMid centerColumnLayout">
                        <div class="box fullDimensions roundedMid columnLayout minGap">
                            <h3>Order Pricing</h3>
                            <p class="flexMin">Total Price: ₱<span id="priceTotalText"></span></p>
                            <p class="flexMin">Price Per Unit: ₱<span id="pricePerUnitText"></span></p>
                            <!-- Discount field only shown to users with the canApplyDiscountToOrders permission -->
                            <div class="<?= in_array('canApplyDiscountToOrders', $_SESSION['permissions']) ? '' : 'hidden' ?>">
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
                                        <label for="groupQuantities[]">Units</label>
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
                <?php echo CsrfM::getTokenField(); ?>
            </form>
        </section>
    </main>
</body>
<script src="../.JS/TimeHelpers.js"></script>
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
    const processList = <?php echo json_encode($processList); ?>;

    const processMap = {}
    processList.forEach(item => {
        if (!processMap[item.id]) {
            processMap[item.id] = [];
        }
        processMap[item.id].push({
            minAssign: item.minAssignDefault,
            maxAssign: item.maxAssignDefault
        });
    });

    // Service and subservice selection functionality
    let option;

    function setSubservices(serviceID) {
        // Clear previous options safely
        while (subserviceType.firstChild) subserviceType.removeChild(subserviceType.firstChild);
        for (let i = 0; i < subservices.length; i++) {
            if (subservices[i].serviceID != serviceID) {
                continue;
            }
            option = document.createElement('option');
            option.value = subservices[i].id;
            option.textContent = subservices[i].name; // replaced innerHTML with textContent for safety
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
    let tempElement;

    function setProcess(serviceID) {
        // Clear previous process elements safely
        while (serviceProcess.firstChild) serviceProcess.removeChild(serviceProcess.firstChild);
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
            processDiv.className = 'flexMin minHeight bordered roundedMin centerColumnLayout tinGap processElement clickable unselectable';
            processDiv.classList.add(hasFirstProcess ? 'redTransBG' : 'yellowTransBG');
            processDiv.dataset.status = hasFirstProcess ? 'pending' : 'active';
            processDiv.dataset.name = serviceProcesses[i].name;
            processDiv.dataset.index = currentProcessIndex++;

            processHead = document.createElement('h3');
            processHead.textContent = serviceProcesses[i].name;
            processHead.className = "whiteText outlineText";
            processParagraph = document.createElement('h5');
            processParagraph.textContent = hasFirstProcess ? '(Pending)' : '(Active)';
            processParagraph.className = "norWestAbsolute closeCorner transText";

            processDiv.appendChild(processHead);
            processDiv.appendChild(processParagraph);

            serviceProcess.appendChild(processDiv);
            currentServiceProcess.push(processDiv);

            tempStatusInput = document.createElement('input');
            tempStatusInput.type = "hidden";
            tempStatusInput.name = "orderProcessStatus[]";
            tempStatusInput.value = hasFirstProcess ? 'pending' : 'active';
            processDiv.appendChild(tempStatusInput);

            // Build assignee range inputs safely without innerHTML
            const assignRangeDiv = document.createElement('div');
            assignRangeDiv.className = "centerHoriRowLayout tinGap unitHeight assignRange";

            const peopleIcon = document.createElement('img');
            peopleIcon.src = "../../Shared/Img/PeopleIcon.png";
            peopleIcon.alt = "People";
            peopleIcon.className = "unitHeight";
            assignRangeDiv.appendChild(peopleIcon);

            const minDiv = document.createElement('div');
            minDiv.className = "centerHoriRowLayout tinGap";
            const minLabel = document.createElement('label');
            minLabel.setAttribute('for', 'minAssigns[]');
            minLabel.textContent = 'Min';
            const minInput = document.createElement('input');
            minInput.type = "number";
            minInput.name = "minAssigns[]";
            minInput.required = true;
            minInput.className = "unitHeight unitWidth regTinPadding centerText roundedTin minAssign";
            minInput.value = processMap[serviceProcesses[i].id][0].minAssign;
            minInput.min = 1;
            minInput.max = 50;
            minDiv.appendChild(minLabel);
            minDiv.appendChild(minInput);
            assignRangeDiv.appendChild(minDiv);

            const maxDiv = document.createElement('div');
            maxDiv.className = "centerHoriRowLayout tinGap";
            const maxLabel = document.createElement('label');
            maxLabel.setAttribute('for', 'maxAssigns[]');
            maxLabel.textContent = 'Max';
            const maxInput = document.createElement('input');
            maxInput.type = "number";
            maxInput.name = "maxAssigns[]";
            maxInput.required = true;
            maxInput.className = "unitHeight unitWidth regTinPadding centerText roundedTin maxAssign";
            maxInput.value = processMap[serviceProcesses[i].id][0].maxAssign;
            maxInput.max = 50;
            maxDiv.appendChild(maxLabel);
            maxDiv.appendChild(maxInput);
            assignRangeDiv.appendChild(maxDiv);

            processDiv.appendChild(assignRangeDiv);

            hasFirstProcess = true;
        }

        // Activate/deactivate processes by clicking
        document.querySelectorAll('.processElement').forEach(function(elem) {
            elem.addEventListener('click', function() {
                if (elem.dataset.status == 'active') {
                    return;
                }

                elem.classList.remove("redTransBG", "greenTransBG");
                elem.classList.add("yellowTransBG");
                elem.dataset.status = 'active';
                elem.querySelector('h3').textContent = elem.dataset.name;
                elem.querySelector('h5').textContent = '(Active)';
                elem.querySelector('input').value = "active";

                // Mark previous processes as complete
                for (let i = elem.dataset.index - 1; i >= 0; i--) {
                    currentServiceProcess[i].classList.remove("redTransBG", "yellowTransBG");
                    currentServiceProcess[i].classList.add("greenTransBG");
                    currentServiceProcess[i].dataset.status = 'complete';
                    currentServiceProcess[i].querySelector('h3').textContent = currentServiceProcess[i].dataset.name;
                    currentServiceProcess[i].querySelector('h5').textContent = '(Complete)';
                    currentServiceProcess[i].querySelector('input').value = "complete";
                }

                // Mark following processes as pending
                for (let i = Number(elem.dataset.index) + 1; i < currentServiceProcess.length; i++) {
                    currentServiceProcess[i].classList.remove("greenTransBG", "yellowTransBG");
                    currentServiceProcess[i].classList.add("redTransBG");
                    currentServiceProcess[i].dataset.status = 'pending';
                    currentServiceProcess[i].querySelector('h3').textContent = currentServiceProcess[i].dataset.name;
                    currentServiceProcess[i].querySelector('h5').textContent = '(Pending)';
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

    addOrderGroupButton.addEventListener('click', function() {
        const tempGroup = document.createElement('div');
        tempGroup.className = 'minHeight noShrink centerHoriRowLayout minGap botBordered';
        tempGroup.id = "orderGroup" + currentOrderGroupIndex++;

        // Remove button
        const removeBtn = document.createElement('a');
        removeBtn.className = 'squareSize unitHeight norEastAbsolute centerColumnLayout closeCorner removeOrderGroup';
        removeBtn.setAttribute('data-group-id', tempGroup.id);
        const xImg = document.createElement('img');
        xImg.src = '../../Shared/Img/XIcon.png';
        xImg.alt = 'X';
        removeBtn.appendChild(xImg);
        tempGroup.appendChild(removeBtn);

        // Description field
        const descDiv = document.createElement('div');
        descDiv.className = 'flexMid';
        const descLabel = document.createElement('label');
        descLabel.setAttribute('for', 'groupDescriptions[]');
        descLabel.textContent = 'Description';
        const descInput = document.createElement('input');
        descInput.type = 'text';
        descInput.name = 'groupDescriptions[]';
        descInput.required = true;
        descInput.className = 'fullWidth';
        descDiv.appendChild(descLabel);
        descDiv.appendChild(descInput);
        tempGroup.appendChild(descDiv);

        // Units field
        const unitsDiv = document.createElement('div');
        unitsDiv.className = 'flexMin';
        const unitsLabel = document.createElement('label');
        unitsLabel.setAttribute('for', 'groupQuantities[]');
        unitsLabel.textContent = 'Units';
        const unitsInput = document.createElement('input');
        unitsInput.type = 'number';
        unitsInput.name = 'groupQuantities[]';
        unitsInput.required = true;
        unitsInput.className = 'fullWidth orderGroupPrice';
        unitsInput.min = '1';
        unitsInput.value = '1';
        unitsDiv.appendChild(unitsLabel);
        unitsDiv.appendChild(unitsInput);
        tempGroup.appendChild(unitsDiv);

        orderGroups.appendChild(tempGroup);

        // Remove handler
        removeBtn.addEventListener('click', function() {
            const groupId = this.getAttribute('data-group-id');
            const groupElement = document.getElementById(groupId);
            if (groupElement) {
                groupElement.remove();
            }
        });

        // Re-attach change listener to new price inputs
        tempGroup.querySelector('.orderGroupPrice').addEventListener('change', showPrice);
    });

    // Pricing Logic
    let quantities = 0;
    let currentPricePerUnit;
    let subserviceMatch;

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

        priceTotalText.textContent = priceDiscount.value != 0 ?
            (quantities * currentPricePerUnit) + " - " + priceDiscount.value + " = ₱" + ((quantities * currentPricePerUnit) - priceDiscount.value) :
            quantities * currentPricePerUnit;
    }

    serviceType.addEventListener('change', showPrice);
    subserviceType.addEventListener('change', showPrice);
    priceDiscount.addEventListener('change', showPrice);

    // Initial price display
    showPrice();

    // Limit max assign minimum equal to min assign logic
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
</script>

</html>