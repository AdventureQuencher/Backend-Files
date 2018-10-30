<?php
class android_connect
{

	
	private $conn;

	function connectDatabase()
	{
			require_once( "../../../etc/db_config.php" );
		//connect to db
		$conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE_LOGIN);
		
		
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		//successful connection, return db object
		return $this->conn;
	}
}
?>
