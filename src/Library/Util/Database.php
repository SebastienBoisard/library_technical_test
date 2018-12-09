<?php

namespace Library\Util;

use Library\Util\Logger;

class Database
{
	private const DB_SERVER   = '127.0.0.1';
	private const DB_USERNAME = 'demo_user';
	private const DB_PASSWORD = 'eTljkdf45Tnd!D23dvoppSln49fnleq';
	private const DB_NAME     = 'library';

    private $db_conn = null;
 
    // get the database connection
    public function getConnection() {
		Logger::getInstance()->addDebug("BEGIN");
 
 		if ($this->db_conn != null) {
 			return $this->db_conn;
 		}

 		$this->db_conn = new \mysqli(self::DB_SERVER, self::DB_USERNAME, self::DB_PASSWORD, self::DB_NAME);

		if ($this->db_conn->connect_error) {
			Logger::getInstance()->addError("Database error: ".$this->db_conn->connect_error.
				" (error=".$this->db_conn->connect_errno.")");
			return null;
		}

		return $this->db_conn;
    }	

    public function closeConnection() {
 		if ($this->db_conn != null) {
    		$this->db_conn->close();
    	}
    }
}
