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
			$statLocale = $stat->countLocale();
			$y1 = [];
			$label1 = [];
		
			foreach ($statLocale as $key => $value) 
			{
				foreach ($value as $key1 => $value1) 
				{
					if ($key1 == "n_locale")
						array_push($y1, intval($value1));
					else
						array_push($label1, $value1);
				}
			}

			$count1 = count($y1);
			$tot1 = 0;
			$title1 = "Lingua";
			$slug1 = "lingua";

			for ($i=0; $i < $count1; $i++)  
				$tot1 += $y1[$i];
			
			for ($i=0; $i < $count1; $i++)
				$y1[$i] = $stat->percent($y1[$i], $tot1);

			$stat->createDoughnutChart($y1, $label1, $count1, $title1, $slug1);

		?>
		
		<a href="menuStat.php"><h3>Torna al menu</h3></a>
		<a href="index.php"><h3>Torna al profilo</h3></a>
		<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
	</body>

</html>