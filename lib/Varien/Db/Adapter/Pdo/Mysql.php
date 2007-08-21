<?php

class Varien_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql
{
    protected $_transactionLevel=0;

    public function beginTransaction()
    {
        if ($this->_transactionLevel===0) {
            parent::beginTransaction();
        }
        $this->_transactionLevel++;
        return $this;
    }

    public function commit()
    {
        if ($this->_transactionLevel===1) {
            parent::commit();
        }
        $this->_transactionLevel--;
        return $this;
    }

    public function rollback()
    {
        if ($this->_transactionLevel===1) {
            return parent::rollback();
        }
        $this->_transactionLevel--;
        return $this;
    }

    public function convertDate($date)
    {
        return strftime('%Y-%m-%d', strtotime($date));
    }

    public function convertDateTime($datetime)
    {
        return strftime('%Y-%m-%d %H:%M:%S', strtotime($datetime));
    }
    

    protected function _connect()
    {
    	parent::_connect();
    	$this->_connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
    	#$this->_connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    }
    /*
    public function raw_query($sql)
    {
    	do {
    		$retry = false;
    		$tries = 0;
	    	try {
	        	$result = $this->getConnection()->query($sql);
	    	} catch (PDOException $e) {
	    		if ($e->getMessage()=='SQLSTATE[HY000]: General error: 2013 Lost connection to MySQL server during query') {
	    			$retry = true;
	    		} else {
	    			throw $e;
	    		}
	    		$tries++;
	    	}
    	} while ($retry && $tries<10);
        
        return $result;
    }
    
    public function raw_fetchRow($sql, $field=null)
    {
        if (!$result = $this->raw_query($sql)) {
            return false;
        }
        if (!$row = $result->fetch(PDO::FETCH_ASSOC)) {
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
    	$result = $this->raw_query($sql);
    	return $result;
    }

	public function dropForeignKey($table, $fk)
	{
        $create = $this->raw_fetchRow("show create table `$table`", 'Create Table');
        if (strpos($create, "CONSTRAINT `$fk` FOREIGN KEY (")!==false) {
            return $this->raw_query("ALTER TABLE `$table` DROP FOREIGN KEY `$fk`");
        }
        return true;
	}
	
	public function dropKey($table, $key)
	{
	    $create = $this->raw_fetchRow("show create table `$table`", 'Create Table');
        if (strpos($create, "KEY `$key` (")!==false) {
            return $this->raw_query("ALTER TABLE `$table` DROP KEY `$key`");
        }
        return true;
	}
*/
}