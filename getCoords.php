<?php

	$conn = connectDatabase();


	$coords = getLocs($conn);

	echo json_encode(array('pin_details' => $coords));

	

	function getLocs($conn)
	{
		$statement = $conn->prepare("SELECT locationName, latitudeNo, longitudeNo FROM location_coord");

		$statement->execute();	//if something is returned when query executed
		
		$statement-> bind_result($rLocation, $rLat, $rLong);
 
		$i = 0;
        while ( $statement-> fetch() ) 
		{
			$locationdata[$i]["location"] = $rLocation;
			$locationdata[$i]["lat"] = $rLat;
			$locationdata[$i]["long"] = $rLong;
			
			++$i;
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
