/**
 * Admin Panel JavaScript for Pundit FC
 */

document.addEventListener("DOMContentLoaded", function () {
    // Sidebar toggle functionality
    const sidebarToggle = document.getElementById("sidebar-toggle");
    const sidebarToggleBtn = document.getElementById("sidebar-toggle-btn");
    const sidebarCollapseToggle = document.getElementById(
        "sidebar-collapse-toggle"
    );
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.querySelector(".main-content");

    // Check for saved sidebar state
    const sidebarState = localStorage.getItem("sidebarState");
    if (sidebarState === "collapsed") {
        sidebar.classList.add("collapsed");
        if (sidebarCollapseToggle) {
            sidebarCollapseToggle
                .querySelector("i")
                .classList.remove("fa-chevron-left");
            sidebarCollapseToggle
                .querySelector("i")
                .classList.add("fa-chevron-right");
        }
    } // Mobile sidebar toggle
    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", function (e) {
            e.preventDefault();
            sidebar.classList.toggle("show");
            document.body.classList.toggle("sidebar-open");
        });
    }

    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener("click", function (e) {
            e.preventDefault();
            sidebar.classList.toggle("show");
            document.body.classList.remove("sidebar-open");
        });
    } // Desktop sidebar collapse toggle
    if (sidebarCollapseToggle) {
        sidebarCollapseToggle.addEventListener("click", function () {
            sidebar.classList.toggle("collapsed");
            const isCollapsed = sidebar.classList.contains("collapsed");

            // Change icon direction based on collapsed state
            const icon = sidebarCollapseToggle.querySelector("i");
            if (isCollapsed) {
                icon.classList.remove("fa-chevron-left");
                icon.classList.add("fa-chevron-right");
                localStorage.setItem("sidebarState", "collapsed");
            } else {
                icon.classList.remove("fa-chevron-right");
                icon.classList.add("fa-chevron-left");
                localStorage.setItem("sidebarState", "expanded");
            }
        });
    } // Desktop navbar toggle button sudah dihapus

    // Handle window resize for sidebar
    window.addEventListener("resize", function () {
        if (window.innerWidth > 768) {
            sidebar.classList.remove("show");
        }
    }); // Click outside sidebar to close on mobile
    document.addEventListener("click", function (event) {
        if (
            window.innerWidth < 768 &&
            sidebar.classList.contains("show") &&
            !sidebar.contains(event.target) &&
            event.target !== sidebarToggle &&
            !sidebarToggle.contains(event.target)
        ) {
            sidebar.classList.remove("show");
            document.body.classList.remove("sidebar-open");
        }
    });

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Active menu item highlight
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll(".menu-item");

    menuItems.forEach((item) => {
        const link = item.querySelector("a");
        if (link.getAttribute("href") === currentPath) {
            item.classList.add("active");
        }
    });

    // Toast notifications
    window.showToast = function (message, type = "success") {
        const toastEl = document.getElementById("adminToast");
        if (!toastEl) return;

        const toast = new bootstrap.Toast(toastEl);
        const toastBody = toastEl.querySelector(".toast-body");
        const toastHeader = toastEl.querySelector(".toast-header i");

        toastBody.innerHTML = message;

        // Update icon based on type
        if (type === "success") {
            toastHeader.className = "fas fa-check-circle text-success me-2";
        } else if (type === "error") {
            toastHeader.className =
                "fas fa-exclamation-circle text-danger me-2";
        } else if (type === "warning") {
            toastHeader.className =
                "fas fa-exclamation-triangle text-warning me-2";
        } else {
            toastHeader.className = "fas fa-info-circle text-info me-2";
        }

        toast.show();
    };

    // Handle confirmation dialogs
    const confirmationForms = document.querySelectorAll(".confirmation-form");

    confirmationForms.forEach((form) => {
        form.addEventListener("submit", function (event) {
            event.preventDefault();

            const confirmMessage =
                form.getAttribute("data-confirm") ||
                "Apakah Anda yakin ingin melakukan tindakan ini?";

            if (confirm(confirmMessage)) {
                form.submit();
            }
        });
    });

    // Ticket scanner functionality
    const scannerContainer = document.getElementById("qr-scanner-container");
    if (scannerContainer) {
        // QR Scanner is initialized in scan.blade.php
        console.log(
            "QR scanner container found, initialization is handled by scan.blade.php"
        );
    }

    // Function to log scan attempts
    function logScan(orderId, success, message = "") {
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");

        fetch("/admin/log-scan", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({
                order_id: orderId,
                success: success,
                message: message,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                console.log("Scan logged successfully");
            })
            .catch((error) => {
                console.error("Error logging scan:", error);
            });
    }

    // Function to display ticket information after successful scan
    function showTicketInfo(order) {
        const resultElement = document.getElementById("scan-result");

        if (resultElement) {
            let statusBadge = "";

            if (order.status === "completed") {
                statusBadge = '<span class="badge bg-success">Completed</span>';
            } else if (order.status === "used") {
                statusBadge =
                    '<span class="badge bg-danger">Already Used</span>';
            } else {
                statusBadge = `<span class="badge bg-info">${order.status}</span>`;
            }

            resultElement.innerHTML = `
                <div class="card admin-card animate-fade-in">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Ticket Valid</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Ticket ID:</p>
                                <h5>${order.id}</h5>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Status:</p>
                                <h5>${statusBadge}</h5>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">User:</p>
                                <h5>${order.user ? order.user.name : "N/A"}</h5>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Purchase Date:</p>
                                <h5>${new Date(
                                    order.created_at
                                ).toLocaleDateString()}</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Game:</p>
                                <h5>${
                                    order.ticket
                                        ? order.ticket.game.home_team +
                                          " vs " +
                                          order.ticket.game.away_team
                                        : "N/A"
                                }</h5>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Seat Category:</p>
                                <h5>${
                                    order.ticket
                                        ? "Category " + order.ticket.category
                                        : "N/A"
                                }</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <p class="mb-0 text-center text-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Ticket has been successfully verified
                        </p>
                    </div>
                </div>
            `;
        }
    }

    // Charts initialization
    initCharts();
});

// Initialize charts for dashboard
function initCharts() {
    // Sales Chart
    const salesChartEl = document.getElementById("salesChart");
    if (salesChartEl) {
        const salesChart = new Chart(salesChartEl, {
            type: "line",
            data: {
                labels: Array.from({ length: 7 }, (_, i) => {
                    const d = new Date();
                    d.setDate(d.getDate() - i);
                    return d.toLocaleDateString("id-ID", { weekday: "short" });
                }).reverse(),
                datasets: [
                    {
                        label: "Penjualan Tiket",
                        data: [65, 59, 80, 81, 56, 55, 40],
                        fill: true,
                        backgroundColor: "rgba(30, 81, 40, 0.1)",
                        borderColor: "#1E5128",
                        tension: 0.4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: "#1C1C1C",
                        titleColor: "#FFFFFF",
                        bodyColor: "#FFFFFF",
                        padding: 12,
                        displayColors: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "rgba(0, 0, 0, 0.05)",
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                    },
                },
            },
        });
    }

    // Revenue Distribution Chart
    const revenueChartEl = document.getElementById("revenueChart");
    if (revenueChartEl) {
        const revenueChart = new Chart(revenueChartEl, {
            type: "doughnut",
            data: {
                labels: ["Category 1", "Category 2", "Category 3"],
                datasets: [
                    {
                        data: [300, 200, 100],
                        backgroundColor: ["#1E5128", "#4E9F3D", "#D8E9A8"],
                        hoverBackgroundColor: ["#183D20", "#3A7A2F", "#C8D998"],
                        borderWidth: 0,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "75%",
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            padding: 20,
                            color: "#333333",
                        },
                    },
                    tooltip: {
                        backgroundColor: "#1C1C1C",
                        titleColor: "#FFFFFF",
                        bodyColor: "#FFFFFF",
                        padding: 12,
                        usePointStyle: true,
                    },
                },
            },
        });
    }

    // Attendance Chart
    const attendanceChartEl = document.getElementById("attendanceChart");
    if (attendanceChartEl) {
        const attendanceChart = new Chart(attendanceChartEl, {
            type: "bar",
            data: {
                labels: ["Game 1", "Game 2", "Game 3", "Game 4", "Game 5"],
                datasets: [
                    {
                        label: "Attendance",
                        data: [85, 70, 95, 65, 80],
                        backgroundColor: "#4E9F3D",
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: "#1C1C1C",
                        titleColor: "#FFFFFF",
                        bodyColor: "#FFFFFF",
                        padding: 12,
                        displayColors: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function (value) {
                                return value + "%";
                            },
                        },
                        grid: {
                            color: "rgba(0, 0, 0, 0.05)",
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                    },
                },
            },
        });
    }
}
