<?php
	require_once 'fbConfig.php';
	require_once 'Stat.php';
?>

<html>
	<head>
		<title></title>
		<style type="text/css">
		    h1{font-family:Arial, Helvetica, sans-serif;color:#999999;}
		</style>
	</head>

	<body>
		<?php

			$stat = new Stat();
			$statOS[] = $stat->os();
			$y = [];
			$label = [];

			foreach ($statOS as $key => $value) 
			{
				foreach ($value as $key1 => $value1) 
				{
					foreach ($value1 as $key2 => $value2) 
					{
						if ($key2 == "count")
							array_push($y, intval($value2));
						else
							array_push($label, $value2);
					}
				}
			}

			$count = count($y);
			$tot = 0;
			$title = "Sistemi operativi";
			$slug = "os";

			for ($i=0; $i < $count; $i++)  
				$tot += $y[$i];
			
			for ($i=0; $i < $count; $i++)
				$y[$i] = $stat->percent($y[$i], $tot);

			$stat->createPieChart($y, $label, $count, $title, $slug);

		?>
		
		<a href="menuStat.php"><h3>Torna al menu</h3></a>
		<a href="index.php"><h3>Torna al profilo</h3></a>
		<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
	</body>

</html>