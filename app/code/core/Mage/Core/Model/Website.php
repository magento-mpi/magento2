<?php
/**
 * Store
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Website extends Varien_Object
{
    /**
     * Get website id
     *
     * @return int
     */
    public function getId()
    {
        if ($this->getWebsiteId()) {
            return $this->getWebsiteId();
        }
        return (int) $this->getConfig()->id;
    }
    
    /**
     * Set website code
     *
     * @param   string $code
     * @return  Mage_Core_Model_Store
     */
    public function setCode($code)
    {
        $this->setData('code', $code);
        
        $config = $this->getConfig();
        if ($config) {
            $this->setId((int)$config->id);
            Mage::dispatchEvent('setWebsiteCode', array('website'=>$this));
        }
        return $this;
    }
    
    /**
     * Get website config data
     *
     * @param string $section
     * @return mixed
     */
    public function getConfig($section='general')
    {
        if (empty($section)) {
            return Mage::getConfig()->getNode('global/websites/'.$this->getCode());
        }
        
        $config = Mage::getConfig()->getNode('global/websites/'.$this->getCode().'/'.$section);
        if (!$config || $config->is('default')) {
            $config = Mage::getConfig()->getNode('global/default/config/'.$section);
        }
        return $config;
    }
    
    public function getStoreCodes()
    {
        $stores = Mage::getConfig()->getNode('global/stores')->children();
        $storeCodes = array();
        foreach ($stores as $storeCode=>$storeConfig) {
            if ($this->getCode()===(string)$storeConfig->general->website) {
                $storeCodes[] = $storeCode;
            }
        }
        return $storeCodes;
    }
    
    /**
     * Get website resource model
     *
     * @return mixed
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('core/website');
    }
    
    /**
     * Load website data
     *
     * @param   int $websiteId
     * @return  Mage_Core_Model_Website
     */
    public function load($websiteId)
    {
        $this->setData($this->getResource()->load($websiteId));
        return $this;
    }
}
