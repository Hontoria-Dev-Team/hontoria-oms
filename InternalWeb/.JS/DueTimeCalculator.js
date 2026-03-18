function getDueTime(dueDate) {
    const diff = new Date(dueDate) - new Date();
    const days = Math.floor(diff / 86400000);
    const hours = Math.floor((diff % 86400000) / 3600000);

    return `${days}d ${hours}h`;
}
