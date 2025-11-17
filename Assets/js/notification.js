function showNotification(message, type = "info") {
  const notif = document.createElement("div");
  notif.className = `toast ${type}`;
  notif.innerText = message;
  document.body.appendChild(notif);

  setTimeout(() => {
    notif.remove();
  }, 4000);
}

// Example usage:
// showNotification("Signup successful! Your STUD_ID is 101", "success");
