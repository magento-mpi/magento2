<?php

class Mage_Core_Model_Mysql4_Config
{
    protected static $_read = null;
    protected static $_write = null;
    protected static $_configTable = null;
    
    function __construct()
    {
        self::$_read = Mage::getSingleton('core/resource')->getConnection('core_read');
        self::$_write = Mage::getSingleton('core/resource')->getConnection('core_write');
        self::$_configTable = Mage::getSingleton('core/resource')->getTableName('core_resource', 'config');
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
        if (self::$_read) {
            $dbConfig = self::$_read->fetchAssoc("select config_key, config_value from ".self::$_configTable);
            foreach ($dbConfig as $path=>$value) {
                Mage::getConfig()->setNode($path, $value['config_value']);
            }
        }
    }
    
}