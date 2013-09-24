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

    /**
     * @var Magento_GiftCardAccount_Helper_Data|null
     */
    protected $_giftCardAccountData = null;

    /**
     * Checkout session
     *
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession = null;

    /**
     * @param Magento_GiftCardAccount_Helper_Data $giftCardAccountData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Magento_GiftCardAccount_Helper_Data $giftCardAccountData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Config $coreConfig,
        Magento_Checkout_Model_Session $checkoutSession,
        array $data = array()
    ) {
        parent::__construct($catalogData, $coreData, $context, $coreConfig, $data);
        $this->_giftCardAccountData = $giftCardAccountData;
        $this->_checkoutSession = $checkoutSession;
    }

    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    public function getQuoteGiftCards()
    {
        return $this->_giftCardAccountData->getCards($this->getQuote());
    }
}
