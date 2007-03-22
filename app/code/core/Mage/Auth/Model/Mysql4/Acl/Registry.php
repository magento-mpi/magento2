<?php

class Mage_Auth_Model_Mysql4_Acl_Registry extends Mage_Auth_Model_Mysql4
{
    function load()
    {
        $aclTable = $this->_getTableName('auth', 'acl');
        $aclSerialized = $this->_read->select()->from($aclTable)->fetchRow();
        $aclData = unserialize($aclSerialized);
        return $aclData;
    }
    
    function save(Mage_Acl_Registry $acl)
    {
        $aclTable = $this->_getTableName('auth', 'acl');
        $aclSerialized = serialize($acl);
        if ($this->load()) {
            $aclData = array('acl_serialized'=>$aclSerialized);
            $this->_write->update($aclTable, $aclData, "acl_id=1");
        } else {
            $aclData = array('acl_id'=>1, 'acl_serialized'=>$aclSerialized);
            $this->_write->insert($aclTable, $aclData);
        }
        return $this;
    }
}