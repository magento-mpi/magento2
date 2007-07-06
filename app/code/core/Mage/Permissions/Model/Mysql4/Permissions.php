<?php
class Mage_Permissions_Model_Mysql4_Permissions {
	protected $_usersTable;
	protected $_roleTable;
	protected $_ruleTable;
	
    /**
     * Read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * Write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;
    
    public function __construct() {
        $resources = Mage::getSingleton('core/resource');
        
        $this->_usersTable        = $resources->getTableName('permissions/admin_user');
        $this->_roleTable         = $resources->getTableName('permissions/admin_role');
        $this->_ruleTable         = $resources->getTableName('permissions/admin_rule');
        
        $this->_read    = $resources->getConnection('permissions_read');
        $this->_write   = $resources->getConnection('permissions_write');
    }
    
    public function load() {
        
    }
    
    public function save() {    	
       
    }
}
?>