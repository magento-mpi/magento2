<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Frontend helper block to add links
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 */
class Enterprise_Checkout_Block_Customer_Link extends Mage_Page_Block_Link_Current
{
    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if (Mage::helper('Enterprise_Checkout_Helper_Data')->isSkuApplied()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
