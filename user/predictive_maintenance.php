<?php include "./globals/head.php"; ?>



<body class="overflow-x-hidden">
	<?php include "./globals/navbar.php"; ?>

	<div id="main" class="container-fluid">
		<div class="row">
			<?php
			include '../src/connection.php';

			$sql = 'Select voltage from telemetry_data where vehicle_id = "' . $vehicle_id . '" order by datetime_received DESC';
			$result = $mysqli->query($sql);
			$data = array();
			while ($row = $result->fetch_assoc()) {
				$data[] = $row;
			}
			$sum = 0;
			$latest = $data[0]['voltage'];
			$previous = $data[1]['voltage'];
			$difference = $latest - $previous;
			$percentage_change = ($difference / $previous) * 100;
			for ($i = 0; $i < count($data); $i++) {
				$sum += $data[$i]['voltage'];
			}
			$average = $sum / count($data);

			$sql = 'Select * from components_changes where vehicle_id = "' . $vehicle_id . '" and component_name = "Battery" order by datetime_change DESC LIMIT 1';
			$result = $mysqli->query($sql);
			$row = $result->fetch_assoc();
			$date_of_change = $row['datetime_change'];
			$today = date("Y-m-d");

			$diff = date_diff(date_create($date_of_change), date_create($today));
			$age =  $diff->format("%y y, %m m");

			$mysqli->close();
			?>
			<div class="col-lg-9  mb-3 ">
				<div class="row mt-0 border p-2">
					<div class="col-12 mx-auto mb-0 mt-0 p-1">
						<h4 class="badge  fs-5 text-dark p-1  "><i class="bi bi-bell-fill me-2"></i>Predictive Maintenance Alerts</h4>
					</div>
					<div class="col-12 mx-auto mb-1 p-1">
						<span style="font-size: 0.9em; padding: 1px; display: flex; align-items: center; justify-content: start; " class=" m-0 alert-light"> <i class="bi bi-exclamation-circle-fill me-2 text-danger" style="font-size: 1.4em;"></i>Battery has reached 5 years. Maintenance is needed. Message sent to <p class="badge bg-success mx-2 mb-0"><?php echo $fullname; ?></p> with contact number <p class=" ms-2 badge bg-primary mb-0"><?php echo $contact; ?>.</p></span>
					</div>
					<div class="col-12 mx-auto mb-1 p-1">
						<span style="font-size: 0.9em; padding: 1px; display: flex; align-items: center; justify-content: start; " class=" m-0 alert-light"> <i class="bi bi-exclamation-circle-fill me-2 text-danger" style="font-size: 1.4em;"></i>Front Tire is low in pressure for the last 10 days. Maintenance is needed. Message sent to <p class="badge bg-success mx-2 mb-0"><?php echo $fullname; ?></p> with contact number <p class=" ms-2 badge bg-primary mb-0"><?php echo $contact; ?>.</p></span>
					</div>
				</div>
			</div>
			<div class="col-lg-9 mb-3  p-3">
				<div class="row">
					<div class="col-6 ">
						<h4 class="border-0 border-bottom border-secondary border-3 p-3">Battery</h4>
						<div class="row  gx-1 mt-3">

							<div class="col-4 p-3   border d-flex  justify-content-center align-items-center  flex-column">
								<p style="font-size: 1.9em; "><?php echo $average; ?> V</p>
								<small style="font-size: 0.8em; color: var(--bs-secondary);" class="mb-3">Average for last 10 readings</small>
								<h5 class="badge bg-secondary  text-center p-2 ">Average</h5>
							</div>
							<div class="col-4 p-3  border  d-flex  justify-content-center align-items-center  flex-column">
								<p style="font-size: 1.9em; "><?php echo $difference; ?> V</p>
								<small style="font-size: 0.8em; color: var(--bs-secondary);" class="mb-3">Latest - Previous Reading</small>
								<h5 class="badge bg-secondary p-2  text-center ">Voltage Difference</h5>
							</div>
							<div class="col-4 p-3   border d-flex  justify-content-center align-items-center  flex-column">
								<p style="font-size: 1.9em; "><?php echo $age; ?></p>
								<small style="font-size: 0.8em; color: var(--bs-secondary);" class="mb-3">From last installation date</small>
								<h5 class="badge bg-secondary p-2 text-center  ">Component Age</h5>
							</div>
						</div>
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