<?php include "./globals/head.php"; ?>

<head>
    <style>
        :root {
            --primary: rgb(174, 14, 14);
            --primary-light: rgb(220, 60, 60);
            --background: #fff7f7;
            --text: #222;
            --border-radius: 12px;
            --box-shadow: 0 2px 12px rgba(174, 14, 14, 0.08);
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

        h5 {
            color: var(--primary);
            font-weight: bold;
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
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include "./globals/navbar.php"; ?>

    <div id="main" class="container-fluid py-4 mt-5">
        <!-- Top Vibration Metrics -->
        <div class="row mb-4">
            <div class="col-md-4 mb-2">
                <div class="card text-center p-3 shadow-sm">
                    <h6>Average Vibration</h6>
                    <span id="avgVibration" class="fs-4">-- Hz</span>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card text-center p-3 shadow-sm">
                    <h6>Max Vibration</h6>
                    <span id="maxVibration" class="fs-4">-- Hz</span>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card text-center p-3 shadow-sm">
                    <h6>Status</h6>
                    <span id="status" class="fs-4 text-success">Normal</span>
                </div>
            </div>
        </div>

        <!-- Week Selector -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="weekPicker" class="form-label fw-bold text-primary">Select Week:</label>
                <input type="week" id="weekPicker" class="form-control" value="2025-W43">
            </div>
        </div>

        <!-- Vibration Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm p-3">
                    <div class="chart-header">
                        <h5 id="chartTitle" class="text-primary mb-0">Motor Vibration per Day (Hz)</h5>
                        <button class="btn btn-outline-primary btn-sm" id="toggleChartType">Switch to Line</button>
                    </div>
                    <canvas id="vibrationChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <?php include "./globals/scripts.php"; ?>


</body>

</html>