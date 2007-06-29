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
    protected $_configCache = array();
    
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
        return (int) $this->getConfig('core/id');
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
        
        $id = (int)$this->getConfig('core/id');
        if ($id) {
            $this->setId($id);
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
    public function getConfig($sectionVar='')
    {
        if (isset($this->_configCache[$sectionVar])) {
            return $this->_configCache[$sectionVar];
        }
        
        $sectionArr = explode('/', $sectionVar);
        
        if (empty($sectionArr[0])) {
            $result = Mage::getConfig()->getNode('global/websites/'.$this->getCode());
        } else {
        
            $result = Mage::getConfig()->getNode('global/websites/'.$this->getCode().'/'.$sectionArr[0]);
            $defaultConfig = Mage::getConfig()->getNode('global/default/'.$sectionArr[0]);
            
            if (!$result || $result->is('default')) {
                if (isset($sectionArr[1])) {
                    if (!empty($defaultConfig)) {
                        return $defaultConfig->{$sectionArr[1]};
                    }
                    $result = false;
                } else {
                    $result = $defaultConfig;
                }
            } elseif (isset($sectionArr[1])) {
                if (!$config->{$sectionArr[1]}) {
                    if (!empty($defaultConfig)) {
                        $result = $defaultConfig->{$sectionArr[1]};
                    } else {
                        $result = false;
                    }
                } else {
                    $result = $config->$sectionArr[1];
                }
            }
        }
        
        $this->_configCache[$sectionVar] = $result;
        return $result;
    }
    
    public function getStoreCodes()
    {
        $stores = Mage::getConfig()->getNode('global/stores')->children();
        $storeCodes = array();
        foreach ($stores as $storeCode=>$storeConfig) {
            if ($this->getCode()===(string)$storeConfig->core->website) {
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
