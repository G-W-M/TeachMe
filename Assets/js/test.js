document.addEventListener("DOMContentLoaded", () => {
  const quizContainer = document.getElementById("quiz-container");
  const form = document.getElementById("tutorTestForm");
  const scoreInput = document.getElementById("scoreInput");

  const questions = [
    {
      question: "What does PHP stand for?",
      options: ["Personal Home Page", "Private Hypertext Processor", "PHP: Hypertext Preprocessor", "Public Hosting Protocol"],
      answer: "PHP: Hypertext Preprocessor"
    },
    {
      question: "Which PHP function hashes passwords securely?",
      options: ["hash()", "password_hash()", "md5()", "crypt()"],
      answer: "password_hash()"
    },
    {
      question: "Which SQL keyword retrieves data from a table?",
      options: ["GET", "EXTRACT", "SELECT", "SHOW"],
      answer: "SELECT"
    },
    {
      question: "Which HTML tag is used to create a hyperlink?",
      options: ["a", "link", "url", "href"],
      answer: "a"
    },
    {
      question: "CSS stands for?",
      options: ["Creative Style Sheets", "Cascading Style Sheets", "Computer Style Syntax", "Coding Style Set"],
      answer: "Cascading Style Sheets"
    }
  ];

  // Render quiz
  questions.forEach((q, i) => {
    const block = document.createElement("div");
    block.classList.add("question-block");
    block.innerHTML = `
      <p><strong>Q${i + 1}:</strong> ${q.question}</p>
      ${q.options.map(opt => `
        <label>
          <input type="radio" name="q${i}" value="${opt}" required> ${opt}
        </label><br>
      `).join('')}
    `;
    quizContainer.appendChild(block);
  });

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    let score = 0;
    questions.forEach((q, i) => {
      const selected = document.querySelector(`input[name="q${i}"]:checked`);
      if (selected && selected.value === q.answer) score++;
    });

    const percentage = Math.round((score / questions.length) * 100);
    scoreInput.value = percentage;

    // Don’t show score, just tell them pass/fail
    if (percentage >= 70) {
      alert(" Great job! You have successfully passed the tutor test. Submitting your result...");
    } else {
      alert("You did not meet the 70% requirement. Please try again.");
    }

    form.submit();
  });
});

/*// test.js
document.addEventListener("DOMContentLoaded", () => {
    const quizForm = document.getElementById("quizForm");
    if (!quizForm) return;

    quizForm.addEventListener("submit", e => {
        e.preventDefault();
        let score = 0;
        const total = quizForm.querySelectorAll("input[type='radio']:checked").length;

        quizForm.querySelectorAll(".question").forEach(q => {
            const correct = q.dataset.correct;
            const selected = q.querySelector("input[type='radio']:checked")?.value;
            if (selected === correct) score++;
        });

        const percentage = (score / total) * 100;
        alert(`Your score: ${percentage}%`);
        if (percentage >= 70) {
            alert("Congratulations! You passed the test.");
        } else {
            alert("Sorry, you did not reach the passing score.");
        }
    });
});
*/