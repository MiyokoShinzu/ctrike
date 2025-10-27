<?php include "./globals/head.php"; ?>

	<style>
		body {
			background-color: #f8f9fa;
			overflow-x: hidden;
			margin: 0;
			padding: 0;
			transition: all 0.3s ease;
		}

		:root {
			--sidebar-width: 240px;
			--sidebar-collapsed-width: 80px;
		}

		#main {
			margin-left: var(--sidebar-width);
			padding: 20px;
			transition: all 0.3s ease;
			width: calc(100% - var(--sidebar-width));
		}

		body.sidebar-collapsed #main {
			margin-left: var(--sidebar-collapsed-width);
			width: calc(100% - var(--sidebar-collapsed-width));
		}

		@media (max-width: 992px) {
			#main {
				margin-left: 0;
				width: 100%;
				padding: 15px;
			}
		}

		.alert-box {
			background: #fff;
			border-radius: 8px;
			box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
			max-height: 70vh;
			overflow-y: auto;
			padding: 15px;
		}

		.alert-item {
			border-left: 4px solid #dc3545;
			background: #ffe6e6;
			padding: 10px;
			border-radius: 6px;
			margin-bottom: 10px;
		}

		.card-small {
			padding: 10px;
			border-radius: 8px;
			font-size: 0.9rem;
		}

		.card-small h6 {
			font-size: 0.85rem;
			margin-bottom: 4px;
		}

		.card-small h4,
		.card-small h5 {
			font-size: 1.1rem;
			margin: 0;
		}

		.card-small p {
			margin: 0;
			font-size: 0.8rem;
		}

		.text-secondary {
			text-transform: uppercase;
			letter-spacing: 0.05rem;
			font-weight: 600;
		}

		@media (min-width: 992px) {
			#alertSidebar {
				position: sticky;
				top: 20px;
			}
		}
	</style>


<body>
	<?php include "./globals/navbar.php"; ?>

	<div id="main" class="container-fluid">
		<div class="row">
			<!-- LEFT SIDEBAR ALERTS -->
			<div id="alertSidebar" class="col-12 col-lg-3 mb-4 mb-lg-0">
				<h5 class="mb-3 fw-bold">Notifiable Alerts</h5>
				<div class="alert-box">
					<?php
					include "../src/connection.php";

					// Fetch components
					$comp_sql = "SELECT component_name, datetime_change, status FROM components_changes";
					$comp_result = $mysqli->query($comp_sql);

					$now = new DateTime();
					$alerts = [];

					while ($row = $comp_result->fetch_assoc()) {
						$install_date = new DateTime($row['datetime_change']);
						$diff = $install_date->diff($now);

						$years = $diff->y;
						$name = strtolower($row['component_name']);

						if (str_contains($name, 'battery') && $years >= 5) {
							$alerts[] = "Battery has reached {$years} years — consider replacement.";
						}

						if (str_contains($name, 'tire') && $years >= 3) {
							$alerts[] = "Front tire has reached {$years} years — inspection or replacement recommended.";
						}
					}

					if (count($alerts) > 0) {
						foreach ($alerts as $alert) {
							echo "<div class='alert-item p-2' style='font-size: 0.6em;'><strong>Alert:</strong> $alert <br><p class='badge mt-3 bg-secondary mx-auto p-1'>Message Sent</p></div>";
						}
					} else {
						echo "<p class='text-muted mb-0'>No alerts yet.</p>";
					}
					?>
					<div class='alert p-2 alert-warning' style='font-size: 0.6em;'><strong>Alert:</strong> Front tire is in low pressure for 3 days. Consider inspection or replacement. <br><p class="badge mt-3 bg-secondary mx-auto p-1">Message Sent</p></div>
				</div>
			</div>

			<!-- RIGHT MAIN CONTENT -->
			<div class="col-12 col-lg-9 bg-white border shadow">
				<?php
				// Determine selected range
				$range = isset($_GET['range']) ? $_GET['range'] : 'latest_10_days';

				switch ($range) {
					case 'latest_2_days':
						$date_condition = "WHERE datetime_received >= DATE_SUB(NOW(), INTERVAL 2 DAY)";
						$label = "Latest 2 Days";
						break;
					case 'latest_3_days':
						$date_condition = "WHERE datetime_received >= DATE_SUB(NOW(), INTERVAL 3 DAY)";
						$label = "Latest 3 Days";
						break;
					case 'latest_month':
						$date_condition = "WHERE datetime_received >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
						$label = "Latest Month";
						break;
					default:
						$date_condition = "WHERE datetime_received >= DATE_SUB(NOW(), INTERVAL 10 DAY)";
						$label = "Latest 10 Days";
						break;
				}

				// Fetch telemetry averages
				$sql = "
                    SELECT 
                        ROUND(AVG(rear_tire_pressure), 2) AS avg_rear_tire_pressure,
                        ROUND(AVG(side_tire_pressure), 2) AS avg_side_tire_pressure,
                        ROUND(AVG(front_tire_pressure), 2) AS avg_front_tire_pressure,
                        ROUND(AVG(voltage), 2) AS avg_voltage,
                        ROUND(AVG(temperature), 2) AS avg_temperature,
                        ROUND(AVG(distance), 2) AS avg_distance,
                        ROUND(AVG(vibration), 2) AS avg_vibration
                    FROM telemetry_data
                    $date_condition
                ";
				$result = $mysqli->query($sql);
				$averages = $result->fetch_assoc();

				// Fetch components again for display
				$components_sql = "SELECT component_name, datetime_change, status FROM components_changes";
				$components = $mysqli->query($components_sql);
				?>

				<!-- TELEMETRY AVERAGES -->
				<div class="container-fluid mt-4">
					<div class="d-flex justify-content-between align-items-center mb-3">
						<h5 class="fw-bold">Telemetry Averages (<?= $label ?>)</h5>

						<form method="GET" class="d-flex">
							<select name="range" class="form-select form-select-sm" onchange="this.form.submit()">
								<option value="latest_2_days" <?= $range === 'latest_2_days' ? 'selected' : '' ?>>Latest 2 Days</option>
								<option value="latest_3_days" <?= $range === 'latest_3_days' ? 'selected' : '' ?>>Latest 3 Days</option>
								<option value="latest_10_days" <?= $range === 'latest_10_days' ? 'selected' : '' ?>>Latest 10 Days</option>
								<option value="latest_month" <?= $range === 'latest_month' ? 'selected' : '' ?>>Latest Month</option>
							</select>
						</form>
					</div>

					<div class="row g-3">
						<?php
						$metrics = [
							"Rear Tire Pressure" => [$averages['avg_rear_tire_pressure'], "PSI"],
							"Side Tire Pressure" => [$averages['avg_side_tire_pressure'], "PSI"],
							"Front Tire Pressure" => [$averages['avg_front_tire_pressure'], "PSI"],
							"Voltage" => [$averages['avg_voltage'], "V"],
							"Temperature" => [$averages['avg_temperature'], "°C"],
							"Distance" => [$averages['avg_distance'], "km"],
							"Vibration" => [$averages['avg_vibration'], ""]
						];

						foreach ($metrics as $label => $data): ?>
							<div class="col-12 col-md-6 col-lg-4">
								<div class="bg-light border rounded shadow-sm text-center card-small">
									<h6 class="text-secondary"><?= $label ?></h6>
									<h4 class="fw-bold text-primary"><?= $data[0] ?> <?= $data[1] ?></h4>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<!-- COMPONENT AGES -->
				<div class="container-fluid mt-5">
					<h5 class="text-start mb-3 fw-bold">Component Ages</h5>
					<div class="row g-3">
						<?php while ($row = $components->fetch_assoc()): ?>
							<?php
							$install_date = new DateTime($row['datetime_change']);
							$now = new DateTime();
							$diff = $install_date->diff($now);
							?>
							<div class="col-12 col-md-6 col-lg-4">
								<div class="bg-white border rounded shadow-sm card-small">
									<h6 class="text-secondary"><?= htmlspecialchars($row['component_name']) ?></h6>
									<p class="text-muted mb-1">Installed: <?= $install_date->format('Y-m-d') ?></p>
									<h5 class="fw-bold text-primary">
										<?= $diff->y ?>y <?= $diff->m ?>m <?= $diff->d ?>d
									</h5>
									<span class="badge bg-info text-dark"><?= htmlspecialchars($row['status']) ?></span>
								</div>
							</div>
						<?php endwhile; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php include "./globals/scripts.php"; ?>

	<script>
		// Sidebar responsiveness
		const sidebarToggle = document.querySelector("#sidebarToggle");
		if (sidebarToggle) {
			sidebarToggle.addEventListener("click", () => {
				document.body.classList.toggle("sidebar-collapsed");
			});
		}
	</script>

</body>

</html>