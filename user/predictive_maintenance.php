<?php include "./globals/head.php"; ?>



<body>
	<?php include "./globals/navbar.php"; ?>

	<div id="main" class="container-fluid">
		<div class="row">
			<div class="col-lg-8 mx-auto mb-3 border shadow">
				<p>Battery Voltage</p>
				<small>Average for Last 10 Readings</small>

				<?php
				include '../src/connection.php';
				
					$sql = 'Select voltage from telemetry_data where vehicle_id = "' . $vehicle_id . '" order by datetime_received DESC';
					$result = $mysqli->query($sql);
					$data = array();
					while ($row = $result->fetch_assoc()) {
						$data[] = $row;
					}
					$sum = 0;
					for($i = 0; $i < count($data); $i++) {
						echo $data[$i]['voltage'] . "<br>";
						$sum += $data[$i]['voltage'];
					}
					$average = $sum / count($data);
					echo "Average Voltage: " . $average;

					$mysqli->close();
				?>

			</div>
			<div class="col-lg-2 mx-auto mb-3 border shadow">

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