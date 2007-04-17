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
    public function __construct() 
    {
        
    }
        
    public function load($website)
    {
        $this->setData(Mage::getModel('core_resource', 'website')->load($website));
    }
    
    public function getConfig()
    {
        return Mage::getConfig()->getWebsiteConfig($this->getWebsiteCode());
    }
    
    public function getId()
    {
        return $this->getWebsiteId();
    }

    public function getDomain()
    {
        return (string)$this->getConfig()->group;
    }

    public function getLanguage()
    {
        return (string)$this->getConfig()->language;
    }
}