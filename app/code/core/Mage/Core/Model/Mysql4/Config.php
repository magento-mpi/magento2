<?php

class Mage_Core_Model_Mysql4_Config extends Mage_Core_Model_Resource_Abstract
{
    protected $_read = null;
    protected $_write = null;
    protected $_configTable = null;
    
    function __construct()
    {
        $this->_init('core/config', 'config_id');
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