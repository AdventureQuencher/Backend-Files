<?php
	if (isset($_POST['email']) && isset($_POST['password'])) 
	{
		$conn = connectDatabase();
		// receiving the post params
		$email = $_POST['email'];
		$password = $_POST['password'];

		// get the user by email and password
		$user = verifyCredentials($email, $password, $conn);

		if ($user != false)
		{
			// user found in the database
			$response["error"] = FALSE;
			$response["user"]["display_name"] = $user["display_name"];
			$response["user"]["email"] = $user["email"];
			echo json_encode($response);
		} 
		else 
		{
			// user cannot be found in the database
			$response["error"] = TRUE;
			$response["user"]["display_name"] = $email;
			$response["user"]["password"] = $password;
			$response["error_msg"] = "Login credentials are wrong. Please try again!";
			echo json_encode($response);
		}
	} 
	else 
	{
		// required post params is missing
		$response["error"] = TRUE; 
		$response["error_msg"] = "Required parameters email or password is missing!";
		echo json_encode($response);
	}

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
		return $conn;
	}

	function verifyCredentials($email, $password, $conn) 
	{
      	$statement = $conn->prepare("SELECT email, display_name, password_hash, salt FROM users WHERE email = ?");
      	$statement->bind_param("s", $email);

		if ($statement->execute())	//if something is returned when query executed
		{
			$statement-> bind_result($rEmail, $rDisplay_name, $rPasswordhash, $rSalt);
 
            while ( $statement-> fetch() ) 
			{
				$userdata["email"] = $rEmail;
				$userdata["display_name"] = $rDisplay_name;
				$userdata["encrypted_password"] = $rPasswordhash;
				$userdata["salt"] = $rSalt;
            }
 
            $statement->close();
 			
            // verifying user password by comparing inputed password, and hashed password on DB
            $hash = checkHashFunction($rSalt, $password);
			
            // check for password equality
            if ($rPasswordhash == $hash) 
			{
                // inputted username and password is correct
                return $userdata;
            }
			else
			{
				return NULL;
			}
			
       } 
		else //return null if username and/or password is incorrect
		{
            return NULL;
        }
    }
 
	//create hash based on salt stored in db
	function checkHashFunction($salt, $password)
	{
		$hash = base64_encode(sha1($password . $salt, true) . $salt);
        return $hash;
    }





/*	$email = $_POST[ "email" ];
	$userpassword = $_POST[ "password" ];

	$conn = connectDatabase($host, $user, $password, $dbnm);
	
	$query = "SELECT * FROM $table WHERE email='$email' AND password='$userpassword';";

	$result = mysqli_query($conn, $query) or die(mysqli_error());
		
	//if query returned a row (username and password is correct)
	if(mysqli_num_rows(mysqli_query($conn, $query)) > 0)
	{
		echo "true";		//true
	}
	else
	{
		echo "false";	//false
	}*/

?>