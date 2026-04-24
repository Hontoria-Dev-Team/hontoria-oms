<!DOCTYPE html>
<html>

<head>
    <title>Inventory Panel - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <h1 class="titleLogo minGap tinHeight">
            <img src="../../Shared/Img/BoxIcon.png" alt="Box"> Inventory Panel
        </h1>
        <?php include("../Views/.Components/MessageBox.php"); ?>
        <section class="rowLayout flexMax midGap">
            <div class="flexMid columnLayout midGap">
                <section class="centerColumnLayout roundedMid flexMid">
                    <div class="box fullHeight fullWidth roundedMid columnLayout tinGap">
                        <div class="centerHoriRowLayout">
                            <h2 class="flexMax">Items:</h2>
                            <button type="button" class="darkBG emphasizedText noBorder shadowed whiteText centerColumnLayout fullHeight" id="createButton">
                                <b>Create</b>
                            </button>
                        </div>
                        <section class="minGap columnLayout scrollable flexMax noFlexBasis noMinHeight contentFlexStart regMinPadding" id="inventoryList">
                            <?php foreach ($inventoryList as $inventory): ?>
                                <div class="roundedMin centerHoriRowLayout flexStatic inventoryElement yellowBorder shadowed clickable fixedScreen noShrink"
                                    data-id="<?= htmlspecialchars($inventory['id']) ?>" data-name="<?= htmlspecialchars($inventory['name']) ?>"
                                    data-quantity="<?= htmlspecialchars($inventoryQuantityMap[$inventory['id']] ?? 0) ?>"
                                    data-min-quantity="<?= htmlspecialchars($inventory['minQuantity']) ?>"
                                    data-max-consumption="<?= htmlspecialchars($inventory['maxAvgConsumption']) ?>"
                                    data-restock-date="<?= $inventoryLastRestockMap[$inventory['id']]['date'] ?>"
                                    data-restock-quantity="<?= $inventoryLastRestockMap[$inventory['id']]['quantity'] ?>">
                                    <div class="capitalFirst centerText regMinPadding flexMax skewedXNegBG shadowed yellowTransBG">
                                        <h3><?= htmlspecialchars($inventory['name']) ?></h3>
                                    </div>
                                    <h5 class="capitalFirst centerText regMinPadding minWidth">
                                        Quantity: <?= htmlspecialchars($inventoryQuantityMap[$inventory['id']] ?? 0) ?>
                                    </h5>
                                </div>
                            <?php endforeach; ?>
                        </section>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
            </div>
            <section class="columnLayout midGap flexMax">
                <section class="centerColumnLayout roundedMid minGap">
                    <div class="centerColumnLayout minGap box roundedMid fullHeight fullWidth">
                        <div class="centerHoriRowLayout minGap fullWidth">
                            <h2 id="selectedTitle" class="capitalFirst flexMid">No Record Selected</h2>
                            <div class="centerHoriRowLayout minGap fullHeight">
                                <button type="button" class="darkBG emphasizedText noBorder shadowed whiteText centerColumnLayout maxHoriPadding fullHeight hidden"
                                    id="updateButton">
                                    Update
                                </button>
                                <button type="button" class="redBG emphasizedText noBorder shadowed whiteText centerColumnLayout fullHeight hidden" id="resetButton">
                                    Reset
                                </button>
                                <button type="button" class="redBG noBorder shadowed centerColumnLayout fullHeight hidden" id="deleteButton">
                                    <img src="../../Shared/Img/GarbageIcon.png" alt="Garbage" class="invertColors unitHeight">
                                </button>
                            </div>
                        </div>
                        <div class="centerHoriRowLayout minGap fullWidth">
                            <h5 id="avgConsumptionText" class="yellowBG roundedTin minHoriPadding outlineText whiteText shadowed flexMid centerText clickable">
                                7-day Avg Consumption
                            </h5>
                            <h5 id="minQuantityText" class="yellowBG roundedTin minHoriPadding outlineText whiteText shadowed flexMid centerText clickable">
                                Min Quantity
                            </h5>
                            <div class="flexMid centerColumnLayout roundedTin">
                                <h5 id="lastRestockDateText" class="capitalFirst midZ whiteBG fullDimensions roundedTin centerText">Last Restock Date</h5>
                                <div class="gradientBorderDiag shadowed minZ"></div>
                            </div>
                            <div class="flexMid centerColumnLayout roundedTin">
                                <h5 id="lastRestockQuantityText" class="capitalFirst midZ whiteBG fullDimensions roundedTin centerText">Last Restock Quantity</h5>
                                <div class="gradientBorderDiag shadowed minZ"></div>
                            </div>
                        </div>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
                <section class="centerColumnLayout roundedMid minGap flexMid noFlexBasis noMinHeight">
                    <div class="columnLayout minGap whiteBG regPadding roundedMid fullHeight fullWidth">
                        <div class="centerHoriRowLayout minGap whiteBG roundedMid">
                            <div class="centerHoriRowLayout tinGap">
                                <div class="micHeight squareSize yellowBG circle shadowed"></div>
                                <h6 class="capitalFirst">Quantity</h6>
                            </div>
                            <div class="centerHoriRowLayout tinGap">
                                <div class="micHeight squareSize redBG circle shadowed"></div>
                                <h6 class="capitalFirst">Consumption</h6>
                            </div>
                            <div class="centerHoriRowLayout tinGap">
                                <div class="micHeight squareSize greenBG circle shadowed"></div>
                                <h6 class="capitalFirst">Added</h6>
                            </div>
                            <div class="centerHoriRowLayout tinGap">
                                <div class="micHeight squareSize" style="display:flex; align-items:center; justify-content:center;">
                                    <div style="width:100%; height:0; border-top:0.1px dotted var(--red); transform: scaleY(2);"></div>
                                </div>
                                <h6 class="capitalFirst">Min Quantity</h6>
                            </div>
                            <div class="eastAbsolute edgeCorner fullWidth reverseCenterHoriRowLayout minGap">
                                <div class="centerHoriRowLayout tinGap">
                                    <h6>Target Date:</h6>
                                    <input type="date" id="targetDateInput" class="unitHeight roundedTin">
                                </div>
                                <div class="centerHoriRowLayout tinGap">
                                    <h6>Days:</h6>
                                    <input type="number" id="dayRangeInput" class="unitHeight duoWidth roundedTin noPadding centerText" min="7" max="60" value="30">
                                </div>
                                <div class="centerHoriRowLayout tinGap">
                                    <h6>Weeks:</h6>
                                    <input type="number" id="weekRangeInput" class="unitHeight duoWidth roundedTin noPadding centerText" min="4" max="24" value="12">
                                </div>
                                <div class="centerHoriRowLayout tinGap">
                                    <h6>Months:</h6>
                                    <input type="number" id="monthRangeInput" class="unitHeight duoWidth roundedTin noPadding centerText" min="6" max="24" value="12">
                                </div>
                            </div>
                        </div>
                        <div class="centerColumnLayout whiteBG roundedMin fullHeight fullWidth">
                            <h4 class="norAbsolute closeCorner topZ">Daily Data</h4>
                            <div id="dailyGraph" class="flexMax centerColumnLayout roundedMin whiteBG midZ fullWidth">
                                <h4>No Record Selected</h4>
                            </div>
                            <div class="gradientBorderDiag minZ shadowed"></div>
                        </div>
                        <div class="centerColumnLayout whiteBG roundedMin fullHeight fullWidth">
                            <h4 class="norAbsolute closeCorner topZ">Weekly Data</h4>
                            <div id="weeklyGraph" class="flexMax centerColumnLayout roundedMin whiteBG midZ fullWidth">
                                <h4>No Record Selected</h4>
                            </div>
                            <div class="gradientBorderDiag minZ shadowed"></div>
                        </div>
                        <div class="centerColumnLayout whiteBG roundedMin fullHeight fullWidth">
                            <h4 class="norAbsolute closeCorner topZ">Monthly Data</h4>
                            <div id="monthlyGraph" class="flexMax centerColumnLayout roundedMin whiteBG midZ fullWidth">
                                <h4>No Record Selected</h4>
                            </div>
                            <div class="gradientBorderDiag minZ shadowed"></div>
                        </div>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
            </section>
        </section>
    </main>
    <?php include("../Views/.Components/ConfirmationBox.php"); ?>
</body>
<script src="../.JS/ConfirmationBox.js"></script>
<script src="../.JS/TimeHelpers.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts" crossorigin="anonymous"></script>
<script>
    const inventoryElement = document.querySelectorAll('.inventoryElement');
    const selectedTitle = document.getElementById('selectedTitle');
    const createButton = document.getElementById('createButton');
    const updateButton = document.getElementById('updateButton');
    const resetButton = document.getElementById('resetButton');
    const deleteButton = document.getElementById('deleteButton');
    const avgConsumptionText = document.getElementById('avgConsumptionText');
    const minQuantityText = document.getElementById('minQuantityText');
    const lastRestockDateText = document.getElementById('lastRestockDateText');
    const lastRestockQuantityText = document.getElementById('lastRestockQuantityText');
    const targetDateInput = document.getElementById('targetDateInput');
    const dayRangeInput = document.getElementById('dayRangeInput');
    const weekRangeInput = document.getElementById('weekRangeInput');
    const monthRangeInput = document.getElementById('monthRangeInput');
    const inventoryRecordList = <?php echo json_encode($inventoryRecordList); ?>;

    const inventoryRecordMap = {};
    inventoryRecordList.forEach(item => {
        if (!inventoryRecordMap[item.inventoryID]) {
            inventoryRecordMap[item.inventoryID] = [];
        }
        inventoryRecordMap[item.inventoryID].push({
            date: item.date,
            quantity: Number(item.quantity),
            consumption: Number(item.consumption),
            added: Number(item.added)
        });
    });

    let tempElement;

    let currentSelected = {
        id: null,
        name: '',
        minQuantity: 0,
        maxAvgConsumption: 0,
        records: []
    };

    inventoryElement.forEach(elem => {
        const currentQty = parseInt(elem.dataset.quantity) || 0;
        const minQty = parseInt(elem.dataset.minQuantity) || 0;
        const maxAvgAllowed = parseInt(elem.dataset.maxConsumption) || 0;

        let avgCons7 = 0;
        if (maxAvgAllowed > 0) {
            const records = inventoryRecordMap[elem.dataset.id] || [];
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const sevenDaysAgo = new Date(today);
            sevenDaysAgo.setDate(today.getDate() - 7);
            let sumCons = 0,
                count = 0;
            for (const rec of records) {
                const recDate = new Date(rec.date + "T00:00:00");
                if (recDate >= sevenDaysAgo && recDate <= today) {
                    sumCons += Number(rec.consumption);
                    count++;
                }
            }
            avgCons7 = count > 0 ? Math.round(sumCons / count) : 0;
        }

        const isViolating = (currentQty < minQty) || (maxAvgAllowed > 0 && avgCons7 > maxAvgAllowed);

        if (isViolating) {
            elem.classList.remove("yellowBorder");
            elem.classList.add("redBorder");
            const firstDiv = elem.querySelector('div');
            if (firstDiv) {
                firstDiv.classList.remove("yellowTransBG");
                firstDiv.classList.add("redTransBG");
            }
        }

        elem.dataset.isUnderQuantity = minQty == 0 ? false : currentQty < minQty;
        elem.dataset.isOverConsuming = maxAvgAllowed == 0 ? false : maxAvgAllowed > 0 && avgCons7 > maxAvgAllowed;
    });

    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    today.setHours(0, 0, 0, 0);

    targetDateInput.value = `${yyyy}-${mm}-${dd}`;
    targetDateInput.max = `${yyyy}-${mm}-${dd}`;

    function OnElementClick(elem) {
        currentSelected.id = elem.dataset.id;
        currentSelected.name = elem.dataset.name;
        currentSelected.minQuantity = parseInt(elem.dataset.minQuantity) || 0;
        currentSelected.maxAvgConsumption = parseInt(elem.dataset.maxConsumption) || 0;
        currentSelected.records = [...(inventoryRecordMap[currentSelected.id] || [])];

        selectedTitle.textContent = currentSelected.name;
        updateButton.classList.remove("hidden");
        resetButton.classList.remove("hidden");
        deleteButton.classList.remove("hidden");

        const sevenDaysAgo = new Date(today);
        sevenDaysAgo.setDate(today.getDate() - 7);

        let last7Consumption = 0;
        const last7DaysRecords = currentSelected.records.filter(rec => {
            const recDate = new Date(rec.date + "T00:00:00");
            return recDate >= sevenDaysAgo && recDate <= today;
        });

        if (last7DaysRecords.length > 0) {
            const sumCons = last7DaysRecords.reduce((s, r) => s + Number(r.consumption), 0);
            last7Consumption = Math.round(sumCons / last7DaysRecords.length);
        }

        avgConsumptionText.textContent = "7-day Avg Consumption: " + last7Consumption;
        minQuantityText.textContent = "Min Quantity: " + currentSelected.minQuantity;

        if (elem.dataset.isOverConsuming == "true") {
            avgConsumptionText.classList.remove("yellowBG");
            avgConsumptionText.classList.add("redBG");
        } else {
            avgConsumptionText.classList.add("yellowBG");
            avgConsumptionText.classList.remove("redBG");
        }

        if (elem.dataset.isUnderQuantity == "true") {
            minQuantityText.classList.remove("yellowBG");
            minQuantityText.classList.add("redBG");
        } else {
            minQuantityText.classList.add("yellowBG");
            minQuantityText.classList.remove("redBG");
        }

        lastRestockDateText.textContent = "Last Restock Date: " + formatDate(elem.dataset.restockDate);
        lastRestockQuantityText.textContent = "Last Restock Quantity: " + elem.dataset.restockQuantity;

        RenderCharts();
    }

    function ShowUpdateBox() {
        confirmationTitle.innerHTML = "Update Record";
        confirmationForm.action = "index.php?page=inventory&action=updateRecord";
        confirmationText.innerHTML = "Please update how much would you like this record's quantity to be changed.";
        confirmationSubmit.value = "Yes Update";
        confirmationCancel.value = "No Cancel";
        confirmationSubmit.classList.add("yellowBG", "noBorder");

        tempElement = document.createElement("input");
        tempElement.type = "number";
        tempElement.name = "change";
        tempElement.placeholder = "Quantity Change";
        tempElement.className = "tempElement";
        tempElement.required = true;
        confirmationForm.appendChild(tempElement);

        tempElement = document.createElement("input");
        tempElement.type = "hidden";
        tempElement.name = "id";
        tempElement.value = currentSelected.id;
        tempElement.className = "tempElement";
        confirmationForm.appendChild(tempElement);

        confirmation.style.display = 'flex';
    }

    function ShowResetBox() {
        confirmationTitle.innerHTML = "Reset Record?";
        confirmationForm.action = "index.php?page=inventory&action=resetRecord";
        confirmationText.innerHTML = "Are you sure to reset the record of " + currentSelected.name + " for the day?";
        confirmationSubmit.value = "Yes Reset";
        confirmationCancel.value = "No Cancel";
        confirmationSubmit.classList.add("redBG", "noBorder");

        tempElement = document.createElement("input");
        tempElement.type = "hidden";
        tempElement.name = "id";
        tempElement.value = currentSelected.id;
        tempElement.className = "tempElement";
        confirmationForm.appendChild(tempElement);

        confirmation.style.display = 'flex';
    }

    function ShowCreateBox() {
        confirmationTitle.innerHTML = "Create Item";
        confirmationForm.action = "index.php?page=inventory&action=createItem";
        confirmationText.innerHTML = "Please enter a unique item name, then enter the initial quantity for this item.";
        confirmationSubmit.value = "Create";
        confirmationCancel.value = "No Cancel";
        confirmationSubmit.classList.add("yellowBG", "noBorder");

        tempElement = document.createElement("input");
        tempElement.type = "number";
        tempElement.name = "quantity";
        tempElement.min = "1";
        tempElement.placeholder = "Initial Quantity";
        tempElement.className = "tempElement";
        tempElement.required = true;
        confirmationForm.appendChild(tempElement);

        tempElement = document.createElement("input");
        tempElement.type = "text";
        tempElement.name = "name";
        tempElement.placeholder = "Item Name";
        tempElement.className = "tempElement";
        tempElement.required = true;
        confirmationForm.appendChild(tempElement);

        confirmation.style.display = 'flex';
    }

    function ShowDeleteBox() {
        confirmationTitle.innerHTML = "Delete Item?";
        confirmationForm.action = "index.php?page=inventory&action=deleteItem";
        confirmationText.innerHTML = "Are you sure to delete " + currentSelected.name + "?";
        confirmationSubmit.value = "Yes Delete";
        confirmationCancel.value = "No Cancel";
        confirmationSubmit.classList.add("redBG", "noBorder");

        tempElement = document.createElement("input");
        tempElement.type = "hidden";
        tempElement.name = "id";
        tempElement.value = currentSelected.id;
        tempElement.className = "tempElement";
        confirmationForm.appendChild(tempElement);

        confirmation.style.display = 'flex';
    }

    function ShowEditMinQuantityBox() {
        confirmationTitle.innerHTML = "Change Min Quantity";
        confirmationForm.action = "index.php?page=inventory&action=changeMinQuantity";
        confirmationText.innerHTML = "Input the number you want to change the minimum quantity to. Set to 0 if you don't want to have a minimum.";
        confirmationSubmit.value = "Update";
        confirmationCancel.value = "No Cancel";
        confirmationSubmit.classList.add("yellowBG", "noBorder");

        tempElement = document.createElement("input");
        tempElement.type = "number";
        tempElement.name = "quantity";
        tempElement.min = "0";
        tempElement.placeholder = "Minimun Quantity (" + currentSelected.minQuantity + ")";
        tempElement.className = "tempElement";
        tempElement.required = true;
        confirmationForm.appendChild(tempElement);

        tempElement = document.createElement("input");
        tempElement.type = "hidden";
        tempElement.name = "id";
        tempElement.value = currentSelected.id;
        tempElement.className = "tempElement";
        confirmationForm.appendChild(tempElement);

        confirmation.style.display = 'flex';
    }

    function ShowEditMaxAvgConsumptionBox() {
        confirmationTitle.innerHTML = "Change Max Avg Consumption";
        confirmationForm.action = "index.php?page=inventory&action=changeMaxAvgConsumption";
        confirmationText.innerHTML = "Input the number you want to change the maximum average consumption to. Set to 0 if you don't want to have a maximum.";
        confirmationSubmit.value = "Update";
        confirmationCancel.value = "No Cancel";
        confirmationSubmit.classList.add("yellowBG", "noBorder");

        tempElement = document.createElement("input");
        tempElement.type = "number";
        tempElement.name = "quantity";
        tempElement.min = "0";
        tempElement.placeholder = "Maximum Average Consumption (" + currentSelected.maxAvgConsumption + ")";
        tempElement.className = "tempElement";
        tempElement.required = true;
        confirmationForm.appendChild(tempElement);

        tempElement = document.createElement("input");
        tempElement.type = "hidden";
        tempElement.name = "id";
        tempElement.value = currentSelected.id;
        tempElement.className = "tempElement";
        confirmationForm.appendChild(tempElement);

        confirmation.style.display = 'flex';
    }

    document.addEventListener('DOMContentLoaded', function() {
        inventoryElement.forEach(elem => {
            elem.addEventListener('click', () => OnElementClick(elem));
        });

        updateButton.addEventListener('click', () => ShowUpdateBox());
        resetButton.addEventListener('click', () => ShowResetBox());
        createButton.addEventListener('click', () => ShowCreateBox());
        deleteButton.addEventListener('click', () => ShowDeleteBox());

        minQuantityText.addEventListener('click', () => ShowEditMinQuantityBox());
        avgConsumptionText.addEventListener('click', () => ShowEditMaxAvgConsumptionBox());

        targetDateInput.addEventListener('change', RenderCharts);

        dayRangeInput.addEventListener('input', function() {
            let val = parseInt(this.value, 10);
            const min = parseInt(this.min, 10) || 7;
            const max = parseInt(this.max, 10) || 60;
            if (isNaN(val) || val < min) this.value = min;
            if (val > max) this.value = max;
            RenderCharts();
        });
        dayRangeInput.addEventListener('blur', function() {
            let val = parseInt(this.value, 10);
            const min = parseInt(this.min, 10) || 7;
            const max = parseInt(this.max, 10) || 60;
            if (isNaN(val) || val < min) this.value = min;
            if (val > max) this.value = max;
        });

        weekRangeInput.addEventListener('input', function() {
            let val = parseInt(this.value, 10);
            const min = parseInt(this.min, 10) || 4;
            const max = parseInt(this.max, 10) || 24;
            if (isNaN(val) || val < min) this.value = min;
            if (val > max) this.value = max;
            RenderCharts();
        });
        weekRangeInput.addEventListener('blur', function() {
            let val = parseInt(this.value, 10);
            const min = parseInt(this.min, 10) || 4;
            const max = parseInt(this.max, 10) || 24;
            if (isNaN(val) || val < min) this.value = min;
            if (val > max) this.value = max;
        });

        monthRangeInput.addEventListener('input', function() {
            let val = parseInt(this.value, 10);
            const min = parseInt(this.min, 10) || 6;
            const max = parseInt(this.max, 10) || 24;
            if (isNaN(val) || val < min) this.value = min;
            if (val > max) this.value = max;
            RenderCharts();
        });
        monthRangeInput.addEventListener('blur', function() {
            let val = parseInt(this.value, 10);
            const min = parseInt(this.min, 10) || 6;
            const max = parseInt(this.max, 10) || 24;
            if (isNaN(val) || val < min) this.value = min;
            if (val > max) this.value = max;
        });
    });

    function PrepareInventoryData(inventoryRecords, lowStockThreshold, targetDateStr, dayRange, weekRange, monthRange) {
        lowStockThreshold = Number(lowStockThreshold) || 0;
        dayRange = Math.max(7, Number(dayRange) || 30);
        weekRange = Math.max(4, Number(weekRange) || 12);
        monthRange = Math.max(6, Number(monthRange) || 12);

        const parsedRecords = inventoryRecords.map(rec => ({
            timestamp: rec.date,
            date: new Date(rec.date + "T00:00:00"),
            quantity: rec.quantity,
            used: rec.consumption,
            added: rec.added
        })).sort((a, b) => a.date - b.date);

        const recordMap = new Map();
        parsedRecords.forEach(rec => recordMap.set(rec.timestamp, rec));

        const maxDate = targetDateStr ? new Date(targetDateStr + "T00:00:00") : new Date();

        function GetRecordForDate(targetDate) {
            const y = targetDate.getFullYear();
            const m = String(targetDate.getMonth() + 1).padStart(2, '0');
            const d = String(targetDate.getDate()).padStart(2, '0');
            const key = `${y}-${m}-${d}`;

            if (recordMap.has(key)) {
                return {
                    ...recordMap.get(key),
                    isOriginal: true
                };
            }

            let last = null;
            for (const rec of parsedRecords) {
                if (rec.date <= targetDate) last = rec;
                else break;
            }
            if (last) {
                return {
                    timestamp: key,
                    date: targetDate,
                    quantity: last.quantity,
                    used: 0,
                    added: 0,
                    isOriginal: false
                };
            }
            return {
                timestamp: key,
                date: targetDate,
                quantity: 0,
                used: 0,
                added: 0,
                isOriginal: false
            };
        }

        const monthAbbr = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        function GenerateDailyData() {
            const start = new Date(maxDate);
            start.setDate(start.getDate() - dayRange + 1);

            const labels = [],
                quantity = [],
                consumption = [],
                added = [];
            let curMonth = -1;

            for (let i = 0; i < dayRange; i++) {
                const d = new Date(start);
                d.setDate(start.getDate() + i);
                const rec = GetRecordForDate(d);

                const mIdx = d.getMonth();
                labels.push(mIdx !== curMonth ? `${monthAbbr[mIdx]} ${d.getDate()}` : `${d.getDate()}`);
                curMonth = mIdx;

                quantity.push(rec.quantity);
                consumption.push(rec.used);
                added.push(rec.added);
            }
            return {
                labels,
                quantity,
                consumption,
                added
            };
        }

        function GenerateWeeklyData() {
            const weekEnd = new Date(maxDate);
            weekEnd.setDate(weekEnd.getDate() + (6 - weekEnd.getDay()));

            const labels = [],
                quantity = [],
                consumption = [],
                added = [];

            for (let w = 0; w < weekRange; w++) {
                const weekStart = new Date(weekEnd);
                weekStart.setDate(weekStart.getDate() - 6);

                const sm = monthAbbr[weekStart.getMonth()];
                const em = monthAbbr[weekEnd.getMonth()];
                const label = (sm === em) ?
                    `${sm} ${weekStart.getDate()} - ${weekEnd.getDate()}` :
                    `${sm} ${weekStart.getDate()} - ${em} ${weekEnd.getDate()}`;
                labels.unshift(label);

                const weekRecs = parsedRecords.filter(rec => rec.date >= weekStart && rec.date <= weekEnd);
                if (weekRecs.length > 0) {
                    const sumQ = weekRecs.reduce((s, r) => s + r.quantity, 0);
                    const sumU = weekRecs.reduce((s, r) => s + r.used, 0);
                    const sumA = weekRecs.reduce((s, r) => s + r.added, 0);
                    const addedCount = weekRecs.filter(r => r.added > 0).length;
                    quantity.unshift(Math.round(sumQ / weekRecs.length));
                    consumption.unshift(Math.round(sumU / weekRecs.length));
                    added.unshift(addedCount > 0 ? Math.round(sumA / addedCount) : 0);
                } else {
                    let lastQty = 0;
                    for (let i = parsedRecords.length - 1; i >= 0; i--) {
                        if (parsedRecords[i].date <= weekEnd) {
                            lastQty = parsedRecords[i].quantity;
                            break;
                        }
                    }
                    quantity.unshift(lastQty);
                    consumption.unshift(0);
                    added.unshift(0);
                }

                weekEnd.setDate(weekEnd.getDate() - 7);
            }
            return {
                labels,
                quantity,
                consumption,
                added
            };
        }

        function GenerateMonthlyData() {
            const end = new Date(maxDate);
            const labels = [],
                quantity = [],
                consumption = [],
                added = [];

            for (let i = monthRange - 1; i >= 0; i--) {
                const d = new Date(end);
                d.setMonth(end.getMonth() - i);
                const mName = monthAbbr[d.getMonth()];
                const year = d.getFullYear();
                labels.push(`${mName} ${year}`);

                const mStart = new Date(year, d.getMonth(), 1);
                const mEnd = new Date(year, d.getMonth() + 1, 0);
                const mRecs = parsedRecords.filter(rec => rec.date >= mStart && rec.date <= mEnd);

                if (mRecs.length > 0) {
                    const sumQ = mRecs.reduce((s, r) => s + r.quantity, 0);
                    const sumU = mRecs.reduce((s, r) => s + r.used, 0);
                    const sumA = mRecs.reduce((s, r) => s + r.added, 0);
                    const addedCount = mRecs.filter(r => r.added > 0).length;
                    quantity.push(Math.round(sumQ / mRecs.length));
                    consumption.push(Math.round(sumU / mRecs.length));
                    added.push(addedCount > 0 ? Math.round(sumA / addedCount) : 0);
                } else {
                    let lastQty = 0;
                    for (let j = parsedRecords.length - 1; j >= 0; j--) {
                        if (parsedRecords[j].date <= mEnd) {
                            lastQty = parsedRecords[j].quantity;
                            break;
                        }
                    }
                    quantity.push(lastQty);
                    consumption.push(0);
                    added.push(0);
                }
            }
            return {
                labels,
                quantity,
                consumption,
                added
            };
        }

        function CreateChartOptions(data, granularity = 'daily') {
            const styles = getComputedStyle(document.documentElement);
            const yellow = styles.getPropertyValue('--yellow').trim() || '#f5b042';
            const red = styles.getPropertyValue('--red').trim() || '#dc3545';
            const green = styles.getPropertyValue('--green').trim() || '#28a745';
            const font = getComputedStyle(document.body).fontFamily || 'sans-serif';
            const prefix = (granularity === 'daily') ? '' : 'Avg ';

            return {
                series: [{
                        name: prefix + 'Quantity',
                        data: data.quantity,
                        color: yellow
                    },
                    {
                        name: prefix + 'Consumption',
                        data: data.consumption,
                        color: red
                    },
                    {
                        name: prefix + 'Added',
                        data: data.added,
                        color: green
                    }
                ],
                chart: {
                    type: 'line',
                    height: '100%',
                    width: '100%',
                    toolbar: {
                        show: true
                    },
                    zoom: {
                        enabled: false
                    },
                    animations: {
                        enabled: false
                    },
                    parentHeightOffset: 0,
                    fontFamily: font,
                    redrawOnWindowResize: true
                },
                stroke: {
                    width: [4, 2, 2],
                    curve: 'straight'
                },
                markers: {
                    size: 3
                },
                xaxis: {
                    categories: data.labels,
                    labels: {
                        rotate: -45,
                        style: {
                            fontSize: '10px',
                            fontFamily: font
                        }
                    },
                    tickPlacement: 'on'
                },
                yaxis: {
                    min: 0,
                    labels: {
                        style: {
                            fontSize: '10px',
                            fontFamily: font
                        }
                    }
                },
                grid: {
                    borderColor: '#e0e0e0',
                    strokeDashArray: 3,
                    padding: {
                        top: 0,
                        bottom: 0,
                        left: 0,
                        right: 0
                    }
                },
                legend: {
                    show: false
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontFamily: font
                    }
                },
                annotations: {
                    yaxis: [{
                        y: lowStockThreshold,
                        borderColor: red,
                        borderWidth: 2,
                        label: {
                            style: {
                                color: '#fff',
                                background: red,
                                fontSize: '10px',
                                fontFamily: font,
                                padding: {
                                    left: 4,
                                    right: 4,
                                    top: 2,
                                    bottom: 2
                                }
                            }
                        }
                    }]
                }
            };
        }

        return {
            GenerateDailyData,
            GenerateWeeklyData,
            GenerateMonthlyData,
            CreateChartOptions
        };
    }

    function RenderCharts() {
        if (!currentSelected.records.length) return;

        let dayRange = parseInt(dayRangeInput.value, 10);
        if (isNaN(dayRange) || dayRange < 7) dayRange = 30;
        let weekRange = parseInt(weekRangeInput.value, 10);
        if (isNaN(weekRange) || weekRange < 4) weekRange = 12;
        let monthRange = parseInt(monthRangeInput.value, 10);
        if (isNaN(monthRange) || monthRange < 6) monthRange = 12;

        dayRangeInput.value = dayRange;
        weekRangeInput.value = weekRange;
        monthRangeInput.value = monthRange;

        const prepared = PrepareInventoryData(
            currentSelected.records,
            currentSelected.minQuantity,
            targetDateInput.value,
            dayRange,
            weekRange,
            monthRange
        );

        const daily = document.getElementById('dailyGraph');
        const weekly = document.getElementById('weeklyGraph');
        const monthly = document.getElementById('monthlyGraph');

        daily.innerHTML = '';
        weekly.innerHTML = '';
        monthly.innerHTML = '';

        const dailyData = prepared.GenerateDailyData();
        const weeklyData = prepared.GenerateWeeklyData();
        const monthlyData = prepared.GenerateMonthlyData();

        new ApexCharts(daily, prepared.CreateChartOptions(dailyData, 'daily')).render();
        new ApexCharts(weekly, prepared.CreateChartOptions(weeklyData, 'weekly')).render();
        new ApexCharts(monthly, prepared.CreateChartOptions(monthlyData, 'monthly')).render();
    }

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(RenderCharts, 150);
    });

    confirmationCancel.addEventListener('click', () => {
        confirmationSubmit.classList.remove("redBG", "yellowBG", "noBorder");
    });

    confirmationBG.addEventListener('click', () => {
        confirmationSubmit.classList.remove("redBG", "yellowBG", "noBorder");
    });
</script>

</html>