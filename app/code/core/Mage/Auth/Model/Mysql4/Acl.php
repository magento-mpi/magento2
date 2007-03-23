<?php

class Mage_Auth_Model_Mysql4_Acl extends Mage_Auth_Model_Mysql4
{
    function load()
    {
        $aclTable = $this->_getTableName('auth_setup', 'acl');
        $aclArr = $this->_read->fetchRow($this->_read->select()->from($aclTable));
        $aclData = unserialize($aclArr['acl_serialized']);
        return $aclData;
    }
    
    function save(Zend_Acl $acl)
    {
        $aclTable = $this->_getTableName('auth_setup', 'acl');
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