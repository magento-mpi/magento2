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
        $stores = Mage::getConfig()->getNode('stores')->children();
        $storeCodes = array();
        foreach ($stores as $storeCode=>$storeConfig) {
            if ($this->getCode()===(string)$storeConfig->system->website->id) {
                $storeCodes[] = $storeCode;
            }
        }
        return $storeCodes;
    }
    
}
