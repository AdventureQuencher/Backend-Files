<?php

	//submitted contents: EMAIL, PASSWORD, CONFIRMATION PASSWORD DISPLAY NAME

	//json response
	$response = array("error" => FALSE);

	$conn = connectDatabase();

	if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password2']) && 
			isset($_POST['display_name']))
	{
		// receiving the post params
		$email = $_POST['email'];
		$password = $_POST['password'];
		$password2 = $_POST['password2'];
		$name = $_POST['display_name'];

		// check if user already exists with the same email
		if (checkExistingEmail($email, $conn))
		{
			// user already existed
			$response["error"] = TRUE;
			$response["error_msg"] = "User already existed with " . $email;
			echo json_encode($response);
		} 
		else
		{
			// create a new user
			$user = registerInfo($email, $password, $name, $conn);
			if ($user)
			{
				// user stored successfully
				$response["error"] = FALSE;
				$response["user"]["display_name"] = $user["display_name"];
				$response["user"]["email"] = $user["email"];
				echo json_encode($response);
			} 
			else
			{
				// user failed to store
				$response["error"] = TRUE; 
				$response["error_msg"] = "Unknown error occurred in registration!";
				echo json_encode($response);
			}
		 }
	} 
	else 
	{
		$response["error"] = TRUE;
		$response["error_msg"] = "Required parameters (email, password or name) is missing!";
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

	function registerInfo($email, $password, $displayName, $conn) 
	{
        $hash = hashFunction($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
 
        $statement = $conn->prepare("INSERT INTO users(email, display_name, password_hash, salt) VALUES(?, ?, ?, ?)");
        $statement->bind_param("ssss", $email, $displayName, $encrypted_password, $salt);
        $result = $statement->execute();
        $statement->close();
 
        // test to see if storage worked
        if ($result) 
		{
            $statement = $conn->prepare("SELECT email, display_name, password_hash, salt FROM users WHERE email = ?");
            $statement->bind_param("s", $email);
            $statement->execute();
            $statement-> bind_result($rEmail, $rDisplayName, $rPasswordhash ,$rSalt);
 
            while ( $statement-> fetch() )
			{
               $user["email"] = $rEmail;
               $user["display_name"] = $rDisplayName;
            }
			
            $statement->close();
            return $user;
        } 
		else 
		{
          return false;
        }
    }
 

	//function returns a hash version of the password
    function hashFunction($password)
	{
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
		
		//store the generated salt and encrypted password into an array and return
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

	function checkExistingEmail($email, $conn) 
	{
        $statement = $conn->prepare("SELECT email from users WHERE email = ?");
        $statement->bind_param("s", $email);
        $statement->execute();
        $statement->store_result();
 
        if ($statement->num_rows > 0) 
		{
            // email already exists in database
            $statement->close();
            return true;
        } 
		else 
		{
            // email does no exist in database
            $statement->close();
            return false;
        }
    }

?>
