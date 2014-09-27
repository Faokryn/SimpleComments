<?php

	/*
	Checks if the directory in which comments.php is located is writable by
	comments.php.  If it is, returns an SQLite3 object with a connection to a
	database file called "comments.db".  If that database file does not exist,
	it is created.  If the directory is not writable, an error is printed to
	the screen.  For now.

	TOD0:
		- Determine a more effective way to present the error message
		- Make the error message more descriptive (i.e. explain how to resolve
		  the error.  Focus is on accessability after all.)
	*/
	function initDB() {
		if (is_writable('.')) {
			return new SQLite3('comments.db');
		}
		else {
			echo "ERROR: Permissions do not allow comments.php to write to " .
			"this directory";
		}
	}

	/*
	Creates a table with appropriate columns to represent comments with the
	given name in the database, if one does not exist already.

	TOD0:
	*/
	function initTable($name) {
		$db = initDB();
		$query = "CREATE TABLE IF NOT EXISTS " . $name . 
		" (id INTEGER PRIMARY KEY, name TEXT NOT NULL, email TEXT NOT NULL," .
		" message TEXT NOT NULL, hide INTEGER CHECK(hide=0 || hide=1)," .
		" date TEXT DEFAULT current_timestamp)";
		$db->exec($query);
	}

	/*
	Checks the validity of a comment and adds valid comments to the database.

	TOD0:
		- Check if values are correct
			- Name -> alphanumeric + a few symbols
			- Email -> valid email
		- Sanatize form input
	*/
	function addComment($hidemail) {

		// Determine if any values are missing
		$missing = [];
		if ($_POST['name'] == '') {
			array_push($missing, 'name');
		}
		if ($_POST['email'] == '') {
			array_push($missing, 'email');
		}
		if ($_POST['message'] == '') {
			array_push($missing, 'message');
		}

		// If anything is missing, inform the user.
		if ($missing) {
			$message =	"Comment not submitted. Missing: " . 
						implode(", ", $missing) . "<br>";
			echo $message;
		}
		// If nothing is missing, build an SQL query and attempt to submit
		else {
			$db = initDB();
			// The table name is the md5 hash of the current URL.
			$tableName = md5($_POST["url"]);

			// Build the query
			$query = 	"INSERT INTO " . $tableName . 
						"(name, email, message, hide) " . "VALUES('" . 
						$_POST["name"] . "', '" . $_POST["email"] . "', '" .
				 		$_POST["message"] . "', ";
			if ($hidemail) {
				$query = $query . "1";
			}
			else {
				$query = $query . "0";
			}
			$query	= $query . ")";

			initTable($tableName);

			// If the query executes, inform the user
			if ($db->exec($query)) {
				echo "<h3>Your comment was submitted successfully!</h3>";
			}
			// If it does not execute, inform the user
			else {
				echo "<h3>Error sending SQL query.</h3>";
			}
		}
	}

	/*
	Finds the appropriate table in the database and writes a comment to the
	page for each row

	TODO:
		- Everything; this is a stub
	*/
	function displayComments() {}


	/*
	Runs when the PHP file is called.  Determines how to handle the POST request
	and executes the proper functions.

	TODO:
		- Handle display comments request
		- Better handling for file called with no POST
	*/
	function handleRequest() {
		if (array_key_exists("name", $_POST)	&&
			array_key_exists("email", $_POST)	&&
			array_key_exists("message", $_POST)	&&
			array_key_exists("url", $_POST)		){

			if (array_key_exists("hidemail", $_POST)) {
				// add comment and hide email
				addComment(true);
			}
			else {
				// add comment and show email
				addComment(false);
			}
		} 
		elseif (true /*CHECK IF THIS IS A DISPLAY COMMENTS REQUEST*/) {
			
		}
		else {
			echo "Something went wrong!";
		}
	}

	handleRequest();

?>