<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multishipping cart link
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Multishipping_Link extends Magento_Core_Block_Template
{
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/multishipping', array('_secure'=>true));
    }

    public function getQuote()
    {
        return Mage::getSingleton('Mage_Checkout_Model_Session')->getQuote();
    }

    public function _toHtml()
    {
        if (!Mage::helper('Mage_Checkout_Helper_Data')->isMultishippingCheckoutAvailable()){
            return '';
        }

        return parent::_toHtml();
    }
}
