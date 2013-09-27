<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCardAccount_Block_Adminhtml_Sales_Order_Create_Payment extends Magento_Core_Block_Template
{
    /**
     * Gift card account data
     *
     * @var Magento_GiftCardAccount_Helper_Data
     */
    protected $_giftCardAccountData = null;

    /**
     * @var Magento_Adminhtml_Model_Sales_Order_Create
     */
    protected $_orderCreate = null;

    /**
     * @param Magento_GiftCardAccount_Helper_Data $giftCardAccountData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Adminhtml_Model_Sales_Order_Create $orderCreate
     * @param array $data
     */
    public function __construct(
        Magento_GiftCardAccount_Helper_Data $giftCardAccountData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Adminhtml_Model_Sales_Order_Create $orderCreate,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_giftCardAccountData = $giftCardAccountData;
        $this->_orderCreate = $orderCreate;
    }

    /**
     * Retrieve order create model
     *
     * @return Magento_Adminhtml_Model_Sales_Order_Create
     */
    protected function _getOrderCreateModel()
    {
        return $this->_orderCreate;
    }

    public function getGiftCards()
    {
        $result = array();
        $quote = $this->_orderCreate->getQuote();
        $cards = $this->_giftCardAccountData->getCards($quote);
        foreach ($cards as $card) {
            $result[] = $card['c'];
        }
        return $result;
    }

    /**
     * Check whether quote uses gift cards
     *
     * @return bool
     */
    public function isUsed()
    {
        $quote = $this->_getOrderCreateModel()->getQuote();

        return ($quote->getGiftCardsAmount() > 0);
    }


    public function isFullyPaid()
    {
        $quote = $this->_orderCreate->getQuote();
        if (!$quote->getGiftCardsAmount()
            || $quote->getBaseGrandTotal() > 0
            || $quote->getCustomerBalanceAmountUsed() > 0
        ) {
            return false;
        }

        return true;
    }
}
