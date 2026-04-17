function getDueTime(dueDate) {
    const diff = new Date(dueDate) - new Date();
    const days = Math.floor(diff / 86400000);
    const hours = Math.floor((diff % 86400000) / 3600000);

    return `${days}d ${hours}h`;
}

function formatDate(inputDate) {
    const date = new Date(inputDate);
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function formatDateTime(inputDate) {
    const date = new Date(inputDate);
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    const timeOptions = { hour: 'numeric', minute: '2-digit', hour12: true };
    return date.toLocaleDateString('en-US', options) + ' ' + date.toLocaleTimeString('en-US', timeOptions);
}
