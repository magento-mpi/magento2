<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml additional helper block for product configuration
 *
 * @category   Magento
 * @package    Magento_GiftWrapping
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Product\Helper\Form;

class Config
    extends \Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Config
{
    /**
     * Get config value data
     *
     * @return mixed
     */
    protected function _getValueFromConfig()
    {
        return \Mage::helper('Magento\GiftWrapping\Helper\Data')->isGiftWrappingAvailableForItems();
    }
}
