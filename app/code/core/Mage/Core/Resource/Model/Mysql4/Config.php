<?php

class Mage_Core_Resource_Model_Mysql4_Config extends Mage_Core_Resource_Model_Mysql4 
{
    protected $_table=null;
    
    function __construct()
    {
        parent::__construct();
        $this->_table = Mage::registry('resources')->getTableName('core', 'config');
    }
    
    function getValueByKey($key)
    {
        return $this->_read->fetchOne("select config_value from $this->_table where config_key=?", $key);
    }
    
    function getValuesByModule($module)
    {
        return $this->_read->fetchAssoc("select config_key, config_value from $this->_table where config_module=?", $module);
    }
    
    public function updateXmlFromDb($xmlRoot)
    {
        $dbConfig = $this->_read->fetchAssoc("select config_key, config_value from $this->_table");
        foreach ($dbConfig as $key=>$value) {
            Mage::getConfig()->setKeyValue($key, $value['config_value']);
        }
        
    }
    
}