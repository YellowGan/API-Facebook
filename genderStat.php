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
			$statGender = $stat->countGender();
			$y = [];
			$label = [];
		
			foreach ($statGender as $key => $value) 
			{
				foreach ($value as $key1 => $value1) 
				{
					if ($key1 == "n_gender")
						array_push($y, intval($value1));
					else
						array_push($label, $value1);
				}
			}

			$count = count($y);
			$tot = 0;
			$title = "Genere";
			$slug = "gender";
			
			for ($i=0; $i < $count; $i++)  
				$tot += $y[$i];
			
			for ($i=0; $i < $count; $i++)
				$y[$i] = $stat->percent($y[$i], $tot);

			$stat->createDoughnutChart($y, $label, $count, $title, $slug);

		?>

		<a href="menuStat.php"><h3>Torna al menu</h3></a>
		<a href="index.php"><h3>Torna al profilo</h3></a>
		<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
	</body>

</html>