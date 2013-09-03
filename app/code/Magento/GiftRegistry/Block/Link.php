<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Front end helper block to add links
 */
class Magento_GiftRegistry_Block_Link extends Magento_Page_Block_Link_Current
{
    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if (Mage::helper('Magento_GiftRegistry_Helper_Data')->isEnabled()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
