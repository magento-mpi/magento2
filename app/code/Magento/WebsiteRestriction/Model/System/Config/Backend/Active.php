<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cleanup blocks HTML cache
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_WebsiteRestriction_Model_System_Config_Backend_Active extends Magento_Core_Model_Config_Value
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'magento_websiterestriction_config_active';

    /**
     * Cleanup blocks HTML cache if value has been changed
     *
     * @return Magento_WebsiteRestriction_Model_System_Config_Backend_Active
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            Mage::app()->cleanCache(array(Magento_Core_Model_Store::CACHE_TAG, Magento_Cms_Model_Block::CACHE_TAG));
        }
        return parent::_afterSave();
    }
}
