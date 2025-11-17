// charts.js
document.addEventListener("DOMContentLoaded", () => {
    const chartCanvas = document.getElementById("adminChart");
    if (!chartCanvas) return;

    // Example: Chart.js usage (ensure chart.js is loaded)
    const ctx = chartCanvas.getContext("2d");
    new Chart(ctx, {
        type: "bar",
        data: {
            labels: ["Tutors", "Learners", "Sessions Completed"],
            datasets: [{
                label: "TeachMe Stats",
                data: [12, 25, 18],
                backgroundColor: ["#003366", "#0059b3", "#3399ff"]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            }
        }
    });
});
