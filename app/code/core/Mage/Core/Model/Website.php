<?php
/**
 * Store
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Website extends Mage_Core_Model_Abstract
{
    protected $_configCache = array();
    
    public function _construct()
    {
        $this->_init('core/website');
    }
    
    public function load($id, $field=null)
    {
        if (!is_numeric($id) && is_null($field)) {
            $this->getResource()->load($this, $id, 'code');
            return $this;
        }
        return parent::load($id, $field);
    }
    
    /**
     * Get website config data
     *
     * @param string $path
     * @return mixed
     */
    public function getConfig($path) {
        if (!isset($this->_configCache[$path])) {

            $config = Mage::getConfig()->getNode('websites/'.$this->getCode().'/'.$path);
            if (!$config) {
            	return false;
                #throw Mage::exception('Mage_Core', 'Invalid websites configuration path: '.$path);
            }
            if (!$config->children()) {
                $value = (string)$config;
            } else {
                $value = array();
                foreach ($config->children() as $k=>$v) {
                    $value[$k] = $v;
                }
            }
            $this->_configCache[$path] = $value;
        }
        return $this->_configCache[$path];
    }
    
    public function getStoreCodes()
    {
        $stores = Mage::getConfig()->getNode('stores')->children();
        $storeCodes = array();
        foreach ($stores as $storeCode=>$storeConfig) {
            if ($this->getCode()===$storeCode) {
                $storeCodes[] = $storeCode;
            }
        }
        return $storeCodes;
    }
    
    public function getStoreCollection() 
    {
		return $this->_storesCollection = Mage::getResourceModel('core/store_collection')
			->addWebsiteFilter($this->getId());
    }
    
    public function getStoresIds($notEmpty=false) 
    {
    	$ids = array();
    	
    	foreach ($this->getStoreCollection()->getItems() as $item) {
    		$ids[] = $item->getId();
    	}
    	
    	if(count($ids)== 0 && $notEmpty) {
    		$ids[] = 0;
    	}
    	
    	return $ids;
    }
}
