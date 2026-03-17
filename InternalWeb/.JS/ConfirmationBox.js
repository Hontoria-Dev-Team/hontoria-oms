const confirmation = document.getElementById('confirmationBox');
const confirmationContent = document.getElementById('confirmationContent');
const confirmationBG = document.getElementById('confirmationBackground');

const confimationTitle = document.getElementById('confirmationTitle');
const confimationText = document.getElementById('confirmationText');
const confirmationForm = document.getElementById('confirmationForm');

const confirmationButtons = document.getElementById('confirmationButtons');
const confirmationSubmit = document.getElementById('submitConfimationButton');
const confirmationCancel = document.getElementById('cancelConfimationButton');

confirmationCancel.addEventListener('click', function () {
    confirmation.style.display = 'none';
});
confirmationBG.addEventListener('click', function () {
    confirmation.style.display = 'none';
});
