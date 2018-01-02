<?php 
	if (
		isset($_GET['rate'])
	)
	{
		$newrate = $_GET['rate'];
		if ($conn = mysqli_connect("localhost", "autoljfb_host", "t2TC24w6UP2J", "autoljfb_host")) 
		{
			if ($query = mysqli_query($conn, "UPDATE rate SET rate = '$newrate' WHERE ID = 1")) 
			{
				echo "Changed";
			}
			else 
			{
				die("Could not perform update");
			}
		}
		else 
		{
			die("Could not connect to database");
		}
	}
	else 
	{
		die("Rate not defined");
	}
?>