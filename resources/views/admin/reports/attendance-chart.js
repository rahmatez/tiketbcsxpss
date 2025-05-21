document.addEventListener("DOMContentLoaded", function () {
    try {
        // Attendance by time chart
        let timeLabels = [];
        let timeData = [];

        try {
            timeLabels = JSON.parse(
                document.getElementById("chart-time-labels").textContent
            );
            timeData = JSON.parse(
                document.getElementById("chart-time-data").textContent
            );
        } catch (e) {
            console.error("Error parsing time data", e);
            timeLabels = Array.from(
                { length: 24 },
                (_, i) => `${i.toString().padStart(2, "0")}:00`
            );
            timeData = Array(24).fill(0);
        }

        const timeCtx = document.getElementById("timeChart").getContext("2d");
        new Chart(timeCtx, {
            type: "bar",
            data: {
                labels: timeLabels,
                datasets: [
                    {
                        label: "Jumlah Kehadiran",
                        data: timeData,
                        backgroundColor: "rgba(78, 159, 61, 0.7)",
                        borderColor: "#4E9F3D",
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        });

        // Attendance by category chart
        let categoryData = [];
        let categories = [];
        let categoryCounts = [];

        try {
            categoryData = JSON.parse(
                document.getElementById("chart-category-data").textContent
            );
            categories = categoryData.map(
                (item) => "Kategori " + item.category
            );
            categoryCounts = categoryData.map((item) => item.scan_count);
        } catch (e) {
            console.error("Error parsing category data", e);
            categories = ["VIP", "Tribune", "Regular"];
            categoryCounts = [30, 20, 50];
        }

        if (categories.length === 0) {
            categories = ["VIP", "Tribune", "Regular"];
            categoryCounts = [30, 20, 50];
        }

        const categoryCtx = document
            .getElementById("categoryChart")
            .getContext("2d");
        new Chart(categoryCtx, {
            type: "doughnut",
            data: {
                labels: categories,
                datasets: [
                    {
                        data: categoryCounts,
                        backgroundColor: [
                            "#4E9F3D",
                            "#1E5128",
                            "#D8E9A8",
                            "#3498DB",
                            "#9B59B6",
                        ],
                        borderColor: "#FFFFFF",
                        borderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: "bottom",
                    },
                },
            },
        });
    } catch (e) {
        console.error("Error initializing charts", e);
    }
});
