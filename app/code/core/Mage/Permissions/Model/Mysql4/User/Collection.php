<?php
class Mage_Permissions_Model_Mysql4_User_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('permissions/user');
    }

}
