// main.js - Global functions
document.addEventListener("DOMContentLoaded", () => {
    console.log("TeachMe global JS loaded.");

    // Simple notification system
    function showNotification(message, type = "info") {
        const notif = document.createElement("div");
        notif.classList.add("notification", type);
        notif.textContent = message;
        document.body.appendChild(notif);
        setTimeout(() => notif.remove(), 3000);
    }

    // Example: auto-dismiss messages
    const notifications = document.querySelectorAll(".notification");
    notifications.forEach(n => setTimeout(() => n.remove(), 3000));

    window.showNotification = showNotification; // global function
});
