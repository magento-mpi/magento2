<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Enterprise_GiftCardAccount_Block_Checkout_Cart_Total extends Magento_Checkout_Block_Total_Default
{
    protected $_template = 'Enterprise_GiftCardAccount::cart/total.phtml';

    /**
     * Gift card account data
     *
     * @var Enterprise_GiftCardAccount_Helper_Data
     */
    protected $_giftCardAccountData = null;

    /**
     * @param Enterprise_GiftCardAccount_Helper_Data $giftCardAccountData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_GiftCardAccount_Helper_Data $giftCardAccountData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_giftCardAccountData = $giftCardAccountData;
        parent::__construct($context, $data);
    }

    public function getQuote()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote();
    }

    public function getQuoteGiftCards()
    {
        return $this->_giftCardAccountData->getCards($this->getQuote());
    }
}
