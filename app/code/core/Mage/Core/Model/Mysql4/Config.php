<?php

class Mage_Core_Model_Mysql4_Config
{
    protected static $_read = null;
    protected static $_write = null;
    protected static $_configTable = null;
    
    function __construct()
    {
        self::$_read = Mage::registry('resources')->getConnection('core_read');
        self::$_write = Mage::registry('resources')->getConnection('core_write');
        self::$_configTable = Mage::registry('resources')->getTableName('core', 'config');
    }
    
    function getValueByKey($key)
    {
        return self::$_read->fetchOne("select config_value from ".self::$_configTable." where config_key=?", $key);
    }
    
    function getValuesByModule($module)
    {
        return self::$_read->fetchAssoc("select config_key, config_value from ".self::$_configTable." where config_module=?", $module);
    }
    
    public function updateXmlFromDb($xmlRoot)
    {
        $dbConfig = self::$_read->fetchAssoc("select config_key, config_value from ".self::$_configTable);
        foreach ($dbConfig as $key=>$value) {
            Mage::getConfig()->setKeyValue($key, $value['config_value']);
        }
        
    }
    
}