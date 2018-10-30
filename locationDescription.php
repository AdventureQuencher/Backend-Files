<?php

	$conn = connectDatabase();

	if (isset($_POST['location'])) 
	{
		$description = getDesc($conn, $_POST['location']);
		
		echo json_encode($description);
		
	}
	else
	{
		$description["error"] = "true";
		echo json_encode($description);
	}

	function getDesc($conn, $loc)
	{
		$queryLocNo = "SELECT locationNo FROM location_coord WHERE locationName = '".$loc;
		$statement = $conn->prepare("SELECT locationDesc, location_Adrs, location_Time FROM location_desc WHERE
					locationNo = (".$queryLocNo."');");

		$statement->execute();	//if something is returned when query executed
		
		$statement-> bind_result($rLocationDesc, $rAddress, $rDates);

        while ( $statement-> fetch() ) 
		{
			$locationdata["error"] = "false";
			$locationdata["desc"] = $rLocationDesc;
			$locationdata["address"] = $rAddress;
			$locationdata["dates"] = $rDates;
        }
 
            $statement->close();
 			
            return $locationdata;
	}

	function connectDatabase()
	{
		require_once( "../../../etc/db_config.php" );
		//connect to db
		$conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE_LOCATION);
		
		
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		//successful connection, return db object
		return $conn;
	}

?>
