<?php

class Mage_Core_Resource_Type_Db_Mysqli_Setup
{
    protected $_connection = null;
    
	public function __construct($config)
	{
		$this->_connection = new mysqli();
        $this->_connection->init();
        $this->_connection->options(MYSQLI_OPT_LOCAL_INFILE, true);
        $conn = $config;
		$this->_connection->real_connect((string)$conn->host, (string)$conn->username, (string)$conn->password, (string)$conn->dbname);

	}
	
	function query($sql)
	{
		if ($this->_connection->multi_query($sql)) {
			do {
			    if ($result = $this->_connection->store_result()) {
			    	$result->free_result();
			    }
			}
			while ($this->_connection->next_result());
		}
	}
}