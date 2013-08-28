<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping order create info block
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftWrapping_Block_Adminhtml_Order_Create_Info
    extends Enterprise_GiftWrapping_Block_Adminhtml_Order_Create_Abstract
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
     * @return array
     */
    public function getDesignSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento_Core_Block_Html_Select')
            ->setData(array(
                'id'    => 'giftwrapping_design',
                'class' => 'select'
            ))
            ->setName('giftwrapping[' . $this->getEntityId() . '][design]')
            ->setOptions($this->getDesignCollection()->toOptionArray());
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
        return ($this->_giftWrappingData->isGiftWrappingAvailableForOrder($this->getStoreId())
            || $this->getAllowPrintedCard()
            || $this->getAllowGiftReceipt())
                && !$this->getQuote()->isVirtual();
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

    /**
     * Get url for ajax to refresh Gift Wrapping block
     *
     * @deprecated since 1.12.0.0
     *
     * @return void
     */
    public function getRefreshWrappingUrl()
    {
        return $this->getUrl('*/giftwrapping/orderOptions');
    }
}
