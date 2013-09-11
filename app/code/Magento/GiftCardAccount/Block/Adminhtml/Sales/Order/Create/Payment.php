<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Block\Adminhtml\Sales\Order\Create;

class Payment extends \Magento\Core\Block\Template
{
    /**
     * Retrieve order create model
     *
     * @return \Magento\Adminhtml\Model\Sales\Order\Create
     */
    protected function _getOrderCreateModel()
    {
        return \Mage::getSingleton('Magento\Adminhtml\Model\Sales\Order\Create');
    }

    public function getGiftCards()
    {
        $result = array();
        $quote = $this->_getOrderCreateModel()->getQuote();
        $cards = \Mage::helper('Magento\GiftCardAccount\Helper\Data')->getCards($quote);
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
        $quote = $this->_getOrderCreateModel()->getQuote();
        if (!$quote->getGiftCardsAmount() || $quote->getBaseGrandTotal() > 0 || $quote->getCustomerBalanceAmountUsed() > 0) {
            return false;
        }

        return true;
    }
}
