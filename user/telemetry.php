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

        .card-metric {
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .metric-label {
            font-weight: 600;
            color: var(--primary);
        }

        .metric-value {
            font-size: 1.4rem;
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

        .toggle-btn {
            float: right;
            font-size: 0.85rem;
            padding: 3px 8px;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php include "./globals/navbar.php"; ?>

    <div id="main" class="container-fluid py-4 mt-5">
        <!-- Metric Cards -->
        <div class="row mb-4">
            <div class="col-md-2 mb-2">
                <div class="card card-metric text-center p-3">
                    <h6 class="metric-label">Battery</h6>
                    <span id="batteryMetric" class="metric-value">--</span>
                </div>
            </div>
            <div class="col-md-2 mb-2">
                <div class="card card-metric text-center p-3">
                    <h6 class="metric-label">Vibration</h6>
                    <span id="vibrationMetric" class="metric-value">--</span>
                </div>
            </div>
            <div class="col-md-2 mb-2">
                <div class="card card-metric text-center p-3">
                    <h6 class="metric-label">Temperature</h6>
                    <span id="temperatureMetric" class="metric-value">--</span>
                </div>
            </div>
            <div class="col-md-2 mb-2">
                <div class="card card-metric text-center p-3">
                    <h6 class="metric-label">Mileage</h6>
                    <span id="mileageMetric" class="metric-value">--</span>
                </div>
            </div>
            <div class="col-md-2 mb-2">
                <div class="card card-metric text-center p-3">
                    <h6 class="metric-label">Tire</h6>
                    <span id="tireMetric" class="metric-value">--</span>
                </div>
            </div>

            <!-- 🔧 New Card for Installation Setup -->
            <div class="col-md-2 mb-2">
                <div class="card card-metric text-center p-3">
                    <h6 class="metric-label">Setup</h6>
                    <button class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#setupModal">
                        Configure
                    </button>
                </div>
            </div>
        </div>

        <!-- Date Picker -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="datePicker" class="form-label fw-bold text-primary">Select Date:</label>
                <input type="date" id="datePicker" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
        </div>

        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm p-3">
                    <h5 class="text-primary mb-3">
                        Battery Voltage (V)
                        <button class="btn btn-outline-primary btn-sm toggle-btn" id="toggleBattery">Bar</button>
                    </h5>
                    <canvas id="batteryChart"></canvas>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm p-3">
                    <h5 class="text-primary mb-3">
                        Motor Vibration (Hz)
                        <button class="btn btn-outline-primary btn-sm toggle-btn" id="toggleVibration">Bar</button>
                    </h5>
                    <canvas id="vibrationChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 🧩 Setup Modal -->
    <div class="modal fade" id="setupModal" tabindex="-1" aria-labelledby="setupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="setupModalLabel">Component Installation Setup</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="setupForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Motor Installation Date</label>
                                <input type="date" name="motor_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Motor Condition</label>
                                <select name="motor_condition" class="form-select">
                                    <option value="brand_new">Brand New</option>
                                    <option value="second_hand">Second Hand</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Battery Installation Date</label>
                                <input type="date" name="battery_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Battery Condition</label>
                                <select name="battery_condition" class="form-select">
                                    <option value="brand_new">Brand New</option>
                                    <option value="second_hand">Second Hand</option>
                                </select>
                            </div>

                            <h6 class="mt-4 text-primary fw-bold">Tires</h6>

                            <!-- Rear Tire -->
                            <div class="col-md-6">
                                <label class="form-label">Rear Tire Installation Date</label>
                                <input type="date" name="rear_tire_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Rear Tire Condition</label>
                                <select name="rear_tire_condition" class="form-select">
                                    <option value="brand_new">Brand New</option>
                                    <option value="second_hand">Second Hand</option>
                                </select>
                            </div>

                            <!-- Side Tire -->
                            <div class="col-md-6">
                                <label class="form-label">Side Tire Installation Date</label>
                                <input type="date" name="side_tire_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Side Tire Condition</label>
                                <select name="side_tire_condition" class="form-select">
                                    <option value="brand_new">Brand New</option>
                                    <option value="second_hand">Second Hand</option>
                                </select>
                            </div>

                            <!-- Front Tire -->
                            <div class="col-md-6">
                                <label class="form-label">Front Tire Installation Date</label>
                                <input type="date" name="front_tire_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Front Tire Condition</label>
                                <select name="front_tire_condition" class="form-select">
                                    <option value="brand_new">Brand New</option>
                                    <option value="second_hand">Second Hand</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveSetup">Save</button>
                </div>
            </div>
        </div>
    </div>

    <?php include "./globals/scripts.php"; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        let charts = {};

        function loadTelemetryData() {
            const selectedDate = $('#datePicker').val();

            $.ajax({
                url: '../api/user_fetch_telemetry_data.php',
                method: 'GET',
                data: {
                    start: selectedDate,
                    end: selectedDate
                },
                dataType: 'json',
                success: function(data) {
                    renderAllCharts(data);
                    updateMetrics(data);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching telemetry:', error);
                }
            });
        }

        function renderAllCharts(data) {
            const configs = [{
                    key: 'battery',
                    label: 'Battery Voltage (V)',
                    color: 'rgb(174,14,14)',
                    unit: 'V',
                    min: 30,
                    max: 72
                },
                {
                    key: 'vibration',
                    label: 'Motor Vibration (G)',
                    color: 'rgb(14,14,174)',
                    unit: 'G',
                    min: 25,
                    max: 100
                },
            ];

            configs.forEach(cfg => {
                const ctx = document.getElementById(`${cfg.key}Chart`).getContext('2d');
                const btn = $(`#toggle${cfg.key.charAt(0).toUpperCase() + cfg.key.slice(1)}`);

                if (charts[cfg.key]) charts[cfg.key].destroy();

                charts[cfg.key] = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: cfg.label,
                            data: data[cfg.key],
                            backgroundColor: cfg.color.replace('rgb', 'rgba').replace(')', ',0.6)'),
                            borderColor: cfg.color,
                            borderWidth: 2,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                min: cfg.min,
                                max: cfg.max,
                                beginAtZero: false
                            }
                        }
                    }
                });

                btn.off('click').on('click', function() {
                    const chart = charts[cfg.key];
                    chart.config.type = chart.config.type === 'bar' ? 'line' : 'bar';
                    chart.update();
                    $(this).text(chart.config.type === 'bar' ? 'Bar' : 'Line');
                });
            });
        }

        function updateMetrics(data) {
            $('#batteryMetric').text(data.battery?.at(-1) ? `${data.battery.at(-1).toFixed(2)} V` : '--');
            $('#vibrationMetric').text(data.vibration?.at(-1) ? `${data.vibration.at(-1).toFixed(2)} Hz` : '--');
            $('#temperatureMetric').text(data.temperature?.at(-1) ? `${data.temperature.at(-1).toFixed(2)} °C` : '--');
            $('#mileageMetric').text(data.speed?.at(-1) ? `${data.speed.at(-1).toFixed(2)} km/h` : '--');
            $('#tireMetric').text(data.tire?.at(-1) ? `${data.tire.at(-1).toFixed(2)} PSI` : '--');
        }

        // Save setup modal
        $('#saveSetup').click(function() {
            const formData = $('#setupForm').serialize();
            $.post('../api/save_installation_setup.php', formData, function(response) {
                alert('Setup saved successfully!');
                $('#setupModal').modal('hide');
            }).fail(function() {
                alert('Error saving setup.');
            });
        });

        $(document).ready(function() {
            loadTelemetryData();
            $('#datePicker').on('change', loadTelemetryData);
        });
    </script>
</body>

</html>