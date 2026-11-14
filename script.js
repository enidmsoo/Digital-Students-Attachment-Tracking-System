// Display a welcome message when the website loads
document.addEventListener("DOMContentLoaded", function () {

    console.log("Digital Student Attachment Tracking System loaded successfully.");

    // Confirm before updating attachment records
    const updateForms = document.querySelectorAll(".update-form");

    updateForms.forEach(function (form) {
        form.addEventListener("submit", function (event) {

            const confirmUpdate = confirm(
                "Are you sure you want to update this student's attachment progress?"
            );

            if (!confirmUpdate) {
                event.preventDefault();
            }

        });
    });

    // Confirm before registering a student
    const registrationForm = document.getElementById("registrationForm");

    if (registrationForm) {
        registrationForm.addEventListener("submit", function (event) {

            const confirmRegistration = confirm(
                "Are you sure you want to register this student?"
            );

            if (!confirmRegistration) {
                event.preventDefault();
            }

        });
    }

    // Search form validation
    const searchForm = document.getElementById("searchForm");

    if (searchForm) {
        searchForm.addEventListener("submit", function (event) {

            const searchInput = document.getElementById("searchInput");

            if (searchInput && searchInput.value.trim() === "") {
                event.preventDefault();
                alert("Please enter a student name or admission number.");
            }

        });
    }

});

// Automatically calculate attachment progress
function calculateProgress() {

    const progressInput = document.getElementById("progress");

    if (progressInput) {

        let progress = Number(progressInput.value);

        if (progress < 0) {
            progressInput.value = 0;
        }

        if (progress > 100) {
            progressInput.value = 100;
        }

    }

}

// Display current date
function displayDate() {

    const dateElement = document.getElementById("currentDate");

    if (dateElement) {

        const today = new Date();

        dateElement.textContent = today.toDateString();

    }

}

document.addEventListener("DOMContentLoaded", displayDate);