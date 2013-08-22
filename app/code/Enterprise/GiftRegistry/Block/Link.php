<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Front end helper block to add links
 */
class Enterprise_GiftRegistry_Block_Link extends Mage_Page_Block_Link_Current
{
    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if (Mage::helper('Enterprise_GiftRegistry_Helper_Data')->isEnabled()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
