<?php

/**
 * Mysqi Resource
 * 
 * TODO: Write all functions (fetchOne, insert ...)
 *
 * @package    Ecom
 * @module     Core
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Resource_Db_Mysqli extends Mage_Core_Resource_Db
{
    /**
     * Mysqli object
     *
     * @var mysqli
     */
    private $_mysqli;
    
    /**
     * Class constructor
     *
     * @param array $config
     */
	public function __construct($config) 
	{
        parent::__construct();
        
		$this->setConfig($config);

		$this->_mysqli = new mysqli();
        $this->_mysqli->init();
        $this->_mysqli->options(MYSQLI_OPT_LOCAL_INFILE, true);
		$this->_mysqli->real_connect($config['host'], $config['username'], $config['password'], $config['dbname']);
		$this->setConnection($this);
	}
	
	function query($sql)
	{
		if ($this->_mysqli->multi_query($sql)) {
			do {
			    if ($result = $this->_mysqli->store_result()) {
			    	$result->free_result();
			    }
			}
			while ($this->_mysqli->next_result());
		}
	}
}