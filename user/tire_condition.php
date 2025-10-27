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
			height: 100%;
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
			height: 240px !important;
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
		<!-- Top Tire Summary -->
		<div class="row mb-4">
			<div class="col-md-4 mb-2">
				<div class="card text-center p-3 shadow-sm">
					<h6>Average Pressure</h6>
					<span id="avgPressure" class="fs-4 text-primary">-- PSI</span>
				</div>
			</div>
			<div class="col-md-4 mb-2">
				<div class="card text-center p-3 shadow-sm">
					<h6>Status</h6>
					<span id="status" class="fs-4 text-success">Normal</span>
				</div>
			</div>
			<div class="col-md-4 mb-2">
				<div class="card text-center p-3 shadow-sm">
					<h6>Total Checks</h6>
					<span id="checkCount" class="fs-4 text-primary">--</span>
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

		<!-- Tire Charts -->
		<div class="row g-4">
			<div class="col-md-4">
				<div class="card shadow-sm p-3">
					<div class="chart-header">
						<h5 class="text-primary mb-0">Rear Tire Pressure</h5>
						<button class="btn btn-outline-primary btn-sm toggle-type" data-chart="rear">Switch to Line</button>
					</div>
					<canvas id="rearChart"></canvas>
				</div>
			</div>
			<div class="col-md-4">
				<div class="card shadow-sm p-3">
					<div class="chart-header">
						<h5 class="text-primary mb-0">Side Tire Pressure</h5>
						<button class="btn btn-outline-primary btn-sm toggle-type" data-chart="side">Switch to Line</button>
					</div>
					<canvas id="sideChart"></canvas>
				</div>
			</div>
			<div class="col-md-4">
				<div class="card shadow-sm p-3">
					<div class="chart-header">
						<h5 class="text-primary mb-0">Front Tire Pressure</h5>
						<button class="btn btn-outline-primary btn-sm toggle-type" data-chart="front">Switch to Line</button>
					</div>
					<canvas id="frontChart"></canvas>
				</div>
			</div>
		</div>
	</div>

	<?php include "./globals/scripts.php"; ?>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		let chartTypes = { rear: 'bar', side: 'bar', front: 'bar' };
		let charts = {};

		function getStatus(p) {
			if (p >= 32 && p <= 36) return 'Normal';
			if ((p >= 28 && p < 32) || (p > 36 && p <= 38)) return 'Warning';
			return 'Critical';
		}

		function getColorClass(status) {
			if (status === 'Normal') return 'text-success';
			if (status === 'Warning') return 'text-warning';
			return 'text-danger';
		}

		function createChart(canvasId, type, labels, data) {
			const ctx = document.getElementById(canvasId).getContext('2d');
			if (charts[canvasId]) charts[canvasId].destroy();

			charts[canvasId] = new Chart(ctx, {
				type: type,
				data: {
					labels: labels,
					datasets: [{
						label: 'Pressure (PSI)',
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
					scales: {
						y: {
							min: 28,
							max: 38
						}
					}
				}
			});
		}

		function updateDashboard(avgRear, avgSide, avgFront, count) {
			const avgAll = ((avgRear + avgSide + avgFront) / 3).toFixed(2);
			const status = getStatus(avgAll);

			$('#avgPressure').text(avgAll + ' PSI');
			$('#status').text(status)
				.removeClass('text-success text-warning text-danger')
				.addClass(getColorClass(status));
			$('#checkCount').text(count);
		}

		function loadTireData(selectedDate) {
			$.ajax({
				url: '../api/user_fetch_tire_condition.php',
				method: 'GET',
				data: { date: selectedDate },
				dataType: 'json',
				success: function(data) {
					const labels = data.labels || [];
					const rear = data.rear || [];
					const side = data.side || [];
					const front = data.front || [];

					createChart('rearChart', chartTypes.rear, labels, rear);
					createChart('sideChart', chartTypes.side, labels, side);
					createChart('frontChart', chartTypes.front, labels, front);

					const avgRear = rear.length ? rear.reduce((a, b) => a + b, 0) / rear.length : 0;
					const avgSide = side.length ? side.reduce((a, b) => a + b, 0) / side.length : 0;
					const avgFront = front.length ? front.reduce((a, b) => a + b, 0) / front.length : 0;

					updateDashboard(avgRear, avgSide, avgFront, labels.length);
				},
				error: function(xhr, status, error) {
					console.error('Error loading tire data:', error);
				}
			});
		}

		// --- Toggle individual chart type ---
		$('.toggle-type').on('click', function() {
			const chartKey = $(this).data('chart');
			chartTypes[chartKey] = chartTypes[chartKey] === 'bar' ? 'line' : 'bar';
			$(this).text(chartTypes[chartKey] === 'bar' ? 'Switch to Line' : 'Switch to Bar');
			loadTireData($('#datePicker').val());
		});

		$('#datePicker').on('change', function() {
			loadTireData($(this).val());
		});

		// Initial Load
		loadTireData($('#datePicker').val());
	</script>
</body>

</html>
