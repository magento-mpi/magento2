<?php

class Mage_Log_Model_Mysql4_Visitor 
{
    /**
     * Visitor data table name
     *
     * @var string
     */
    protected $_visitorTable;
    
    /**
     * Database read connection
     * 
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;
    
    /**
     * Database write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;
    
    /**
     * Open session
     *
     * @param string $savePath ignored
     * @param string $sessName ignored
     * @return boolean
     */
    public function __construct()
    {
        $this->_visitorTable = Mage::registry('resources')->getTableName('log_resource', 'visitor');
        $this->_read = Mage::registry('resources')->getConnection('log_read');
        $this->_write = Mage::registry('resources')->getConnection('log_write');
    }
    
    public function load($sessId)
    {
    	$data = array();
    	if ($this->_read) {
    		$data = $this->_read->fetchRow("SELECT * FROM $this->_visitorTable WHERE session_id = ?", array($sessId));
    	}
        return $data;
    }
    
    public function save(Mage_Log_Model_Visitor $visitor)
    {
        $sessId = $visitor->getSessionId();
		if ($this->_write) {
	        $exists = $this->_write->fetchOne("SELECT session_id FROM $this->_visitorTable WHERE session_id = ?", array($sessId));
	        if ($exists) {
	            $where = $this->_write->quoteInto('session_id=?', $sessId);
	            $this->_write->update($this->_visitorTable, $visitor->getData(), $where);
	        } else {
	            $this->_write->insert($this->_visitorTable, $visitor->getData());
	        }
		}
        
        return true;
    }
    
    public function delete(Mage_Log_Model_Visitor $visitor)
    {
        $this->_write->query("DELETE FROM $this->_visitorTable WHERE session_id = ?", $visitor->getSessionId());
        
        return true;
    }
}