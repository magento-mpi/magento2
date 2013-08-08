<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System config translate inline fields backend model
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Translate extends Magento_Core_Model_Config_Data
{
    /**
     * Path to config node with list of caches
     *
     * @var string
     */
    const XML_PATH_INVALID_CACHES = 'dev/translate_inline/invalid_caches';

    /**
     * Set status 'invalidate' for blocks and other output caches
     *
     * @return Mage_Backend_Model_Config_Backend_Translate
     */
    protected function _afterSave()
    {
        $types = array_keys(Mage::getStoreConfig(self::XML_PATH_INVALID_CACHES));
        if ($this->isValueChanged()) {
            Mage::app()->getCacheInstance()->invalidateType($types);
        }

        return $this;
    }
}
