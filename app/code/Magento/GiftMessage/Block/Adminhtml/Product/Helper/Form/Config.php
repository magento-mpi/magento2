<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml additional helper block for product configuration
 *
 * @category   Magento
 * @package    Magento_GiftMessage
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftMessage\Block\Adminhtml\Product\Helper\Form;

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
        return \Mage::getStoreConfig(\Magento\GiftMessage\Helper\Message::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS);
    }
}
