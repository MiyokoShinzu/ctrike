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
			height: 100%;
		}

		h3, h5 {
			color: var(--primary);
			font-weight: bold;
			text-transform: uppercase;
		}

		#main {
			transition: margin-left 0.3s ease, width 0.3s ease;
			margin-left: 250px;
			width: calc(100% - 250px);
		}

		.sidebar.collapsed + #main {
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

		<!-- Top Battery Metrics -->
		<div class="row mb-4">
			<div class="col-md-3 mb-2">
				<div class="card text-center p-3 shadow-sm h-100">
					<h6>Average Voltage</h6>
					<span id="avgVoltage" class="fs-4">-- V</span>
				</div>
			</div>
			<div class="col-md-3 mb-2">
				<div class="card text-center p-3 shadow-sm h-100">
					<h6>Max Voltage</h6>
					<span id="maxVoltage" class="fs-4">-- V</span>
				</div>
			</div>
			<div class="col-md-3 mb-2">
				<div class="card text-center p-3 shadow-sm h-100">
					<h6>Min Voltage</h6>
					<span id="minVoltage" class="fs-4">-- V</span>
				</div>
			</div>
			<div class="col-md-3 mb-2">
				<div class="card text-center p-3 shadow-sm h-100">
					<h6>Status</h6>
					<span id="batteryStatus" class="fs-4">--</span>
				</div>
			</div>
		</div>

		<!-- Date Selector -->
		<div class="row mb-3">
			<div class="col-md-3">
				<label for="datePicker" class="form-label fw-bold text-primary">Select Date:</label>
				<input type="date" id="datePicker" class="form-control" value="<?= date('Y-m-d') ?>">
			</div>
		</div>

		<!-- Voltage Graph -->
		<div class="row mb-4">
			<div class="col-12">
				<div class="card shadow-sm p-3">
					<div class="chart-header">
						<h5 id="chartTitle" class="text-primary mb-0">Battery Voltage Readings (V)</h5>
						<button class="btn btn-outline-primary btn-sm" id="toggleChartType">Switch to Line</button>
					</div>
					<canvas id="voltageChart"></canvas>
				</div>
			</div>
		</div>
	</div>

	<?php include "./globals/scripts.php"; ?>

	<script>
		let chartType = 'bar';
		let batteryData = [];
		const ctx = document.getElementById('voltageChart').getContext('2d');
		let voltageChart = createChart(chartType);

		function createChart(type) {
			return new Chart(ctx, {
				type,
				data: {
					labels: [],
					datasets: [{
						label: 'Voltage (V)',
						data: [],
						backgroundColor: 'rgba(174,14,14,0.6)',
						borderColor: 'rgb(174,14,14)',
						fill: true,
						tension: 0.3
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					scales: {
						y: {
							beginAtZero: false,
							min: 30,
							max: 72
						}
					}
				}
			});
		}

		function getStatus(voltage) {
			if (voltage >= 60) return 'Normal';
			if (voltage >= 45) return 'Warning';
			return 'Critical';
		}

		function updateMetrics() {
			const voltages = batteryData.map(d => d.voltage);
			if (!voltages.length) return;

			const avg = voltages.reduce((a, b) => a + b, 0) / voltages.length;
			const max = Math.max(...voltages);
			const min = Math.min(...voltages);

			$('#avgVoltage').text(avg.toFixed(2) + ' V');
			$('#maxVoltage').text(max.toFixed(2) + ' V');
			$('#minVoltage').text(min.toFixed(2) + ' V');

			const status = getStatus(avg);
			const elem = $('#batteryStatus');
			elem.text(status)
				.removeClass('text-success text-warning text-danger')
				.addClass(status === 'Normal' ? 'text-success' :
						  status === 'Warning' ? 'text-warning' : 'text-danger');
		}

		function updateChart() {
			voltageChart.data.labels = batteryData.map(d => d.time);
			voltageChart.data.datasets[0].data = batteryData.map(d => d.voltage);
			voltageChart.update();
		}

		function loadBatteryData(date) {
			$('#chartTitle').text(`Battery Voltage Readings (V) — ${date}`);
			$.getJSON(`../api/user_fetch_battery_monitor.php?date=${date}`, function (data) {
				if (data.error) {
					console.error(data.error);
					return;
				}

				batteryData = data.labels.map((time, i) => ({
					time,
					voltage: data.battery[i] ?? 0
				}));

				updateMetrics();
				updateChart();
			}).fail(function (xhr, status, err) {
				console.error('AJAX error:', status, err);
			});
		}

		$('#toggleChartType').on('click', function () {
			chartType = chartType === 'bar' ? 'line' : 'bar';
			voltageChart.destroy();
			voltageChart = createChart(chartType);
			updateChart();
			$(this).text(chartType === 'bar' ? 'Switch to Line' : 'Switch to Bar');
		});

		$('#datePicker').on('change', function () {
			loadBatteryData($(this).val());
		});

		$(document).ready(function () {
			loadBatteryData($('#datePicker').val());
		});
	</script>
</body>
</html>
