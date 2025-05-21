/**
 * Simple helper to add a link back to home in the auth pages
 * This file is loaded by the auth layout
 */
document.addEventListener("DOMContentLoaded", function () {
    // Just to initialize any bootstrap components that might be in the auth pages
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (tooltips.length > 0) {
        tooltips.forEach((tooltip) => {
            new bootstrap.Tooltip(tooltip);
        });
    }
});
