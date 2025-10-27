<?php include "./globals/head.php"; ?>

<head>
    <style>
        :root {
            --primary: rgb(174, 14, 14);
            --primary-light: rgb(220, 60, 60);
            --accent: #ff904c;
            --background: #fff7f7;
            --text: #222;
            --border-radius: 12px;
            --box-shadow: 0 2px 12px rgba(174, 14, 14, 0.08);
            --transition: 0.3s cubic-bezier(.25, .8, .25, 1);
        }

        body {
            background: var(--background);
            color: var(--text);
            font-family: 'Poppins', sans-serif;
        }

        .card {
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            background: #fff;
        }

        h3,
        h5 {
            color: var(--primary);
            font-weight: bold;
            text-transform: uppercase;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            border-radius: var(--border-radius);
            border: none;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background: var(--primary-light);
        }

        #main {
            transition: margin-left 0.3s ease, width 0.3s ease;
            margin-left: 250px;
            width: calc(100% - 250px);
        }

        .sidebar.collapsed+#main {
            margin-left: 80px;
            width: calc(100% - 80px);
        }

        @media (max-width: 991px) {
            #main {
                margin-left: 0;
                width: 100%;
            }
        }

        canvas {
            height: 250px !important;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .chart-header button {
            font-size: 0.85rem;
            padding: 4px 10px;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include "./globals/navbar.php"; ?>

    <div id="main" class="container-fluid py-4 mt-5">
        <!-- Top Mileage Metrics -->
        <div class="row mb-4">
            <div class="col-md-3 mb-2">
                <div class="card text-center p-3 shadow-sm h-100">
                    <h6>Total Mileage</h6>
                    <span id="totalMileage" class="fs-4 text-primary">0 km</span>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card text-center p-3 shadow-sm h-100">
                    <h6>Average per Hour</h6>
                    <span id="avgMileage" class="fs-4 text-primary">0 km</span>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card text-center p-3 shadow-sm h-100">
                    <h6>Data Points</h6>
                    <span id="tripsWeek" class="fs-4 text-primary">0</span>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card text-center p-3 shadow-sm h-100">
                    <h6>Max Reading</h6>
                    <span id="maxTrip" class="fs-4 text-primary">0 km</span>
                </div>
            </div>
        </div>

        <!-- Date Selector -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="datePicker" class="form-label fw-bold text-primary">Select Date:</label>
                <input type="date" id="datePicker" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>

        <!-- Mileage Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm p-3">
                    <div class="chart-header">
                        <h5 id="chartTitle" class="text-primary mb-0">Mileage per Hour (km)</h5>
                        <button class="btn btn-outline-primary btn-sm" id="toggleChartType">Switch to Line</button>
                    </div>
                    <canvas id="mileageChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <?php include "./globals/scripts.php"; ?>

    <script>
        const ctx = document.getElementById('mileageChart').getContext('2d');
        let mileageChart = null;
        let chartType = 'bar';

        function createChart(type, labels, data) {
            return new Chart(ctx, {
                type: type,
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Mileage (km)',
                        data: data,
                        backgroundColor: 'rgba(174,14,14,0.6)',
                        borderColor: 'rgb(174,14,14)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, max: 100 } }
                }
            });
        }

        function updateDashboard(data) {
            const total = data.mileage.reduce((a, b) => a + b, 0);
            const avg = total / data.mileage.length;
            const max = Math.max(...data.mileage);
            const trips = data.mileage.length;

            $('#totalMileage').text(`${total.toFixed(1)} km`);
            $('#avgMileage').text(`${avg.toFixed(1)} km`);
            $('#maxTrip').text(`${max.toFixed(1)} km`);
            $('#tripsWeek').text(trips);

            if (mileageChart) mileageChart.destroy();
            mileageChart = createChart(chartType, data.labels, data.mileage);
        }

        function loadMileageData(selectedDate) {
            $('#chartTitle').text(`Mileage Readings for ${selectedDate}`);

            $.ajax({
                url: `../api/user_fetch_mileage.php`,
                method: "GET",
                data: { start: selectedDate, end: selectedDate },
                dataType: "json",
                success: function (response) {
                    updateDashboard(response);
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }

        $('#toggleChartType').on('click', function () {
            chartType = chartType === 'bar' ? 'line' : 'bar';
            $(this).text(chartType === 'bar' ? 'Switch to Line' : 'Switch to Bar');
            loadMileageData($('#datePicker').val());
        });

        $('#datePicker').on('change', function () {
            loadMileageData($(this).val());
        });

        $(document).ready(() => {
            loadMileageData($('#datePicker').val());
        });
    </script>
</body>
</html>
