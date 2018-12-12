<?php

namespace Library\Util;

use Library\Util\Logger;

class Database
{
    private $db_conn = null;
 
    /** 
     * getConnection returns the instance of the database handler if it exists, or else creates it.
     *
     * @return \mysqli - The database handler.
     */
    public function getConnection() : \mysqli
    {
        if ($this->db_conn != null) {
            return $this->db_conn;
        }

        $database_config = require(__DIR__ . '/../../../config/database.php');

        $this->db_conn = new \mysqli(
            $database_config['server'], 
            $database_config['user_name'], 
            $database_config['user_password'], 
            $database_config['database_name']
        );

        if ($this->db_conn->connect_error) {
            Logger::getInstance()->addError("Database error: ".$this->db_conn->connect_error.
                " (error=".$this->db_conn->connect_errno.")");
            return null;
        }

        return $this->db_conn;
    }   

    /** 
     * closeConnection closes the database handler.
     */
    public function closeConnection()
    {
        if ($this->db_conn != null) {
            $this->db_conn->close();
        }
    }
}