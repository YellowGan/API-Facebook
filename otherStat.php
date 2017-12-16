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
			$statFriend[] = $stat->friend();

			foreach ($statFriend as $key => $value) 
			{
				foreach ($value as $key1 => $value1) 
				{
					foreach ($value1 as $key2 => $value2) 
					{
						echo "<h3>Media Amici </h3>" .$value2. "<br>";
					}
				}
			}

			echo "<hr>";

			echo "<h3>Numero Medio di Pagine per Utente </h3>" .$stat->averagePages();;

			echo "<hr>";

		?>

		<a href="menuStat.php"><h3>Torna al menu</h3></a>
		<a href="index.php"><h3>Torna al profilo</h3></a>
	</body>

</html>