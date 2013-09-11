<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\GiftCardAccount\Block\Checkout\Cart;

class Total extends \Magento\Checkout\Block\Total\DefaultTotal
{
    protected $_template = 'Magento_GiftCardAccount::cart/total.phtml';

    public function getQuote()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote();
    }

    public function getQuoteGiftCards()
    {
        return \Mage::helper('Magento\GiftCardAccount\Helper\Data')->getCards($this->getQuote());
    }
}
