// signup.js
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("signupForm");

  form.addEventListener("submit", (e) => {
    const email = form.email.value.trim();
    const password = form.password.value;
    const confirm = form.confirm_password.value;

    if (!email.endsWith("@strathmore.edu")) {
      alert("Please use your Strathmore email address.");
      e.preventDefault();
    } else if (password !== confirm) {
      alert("Passwords do not match.");
      e.preventDefault();
    }
  });
});
