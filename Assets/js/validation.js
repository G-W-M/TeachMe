// validation.js
document.addEventListener("DOMContentLoaded", () => {
    const inputs = document.querySelectorAll("input");

    inputs.forEach(input => {
        input.addEventListener("input", () => {
            if (input.value.trim() === "") {
                input.style.border = "1px solid red";
            } else {
                input.style.border = "1px solid #ccc";
            }
        });
    });
});
