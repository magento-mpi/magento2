<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * GiftWrapping total calculator for quote
 *
 */
class Magento_GiftWrapping_Model_Total_Quote_Giftwrapping extends Magento_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * @var Magento_Core_Model_Store
     */
    protected $_store;

    /**
     * @var Magento_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * @var Magento_Sales_Model_Quote|Magento_Sales_Model_Quote_Address
     */
    protected $_quoteEntity;

    /**
     * Init total model, set total code
     */
    public function __construct()
    {
        $this->setCode('giftwrapping');
    }

    /**
     * Collect gift wrapping totals
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  Magento_GiftWrapping_Model_Total_Quote_Giftwrapping
     */
    public function collect(Magento_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        if ($address->getAddressType() != Magento_Sales_Model_Quote_Address::TYPE_SHIPPING) {
            return $this;
        }

        $this->_quote = $address->getQuote();
        $this->_store = $this->_quote->getStore();
        $quote = $this->_quote;
        if ($quote->getIsMultiShipping()) {
            $this->_quoteEntity = $address;
        } else {
            $this->_quoteEntity = $quote;
        }

        $this->_collectWrappingForItems($address)
            ->_collectWrappingForQuote($address)
            ->_collectPrintedCard($address);

        $address->setBaseGrandTotal(
            $address->getBaseGrandTotal()
            + $address->getGwItemsBasePrice()
            + $address->getGwBasePrice()
            + $address->getGwCardBasePrice()
        );
        $address->setGrandTotal(
            $address->getGrandTotal()
            + $address->getGwItemsPrice()
            + $address->getGwPrice()
            + $address->getGwCardPrice()
        );

        if ($quote->getIsNewGiftWrappingCollecting()) {
            $quote->setGwItemsBasePrice(0);
            $quote->setGwItemsPrice(0);
            $quote->setGwBasePrice(0);
            $quote->setGwPrice(0);
            $quote->setGwCardBasePrice(0);
            $quote->setGwCardPrice(0);
            $quote->setIsNewGiftWrappingCollecting(false);
        }
        $quote->setGwItemsBasePrice($address->getGwItemsBasePrice() + $quote->getGwItemsBasePrice());
        $quote->setGwItemsPrice($address->getGwItemsPrice() + $quote->getGwItemsPrice());
        $quote->setGwBasePrice($address->getGwBasePrice() + $quote->getGwBasePrice());
        $quote->setGwPrice($address->getGwPrice() + $quote->getGwPrice());
        $quote->setGwCardBasePrice($address->getGwCardBasePrice() + $quote->getGwCardBasePrice());
        $quote->setGwCardPrice($address->getGwCardPrice() + $quote->getGwCardPrice());

        return $this;
    }

    /**
     * Collect wrapping total for items
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  Magento_GiftWrapping_Model_Total_Quote_Giftwrapping
     */
    protected function _collectWrappingForItems($address)
    {
        $items = $this->_getAddressItems($address);
        $wrappingForItemsBaseTotal = false;
        $wrappingForItemsTotal = false;

        foreach ($items as $item) {
            if ($item->getProduct()->isVirtual() || $item->getParentItem() || !$item->getGwId()) {
                continue;
            }
            if ($item->getProduct()->getGiftWrappingPrice()) {
                $wrappingBasePrice = $item->getProduct()->getGiftWrappingPrice();
            } else {
                $wrapping = $this->_getWrapping($item->getGwId(), $this->_store);
                $wrappingBasePrice = $wrapping->getBasePrice();
            }
            $wrappingPrice = $this->_store->convertPrice($wrappingBasePrice);
            $item->setGwBasePrice($wrappingBasePrice);
            $item->setGwPrice($wrappingPrice);
            $wrappingForItemsBaseTotal += $wrappingBasePrice;
            $wrappingForItemsTotal += $wrappingPrice;
        }
        $address->setGwItemsBasePrice($wrappingForItemsBaseTotal);
        $address->setGwItemsPrice($wrappingForItemsTotal);

        return $this;
    }

    /**
     * Collect wrapping total for quote
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  Magento_GiftWrapping_Model_Total_Quote_Giftwrapping
     */
    protected function _collectWrappingForQuote($address)
    {
        $wrappingBasePrice = false;
        $wrappingPrice = false;
        if ($this->_quoteEntity->getGwId()) {
            $wrapping = $this->_getWrapping($this->_quoteEntity->getGwId(), $this->_store);
            $wrappingBasePrice = $wrapping->getBasePrice();
            $wrappingPrice = $this->_store->convertPrice($wrappingBasePrice);
        }
        $address->setGwBasePrice($wrappingBasePrice);
        $address->setGwPrice($wrappingPrice);
        return $this;
    }

    /**
     * Collect printed card total for quote
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  Magento_GiftWrapping_Model_Total_Quote_Giftwrapping
     */
    protected function _collectPrintedCard($address)
    {
        $printedCardBasePrice = false;
        $printedCardPrice = false;
        if ($this->_quoteEntity->getGwAddCard()) {
            $printedCardBasePrice = Mage::helper('Magento_GiftWrapping_Helper_Data')->getPrintedCardPrice($this->_store);
            $printedCardPrice = $this->_store->convertPrice($printedCardBasePrice);
        }
        $address->setGwCardBasePrice($printedCardBasePrice);
        $address->setGwCardPrice($printedCardPrice);
        return $this;
    }

    /**
     * Return wrapping model for wrapping ID
     *
     * @param  int $wrappingId
     * @param  Magento_Core_Model_Store $store
     * @return Magento_GiftWrapping_Model_Wrapping
     */
    protected function _getWrapping($wrappingId, $store)
    {
        $wrapping = Mage::getModel('Magento_GiftWrapping_Model_Wrapping');
        $wrapping->setStoreId($store->getId());
        $wrapping->load($wrappingId);
        return $wrapping;
    }

    /**
     * Assign wrapping totals and labels to address object
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  Magento_Sales_Model_Quote_Address_Total_Subtotal
     */
    public function fetch(Magento_Sales_Model_Quote_Address $address)
    {
        $address->addTotal(array(
            'code'  => $this->getCode(),
            'gw_price' => $address->getGwPrice(),
            'gw_base_price' => $address->getGwBasePrice(),
            'gw_items_price' => $address->getGwItemsPrice(),
            'gw_items_base_price' => $address->getGwItemsBasePrice(),
            'gw_card_price' => $address->getGwCardPrice(),
            'gw_card_base_price' => $address->getGwCardBasePrice()
        ));
        return $this;
    }
}
