document.addEventListener("DOMContentLoaded", function() {
    const darkThemeToggle = document.getElementById("dark-theme-toggle");
    const darkThemeIcon = document.getElementById("dark-theme-icon");

    darkThemeToggle.addEventListener("click", function() {
        const darkThemeStylesheet = document.getElementById("dark-theme");
        const isEnabled = darkThemeStylesheet.getAttribute("disabled") === null;

        if (isEnabled) {
            darkThemeStylesheet.setAttribute("disabled", "disabled");
            localStorage.setItem("dark-theme", "false");
        } else {
            darkThemeStylesheet.removeAttribute("disabled");
            localStorage.setItem("dark-theme", "true");
        }
        updateThemeIcon(!isEnabled);
    });

    function updateThemeIcon(isEnabled) {
        if (isEnabled) {
            darkThemeIcon.classList.remove("bi-moon");
            darkThemeIcon.classList.add("bi-sun");
        } else {
            darkThemeIcon.classList.remove("bi-sun");
            darkThemeIcon.classList.add("bi-moon");
        }
    }

    const darkThemeStylesheet = document.getElementById("dark-theme");
    const darkThemeEnabled = localStorage.getItem("dark-theme") === "true";

    if (darkThemeEnabled) {
        darkThemeStylesheet.removeAttribute("disabled");
    }
    updateThemeIcon(darkThemeEnabled);
});
