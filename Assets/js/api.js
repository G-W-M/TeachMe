// api.js
function postData(url = "", data = {}) {
    return fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json());
}

// Example: submitting feedback
document.addEventListener("DOMContentLoaded", () => {
    const feedbackForm = document.getElementById("feedbackForm");
    if (!feedbackForm) return;

    feedbackForm.addEventListener("submit", e => {
        e.preventDefault();
        const formData = {
            session_id: feedbackForm.session_id.value,
            rating: feedbackForm.rating.value,
            comments: feedbackForm.comments.value
        };

        postData("../modules/learner/give_feedback.php", formData)
            .then(data => {
                if (data.success) alert("Feedback submitted!");
            })
            .catch(err => console.error(err));
    });
});
