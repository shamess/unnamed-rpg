<?php

/**
* Handles database connections. Very much just a utility class.
*/
class Database {
	private $server, $user, $password, $database = null;
	
	/**
	* Construtor
	*
	* @param String Server to connect to
	* @param String User to connect with
	* @param String Password to authenticate with
	* @param String Database name
	*/
	function __construct ($server, $user, $password, $database) {
		$this->server = $server;
		$this->user = $user;
		$this->password = $password;
		$this->database = $database;
	}
	
	/**
	* Creates a connection to the database
	*
	* This needs to happen on every relavant place since PHP closes connections on file end, which
	* means we can't just store the connection.
	*
	* @return Resource mysqllink that can be used on queries
	*/
	private function connect () {
		if (empty ($this->connection)) {
			//  Connect to the server
			$this->connection = mysql_connect ($this->server, $this->user, $this->password, false);
			//  If the connection is false, it means we couldn't connect for some reason. Since this is a database
			//  driven site, we should do a fatal error, not just the warning that we usually get.
			if ($this->connection == false) {
				$this->SQLDebug();
				throw new DatabaseNoConnectException ("Database connection could not be established");
			}
			
			mysql_select_db ($this->database);
		}
		
		return $this->connection;
	}
	
	/**
	* Runs a query, and gives it's result to be passed to another function
	*
	* Since we need to run connect() every time, it's just easier to do that in here.
	*
	* @see mysql_query
	* @param String SQL query to be run. No formatting is done on this
	* @return Resource Can be passed to mysql_fetch_array, or whatever
	*/
	public function query ($sql) {
		$return = @mysql_query ($sql, $this->connect());
	
		if (mysql_errno() !== 0) {
			$this->SQLDebug ($sql);
		}
		
		return $return;
	}
	
	/**
	* Convenience method to escape strings
	*
	* @param String to be escaped
	* @return String escaped
	*/
	public function escape ($string) {
		return mysql_real_escape_string ($string, $this->connect());
	}
	
	/**
	* Gets the value returned by a query
	*
	* Convenience method because I'm tired of doing a mysql_fetch_assoc for one value from the
	* query, and having to put the array returned into a seperate variable first. This function
	* simply returns the value.
	* 
	* Remember that the query must evaluate to a single field of a single row returned.
	*
	* @param string An SQL line to run
	* @return mixed Whatever the result of the query is
	*/
	public function getSingleValue ($query) {
		$tmp_value = @mysql_fetch_row ($this->query ($query));
		
		if (mysql_errno() !== 0) {
			$this->SQLDebug ($query);
		}
		return $tmp_value[0];
	}
	
	public function getLastInsertId () { return mysql_insert_id ($this->connect());	}
	public function getLastId () { return $this->getLastInsertId($this->connect()); }
	public function affectedRows () { return mysql_affected_rows ($this->connect()); }

	/**
	* Echos the SQL given to it, and the error message it may have caused
	*
	* To be run after the query has been (and likely failed). Obviously only for debugging.
	*
	* @param string SQL
	* @return void. It does echo though
	*/
	public function SQLDebug ($qry) {
		// get the file and line information on where this error happened (experimental)
		$debug = debug_backtrace ();

		echo "<div class=\"error\"><p>An error occured on with your query. The query run was: <b>".$qry."</b><br /><br />\n";
		// was there an error message?
		$error_message = mysql_error ();
		if (!empty ($error_message)) echo "The error message we were given was: <b>".$error_message."</b>.<br /><br />\n";
		
		echo "Here's the error backtrace:</p>\n<ul>";
		foreach ($debug as $error) {
			echo "<li>".$error['file']." on ".$error['line']."</li>\n";
		}
		echo "</ul>\n";
		
		echo "</div>\n";
	}
}

class DatabaseNoConnectException extends Exception {
	public function __toString () {
		return "Invalid database credentials caused failure to connect";
	}
}

if (defined ("database_server"))
	$Database = new Database (database_server, database_user, database_password, database_name);

?>