<?php
class Mage_Permissions_Model_Mysql4_Users_Collection extends Varien_Data_Collection_Db {
	protected $_usersTable;
	protected $_roleTable;
	protected $_ruleTable;
	protected $_usersRelTable;

    public function __construct()
    {
        $resources = Mage::getSingleton('core/resource');

        parent::__construct($resources->getConnection('tag_read'));

        $this->_usersTable        = $resources->getTableName('permissions/admin_user');
        $this->_roleTable         = $resources->getTableName('permissions/admin_role');
        $this->_ruleTable         = $resources->getTableName('permissions/admin_rule');
        $this->_usersRelTable	  = $resources->getTableName('permissions/admin_users_in_roles');

        $this->_sqlSelect->from($this->_usersTable);
    }

    public function addRoleFilter($roleId)
    {
    	$this->_sqlSelect->where("{$this->_roleTable}.parent_id={$roleId}");
    	return $this;
    }
}
?>