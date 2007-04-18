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
        
    public function load($website)
    {
        $row = Mage::getModel('core_resource', 'website')->load($website);
        if (!empty($row)) {
            $this->setData($row);
        }
        return $this;
    }
    
    public function getConfig()
    {
        return Mage::getConfig()->getWebsiteConfig($this->getWebsiteCode());
    }
    
    public function getId()
    {
        if($this->getWebsiteId())
        {
            return $this->getWebsiteId();
        }
        return 1;
    }

    public function getLanguage()
    {
        return $this->getLanguageCode();
    }
    
    public function getDomain()
    {
        return (string)$this->getConfig()->group;
    }
}