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

		<!-- Date Selector -->
		<div class="row mb-3">
			<div class="col-md-3">
				<label for="datePicker" class="form-label fw-bold text-primary">Select Date:</label>
				<input type="date" id="datePicker" class="form-control" value="<?= date('Y-m-d') ?>">
			</div>
		</div>

		<!-- Vibration Chart -->
		<div class="row mb-4">
			<div class="col-12">
				<div class="card shadow-sm p-3">
					<div class="chart-header">
						<h5 id="chartTitle" class="text-primary mb-0">Motor Vibration (Hz) — <?= date('Y-m-d') ?></h5>
						<button class="btn btn-outline-primary btn-sm" id="toggleChartType">Switch to Line</button>
					</div>
					<canvas id="vibrationChart"></canvas>
				</div>
			</div>
		</div>
	</div>

	<?php include "./globals/scripts.php"; ?>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		$(document).ready(function() {
			let chartType = 'bar';
			let vibrationChart = null;
			const ctx = document.getElementById('vibrationChart').getContext('2d');

			function getStatus(value) {
				if (value <= 40) return 'Normal';
				if (value <= 70) return 'Moderate';
				return 'Warning';
			}

			function getColorClass(status) {
				if (status === 'Normal') return 'text-success';
				if (status === 'Moderate') return 'text-warning';
				return 'text-danger';
			}

			function createChart(type, labels, data) {
				return new Chart(ctx, {
					type: type,
					data: {
						labels: labels,
						datasets: [{
							label: 'Vibration (Hz) [Min: 25, Max: 100]',
							data: data,
							backgroundColor: 'rgba(14,14,174,0.6)',
							borderColor: 'rgb(14,14,174)',
							fill: true,
							tension: 0.3
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						scales: {
							y: {
								min: 25,
								max: 100
							}
						}
					}
				});
			}

			function updateDashboard(data) {
				const timeLabels = data.labels || [];
				const vibrationData = data.vibration || [];

				const avg = vibrationData.length ? vibrationData.reduce((a, b) => a + b, 0) / vibrationData.length : 0;
				const max = vibrationData.length ? Math.max(...vibrationData) : 0;
				const status = getStatus(avg);

				$('#avgVibration').text(avg.toFixed(2) + ' Hz');
				$('#maxVibration').text(max.toFixed(2) + ' Hz');
				$('#status')
					.text(status)
					.removeClass('text-success text-warning text-danger')
					.addClass(getColorClass(status));

				if (vibrationChart) vibrationChart.destroy();
				vibrationChart = createChart(chartType, timeLabels, vibrationData);
			}

			function loadDailyData(selectedDate) {
				$('#chartTitle').text(`Motor Vibration (Hz) — ${selectedDate}`);

				$.ajax({
					url: '../api/user_fetch_motor_vibration.php',
					method: 'GET',
					data: { date: selectedDate },
					dataType: 'json',
					success: updateDashboard,
					error: function(xhr, status, error) {
						console.error('Error loading vibration data:', error);
					}
				});
			}

			$('#toggleChartType').on('click', function() {
				chartType = chartType === 'bar' ? 'line' : 'bar';
				$(this).text(chartType === 'bar' ? 'Switch to Line' : 'Switch to Bar');
				loadDailyData($('#datePicker').val());
			});

			$('#datePicker').on('change', function() {
				loadDailyData($(this).val());
			});

			// Initial load
			loadDailyData($('#datePicker').val());
		});
	</script>
</body>

</html>
