<!DOCTYPE html>
<html>

<head>
    <title>Sales Panel - Hontoria OMS</title>
    <link rel="stylesheet" href="../../Shared/CSS/Main.css">
    <style>
        /* Minimal grid helpers – use them only here */
        .calendarWeekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
        }

        .calendarDaysGrid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
        }
    </style>
</head>

<body class="asideLayout fixedScreen">
    <?php include("../Views/.Components/SideBar.php"); ?>
    <main class="columnLayout midGap">
        <h1 class="titleLogo minGap tinHeight">
            <img src="../../Shared/Img/PesoIcon.png" alt="Peso"> Sales Panel
        </h1>
        <?php include("../Views/.Components/MessageBox.php"); ?>
        <section class="rowLayout flexMax midGap">
            <div class="flexMinExtra columnLayout midGap">
                <section class="centerColumnLayout roundedMid flexMid">
                    <div class="box fullHeight fullWidth roundedMid columnLayout tinGap">
                        <section class="minGap columnLayout flexMax" id="calendarContainer">
                            <div class="centerHoriRowLayout minGap">
                                <div class="centerHoriRowLayout tinGap flexMin">
                                    <h2 type="button" class="noBorder darkBG whiteText circle squareSize unitHeight centerColumnLayout clickable shadowed" id="prevMonthBtn">
                                        <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors mirrorX">
                                    </h2>
                                    <h2 id="monthYearDisplay" class="capitalFirst centerText flexMid">Month Year</h2>
                                    <h2 type="button" class="noBorder darkBG whiteText circle squareSize unitHeight centerColumnLayout clickable shadowed" id="nextMonthBtn">
                                        <img src="../../Shared/Img/ArrowIcon.png" alt="Arrow" class="invertColors">
                                    </h2>
                                </div>
                                <div class="centerHoriRowLayout tinGap fullHeight">
                                    <input type="number" id="yearInput" class="fullHeight emphasizedText whiteText outlineText roundedTin centerText tinWidth noPadding centerText"
                                        min="1980" max="9999">
                                    <button type="button" class="darkBG noBorder shadowed centerColumnLayout fullHeight clickable" id="goYearBtn">
                                        <h3 class="whiteText">Go</h3>
                                    </button>
                                    <button type="button" class="yellowBG noBorder shadowed centerColumnLayout fullHeight clickable" id="todayBtn">
                                        <h3 class="whiteText outlineText">Today</h3>
                                    </button>
                                </div>
                            </div>
                            <div class="calendarWeekdays" id="weekdaysContainer">
                                <h3 class="centerText">Sun</h3>
                                <h3 class="centerText">Mon</h3>
                                <h3 class="centerText">Tue</h3>
                                <h3 class="centerText">Wed</h3>
                                <h3 class="centerText">Thu</h3>
                                <h3 class="centerText">Fri</h3>
                                <h3 class="centerText">Sat</h3>
                            </div>
                            <div class="calendarDaysGrid flexMax tinGap" id="daysGrid"></div>
                        </section>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
                <section class="centerColumnLayout roundedMid flexMid">
                    <div class="box fullHeight fullWidth roundedMid columnLayout tinGap">
                        <div class="centerHoriRowLayout tinGap">
                            <h3 class="flexMid" id="selectedDateText">Selected Date</h3>
                            <h5 id="selectedDateProfitText" class="capitalFirst midZ roundedTin centerText yellowBG whiteText outlineText shadowed flexMid">
                                Profit: ₱0
                            </h5>
                        </div>
                        <div class="rowLayout tinGap flexMax">
                            <div class="fullHeight flexMax columnLayout minGap">
                                <h5 id="selectedDateInflowText" class="capitalFirst midZ roundedTin centerText greenTransBG greenBorder whiteText outlineText shadowed">
                                    Inflow: ₱0
                                </h5>
                                <div class="flexMax scrollable regTinPadding columnLayout tinGap noFlexBasis noMinHeight" id="inflowRecordsContainer">
                                    <h4 class="centerMarginsSelf">No Inflow</h4>
                                </div>
                                <button type="button" class="darkBG noBorder shadowed centerColumnLayout fitHeight clickable roundedTin" id="addInflowRecordButton">
                                    <h5 class="whiteText">Add Inflow Record</h5>
                                </button>
                            </div>
                            <div class="fullHeight flexMax columnLayout minGap">
                                <h5 id="selectedDateOutflowText" class="capitalFirst midZ roundedTin centerText redTransBG redBorder whiteText outlineText shadowed">
                                    Outflow: ₱0
                                </h5>
                                <div class="flexMax scrollable regTinPadding columnLayout tinGap noFlexBasis noMinHeight" id="outflowRecordsContainer">
                                    <h4 class="centerMarginsSelf">No Outflow</h4>
                                </div>
                                <button type="button" class="darkBG noBorder shadowed centerColumnLayout fitHeight clickable roundedTin" id="addOutflowRecordButton">
                                    <h5 class="whiteText">Add Outflow Record</h5>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
            </div>
            <section class="columnLayout midGap flexMax">
                <section class="centerColumnLayout roundedMid minGap">
                    <div class="centerColumnLayout tinGap box roundedMid fullHeight fullWidth">
                        <div class="centerHoriRowLayout tinGap fullWidth">
                            <h5 class="flexMid capitalFirst midZ roundedTin centerText greenTransBG greenBorder whiteText outlineText shadowed" id="monthInflowText">
                                Month Inflow: ₱0
                            </h5>
                            <h5 class="flexMid capitalFirst midZ roundedTin centerText greenTransBG greenBorder whiteText outlineText shadowed" id="weekInflowText">
                                Week Inflow: ₱0
                            </h5>
                            <h5 class="flexMid capitalFirst midZ roundedTin centerText greenTransBG greenBorder whiteText outlineText shadowed" id="avgInflowText">
                                30-day Avg Inflow: ₱0
                            </h5>
                        </div>
                        <div class="centerHoriRowLayout tinGap fullWidth">
                            <h5 class="flexMid capitalFirst midZ roundedTin centerText redTransBG redBorder whiteText outlineText shadowed" id="monthOutflowText">
                                Month Outflow: ₱0
                            </h5>
                            <h5 class="flexMid capitalFirst midZ roundedTin centerText redTransBG redBorder whiteText outlineText shadowed" id="weekOutflowText">
                                Week Outflow: ₱0
                            </h5>
                            <h5 class="flexMid capitalFirst midZ roundedTin centerText redTransBG redBorder whiteText outlineText shadowed" id="avgOutflowText">
                                30-day Avg Outflow: ₱0
                            </h5>
                        </div>
                        <div class="centerHoriRowLayout tinGap fullWidth">
                            <h5 class="flexMid capitalFirst midZ roundedTin centerText yellowBG whiteText outlineText shadowed" id="monthProfitText">
                                Month Profit: ₱0
                            </h5>
                            <h5 class="flexMid capitalFirst midZ roundedTin centerText yellowBG whiteText outlineText shadowed" id="weekProfitText">
                                Week Profit: ₱0
                            </h5>
                            <h5 class="flexMid capitalFirst midZ roundedTin centerText yellowBG whiteText outlineText shadowed" id="avgProfitText">
                                30-day Avg Profit: ₱0
                            </h5>
                        </div>
                    </div>
                    <div class="gradientBorderDiag"></div>
                </section>
                <section class="centerColumnLayout roundedMid minGap flexMid noFlexBasis noMinHeight">
                    <div class="columnLayout minGap whiteBG regPadding roundedMid fullHeight fullWidth">
                        <div class="reverseCenterHoriRowLayout minGap">
                            <select id="granularitySelect" class="unitHeight tinWidth roundedTin noPadding centerText minText">
                                <option value="daily" selected>Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                            <div class="centerHoriRowLayout tinGap">
                                <h6 id="granularityText">Days:</h6>
                                <input type="number" id="granularityRangeInput" class="unitHeight duoWidth roundedTin noPadding centerText">
                            </div>
                        </div>
                        <div class="centerColumnLayout whiteBG roundedMin fullHeight fullWidth">
                            <div id="salesGraph" class="flexMax centerColumnLayout roundedMin whiteBG midZ fullWidth">

                            </div>
                            <div class="gradientBorderDiag minZ shadowed"></div>
                        </div>
                        <div class="centerColumnLayout whiteBG roundedMin fullHeight fullWidth">
                            <div id="inflowStreamsGraph" class="flexMax centerColumnLayout roundedMin whiteBG midZ fullWidth">

                            </div>
                            <div class="gradientBorderDiag minZ shadowed"></div>
                        </div>
                        <div class="centerColumnLayout whiteBG roundedMin fullHeight fullWidth">
                            <div id="outflowStreamsGraph" class="flexMax centerColumnLayout roundedMin whiteBG midZ fullWidth">

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
    const salesRecordsRaw = <?php echo json_encode($salesRecords); ?>;
    const salesRecords = salesRecordsRaw.map(rec => ({
        ...rec,
        isInflow: rec.isInflow === '1',
        value: Number(rec.value)
    }));

    const salesOrders = <?php echo json_encode($salesOrders); ?>;

    // ========================== GLOBAL DATA ==========================
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
    ];
    const monthAbbr = monthNames; // used in graphs

    // ========================== COLOR HELPERS ==========================
    function hashCode(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash |= 0;
        }
        return Math.abs(hash);
    }

    function inflowShade(type) {
        const hash = hashCode(type);
        const h = 60 + (hash % 181); // 60–240 (yellow → green → blue)
        const s = 50 + (hash * 7 % 41); // 50–90%
        const l = 40 + (hash * 3 % 21); // 40–60%
        return `hsl(${h}, ${s}%, ${l}%)`;
    }

    function outflowShade(type) {
        const hash = hashCode(type);
        const interval = hash % 2;
        let h;
        if (interval === 0) {
            h = hash % 61; // 0–60 (red → orange → yellow)
        } else {
            h = 330 + (hash % 31); // 330–360 (pink/magenta)
        }
        const s = 50 + (hash * 7 % 41);
        const l = 40 + (hash * 3 % 21);
        return `hsl(${h}, ${s}%, ${l}%)`;
    }

    // ========================== CALENDAR ==========================
    const monthYearDisplay = document.getElementById('monthYearDisplay');
    const daysGrid = document.getElementById('daysGrid');
    const prevMonthBtn = document.getElementById('prevMonthBtn');
    const nextMonthBtn = document.getElementById('nextMonthBtn');
    const yearInput = document.getElementById('yearInput');
    const goYearBtn = document.getElementById('goYearBtn');
    const todayBtn = document.getElementById('todayBtn');

    let currentDate = new Date();

    function daysInMonth(year, month) {
        return new Date(year, month + 1, 0).getDate();
    }

    function firstDayOfMonth(year, month) {
        return new Date(year, month, 1).getDay();
    }

    function isFutureMonth(year, month) {
        const now = new Date();
        const currentYear = now.getFullYear();
        const currentMonth = now.getMonth();
        return (year > currentYear) || (year === currentYear && month > currentMonth);
    }

    function updateNextButtonState(year, month) {
        const now = new Date();
        const currentYear = now.getFullYear();
        const currentMonth = now.getMonth();
        // Disable next button only when the displayed month is the current month
        if (year === currentYear && month === currentMonth) {
            nextMonthBtn.classList.add('faded', 'unclickable');
        } else {
            nextMonthBtn.classList.remove('faded', 'unclickable');
        }
    }

    function getProfitForDate(dateStr) {
        let profit = 0;
        for (const rec of salesRecords) {
            if (rec.date === dateStr) {
                profit += rec.isInflow ? rec.value : -rec.value;
            }
        }
        return profit;
    }

    function renderCalendar(year, month, selectedDate = null) {
        monthYearDisplay.textContent = monthNames[month] + ' ' + year;
        yearInput.value = year;

        const days = daysInMonth(year, month);
        const startDay = firstDayOfMonth(year, month);
        const todayMidnight = new Date();
        todayMidnight.setHours(0, 0, 0, 0);

        daysGrid.innerHTML = '';

        const pad = (n) => String(n).padStart(2, '0');

        // Helper: build a filler cell (previous or next month)
        function createFillerCell(dateStr, dayNum, isToday, isFuture, profit) {
            const div = document.createElement('div');
            div.classList.add('centerColumnLayout', 'whiteText', 'outlineText', 'roundedTin', 'shadowed');

            // Always faded and unclickable for filler cells
            div.classList.add('faded', 'unclickable');

            if (isToday) {
                // Today styling but forced faded and unclickable
                if (profit > 0) div.classList.add('greenBG');
                else if (profit < 0) div.classList.add('redBG');
                else div.classList.add('yellowBG');
                // No clickable, no dateElement, no dataset
            } else if (isFuture) {
                div.classList.add('bordered', 'darkFadedBG');
            } else { // past filler
                if (profit > 0) div.classList.add('greenTransBG', 'greenBorder');
                else if (profit < 0) div.classList.add('redTransBG', 'redBorder');
                else div.classList.add('yellowTransBG', 'yellowBorder');
            }
            // Never a dateElement, never clickable, never selected underline
            div.innerHTML = '<h4>' + dayNum + '</h4>';
            return div;
        }

        // ====================== LEADING (previous month) FILLER ======================
        if (startDay > 0) {
            const prevMonthYear = (month === 0) ? year - 1 : year;
            const prevMonth = (month === 0) ? 11 : month - 1;
            const prevDays = daysInMonth(prevMonthYear, prevMonth);
            const startPrevDay = prevDays - startDay + 1;

            for (let d = startPrevDay; d <= prevDays; d++) {
                const cellDate = new Date(prevMonthYear, prevMonth, d);
                const isToday = cellDate.getTime() === todayMidnight.getTime();
                const isFuture = cellDate.getTime() > todayMidnight.getTime();
                const dateStr = `${prevMonthYear}-${pad(prevMonth+1)}-${pad(d)}`;
                const profit = getProfitForDate(dateStr);
                daysGrid.appendChild(createFillerCell(dateStr, d, isToday, isFuture, profit));
            }
        }

        // ====================== CURRENT MONTH DAYS ======================
        for (let d = 1; d <= days; d++) {
            const cellDate = new Date(year, month, d);
            const isToday = cellDate.getTime() === todayMidnight.getTime();
            const isFuture = cellDate.getTime() > todayMidnight.getTime();
            const dateStr = `${year}-${pad(month+1)}-${pad(d)}`;
            const profit = getProfitForDate(dateStr);

            const div = document.createElement('div');
            div.classList.add('centerColumnLayout', 'whiteText', 'outlineText', 'roundedTin', 'shadowed');

            if (isToday) {
                if (profit > 0) div.classList.add('greenBG');
                else if (profit < 0) div.classList.add('redBG');
                else div.classList.add('yellowBG');
                div.classList.add('clickable', 'dateElement');
                div.dataset.date = dateStr;
                if (dateStr === selectedDate) div.classList.add('underlineText');
            } else if (isFuture) {
                div.classList.add('bordered', 'darkFadedBG', 'unclickable');
            } else { // past (current month)
                if (profit > 0) div.classList.add('greenTransBG', 'greenBorder');
                else if (profit < 0) div.classList.add('redTransBG', 'redBorder');
                else div.classList.add('yellowTransBG', 'yellowBorder');
                div.classList.add('clickable', 'dateElement');
                div.dataset.date = dateStr;
                if (dateStr === selectedDate) div.classList.add('underlineText');
            }

            div.innerHTML = '<h4>' + d + '</h4>';
            daysGrid.appendChild(div);
        }

        // ====================== TRAILING (next month) FILLER ======================
        const totalCells = 42;
        const filledCells = startDay + days;
        const remainingCells = totalCells - filledCells;

        if (remainingCells > 0) {
            const nextMonthYear = (month === 11) ? year + 1 : year;
            const nextMonth = (month === 11) ? 0 : month + 1;

            for (let d = 1; d <= remainingCells; d++) {
                const cellDate = new Date(nextMonthYear, nextMonth, d);
                const isToday = cellDate.getTime() === todayMidnight.getTime();
                const isFuture = cellDate.getTime() > todayMidnight.getTime();
                const dateStr = `${nextMonthYear}-${pad(nextMonth+1)}-${pad(d)}`;
                const profit = getProfitForDate(dateStr);
                daysGrid.appendChild(createFillerCell(dateStr, d, isToday, isFuture, profit));
            }
        }

        updateNextButtonState(year, month);
    }

    function goToPrevMonth() {
        if (currentDate.getMonth() === 0) {
            currentDate.setMonth(11);
            currentDate.setFullYear(currentDate.getFullYear() - 1);
        } else {
            currentDate.setMonth(currentDate.getMonth() - 1);
        }
        const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
        selectedDateStr = lastDay.getFullYear() + '-' + String(lastDay.getMonth() + 1).padStart(2, '0') + '-' + String(lastDay.getDate()).padStart(2, '0');
        renderCalendar(currentDate.getFullYear(), currentDate.getMonth(), selectedDateStr);
        updateSalesDetail(selectedDateStr);
        renderSalesGraphs();
    }

    function goToNextMonth() {
        const nextMonth = (currentDate.getMonth() === 11) ? 0 : currentDate.getMonth() + 1;
        const nextYear = (currentDate.getMonth() === 11) ? currentDate.getFullYear() + 1 : currentDate.getFullYear();
        if (isFutureMonth(nextYear, nextMonth)) return;

        if (currentDate.getMonth() === 11) {
            currentDate.setMonth(0);
            currentDate.setFullYear(currentDate.getFullYear() + 1);
        } else {
            currentDate.setMonth(currentDate.getMonth() + 1);
        }
        selectedDateStr = currentDate.getFullYear() + '-' + String(currentDate.getMonth() + 1).padStart(2, '0') + '-01';
        renderCalendar(currentDate.getFullYear(), currentDate.getMonth(), selectedDateStr);
        updateSalesDetail(selectedDateStr);
        renderSalesGraphs();
    }

    function goToYear(year) {
        if (isNaN(year) || year < 1 || year > 9999) {
            return;
        }

        currentDate.setFullYear(year);
        // If the resulting (year, current month) is in the future, reset to today
        if (isFutureMonth(currentDate.getFullYear(), currentDate.getMonth())) {
            currentDate = new Date();
            selectedDateStr = todayStr;
        } else {
            const lastDayOfMonth = daysInMonth(currentDate.getFullYear(), currentDate.getMonth());
            const currentDay = parseInt(selectedDateStr.split('-')[2], 10);
            const newDay = Math.min(currentDay, lastDayOfMonth);
            selectedDateStr = currentDate.getFullYear() + '-' +
                String(currentDate.getMonth() + 1).padStart(2, '0') + '-' +
                String(newDay).padStart(2, '0');
        }

        renderCalendar(currentDate.getFullYear(), currentDate.getMonth(), selectedDateStr);
        updateSalesDetail(selectedDateStr);
        renderSalesGraphs();
    }

    function goToToday() {
        currentDate = new Date();
        selectedDateStr = todayStr;
        renderCalendar(currentDate.getFullYear(), currentDate.getMonth(), selectedDateStr);
        updateSalesDetail(selectedDateStr);
        renderSalesGraphs();
    }

    prevMonthBtn.addEventListener('click', goToPrevMonth);
    nextMonthBtn.addEventListener('click', goToNextMonth);
    goYearBtn.addEventListener('click', function() {
        goToYear(parseInt(yearInput.value, 10));
    });
    yearInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') goToYear(parseInt(yearInput.value, 10));
    });
    todayBtn.addEventListener('click', goToToday);

    // ========================== SALES DETAIL PANEL ==========================
    const selectedDateText = document.getElementById('selectedDateText');
    const selectedDateProfitText = document.getElementById('selectedDateProfitText');
    const selectedDateInflowText = document.getElementById('selectedDateInflowText');
    const selectedDateOutflowText = document.getElementById('selectedDateOutflowText');
    const inflowRecordsContainer = document.getElementById('inflowRecordsContainer');
    const outflowRecordsContainer = document.getElementById('outflowRecordsContainer');
    const addInflowRecordButton = document.getElementById('addInflowRecordButton');
    const addOutflowRecordButton = document.getElementById('addOutflowRecordButton');

    function formatDisplayDate(dateStr) {
        const parts = dateStr.split('-');
        const year = parseInt(parts[0]);
        const month = parseInt(parts[1]) - 1;
        const day = parseInt(parts[2]);
        return monthNames[month] + ' ' + day + ', ' + year;
    }

    function updateAddButtonsVisibility(dateStr) {
        const isToday = dateStr === todayStr;
        if (addInflowRecordButton) addInflowRecordButton.classList.toggle('hidden', !isToday);
        if (addOutflowRecordButton) addOutflowRecordButton.classList.toggle('hidden', !isToday);
    }

    function updateSalesDetail(dateStr) {
        const isToday = dateStr === todayStr;
        const dayRecords = salesRecords.filter(r => r.date === dateStr);

        let totalInflow = 0;
        let totalOutflow = 0;
        const inflows = [];
        const outflows = [];

        dayRecords.forEach(rec => {
            if (rec.isInflow) {
                totalInflow += rec.value;
                inflows.push(rec);
            } else {
                totalOutflow += rec.value;
                outflows.push(rec);
            }
        });

        const profit = totalInflow - totalOutflow;

        selectedDateText.textContent = formatDisplayDate(dateStr);

        selectedDateProfitText.textContent = profit < 0 ? 'Profit: -₱' + Math.abs(profit).toLocaleString() : 'Profit: ₱' + profit.toLocaleString();
        selectedDateProfitText.classList.remove('yellowBG', 'greenBG', 'redBG');
        if (profit > 0) {
            selectedDateProfitText.classList.add('greenBG');
        } else if (profit < 0) {
            selectedDateProfitText.classList.add('redBG');
        } else {
            selectedDateProfitText.classList.add('yellowBG');
        }

        selectedDateInflowText.textContent = 'Inflow: ₱' + totalInflow.toLocaleString();

        inflowRecordsContainer.innerHTML = '';
        if (inflows.length === 0) {
            inflowRecordsContainer.innerHTML = '<h4 class="centerMarginsSelf">No Inflow</h4>';
        } else {
            inflows.forEach(rec => {
                const div = document.createElement('div');
                div.classList.add(
                    'roundedTin', 'columnLayout', 'flexStatic', 'greenBorder',
                    'shadowed', 'fixedScreen', 'noShrink', 'fullWidth', 'fitHeight',
                    'relatived'
                );
                div.innerHTML = `
                    ${isToday ? `<a class="squareSize unitHeight norWestAbsolute centerColumnLayout edgeCorner clickable recordRemove" data-id="${rec.id}">
                        <img src="../../Shared/Img/XIcon.png" alt="X">
                    </a>` : ''}
                    <h5 class="capitalFirst centerText regtinPadding flexMax shadowed greenTransBG whiteText outlineText">
                        ${rec.description}
                    </h5>
                    <h6 class="capitalFirst centerText regMinPadding">₱${rec.value.toLocaleString()}</h6>
                `;
                inflowRecordsContainer.appendChild(div);
            });
        }

        selectedDateOutflowText.textContent = 'Outflow: ₱' + totalOutflow.toLocaleString();

        outflowRecordsContainer.innerHTML = '';
        if (outflows.length === 0) {
            outflowRecordsContainer.innerHTML = '<h4 class="centerMarginsSelf">No Outflow</h4>';
        } else {
            outflows.forEach(rec => {
                const div = document.createElement('div');
                div.classList.add(
                    'roundedTin', 'columnLayout', 'flexStatic', 'redBorder',
                    'shadowed', 'fixedScreen', 'noShrink', 'fullWidth', 'fitHeight',
                    'relatived'
                );
                div.innerHTML = `
                    ${isToday ? `<a class="squareSize unitHeight norWestAbsolute centerColumnLayout edgeCorner clickable recordRemove" data-id="${rec.id}">
                        <img src="../../Shared/Img/XIcon.png" alt="X">
                    </a>` : ''}
                    <h5 class="capitalFirst centerText regtinPadding flexMax shadowed redTransBG whiteText outlineText">
                        ${rec.description}
                    </h5>
                    <h6 class="capitalFirst centerText regMinPadding">₱${rec.value.toLocaleString()}</h6>
                `;
                outflowRecordsContainer.appendChild(div);
            });
        }

        inflowRecordsContainer.addEventListener('click', (e) => {
            const removeBtn = e.target.closest('.recordRemove');
            if (!removeBtn) return;
            e.stopPropagation();

            const recordID = removeBtn.dataset.id;
            const oldInputs = confirmationForm.querySelectorAll('.tempElement');
            oldInputs.forEach(el => el.remove());

            confirmationTitle.textContent = "Delete Inflow Record?";
            confirmationForm.action = "index.php?page=sales&action=deleteRecord";
            confirmationText.textContent = "Are you sure you want to delete this inflow record?";
            confirmationSubmit.value = "Delete";
            confirmationSubmit.classList.add("redBG", "noBorder");

            const hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = "recordID";
            hiddenInput.value = recordID;
            hiddenInput.className = "tempElement";
            confirmationForm.appendChild(hiddenInput);

            confirmation.style.display = 'flex';
        });

        outflowRecordsContainer.addEventListener('click', (e) => {
            const removeBtn = e.target.closest('.recordRemove');
            if (!removeBtn) return;
            e.stopPropagation();

            const recordID = removeBtn.dataset.id;
            const oldInputs = confirmationForm.querySelectorAll('.tempElement');
            oldInputs.forEach(el => el.remove());

            confirmationTitle.textContent = "Delete Outflow Record?";
            confirmationForm.action = "index.php?page=sales&action=deleteRecord";
            confirmationText.textContent = "Are you sure you want to delete this outflow record?";
            confirmationSubmit.value = "Delete";
            confirmationSubmit.classList.add("redBG", "noBorder");

            const hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = "recordID";
            hiddenInput.value = recordID;
            hiddenInput.className = "tempElement";
            confirmationForm.appendChild(hiddenInput);

            confirmation.style.display = 'flex';
        });

        updateSalesSummary(dateStr);
        renderSalesGraphs(); // refresh graphs after detail update
        updateAddButtonsVisibility(dateStr);
    }

    daysGrid.addEventListener('click', function(e) {
        const target = e.target.closest('.dateElement');
        if (!target) return;
        const dateStr = target.dataset.date;
        if (dateStr && dateStr !== selectedDateStr) {
            selectedDateStr = dateStr;
            renderCalendar(currentDate.getFullYear(), currentDate.getMonth(), selectedDateStr);
            updateSalesDetail(selectedDateStr);
        }
    });

    const todayObj = new Date();
    const todayStr = todayObj.getFullYear() + '-' +
        String(todayObj.getMonth() + 1).padStart(2, '0') + '-' +
        String(todayObj.getDate()).padStart(2, '0');

    let selectedDateStr = todayStr;

    // ========================== SALES SUMMARY ==========================
    const monthInflowText = document.getElementById('monthInflowText');
    const weekInflowText = document.getElementById('weekInflowText');
    const avgInflowText = document.getElementById('avgInflowText');
    const monthOutflowText = document.getElementById('monthOutflowText');
    const weekOutflowText = document.getElementById('weekOutflowText');
    const avgOutflowText = document.getElementById('avgOutflowText');
    const monthProfitText = document.getElementById('monthProfitText');
    const weekProfitText = document.getElementById('weekProfitText');
    const avgProfitText = document.getElementById('avgProfitText');

    function updateSalesSummary(dateStr) {
        const date = new Date(dateStr + 'T00:00:00');
        const currentMonth = date.getMonth();
        const currentYear = date.getFullYear();

        function sumRange(startDate, endDate) {
            let inflow = 0,
                outflow = 0;
            salesRecords.forEach(rec => {
                const recDate = new Date(rec.date + 'T00:00:00');
                if (recDate >= startDate && recDate <= endDate) {
                    if (rec.isInflow) inflow += rec.value;
                    else outflow += rec.value;
                }
            });
            return {
                inflow,
                outflow,
                profit: inflow - outflow
            };
        }

        const monthStart = new Date(currentYear, currentMonth, 1);
        const monthEnd = new Date(currentYear, currentMonth + 1, 0);
        const monthTotals = sumRange(monthStart, monthEnd);

        monthInflowText.textContent = `Month Inflow: ₱${monthTotals.inflow.toLocaleString()}`;
        monthOutflowText.textContent = `Month Outflow: ₱${monthTotals.outflow.toLocaleString()}`;
        monthProfitText.textContent = monthTotals.profit < 0 ?
            `Month Profit: -₱${Math.abs(monthTotals.profit).toLocaleString()}` :
            `Month Profit: ₱${monthTotals.profit.toLocaleString()}`;
        monthProfitText.classList.remove('yellowBG', 'greenBG', 'redBG');
        monthProfitText.classList.add(monthTotals.profit > 0 ? 'greenBG' : (monthTotals.profit < 0 ? 'redBG' : 'yellowBG'));

        const dayOfWeek = date.getDay();
        const weekStart = new Date(date);
        weekStart.setDate(date.getDate() - dayOfWeek);
        weekStart.setHours(0, 0, 0, 0);
        const weekEnd = new Date(weekStart);
        weekEnd.setDate(weekStart.getDate() + 6);
        weekEnd.setHours(23, 59, 59, 999);
        const weekTotals = sumRange(weekStart, weekEnd);

        weekInflowText.textContent = `Week Inflow: ₱${weekTotals.inflow.toLocaleString()}`;
        weekOutflowText.textContent = `Week Outflow: ₱${weekTotals.outflow.toLocaleString()}`;
        weekProfitText.textContent = weekTotals.profit < 0 ?
            `Week Profit: -₱${Math.abs(weekTotals.profit).toLocaleString()}` :
            `Week Profit: ₱${weekTotals.profit.toLocaleString()}`;
        weekProfitText.classList.remove('yellowBG', 'greenBG', 'redBG');
        weekProfitText.classList.add(weekTotals.profit > 0 ? 'greenBG' : (weekTotals.profit < 0 ? 'redBG' : 'yellowBG'));

        const thirtyDaysAgo = new Date(date);
        thirtyDaysAgo.setDate(date.getDate() - 29);
        thirtyDaysAgo.setHours(0, 0, 0, 0);
        const thirtyEnd = new Date(date);
        thirtyEnd.setHours(23, 59, 59, 999);

        let totalInflow30 = 0,
            inflowCount = 0;
        let totalOutflow30 = 0,
            outflowCount = 0;

        salesRecords.forEach(rec => {
            const recDate = new Date(rec.date + 'T00:00:00');
            if (recDate >= thirtyDaysAgo && recDate <= thirtyEnd) {
                if (rec.isInflow) {
                    totalInflow30 += rec.value;
                    inflowCount++;
                } else {
                    totalOutflow30 += rec.value;
                    outflowCount++;
                }
            }
        });

        const avgInflow = inflowCount ? Math.round(totalInflow30 / inflowCount) : 0;
        const avgOutflow = outflowCount ? Math.round(totalOutflow30 / outflowCount) : 0;
        const avgProfit = avgInflow - avgOutflow;

        avgInflowText.textContent = `30-day Avg Inflow: ₱${avgInflow.toLocaleString()}`;
        avgOutflowText.textContent = `30-day Avg Outflow: ₱${avgOutflow.toLocaleString()}`;
        avgProfitText.textContent = avgProfit < 0 ?
            `30-day Avg Profit: -₱${Math.abs(avgProfit).toLocaleString()}` :
            `30-day Avg Profit: ₱${avgProfit.toLocaleString()}`;
        avgProfitText.classList.remove('yellowBG', 'greenBG', 'redBG');
        avgProfitText.classList.add(avgProfit > 0 ? 'greenBG' : (avgProfit < 0 ? 'redBG' : 'yellowBG'));
    }

    // ========================== GRANULARITY CONTROLS ==========================
    const granularitySelect = document.getElementById('granularitySelect');
    const granularityRangeInput = document.getElementById('granularityRangeInput');
    const granularityText = document.getElementById('granularityText');

    const defaultRanges = {
        daily: 30,
        weekly: 12,
        monthly: 12
    };
    const minRanges = {
        daily: 7,
        weekly: 4,
        monthly: 6
    };
    const maxRanges = {
        daily: 60,
        weekly: 24,
        monthly: 24
    };

    function setGranularityUI(gran) {
        granularityRangeInput.value = defaultRanges[gran];
        granularityRangeInput.min = minRanges[gran];
        granularityRangeInput.max = maxRanges[gran];
        granularityText.textContent = gran === 'daily' ? 'Days:' : (gran === 'weekly' ? 'Weeks:' : 'Months:');
    }

    // Initialize and attach listeners
    setGranularityUI('daily');

    granularitySelect.addEventListener('change', function() {
        setGranularityUI(this.value);
        renderSalesGraphs();
    });

    granularityRangeInput.addEventListener('input', function() {
        let val = parseInt(this.value, 10);
        const min = parseInt(this.min, 10);
        const max = parseInt(this.max, 10);
        if (isNaN(val) || val < min) this.value = min;
        if (val > max) this.value = max;
        renderSalesGraphs();
    });

    granularityRangeInput.addEventListener('blur', function() {
        let val = parseInt(this.value, 10);
        const min = parseInt(this.min, 10);
        const max = parseInt(this.max, 10);
        if (isNaN(val) || val < min) this.value = min;
        if (val > max) this.value = max;
    });

    // ========================== DYNAMIC GRAPHS ==========================
    function buildSalesGraphOptions(dateStr, granularity, range) {
        const targetDate = new Date(dateStr + 'T00:00:00');
        let labels = [];
        let dailyInflow = [],
            dailyOutflow = [],
            dailyProfit = [];
        const inflowByType = {},
            outflowByType = {};

        // ---- Daily ----
        if (granularity === 'daily') {
            const start = new Date(targetDate);
            start.setDate(start.getDate() - range + 1);
            let curMonth = -1;
            for (let i = 0; i < range; i++) {
                const d = new Date(start);
                d.setDate(start.getDate() + i);
                const mIdx = d.getMonth();
                const day = d.getDate();
                labels.push(mIdx !== curMonth ? `${monthAbbr[mIdx]} ${day}` : `${day}`);
                curMonth = mIdx;

                const dateKey = `${d.getFullYear()}-${String(mIdx+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                const dayRecords = salesRecords.filter(r => r.date === dateKey);
                let inSum = 0,
                    outSum = 0;
                dayRecords.forEach(rec => {
                    if (rec.isInflow) {
                        inSum += rec.value;
                        if (!inflowByType[rec.type]) inflowByType[rec.type] = new Array(range).fill(0);
                        inflowByType[rec.type][i] += rec.value;
                    } else {
                        outSum += rec.value;
                        if (!outflowByType[rec.type]) outflowByType[rec.type] = new Array(range).fill(0);
                        outflowByType[rec.type][i] += rec.value;
                    }
                });
                dailyInflow.push(inSum);
                dailyOutflow.push(outSum);
                dailyProfit.push(inSum - outSum);
            }
        }
        // ---- Weekly (forward iteration) ----
        else if (granularity === 'weekly') {
            const dayOfWeek = targetDate.getDay();
            let lastWeekEnd = new Date(targetDate);
            lastWeekEnd.setDate(lastWeekEnd.getDate() + (6 - dayOfWeek));
            lastWeekEnd.setHours(23, 59, 59, 999);

            const firstWeekEnd = new Date(lastWeekEnd);
            firstWeekEnd.setDate(firstWeekEnd.getDate() - (7 * (range - 1)));
            firstWeekEnd.setHours(23, 59, 59, 999);

            let weekEnd = new Date(firstWeekEnd);
            for (let w = 0; w < range; w++) {
                const weekStart = new Date(weekEnd);
                weekStart.setDate(weekStart.getDate() - 6);
                weekStart.setHours(0, 0, 0, 0);

                const sm = monthAbbr[weekStart.getMonth()];
                const em = monthAbbr[weekEnd.getMonth()];
                labels.push(sm === em ?
                    `${sm} ${weekStart.getDate()} - ${weekEnd.getDate()}` :
                    `${sm} ${weekStart.getDate()} - ${em} ${weekEnd.getDate()}`);

                const weekRecs = salesRecords.filter(rec => {
                    const d = new Date(rec.date + 'T00:00:00');
                    return d >= weekStart && d <= weekEnd;
                });

                let inSum = 0,
                    outSum = 0;
                weekRecs.forEach(rec => {
                    if (rec.isInflow) inSum += rec.value;
                    else outSum += rec.value;
                });
                dailyInflow.push(inSum);
                dailyOutflow.push(outSum);
                dailyProfit.push(inSum - outSum);

                weekRecs.forEach(rec => {
                    if (rec.isInflow) {
                        if (!inflowByType[rec.type]) inflowByType[rec.type] = new Array(range).fill(0);
                        inflowByType[rec.type][w] += rec.value;
                    } else {
                        if (!outflowByType[rec.type]) outflowByType[rec.type] = new Array(range).fill(0);
                        outflowByType[rec.type][w] += rec.value;
                    }
                });

                weekEnd.setDate(weekEnd.getDate() + 7);
            }
        }
        // ---- Monthly (forward iteration) ----
        else if (granularity === 'monthly') {
            const oldestMonth = new Date(targetDate);
            oldestMonth.setMonth(oldestMonth.getMonth() - (range - 1));
            oldestMonth.setDate(1);
            oldestMonth.setHours(0, 0, 0, 0);

            let monthStart = new Date(oldestMonth);
            for (let m = 0; m < range; m++) {
                const year = monthStart.getFullYear();
                const month = monthStart.getMonth();
                labels.push(`${monthAbbr[month]} ${year}`);

                const monthEnd = new Date(year, month + 1, 0, 23, 59, 59, 999);
                const monthRecs = salesRecords.filter(rec => {
                    const d = new Date(rec.date + 'T00:00:00');
                    return d >= monthStart && d <= monthEnd;
                });

                let inSum = 0,
                    outSum = 0;
                monthRecs.forEach(rec => {
                    if (rec.isInflow) inSum += rec.value;
                    else outSum += rec.value;
                });
                dailyInflow.push(inSum);
                dailyOutflow.push(outSum);
                dailyProfit.push(inSum - outSum);

                monthRecs.forEach(rec => {
                    if (rec.isInflow) {
                        if (!inflowByType[rec.type]) inflowByType[rec.type] = new Array(range).fill(0);
                        inflowByType[rec.type][m] += rec.value;
                    } else {
                        if (!outflowByType[rec.type]) outflowByType[rec.type] = new Array(range).fill(0);
                        outflowByType[rec.type][m] += rec.value;
                    }
                });

                monthStart.setMonth(monthStart.getMonth() + 1);
            }
        }

        // ---- Dynamic padding helper ----
        function paddedMinMax(arrays) {
            const flat = arrays.flat();
            if (flat.length === 0) return {
                min: 0,
                max: 1
            };
            const min = Math.min(...flat);
            const max = Math.max(...flat);
            if (max === min) return {
                min: min - 1,
                max: max + 1
            };
            return {
                min: min >= 0 ? 0 : min * 1.1,
                max: max * 1.1
            };
        }

        const styles = getComputedStyle(document.documentElement);
        const yellow = styles.getPropertyValue('--yellow').trim() || '#f5b042';
        const red = styles.getPropertyValue('--red').trim() || '#dc3545';
        const green = styles.getPropertyValue('--green').trim() || '#28a745';
        const font = getComputedStyle(document.body).fontFamily || 'sans-serif';

        // ---- General Sales ----
        const generalMinMax = paddedMinMax([dailyInflow, dailyOutflow, dailyProfit]);
        const generalSalesOptions = {
            series: [{
                    name: 'Inflow',
                    data: dailyInflow,
                    color: green
                },
                {
                    name: 'Outflow',
                    data: dailyOutflow,
                    color: red
                },
                {
                    name: 'Profit',
                    data: dailyProfit,
                    color: yellow
                }
            ],
            chart: {
                type: 'line',
                height: '100%',
                width: '100%',
                stacked: false, // ← explicit non-stacked
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
                width: 3,
                curve: 'straight'
            },
            markers: {
                size: 3
            },
            xaxis: {
                categories: labels,
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
                min: generalMinMax.min,
                max: generalMinMax.max,
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
            }
        };

        // ---- Inflow Streams ----
        const inflowTypes = Object.keys(inflowByType);
        const inflowSeries = inflowTypes.map(type => ({
            name: type,
            data: inflowByType[type],
            color: inflowShade(type)
        }));
        const inflowMinMax = paddedMinMax(Object.values(inflowByType));
        const inflowStreamsOptions = {
            series: inflowSeries,
            chart: {
                type: 'line',
                height: '100%',
                width: '100%',
                stacked: false,
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
                width: 2,
                curve: 'straight'
            },
            markers: {
                size: 2
            },
            xaxis: {
                categories: labels,
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
                min: inflowMinMax.min,
                max: inflowMinMax.max,
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
            }
        };

        // ---- Outflow Streams ----
        const outflowTypes = Object.keys(outflowByType);
        const outflowSeries = outflowTypes.map(type => ({
            name: type,
            data: outflowByType[type],
            color: outflowShade(type)
        }));
        const outflowMinMax = paddedMinMax(Object.values(outflowByType));
        const outflowStreamsOptions = {
            series: outflowSeries,
            chart: {
                type: 'line',
                height: '100%',
                width: '100%',
                stacked: false,
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
                width: 2,
                curve: 'straight'
            },
            markers: {
                size: 2
            },
            xaxis: {
                categories: labels,
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
                min: outflowMinMax.min,
                max: outflowMinMax.max,
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
            }
        };

        return {
            generalSalesOptions,
            inflowStreamsOptions,
            outflowStreamsOptions
        };
    }

    function renderSalesGraphs() {
        if (!selectedDateStr) return;
        const gran = granularitySelect.value;
        let range = parseInt(granularityRangeInput.value, 10);
        const min = minRanges[gran];
        const max = maxRanges[gran];
        if (isNaN(range) || range < min) range = defaultRanges[gran];
        if (range > max) range = max;
        granularityRangeInput.value = range; // ensure displayed value is clamped

        const opts = buildSalesGraphOptions(selectedDateStr, gran, range);

        const salesGraph = document.getElementById('salesGraph');
        const inflowStreamsGraph = document.getElementById('inflowStreamsGraph');
        const outflowStreamsGraph = document.getElementById('outflowStreamsGraph');

        if (salesGraph) {
            salesGraph.innerHTML = '<h4 class="norAbsolute closeCorner topZ">General Sales Graph</h4>';
            new ApexCharts(salesGraph, opts.generalSalesOptions).render();
        }
        if (inflowStreamsGraph) {
            inflowStreamsGraph.innerHTML = '<h4 class="norAbsolute closeCorner topZ">Inflow Streams Graph</h4>';
            new ApexCharts(inflowStreamsGraph, opts.inflowStreamsOptions).render();
        }
        if (outflowStreamsGraph) {
            outflowStreamsGraph.innerHTML = '<h4 class="norAbsolute closeCorner topZ">Outflow Streams Graph</h4>';
            new ApexCharts(outflowStreamsGraph, opts.outflowStreamsOptions).render();
        }
    }

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(renderSalesGraphs, 150);
    });

    // ----- Add Inflow Record -----
    addInflowRecordButton.addEventListener('click', () => {
        const oldInputs = confirmationForm.querySelectorAll('.tempElement');
        oldInputs.forEach(el => el.remove());

        confirmationTitle.innerHTML = "Add Inflow Record";
        confirmationForm.action = "index.php?page=sales&action=createInflowRecord";
        confirmationSubmit.value = "Add Inflow";
        confirmationSubmit.classList.add("yellowBG", "noBorder");

        // ---- wrapper for all temporary elements ----
        const wrapper = document.createElement("div");
        wrapper.className = "tempElement columnLayout minGap";

        // Order Inflow checkbox + label inside a row container
        const checkboxRow = document.createElement("div");
        checkboxRow.className = "centerHoriRowLayout tinGap";

        const isOrderInflowCheckbox = document.createElement("input");
        isOrderInflowCheckbox.type = "checkbox";
        isOrderInflowCheckbox.id = "isOrderInflow";
        isOrderInflowCheckbox.name = "isOrderInflow";
        isOrderInflowCheckbox.checked = true;

        const label = document.createElement("h4");
        label.textContent = " Is Order Inflow";

        checkboxRow.appendChild(isOrderInflowCheckbox);
        checkboxRow.appendChild(label);
        wrapper.appendChild(checkboxRow);

        // dynamic container for the specific form fields
        const dynamicContainer = document.createElement("div");
        dynamicContainer.id = "dynamicInflowInputs";
        dynamicContainer.className = "columnLayout minGap";
        wrapper.appendChild(dynamicContainer);

        // function to render the correct set of inputs inside dynamicContainer
        function renderInflowInputs() {
            dynamicContainer.innerHTML = '';
            if (isOrderInflowCheckbox.checked) {
                // ---- Order Inflow ----
                confirmationText.innerHTML = "Select the order to record payment for and input the payment for the order.";
                const unpaidOrders = salesOrders.filter(so =>
                    parseFloat(so.priceTotal) - parseFloat(so.pricePaid) > 0
                );

                if (unpaidOrders.length === 0) {
                    const warning = document.createElement("h4");
                    warning.textContent = "No unpaid orders available.";
                    warning.className = "redText centerText";
                    dynamicContainer.appendChild(warning);
                    return;
                }

                const select = document.createElement("select");
                select.name = "orderID";
                select.required = true;

                const defaultOption = document.createElement("option");
                defaultOption.value = "";
                defaultOption.textContent = "Select an order";
                defaultOption.disabled = true;
                defaultOption.selected = true;
                select.appendChild(defaultOption);

                unpaidOrders.forEach(order => {
                    const remaining = (parseFloat(order.priceTotal) - parseFloat(order.pricePaid)).toFixed(2);
                    const option = document.createElement("option");
                    option.value = order.orderID;
                    option.textContent = `Order #${order.orderID} – Remaining: ₱${remaining}`;
                    option.dataset.remaining = remaining;
                    select.appendChild(option);
                });

                const paymentInput = document.createElement("input");
                paymentInput.type = "number";
                paymentInput.name = "value";
                paymentInput.placeholder = "Payment Amount";
                paymentInput.step = "0.01";
                paymentInput.min = "0.01";
                paymentInput.required = true;

                select.addEventListener('change', () => {
                    const selectedOption = select.options[select.selectedIndex];
                    if (selectedOption && selectedOption.dataset.remaining) {
                        paymentInput.max = selectedOption.dataset.remaining;
                    }
                });

                dynamicContainer.appendChild(select);
                dynamicContainer.appendChild(paymentInput);
            } else {
                // ---- Other Inflow ----
                confirmationText.innerHTML = "Enter the inflow details for today. The type must be exact in spelling, please be careful with inputs.";
                const typeInput = document.createElement("input");
                typeInput.type = "text";
                typeInput.name = "type";
                typeInput.placeholder = "Type";
                typeInput.maxLength = 25;
                typeInput.required = true;

                const descInput = document.createElement("input");
                descInput.type = "text";
                descInput.name = "description";
                descInput.placeholder = "Description";
                descInput.maxLength = 25;
                descInput.required = true;

                const valueInput = document.createElement("input");
                valueInput.type = "number";
                valueInput.name = "value";
                valueInput.placeholder = "Amount";
                valueInput.step = "0.01";
                valueInput.min = "0.01";
                valueInput.required = true;

                dynamicContainer.appendChild(typeInput);
                dynamicContainer.appendChild(descInput);
                dynamicContainer.appendChild(valueInput);
            }
        }

        renderInflowInputs();
        isOrderInflowCheckbox.addEventListener('change', renderInflowInputs);

        // Append the whole wrapper at once
        confirmationForm.appendChild(wrapper);

        confirmation.style.display = 'flex';
    });

    // ----- Add Outflow Record -----
    addOutflowRecordButton.addEventListener('click', () => {
        const oldInputs = confirmationForm.querySelectorAll('.tempElement');
        oldInputs.forEach(el => el.remove());

        confirmationTitle.innerHTML = "Add Outflow Record";
        confirmationForm.action = "index.php?page=sales&action=createOutflowRecord";
        confirmationText.innerHTML = "Enter the outflow details for today. The type must be exact in spelling, please be careful with inputs.";
        confirmationSubmit.value = "Add Outflow";
        confirmationSubmit.classList.add("yellowBG", "noBorder");

        // Create value input (number, two decimal places)
        const valueInput = document.createElement("input");
        valueInput.type = "number";
        valueInput.name = "value";
        valueInput.placeholder = "Amount";
        valueInput.step = "0.01";
        valueInput.min = "0.01";
        valueInput.className = "tempElement";
        valueInput.required = true;
        confirmationForm.appendChild(valueInput);

        // Create description input (text, max 25 chars)
        const descInput = document.createElement("input");
        descInput.type = "text";
        descInput.name = "description";
        descInput.placeholder = "Description";
        descInput.maxLength = 25;
        descInput.className = "tempElement";
        descInput.required = true;
        confirmationForm.appendChild(descInput);

        // Create type input (text, max 25 chars)
        const typeInput = document.createElement("input");
        typeInput.type = "text";
        typeInput.name = "type";
        typeInput.placeholder = "Type";
        typeInput.maxLength = 25;
        typeInput.className = "tempElement";
        typeInput.required = true;
        confirmationForm.appendChild(typeInput);

        confirmation.style.display = 'flex';
    });

    // ========================== INITIALIZATION ==========================
    function onDOMReady() {
        yearInput.max = new Date().getFullYear(); // prevent spinner from going past current year
        setGranularityUI('daily'); // ensure UI is ready before first render
        renderCalendar(currentDate.getFullYear(), currentDate.getMonth(), selectedDateStr);
        updateSalesDetail(selectedDateStr);
        updateAddButtonsVisibility(selectedDateStr);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', onDOMReady);
    } else {
        onDOMReady();
    }
</script>

</html>