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
		- Add date column
		- Consider adding an option to link email
		- Consider adding an option to show website
	*/
	function initTable($name) {
		$db = initDB();
		$query = "CREATE TABLE IF NOT EXISTS " . $name . 
		" (id INTEGER PRIMARY KEY, name TEXT NOT NULL," . 
		" email TEXT NOT NULL, message TEXT NOT NULL)";
		$db->exec($query);
	}

	/*
	Adds a new comment to the database.

	TOD0:
		- Everything; this is a stub
	*/
	function addComment() {}

	/*
	Finds the appropriate table in the database and writes a comment to the
	page for each row

	TOD0:
		- Everything; this is a stub
	*/
	function displayComments() {}


	initTable("test");  // for testing; remove me later

?>