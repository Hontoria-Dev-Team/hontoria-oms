let idleTimer;

function resetIdleTimer() {
    clearTimeout(idleTimer);
    idleTimer = setTimeout(function () {
        location.reload();
    }, 60000); // 60 seconds of idle
}

// Listen for any user activity
document.addEventListener('mousemove', resetIdleTimer);
document.addEventListener('keydown', resetIdleTimer);
document.addEventListener('click', resetIdleTimer);
document.addEventListener('scroll', resetIdleTimer);
document.addEventListener('touchstart', resetIdleTimer);

// Start the timer
resetIdleTimer();
