<?php
/**
 * Website
 *
 * @package    Ecom
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Website extends Varien_Data_Object
{
    public function setCode($code)
    {
        $this->setData('code', $code);
        
        $config = $this->getConfig();
        $this->setId((int)$config->id);
        $this->setLanguage((string)$config->language);
        $this->setGroup((string)$config->group);
        
        return $this;
    }
    
    public function getId()
    {
        if ($this->getWebsiteId()) {
            return $this->getWebsiteId();
        }
        return (int) $this->getConfig()->id;
    }
    
    public function getResource()
    {
        static $resource;
        if (!$resource) {
            $resource = Mage::getModel('core_resource', 'website');
        }
        return $resource;
    }
    
    public function load($websiteId)
    {
        $this->setData($this->getResource()->load($websiteId));
        return $this;
    }
    
    public function getConfig()
    {
        return Mage::getConfig()->getWebsiteConfig($this->getCode());
    }
}