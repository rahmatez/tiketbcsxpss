// Custom JavaScript for Pundit FC

document.addEventListener("DOMContentLoaded", function () {
    // Initialize animation for elements with animate-fade-in class
    const animatedElements = document.querySelectorAll(".animate-fade-in");
    animatedElements.forEach((element, index) => {
        element.style.opacity = "0";
        setTimeout(() => {
            element.style.animation = "fadeIn 0.5s ease forwards";
        }, index * 100); // Stagger the animations
    });

    // Home page filter functionality
    const searchInput = document.getElementById("search-teams");
    const tournamentFilter = document.getElementById("filter-tournament");
    const matchTypeFilter = document.getElementById("filter-match-type");
    const resetButton = document.getElementById("reset-filters");
    const clearSearchBtn = document.getElementById("clear-search");

    if (searchInput && tournamentFilter && matchTypeFilter) {
        function filterGames() {
            const searchTerm = searchInput.value.toLowerCase();
            const tournamentValue = tournamentFilter.value.toLowerCase();
            const matchTypeValue = matchTypeFilter.value;

            let visibleCount = 0;

            document.querySelectorAll(".game-item").forEach((game) => {
                const homeTeam = game.getAttribute("data-home-team");
                const awayTeam = game.getAttribute("data-away-team");
                const tournament = game.getAttribute("data-tournament");
                const matchType = game.getAttribute("data-match-type");

                const matchesSearch =
                    !searchTerm ||
                    homeTeam.includes(searchTerm) ||
                    awayTeam.includes(searchTerm);

                const matchesTournament =
                    !tournamentValue || tournament === tournamentValue;

                const matchesType =
                    !matchTypeValue || matchType === matchTypeValue;

                if (matchesSearch && matchesTournament && matchesType) {
                    game.classList.remove("d-none");
                    visibleCount++;
                } else {
                    game.classList.add("d-none");
                }
            });

            // Show/hide no results message
            const noResultsMessage =
                document.getElementById("no-results-message");
            if (noResultsMessage) {
                if (visibleCount === 0) {
                    noResultsMessage.classList.remove("d-none");
                } else {
                    noResultsMessage.classList.add("d-none");
                }
            }
        }

        function resetFilters() {
            searchInput.value = "";
            tournamentFilter.value = "";
            matchTypeFilter.value = "";
            filterGames();
        }

        // Add event listeners
        searchInput.addEventListener("input", filterGames);
        tournamentFilter.addEventListener("change", filterGames);
        matchTypeFilter.addEventListener("change", filterGames);

        if (resetButton) resetButton.addEventListener("click", resetFilters);
        if (clearSearchBtn)
            clearSearchBtn.addEventListener("click", resetFilters);
    }

    // Countdown timer
    const countdownEl = document.getElementById("match-countdown");
    if (countdownEl) {
        const matchTime = new Date(
            countdownEl.getAttribute("data-match-time")
        ).getTime();

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = matchTime - now;

            if (distance < 0) {
                document.getElementById("days").innerHTML = "0";
                document.getElementById("hours").innerHTML = "0";
                document.getElementById("minutes").innerHTML = "0";
                document.getElementById("seconds").innerHTML = "0";
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor(
                (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
            );
            const minutes = Math.floor(
                (distance % (1000 * 60 * 60)) / (1000 * 60)
            );
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("days").innerHTML = days;
            document.getElementById("hours").innerHTML = hours;
            document.getElementById("minutes").innerHTML = minutes;
            document.getElementById("seconds").innerHTML = seconds;
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    }

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Form validation styling
    const forms = document.querySelectorAll(".needs-validation");
    Array.from(forms).forEach((form) => {
        form.addEventListener(
            "submit",
            (event) => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add("was-validated");
            },
            false
        );
    });

    // Loading overlay
    window.showLoading = function () {
        document.querySelector(".loading-overlay").classList.add("show");
    };
    window.hideLoading = function () {
        document.querySelector(".loading-overlay").classList.remove("show");
    };

    // Add loading overlay on form submits
    const submitForms = document.querySelectorAll('form[data-loading="true"]');
    submitForms.forEach((form) => {
        form.addEventListener("submit", () => {
            showLoading();
        });
    }); // Stats number animation has been removed

    // Share functionality
    const shareButtons = document.querySelectorAll(".btn-share");
    shareButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const title = this.getAttribute("data-title");
            const text = this.getAttribute("data-text");
            const url = window.location.href;

            if (navigator.share) {
                navigator
                    .share({
                        title: title,
                        text: text,
                        url: url,
                    })
                    .catch(console.error);
            } else {
                // Fallback
                const tempInput = document.createElement("input");
                document.body.appendChild(tempInput);
                tempInput.value = `${text} ${url}`;
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);

                alert("Link pertandingan telah disalin ke clipboard!");
            }
        });
    });

    // Animation on scroll
    const animateElements = document.querySelectorAll(".animate-fade-in");
    const animateObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add(
                        "animate__animated",
                        "animate__fadeIn"
                    );
                    animateObserver.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.1 }
    );

    animateElements.forEach((el) => {
        animateObserver.observe(el);
    });
});

// Ticket category selection
const ticketCategories = document.querySelectorAll(".ticket-category-select");
ticketCategories.forEach((category) => {
    category.addEventListener("click", function () {
        // Remove selection from all categories
        ticketCategories.forEach((cat) => cat.classList.remove("selected"));
        // Add selection to clicked category
        this.classList.add("selected");
        // Update hidden input value
        const categoryInput = document.getElementById("ticket_category");
        if (categoryInput) {
            categoryInput.value = this.dataset.category;
            // Trigger change event to update UI if needed
            const event = new Event("change");
            categoryInput.dispatchEvent(event);
        }
    });
});

// Quantity selector with +/- buttons
const quantitySelectors = document.querySelectorAll(".quantity-selector");
quantitySelectors.forEach((selector) => {
    const minusBtn = selector.querySelector(".btn-minus");
    const plusBtn = selector.querySelector(".btn-plus");
    const input = selector.querySelector("input");

    if (minusBtn && plusBtn && input) {
        minusBtn.addEventListener("click", () => {
            const currentValue = parseInt(input.value);
            if (currentValue > parseInt(input.min)) {
                input.value = currentValue - 1;
                // Trigger change event
                const event = new Event("change");
                input.dispatchEvent(event);
            }
        });

        plusBtn.addEventListener("click", () => {
            const currentValue = parseInt(input.value);
            if (currentValue < parseInt(input.max)) {
                input.value = currentValue + 1;
                // Trigger change event
                const event = new Event("change");
                input.dispatchEvent(event);
            }
        });
    }
});

// Share button functionality
const shareButtons = document.querySelectorAll(".btn-share");
if (shareButtons.length > 0 && navigator.share) {
    shareButtons.forEach((button) => {
        button.addEventListener("click", async () => {
            try {
                await navigator.share({
                    title: button.dataset.title,
                    text: button.dataset.text,
                    url: button.dataset.url || window.location.href,
                });
            } catch (err) {
                console.error("Share failed:", err);
            }
        });
        button.style.display = "inline-flex";
    });
} else {
    // Hide share buttons if Web Share API is not supported
    shareButtons.forEach((button) => {
        button.style.display = "none";
    });
}

// Initialize dynamic countdown timers
const countdownElements = document.querySelectorAll("[data-countdown]");
countdownElements.forEach((element) => {
    const targetDate = new Date(element.dataset.countdown).getTime();

    const countdownFunction = setInterval(() => {
        const now = new Date().getTime();
        const distance = targetDate - now;

        if (distance < 0) {
            clearInterval(countdownFunction);
            element.innerHTML = element.dataset.expiredText || "Expired";
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor(
            (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
        );
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000); // Update countdown HTML with better formatting
        element.innerHTML = `
                <div class="countdown-container mx-auto">
                    <div class="countdown-item">
                        <div class="countdown-value">${days}</div>
                        <div class="countdown-label">Hari</div>
                    </div>
                    <div class="countdown-item">
                        <div class="countdown-value">${hours}</div>
                        <div class="countdown-label">Jam</div>
                    </div>
                    <div class="countdown-item">
                        <div class="countdown-value">${minutes}</div>
                        <div class="countdown-label">Menit</div>
                    </div>
                    <div class="countdown-item">
                        <div class="countdown-value">${seconds}</div>
                        <div class="countdown-label">Detik</div>
                    </div>
                </div>
            `;
    }, 1000);
});

// Save QR Code functionality    const saveQrButtons = document.querySelectorAll(".btn-save-qr");
saveQrButtons.forEach((button) => {
    button.addEventListener("click", function () {
        const qrContainer = document.querySelector(this.dataset.qrContainer);
        if (!qrContainer) return;
        // Use html2canvas to capture the QR code
        if (typeof html2canvas !== "undefined") {
            html2canvas(qrContainer).then((canvas) => {
                const link = document.createElement("a");
                link.download = "pundit-fc-ticket.png";
                link.href = canvas.toDataURL("image/png");
                link.click();
            });
        }
    });
});

// Function to update price when quantity or ticket category changes
function updateTicketPrice() {
    const ticketCategory = document.getElementById("ticket_category");
    const quantity = document.getElementById("purchase_quantity");
    const priceDisplay = document.getElementById("ticket_price");

    if (!ticketCategory || !quantity || !priceDisplay) return;

    const selectedOption = ticketCategory.options[ticketCategory.selectedIndex];
    const price = parseFloat(selectedOption.dataset.price);
    const quantityValue = parseInt(quantity.value);

    const totalPrice = price * quantityValue;
    priceDisplay.textContent = "Rp" + totalPrice.toLocaleString("id-ID");

    // Update any other elements that need updating
    const stockInfo = document.getElementById("stock_quantity");
    if (stockInfo && selectedOption) {
        const purchased = parseInt(selectedOption.dataset.purchased) || 0;
        const total = parseInt(selectedOption.dataset.total) || 0;
        const remaining = total - purchased;

        stockInfo.textContent =
            purchased + "/" + total + ". Tersisa " + remaining + " tiket";

        // Update UI based on availability
        const buyButton = document.getElementById("buy_button");
        const purchaseInfo = document.getElementById("purchase_info");

        if (buyButton && purchaseInfo) {
            if (remaining <= 0) {
                buyButton.style.display = "none";
                purchaseInfo.innerText = "Tiket Habis Terjual!";
                purchaseInfo.className = "alert alert-danger";
            } else if (remaining < 5) {
                buyButton.style.display = "inline-block";
                purchaseInfo.innerText =
                    "Segera Habis! Tersisa " + remaining + " tiket";
                purchaseInfo.className = "alert alert-warning";
            } else {
                buyButton.style.display = "inline-block";
                purchaseInfo.innerText = "Maksimal pembelian 2 tiket per akun!";
                purchaseInfo.className = "alert alert-info";
            }
        }
    }
}

// Add to calendar functionality
function addToCalendar(title, startTime, endTime, location, description) {
    // Get the current date and add one hour to get the end time
    const start = new Date(startTime);
    const end = endTime
        ? new Date(endTime)
        : new Date(start.getTime() + 2 * 60 * 60 * 1000); // Default 2 hours duration

    // Format dates for Google Calendar
    const startFormatted = start.toISOString().replace(/-|:|\.\d+/g, "");
    const endFormatted = end.toISOString().replace(/-|:|\.\d+/g, "");

    // Build Google Calendar URL
    const googleCalendarUrl =
        "https://www.google.com/calendar/render?" +
        "action=TEMPLATE" +
        "&text=" +
        encodeURIComponent(title) +
        "&dates=" +
        startFormatted +
        "/" +
        endFormatted +
        "&details=" +
        encodeURIComponent(description || "") +
        "&location=" +
        encodeURIComponent(location || "") +
        "&sf=true&output=xml";

    // Open in a new tab
    window.open(googleCalendarUrl, "_blank");
}
