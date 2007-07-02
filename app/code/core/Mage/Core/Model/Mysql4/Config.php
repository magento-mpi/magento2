<?php

class Mage_Core_Model_Mysql4_Config
{
    protected $_read = null;
    protected $_write = null;
    protected $_configTable = null;
    
    function __construct()
    {
        $this->_read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $this->_configTable = Mage::getSingleton('core/resource')->getTableName('core/config');
    }
    
    function getValueByKey($key)
    {
        return $this->_read->fetchOne("select config_value from ".$this->_configTable." where config_key=?", $key);
    }
    
    function getValuesByModule($module)
    {
        return $this->_read->fetchAssoc("select config_key, config_value from ".$this->_configTable." where config_module=?", $module);
    }
    
    public function updateXmlFromDb($xmlRoot)
    {
        if ($this->_read) {
            $dbConfig = $this->_read->fetchAssoc("select config_key, config_value from ".$this->_configTable);
            foreach ($dbConfig as $path=>$value) {
                Mage::getConfig()->setNode($path, $value['config_value']);
            }
        }
    }
    
}