<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multishipping cart link
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Multishipping_Link extends Magento_Core_Block_Template
{
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/multishipping', array('_secure'=>true));
    }

    public function getQuote()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote();
    }

    public function _toHtml()
    {
        if (!Mage::helper('Magento_Checkout_Helper_Data')->isMultishippingCheckoutAvailable()){
            return '';
        }

        return parent::_toHtml();
    }
}
