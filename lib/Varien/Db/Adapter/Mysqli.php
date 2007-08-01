<?php

class Varien_Db_Adapter_Mysqli extends Zend_Db_Adapter_Mysqli
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
    
    public function raw_query($sql)
    {
        return $this->getConnection()->query($sql);
    }
    
    public function raw_fetchRow($sql, $field=null)
    {
        if (!$result = $this->raw_query($sql)) {
            return false;
        }
        if (!$row = $result->fetch_assoc()) {
            return false;
        }
        if (empty($field)) {
            return $row;
        } else {
            return isset($row[$field]) ? $row[$field] : false;
        }
    }
    
    public function multi_query($sql)
	{
	    $this->getConnection()->autocommit(FALSE);
		if ($this->getConnection()->multi_query($sql)) {
			do {
			    if ($result = $this->getConnection()->store_result()) {
			    	$result->free_result();
			    }
			    elseif($this->getConnection()->error) {
			        throw new Zend_Db_Adapter_Mysqli_Exception($this->getConnection()->error);
			    }
			}
			while ($this->getConnection()->next_result());
		}
		$this->getConnection()->commit();
		return true;
	}
	
	public function dropForeignKey($table, $fk)
	{
        $create = $this->raw_fetchRow("show create table `$table`", 'Create Table');
        if (strpos($create, "CONSTRAINT `$fk` FOREIGN KEY")!==false) {
            $this->raw_query("ALTER TABLE `$table` DROP FOREIGN KEY `$fk`");
        }
	}
}