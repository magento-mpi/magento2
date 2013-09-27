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
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_GiftCardAccount_Helper_Data $giftCardAccountData
     * @param Magento_Sales_Model_Config $salesConfig
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Config $coreConfig,
        Magento_Customer_Model_Session $customerSession,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_GiftCardAccount_Helper_Data $giftCardAccountData,
        Magento_Sales_Model_Config $salesConfig,
        array $data = array()
    ) {
        $this->_giftCardAccountData = $giftCardAccountData;
        parent::__construct($catalogData, $coreData, $context, $coreConfig, $customerSession, $checkoutSession,
            $storeManager, $salesConfig, $data);
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
