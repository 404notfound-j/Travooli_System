document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const warning = document.getElementById("form-warning");

    form.addEventListener("submit", function (e) {
        // Collect required fields
        const requiredFields = form.querySelectorAll("[required]");
        let allFilled = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                allFilled = false;
            }
        });

        if (!allFilled) {
            e.preventDefault(); // Prevent form submission
            warning.style.display = "block"; // Show warning message
        } else {
            warning.style.display = "none"; // Hide if everything is filled
        }
    });
});
