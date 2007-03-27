<?php

class Mage_Core_Model_Mysql4_Config extends Mage_Core_Model_Mysql4 
{
    protected $_table=null;
    
    function __construct()
    {
        parent::__construct();
        $this->_table = $this->_getTableName('core', 'config');
    }
    
    function getValueByKey($key)
    {
        return $this->_read->fetchOne("select config_value from $this->_table where config_key=?", $key);
    }
    
    function getValuesByModule($module)
    {
        return $this->_read->fetchAssoc("select config_key, config_value from $this->_table where config_module=?", $module);
    }
    
    function updateXmlFromDb($xmlRoot)
    {
        
        
    }
    
}