<?php
class Mage_Permissions_Model_Mysql4_Rules_Collection extends Varien_Data_Collection_Db {
	protected $_usersTable;
	protected $_roleTable;
	protected $_ruleTable;
	
    public function __construct() {
        $resources = Mage::getSingleton('core/resource');
        
        parent::__construct($resources->getConnection('tag_read'));
        
        $this->_usersTable        = $resources->getTableName('permissions/admin_user');
        $this->_roleTable         = $resources->getTableName('permissions/admin_role');
        $this->_ruleTable         = $resources->getTableName('permissions/admin_rule');
        /*
        $this->_sqlSelect->from($this->_tagTable, array('total' => "COUNT(*)", $this->_tagTable.'.*'))
            ->join($this->_tagRelTable, $this->_tagTable.'.tag_id='.$this->_tagRelTable.'.tag_id')
            ->group($this->_tagRelTable.'.tag_id');

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('tag/tag'));
        */
        
        $this->_sqlSelect->from($this->_ruleTable);
    }
    
    public function getByRoles($id) {
    	$id = $id ? $id : 0; 
       	$row = $this->_sqlSelect->where("role_id = {$id}");        	
       	return $this;
    }
}
?>