<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Order\Create;

/**
 * Gift wrapping order create info block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Info extends \Magento\GiftWrapping\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->canDisplayGiftWrappingForOrder() ? parent::_toHtml() : '';
    }

    /**
     * Select element for choosing gift wrapping design
     *
     * @param string $type 'quote', 'quote_item', etc
     * @throws \InvalidArgumentException
     * @return array
     */
    public function getDesignSelectHtml($type)
    {
        if (empty($type)) {
            throw new \InvalidArgumentException('type is obligatory');
        }

        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            ['id' => 'giftwrapping_design', 'class' => 'select']
        )->setName(
            'giftwrapping[' . $type . '][' . $this->getEntityId() . '][design]'
        )->setOptions(
            $this->getDesignCollection()->toOptionArray()
        );
        return $select->getHtml();
    }

    /**
     * Retrieve wrapping design from current quote
     *
     * @return int
     */
    public function getWrappingDesignValue()
    {
        return (int)$this->getQuote()->getGwId();
    }

    /**
     * Retrieve wrapping gift receipt from current quote
     *
     * @return int
     */
    public function getWrappingGiftReceiptValue()
    {
        return (int)$this->getQuote()->getGwAllowGiftReceipt();
    }

    /**
     * Retrieve wrapping printed card from current quote
     *
     * @return int
     */
    public function getWrappingPrintedCardValue()
    {
        return (int)$this->getQuote()->getGwAddCard();
    }

    /**
     * Check ability to display both prices for printed card in shopping cart
     *
     * @return bool
     */
    public function getDisplayCardBothPrices()
    {
        return $this->_giftWrappingData->displayCartCardBothPrices($this->getStoreId());
    }

    /**
     * Check ability to display prices including tax for printed card in shopping cart
     *
     * @return bool
     */
    public function getDisplayCardPriceInclTax()
    {
        return $this->_giftWrappingData->displayCartCardIncludeTaxPrice($this->getStoreId());
    }

    /**
     * Check allow printed card
     *
     * @return bool
     */
    public function getAllowPrintedCard()
    {
        return $this->_giftWrappingData->allowPrintedCard($this->getStoreId());
    }

    /**
     * Check allow gift receipt
     *
     * @return bool
     */
    public function getAllowGiftReceipt()
    {
        return $this->_giftWrappingData->allowGiftReceipt($this->getStoreId());
    }

    /**
     * Check ability to display gift wrapping during backend order create
     *
     * @return bool
     */
    public function canDisplayGiftWrappingForOrder()
    {
        return ($this->_giftWrappingData->isGiftWrappingAvailableForOrder(
            $this->getStoreId()
        ) || $this->getAllowPrintedCard() || $this->getAllowGiftReceipt()) && !$this->getQuote()->isVirtual();
    }

    /**
     * Checking for gift wrapping for the entire Order
     *
     * @return bool
     */
    public function isGiftWrappingForEntireOrder()
    {
        return $this->_giftWrappingData->isGiftWrappingAvailableForOrder($this->getStoreId());
    }
}
