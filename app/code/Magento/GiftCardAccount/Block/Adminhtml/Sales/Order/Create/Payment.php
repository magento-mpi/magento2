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
     * Gift card account data
     *
     * @var \Magento\GiftCardAccount\Helper\Data
     */
    protected $_giftCardAccountData = null;

    /**
     * @var \Magento\Adminhtml\Model\Sales\Order\Create
     */
    protected $_orderCreate = null;

    /**
     * @param \Magento\GiftCardAccount\Helper\Data $giftCardAccountData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Adminhtml\Model\Sales\Order\Create $orderCreate
     * @param array $data
     */
    public function __construct(
        \Magento\GiftCardAccount\Helper\Data $giftCardAccountData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Adminhtml\Model\Sales\Order\Create $orderCreate,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_giftCardAccountData = $giftCardAccountData;
        $this->_orderCreate = $orderCreate;
    }

    /**
     * Retrieve order create model
     *
     * @return \Magento\Adminhtml\Model\Sales\Order\Create
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
