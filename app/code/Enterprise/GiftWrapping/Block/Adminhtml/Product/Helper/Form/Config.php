<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml additional helper block for product configuration
 *
 * @category   Enterprise
 * @package    Enterprise_GiftWrapping
 */
class Enterprise_GiftWrapping_Block_Adminhtml_Product_Helper_Form_Config
    extends Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Config
{
    /**
     * Get config value data
     *
     * @return mixed
     */
    protected function _getValueFromConfig()
    {
        return Mage::helper('Enterprise_GiftWrapping_Helper_Data')->isGiftWrappingAvailableForItems();
    }
}
