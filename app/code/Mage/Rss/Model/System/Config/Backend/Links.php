<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cache cleaner backend model
 *
 */
class Mage_Rss_Model_System_Config_Backend_Links extends Mage_Core_Model_Config_Value
{
    /**
     * Invalidate cache type, when value was changed
     *
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            /** @var Mage_Core_Model_Cache_TypeListInterface $cacheTypeList */
            $cacheTypeList = Mage::getObjectManager()->get('Mage_Core_Model_Cache_TypeListInterface');
            $cacheTypeList->invalidate(Mage_Core_Block_Abstract::CACHE_GROUP);
        }
    }

}
