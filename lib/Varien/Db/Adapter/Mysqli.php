<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Varien
 * @package    Varien_Db
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


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
        if (!extension_loaded('mysqli')) {
            throw new Zend_Db_Adapter_Exception('mysqli extension is not installed');
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

	    $port = !empty($this->_config['port']) ? $this->_config['port'] : null;
	    $socket = !empty($this->_config['unix_socket']) ? $this->_config['unix_socket'] : null;
	    // socket specified in host config
	    if (strpos($this->_config['host'], '/')!==false) {
	        $socket = $this->_config['host'];
	        $this->_config['host'] = null;
	    } elseif (strpos($this->_config['host'], ':')!==false) {
	        list($this->_config['host'], $port) = explode(':', $this->_config['host']);
	    }

#echo "<pre>".print_r($this->_config,1)."</pre>"; die;
		@$conn->real_connect(
		    $this->_config['host'],
		    $this->_config['username'],
		    $this->_config['password'],
		    $this->_config['dbname'],
		    $port,
		    $socket
		);
        if (mysqli_connect_errno()) {
            throw new Zend_Db_Adapter_Mysqli_Exception(mysqli_connect_error());
        }

        $this->_connection = $conn;
    }

    public function raw_query($sql)
    {
    	do {
    		$retry = false;
    		$tries = 0;
	    	try {
	        	$result = $this->getConnection()->query($sql);
	    	}
	    	catch (Exception $e) {
	    		if ($e->getMessage()=='SQLSTATE[HY000]: General error: 1205 Lock wait timeout exceeded; try restarting transaction') {
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
	    $this->beginTransaction();
	    try {
		    if ($result = $this->getConnection()->store_result()) {
		    	$result->free_result();
		    }
			if ($this->getConnection()->multi_query($sql)) {
				do {
				    if ($result = $this->getConnection()->store_result()) {
				    	$result->free_result();
				    }
				    elseif($this->getConnection()->error) {
				        throw new Zend_Db_Adapter_Mysqli_Exception('Level3:'.$this->getConnection()->error);
				    }
				}
				while ($this->getConnection()->next_result());
				$this->commit();
			} else {
				throw new Zend_Db_Adapter_Mysqli_Exception('Level2:'.$this->getConnection()->error);
			}
	    } catch (Exception $e) {
			$this->rollback();
			throw $e;
	    }

		return true;
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
}