document.addEventListener("DOMContentLoaded", () => {
  const signupForm = document.querySelector(".signup-box form");
  const loginForm = document.querySelector(".login-box form");

  // ---------------- SIGNUP FORM ----------------
  if (signupForm) {
    signupForm.addEventListener("submit", (e) => {
      const email = signupForm.email.value.trim();
      const password = signupForm.password.value;
      const name = signupForm.user_name.value.trim();

      let errors = [];

      if (!email.endsWith("@strathmore.edu")) {
        errors.push("Please use your Strathmore email address.");
      }
      if (password.length < 6) {
        errors.push("Password must be at least 6 characters.");
      }
      if (name.length < 3) {
        errors.push("Name must be at least 3 characters.");
      }

      if (errors.length > 0) {
        e.preventDefault();
        alert(errors.join("\n"));
      }
    });
  }

  // ---------------- LOGIN FORM ----------------
  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      const id = loginForm.user_id.value.trim();
      const password = loginForm.password.value;

      if (!id || !password) {
        e.preventDefault();
        alert("Please fill in all fields.");
      }
    });
  }
});
