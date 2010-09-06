<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Enter description here ...
 *
 * @method Mage_Sales_Model_Resource_Quote_Address_Item _getResource()
 * @method Mage_Sales_Model_Resource_Quote_Address_Item getResource()
 * @method Mage_Sales_Model_Quote_Address_Item getParentItemId()
 * @method int setParentItemId(int $value)
 * @method Mage_Sales_Model_Quote_Address_Item getQuoteAddressId()
 * @method int setQuoteAddressId(int $value)
 * @method Mage_Sales_Model_Quote_Address_Item getQuoteItemId()
 * @method int setQuoteItemId(int $value)
 * @method Mage_Sales_Model_Quote_Address_Item getCreatedAt()
 * @method string setCreatedAt(string $value)
 * @method Mage_Sales_Model_Quote_Address_Item getUpdatedAt()
 * @method string setUpdatedAt(string $value)
 * @method Mage_Sales_Model_Quote_Address_Item getAppliedRuleIds()
 * @method string setAppliedRuleIds(string $value)
 * @method Mage_Sales_Model_Quote_Address_Item getAdditionalData()
 * @method string setAdditionalData(string $value)
 * @method Mage_Sales_Model_Quote_Address_Item getWeight()
 * @method float setWeight(float $value)
 * @method float setQty(float $value)
 * @method Mage_Sales_Model_Quote_Address_Item getDiscountAmount()
 * @method float setDiscountAmount(float $value)
 * @method float setTaxAmount(float $value)
 * @method Mage_Sales_Model_Quote_Address_Item getRowTotal()
 * @method float setRowTotal(float $value)
 * @method Mage_Sales_Model_Quote_Address_Item getBaseRowTotal()
 * @method float setBaseRowTotal(float $value)
 * @method Mage_Sales_Model_Quote_Address_Item getRowTotalWithDiscount()
 * @method float setRowTotalWithDiscount(float $value)
 * @method Mage_Sales_Model_Quote_Address_Item getBaseDiscountAmount()
 * @method float setBaseDiscountAmount(float $value)
 * @method float setBaseTaxAmount(float $value)
 * @method Mage_Sales_Model_Quote_Address_Item getRowWeight()
 * @method float setRowWeight(float $value)
 * @method Mage_Sales_Model_Quote_Address_Item getProductId()
 * @method int setProductId(int $value)
 * @method Mage_Sales_Model_Quote_Address_Item getSuperProductId()
 * @method int setSuperProductId(int $value)
 * @method Mage_Sales_Model_Quote_Address_Item getParentProductId()
 * @method int setParentProductId(int $value)
 * @method Mage_Sales_Model_Quote_Address_Item getSku()
 * @method string setSku(string $value)
 * @method Mage_Sales_Model_Quote_Address_Item getImage()
 * @method string setImage(string $value)
 * @method Mage_Sales_Model_Quote_Address_Item getName()
 * @method string setName(string $value)
 * @method Mage_Sales_Model_Quote_Address_Item getDescription()
 * @method string setDescription(string $value)
 * @method Mage_Sales_Model_Quote_Address_Item getFreeShipping()
 * @method int setFreeShipping(int $value)
 * @method Mage_Sales_Model_Quote_Address_Item getIsQtyDecimal()
 * @method int setIsQtyDecimal(int $value)
 * @method Mage_Sales_Model_Quote_Address_Item getDiscountPercent()
 * @method float setDiscountPercent(float $value)
 * @method Mage_Sales_Model_Quote_Address_Item getNoDiscount()
 * @method int setNoDiscount(int $value)
 * @method Mage_Sales_Model_Quote_Address_Item getTaxPercent()
 * @method float setTaxPercent(float $value)
 * @method Mage_Sales_Model_Quote_Address_Item getBasePrice()
 * @method float setBasePrice(float $value)
 * @method Mage_Sales_Model_Quote_Address_Item getBaseCost()
 * @method float setBaseCost(float $value)
 * @method Mage_Sales_Model_Quote_Address_Item getPriceInclTax()
 * @method float setPriceInclTax(float $value)
 * @method Mage_Sales_Model_Quote_Address_Item getBasePriceInclTax()
 * @method float setBasePriceInclTax(float $value)
 * @method Mage_Sales_Model_Quote_Address_Item getRowTotalInclTax()
 * @method float setRowTotalInclTax(float $value)
 * @method Mage_Sales_Model_Quote_Address_Item getBaseRowTotalInclTax()
 * @method float setBaseRowTotalInclTax(float $value)
 * @method Mage_Sales_Model_Quote_Address_Item getGiftMessageId()
 * @method int setGiftMessageId(int $value)
 * @method Mage_Sales_Model_Quote_Address_Item getHiddenTaxAmount()
 * @method float setHiddenTaxAmount(float $value)
 * @method Mage_Sales_Model_Quote_Address_Item getBaseHiddenTaxAmount()
 * @method float setBaseHiddenTaxAmount(float $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Quote_Address_Item extends Mage_Sales_Model_Quote_Item_Abstract
{
    /**
     * Quote address model object
     *
     * @var Mage_Sales_Model_Quote_Address
     */
    protected $_address;
    protected $_quote;

    protected function _construct()
    {
        $this->_init('sales/quote_address_item');
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
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Quote_Address_Item
     */
    public function setAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_address = $address;
        $this->_quote   = $address->getQuote();
        return $this;
    }

    /**
     * Retrieve address model
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        return $this->_address;
    }

    /**
     * Retrieve quote model instance
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }


    public function importQuoteItem(Mage_Sales_Model_Quote_Item $quoteItem)
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
