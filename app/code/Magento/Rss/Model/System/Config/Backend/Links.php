<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cache cleaner backend model
 *
 */
class Magento_Rss_Model_System_Config_Backend_Links extends Magento_Core_Model_Config_Value
{
    /**
     * Invalidate cache type, when value was changed
     *
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            /** @var Magento_Core_Model_Cache_TypeListInterface $cacheTypeList */
            $cacheTypeList = Mage::getObjectManager()->get('Magento_Core_Model_Cache_TypeListInterface');
            $cacheTypeList->invalidate(Magento_Core_Block_Abstract::CACHE_GROUP);
        }
    }

}
