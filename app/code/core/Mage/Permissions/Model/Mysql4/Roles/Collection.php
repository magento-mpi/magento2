<?php
class Mage_Permissions_Model_Mysql4_Roles_Collection extends Varien_Data_Collection_Db {
	protected $_usersTable;
	protected $_roleTable;
	protected $_ruleTable;
	protected $_usersRelTable;
	
    public function __construct() {
        $resources = Mage::getSingleton('core/resource');
        
        parent::__construct($resources->getConnection('tag_read'));
        
        $this->_usersTable        = $resources->getTableName('permissions/admin_user');
        $this->_roleTable         = $resources->getTableName('permissions/admin_role');
        $this->_ruleTable         = $resources->getTableName('permissions/admin_rule');
        $this->_usersRelTable	  = $resources->getTableName('permissions/admin_users_in_roles');
        /*
        $this->_sqlSelect->from($this->_tagTable, array('total' => "COUNT(*)", $this->_tagTable.'.*'))
            ->join($this->_tagRelTable, $this->_tagTable.'.tag_id='.$this->_tagRelTable.'.tag_id')
            ->group($this->_tagRelTable.'.tag_id');

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('tag/tag'));
        */
        
        $this->_sqlSelect->from($this->_roleTable, '*');
        $this->_sqlSelect->where("{$this->_roleTable}.role_type='G'");
    }
    
    public function addUserRel($uid) {
    	if (empty($uid)) return $this;
    	
    	$this->_sqlSelect->joinLeft($this->_usersRelTable, "{$this->_usersRelTable}.role_id={$this->_roleTable}.role_id AND {$this->_usersRelTable}.user_id = {$uid}", "IF({$this->_usersRelTable}.id IS NULL, 0, 1) AS is_checked");
    	
    	return $this;
    }
    
    public function toOptionArray() {
	        return $this->_toOptionArray('role_id', 'role_name');
    }
}
?>