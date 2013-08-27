<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @method Magento_Sales_Model_Resource_Quote_Address_Item _getResource()
 * @method Magento_Sales_Model_Resource_Quote_Address_Item getResource()
 * @method int getParentItemId()
 * @method Magento_Sales_Model_Quote_Address_Item setParentItemId(int $value)
 * @method int getQuoteAddressId()
 * @method Magento_Sales_Model_Quote_Address_Item setQuoteAddressId(int $value)
 * @method int getQuoteItemId()
 * @method Magento_Sales_Model_Quote_Address_Item setQuoteItemId(int $value)
 * @method string getCreatedAt()
 * @method Magento_Sales_Model_Quote_Address_Item setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Magento_Sales_Model_Quote_Address_Item setUpdatedAt(string $value)
 * @method string getAppliedRuleIds()
 * @method Magento_Sales_Model_Quote_Address_Item setAppliedRuleIds(string $value)
 * @method string getAdditionalData()
 * @method Magento_Sales_Model_Quote_Address_Item setAdditionalData(string $value)
 * @method float getWeight()
 * @method Magento_Sales_Model_Quote_Address_Item setWeight(float $value)
 * @method Magento_Sales_Model_Quote_Address_Item setQty(float $value)
 * @method float getDiscountAmount()
 * @method Magento_Sales_Model_Quote_Address_Item setDiscountAmount(float $value)
 * @method Magento_Sales_Model_Quote_Address_Item setTaxAmount(float $value)
 * @method float getRowTotal()
 * @method Magento_Sales_Model_Quote_Address_Item setRowTotal(float $value)
 * @method float getBaseRowTotal()
 * @method Magento_Sales_Model_Quote_Address_Item setBaseRowTotal(float $value)
 * @method float getRowTotalWithDiscount()
 * @method Magento_Sales_Model_Quote_Address_Item setRowTotalWithDiscount(float $value)
 * @method float getBaseDiscountAmount()
 * @method Magento_Sales_Model_Quote_Address_Item setBaseDiscountAmount(float $value)
 * @method Magento_Sales_Model_Quote_Address_Item setBaseTaxAmount(float $value)
 * @method float getRowWeight()
 * @method Magento_Sales_Model_Quote_Address_Item setRowWeight(float $value)
 * @method int getProductId()
 * @method Magento_Sales_Model_Quote_Address_Item setProductId(int $value)
 * @method int getSuperProductId()
 * @method Magento_Sales_Model_Quote_Address_Item setSuperProductId(int $value)
 * @method int getParentProductId()
 * @method Magento_Sales_Model_Quote_Address_Item setParentProductId(int $value)
 * @method string getSku()
 * @method Magento_Sales_Model_Quote_Address_Item setSku(string $value)
 * @method string getImage()
 * @method Magento_Sales_Model_Quote_Address_Item setImage(string $value)
 * @method string getName()
 * @method Magento_Sales_Model_Quote_Address_Item setName(string $value)
 * @method string getDescription()
 * @method Magento_Sales_Model_Quote_Address_Item setDescription(string $value)
 * @method int getFreeShipping()
 * @method Magento_Sales_Model_Quote_Address_Item setFreeShipping(int $value)
 * @method int getIsQtyDecimal()
 * @method Magento_Sales_Model_Quote_Address_Item setIsQtyDecimal(int $value)
 * @method float getDiscountPercent()
 * @method Magento_Sales_Model_Quote_Address_Item setDiscountPercent(float $value)
 * @method int getNoDiscount()
 * @method Magento_Sales_Model_Quote_Address_Item setNoDiscount(int $value)
 * @method float getTaxPercent()
 * @method Magento_Sales_Model_Quote_Address_Item setTaxPercent(float $value)
 * @method float getBasePrice()
 * @method Magento_Sales_Model_Quote_Address_Item setBasePrice(float $value)
 * @method float getBaseCost()
 * @method Magento_Sales_Model_Quote_Address_Item setBaseCost(float $value)
 * @method float getPriceInclTax()
 * @method Magento_Sales_Model_Quote_Address_Item setPriceInclTax(float $value)
 * @method float getBasePriceInclTax()
 * @method Magento_Sales_Model_Quote_Address_Item setBasePriceInclTax(float $value)
 * @method float getRowTotalInclTax()
 * @method Magento_Sales_Model_Quote_Address_Item setRowTotalInclTax(float $value)
 * @method float getBaseRowTotalInclTax()
 * @method Magento_Sales_Model_Quote_Address_Item setBaseRowTotalInclTax(float $value)
 * @method int getGiftMessageId()
 * @method Magento_Sales_Model_Quote_Address_Item setGiftMessageId(int $value)
 * @method float getHiddenTaxAmount()
 * @method Magento_Sales_Model_Quote_Address_Item setHiddenTaxAmount(float $value)
 * @method float getBaseHiddenTaxAmount()
 * @method Magento_Sales_Model_Quote_Address_Item setBaseHiddenTaxAmount(float $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Quote_Address_Item extends Magento_Sales_Model_Quote_Item_Abstract
{
    /**
     * Quote address model object
     *
     * @var Magento_Sales_Model_Quote_Address
     */
    protected $_address;
    protected $_quote;

    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Quote_Address_Item');
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->getAddress()) {
            $this->setQuoteAddressId($this->getAddress()->getId());
        }
        return $this;
    }

    /**
     * Declare address model
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  Magento_Sales_Model_Quote_Address_Item
     */
    public function setAddress(Magento_Sales_Model_Quote_Address $address)
    {
        $this->_address = $address;
        $this->_quote   = $address->getQuote();
        return $this;
    }

    /**
     * Retrieve address model
     *
     * @return Magento_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        return $this->_address;
    }

    /**
     * Retrieve quote model instance
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }


    public function importQuoteItem(Magento_Sales_Model_Quote_Item $quoteItem)
    {
        $this->_quote = $quoteItem->getQuote();
        $this->setQuoteItem($quoteItem)
            ->setQuoteItemId($quoteItem->getId())
            ->setProductId($quoteItem->getProductId())
            ->setProduct($quoteItem->getProduct())
            ->setSku($quoteItem->getSku())
            ->setName($quoteItem->getName())
            ->setDescription($quoteItem->getDescription())
            ->setWeight($quoteItem->getWeight())
            ->setPrice($quoteItem->getPrice())
            ->setCost($quoteItem->getCost());

        if (!$this->hasQty()) {
            $this->setQty($quoteItem->getQty());
        }
        $this->setQuoteItemImported(true);
        return $this;
    }

    public function getOptionBycode($code)
    {
        if ($this->getQuoteItem()) {
            return $this->getQuoteItem()->getOptionBycode($code);
        }
        return null;
    }
}
