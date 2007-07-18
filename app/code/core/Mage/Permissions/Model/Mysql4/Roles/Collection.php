<?php
class Mage_Permissions_Model_Mysql4_Roles_Collection extends Varien_Data_Collection_Db
{
	protected $_usersTable;
	protected $_roleTable;
	protected $_ruleTable;

    public function __construct()
    {
        $resources = Mage::getSingleton('core/resource');

        parent::__construct($resources->getConnection('tag_read'));

        $this->_usersTable        = $resources->getTableName('permissions/admin_user');
        $this->_roleTable         = $resources->getTableName('permissions/admin_role');
        $this->_ruleTable         = $resources->getTableName('permissions/admin_rule');

        $this->_sqlSelect->from($this->_roleTable, '*');
        $this->_sqlSelect->where("{$this->_roleTable}.role_type='G'");
    }

    public function toOptionArray()
    {
	   return $this->_toOptionArray('role_id', 'role_name');
    }

    public function addTreeOrder()
    {
        $this->_sqlSelect->order(array("parent_id", "role_id"));
        return $this;
    }
}
?>