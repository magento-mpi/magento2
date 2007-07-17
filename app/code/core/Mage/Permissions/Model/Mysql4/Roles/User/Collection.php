<?php
class Mage_Permissions_Model_Mysql4_Roles_User_Collection extends Varien_Data_Collection_Db
{

	protected $_roleTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('tag_read'));

        $this->_roleTable = Mage::getSingleton('core/resource')->getTableName('permissions/admin_role');
        $this->_sqlSelect->from($this->_roleTable, '*');
        $this->_sqlSelect->where("{$this->_roleTable}.role_type='U'");
    }

    public function setRoleFilter($roleId)
    {
        $this->_sqlSelect->where("{$this->_roleTable}.parent_id = ?", $roleId);
    }
}
?>