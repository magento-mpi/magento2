<?php

class Mage_Core_Resource_Type_Db_Mysqli_Adapter extends Zend_Db_Adapter_Mysqli
{
    /**
     * Creates a real connection to the database with multi-query capability.
     *
     * @return void
     * @throws Zend_Db_Adapter_Mysqli_Exception
     */
    protected function _connect()
    {
        if ($this->_connection) {
            return;
        }
        // Suppress connection warnings here.
        // Throw an exception instead.
        @$conn = new mysqli();
        if (false===$conn || mysqli_connect_errno()) {
            throw new Zend_Db_Adapter_Mysqli_Exception(mysqli_connect_errno());
        }
        
        $conn->init();
	    $conn->options(MYSQLI_OPT_LOCAL_INFILE, true);
	    #$conn->options(MYSQLI_CLIENT_MULTI_QUERIES, true);
		$conn->real_connect($this->_config['host'], $this->_config['username'], $this->_config['password'], $this->_config['dbname']);
        
        $this->_connection = $conn;
    }
    
    public function multi_query($sql)
	{
		if ($this->getConnection()->multi_query($sql)) {
			do {
			    if ($result = $this->getConnection()->store_result()) {
			    	$result->free_result();
			    }
			}
			while ($this->getConnection()->next_result());
		}
	}
}