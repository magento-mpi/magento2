<?php
/**
 * Store switcher block
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Block_Store_Switcher extends Mage_Core_Block_Template
{
    public function getStoreCount()
    {
        return $this->getStores()->getSize();
    }
    
    public function getStores()
    {
        $stores = $this->getData('stores');
        if (is_null($stores)) {
            $stores = Mage::getSingleton('core/store')
                ->getWebsite()
                    ->getStoreCollection()
                        ->load();
            $this->setData('stores', $stores);
        }
        
        return $stores;
    }
    
    public function getCurrentStoreId()
    {
        return Mage::getSingleton('core/store')->getId();
    }
}
