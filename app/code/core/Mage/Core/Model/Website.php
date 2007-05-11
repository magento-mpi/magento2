<?php
/**
 * Website
 *
 * @package    Ecom
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Website extends Varien_Object
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
        return Mage::getSingleton('core_resource', 'website');
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
    
    public function getArrCategoriesId()
    {
        $arr = array();
        // TODO: depended from website id
        $nodes = Mage::getModel('catalog_resource','category_tree')
            ->load(2,10) // TODO: from config
            ->getNodes();
        foreach ($nodes as $node) {
            $arr[] = $node->getId();
        }
        
        return $arr;
    }
}