<?php
/*
	SIMPLE COMMENTS
	A commenting system designed to be fast and easy to implement.

	Created by Colin O'Neill <faokryn@gmail.com> github.com/Faokryn

	comments.php -	Receives form data from comments.js and stores it in 
					comments.db, retreives data from comments.db, formats it as 
					HTML, and returns it to comments.js

	This software is released under the GNU GENERAL PUBLIC LICENSE Version 2
	https://github.com/Faokryn/SimpleComments/blob/master/LICENSE
*/

	/*
	Checks if the directory in which comments.php is located is writable by
	comments.php.  If it is, returns an SQLite3 object with a connection to a
	database file called "comments.db".  If that database file does not exist,
	it is created.  If the directory is not writable, an error is printed to
	the screen.  For now.
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
	Creates a table with appropriate columns to represent comments, using the
	given name, in the database (if one does not exist already).
	*/
	function initTable($name) {
		$db = initDB();
		$query = "CREATE TABLE IF NOT EXISTS '" . $name . 
		"' (id INTEGER PRIMARY KEY, name TEXT NOT NULL, email TEXT NOT NULL," .
		" message TEXT NOT NULL, hide INTEGER CHECK(hide=0 || hide=1)," .
		" date TEXT DEFAULT current_timestamp)";
		$db->exec($query);
	}

	/*
	Checks the validity of a comment and adds valid comments to the database.
	*/
	function addComment($hidemail) {

		// Determine if the values from the POST request are valid, if they 
		// aren't, add an informative error to the $errors array.  If they are,
		// sanatize them.
		$errors = [];
		if ($_POST["name"] == "") {
			array_push($errors, "'Name' field is empty.");
		}
		else {
			$_POST["name"] = 
			filter_input(INPUT_POST,"name", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		}
		if ($_POST["email"] == "") {
			array_push($errors, "'Email' field is empty.");
		}
		elseif (!($_POST["email"] = filter_input(INPUT_POST,"email",
				FILTER_VALIDATE_EMAIL))) {
			$msg = "'Email' field does not contain a valid email address.";
			array_push($errors, $msg);
		}
		else {
			$_POST["email"] = strtolower($_POST["email"]);
		}
		if ($_POST["message"] == "") {
			array_push($errors, "'Comment' field is empty.");
		}
		else {
			$_POST["message"] = 
			filter_input(INPUT_POST,"message", 
			FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		}

		$_POST["url"] = filter_input(INPUT_POST,"url", FILTER_SANITIZE_URL);

		// If anything is invalid, inform the user.
		if ($errors) {
			$msg = "Comment not submitted due to the following errors:\n\t• " .
				implode("\n\t• ", $errors) . "\n";
			$response = [
				"valid" => false,
				"msg" => $msg
			];
			echo json_encode($response);
		}
		// If nothing is invalid, build an SQL query and attempt to submit
		else {
			$db = initDB();
			// The table name is the md5 hash of the current URL.
			$tableName = md5($_POST["url"]);
			initTable($tableName);

			// Build the query
			$query = "INSERT INTO '" . $tableName . 
					"' (name, email, message, hide) VALUES ('" . 
					$_POST["name"] . "', '" . $_POST["email"] . 
					"', '" . $_POST["message"];

			if ($hidemail) {
				$query = $query . "', 1)";
			}
			else {
				$query = $query . "', 0)";
			}

			// If the query executes, inform the user
			if ($db->exec($query)) {
				$msg = "<h3>Your comment was submitted successfully!</h3>";
				$response = [
					"valid" => true,
					"msg" => $msg
				];
				echo json_encode($response);
			}
			// If it does not execute, inform the user
			else {
				$msg = "<h3>Error sending SQL query.</h3>";
				$response = [
					"valid" => false,
					"msg" => $msg
				];
				echo json_encode($response);
			}
		}
	}

	/*
	Finds the appropriate table in the database and writes a comment to the
	page for each row
	*/
	function displayComments() {
		// The table name is the md5 hash of the current URL.
		$tableName = md5($_POST["url"]);
		initTable($tableName);

		$db = initDB();

		// Get everything from the table for the current page
		$query = "SELECT * FROM '" . $tableName . "'";
		$result = $db->query($query);

		// Get the array representing the next row while it is not false
		while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
			// Build the HTML for a single comment div
			$output = "<hr class='sc_line'><div class='sc_comment'>"

			. "<img src='http://cdn.libravatar.org/avatar/" . md5($row["email"])
			. "?d=http://www.gravatar.com/avatar/" . md5($row["email"]) . 
			"' class='sc_avatar'>"

			. "<div class='sc_comment_body'>" . "<h6 class='sc_date'>" . 
			$row["date"] . "</h6>";

			// Make the name a "mailto" link to the given email, if the email
			// was not selected to be hidden
			if ($row["hide"] == 0) {
				$output = $output . "<a class='sc_email_link' href='mailto:" . 
				$row["email"] . "'><h4 class='sc_name'>". $row["name"] . 
				"</h4></a>";
			}
			else {
				$output = $output . "<h4 class='sc_name'>". $row["name"] . 
				"</h4>";
			}

			$output = $output . "<p class='sc_comment_message'>" . 
			$row["message"] . "</p></div></div>";

			// echo the HTML so it can be picked up be the HTTP request
			echo $output;
		}
	}


	/*
	Runs when the PHP file is called.  Determines how to handle the POST request
	and executes the proper functions.
	*/
	function handleRequest() {
		// If the request has these data, it is a comment submission
		if (array_key_exists("name", $_POST)	&&
			array_key_exists("email", $_POST)	&&
			array_key_exists("message", $_POST)	&&
			array_key_exists("url", $_POST)		){

			// Check whether or not email is hidden, and execute addComment
			// as appropriate
			if (array_key_exists("hidemail", $_POST)) {
				// add comment and hide email
				addComment(true);
			}
			else {
				// add comment and show email
				addComment(false);
			}
		} 
		// If the request has displayCall, it is a request for the comments
		// stored in the database
		elseif (array_key_exists("displayCall", $_POST)) {
			// build and respond with HTML for the comments
			displayComments();
		}
		// If an HTTP request was received, but it was not a form submission or
		// a request for database data, then something went wrong.
		else {
			echo "<h1>Something went wrong!</h1>";
		}
	}

	handleRequest();
?>