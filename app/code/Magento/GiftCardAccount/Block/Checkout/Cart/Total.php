<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_GiftCardAccount_Block_Checkout_Cart_Total extends Magento_Checkout_Block_Total_Default
{
    protected $_template = 'Magento_GiftCardAccount::cart/total.phtml';

    public function getQuote()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote();
    }

    public function getQuoteGiftCards()
    {
        return Mage::helper('Magento_GiftCardAccount_Helper_Data')->getCards($this->getQuote());
    }
}
