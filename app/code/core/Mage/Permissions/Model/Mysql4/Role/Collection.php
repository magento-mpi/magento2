<?php
class Mage_Permissions_Model_Mysql4_Role_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('permissions/role');
    }

    /**
     * Enter description here...
     *
     * @param int $userId
     * @return Mage_Permissions_Model_Mysql4_Role_Collection
     */
    public function setUserFilter($userId)
    {
        $this->addFieldToFilter('user_id', $userId);
        $this->addFieldToFilter('role_type', 'U');
        return $this;
    }

}
