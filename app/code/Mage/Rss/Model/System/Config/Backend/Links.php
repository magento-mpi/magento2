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
class Mage_Rss_Model_System_Config_Backend_Links extends Magento_Core_Model_Config_Data
{
    /**
     * Invalidate cache type, when value was changed
     *
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            Mage::app()->getCacheInstance()->invalidateType(Magento_Core_Block_Abstract::CACHE_GROUP);
        }
    }

}
