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
 * Gift wrapping order create abstract block
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftWrapping_Block_Adminhtml_Order_Create_Abstract
    extends Magento_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * @var Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    protected $_designCollection;

    /**
     * Gift wrapping data
     *
     * @var Magento_GiftWrapping_Helper_Data
     */
    protected $_giftWrappingData;

    /**
     * @var Magento_GiftWrapping_Model_Resource_Wrapping_Collection
     */
    protected $_wrappingCollection;

    /**
     * @param Magento_GiftWrapping_Helper_Data $giftWrappingData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_GiftWrapping_Model_Resource_Wrapping_Collection $wrappingCollection
     * @param array $data
     */
    public function __construct(
        Magento_GiftWrapping_Helper_Data $giftWrappingData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_GiftWrapping_Model_Resource_Wrapping_Collection $wrappingCollection,
        array $data = array()
    ) {
        $this->_giftWrappingData = $giftWrappingData;
        $this->_wrappingCollection = $wrappingCollection;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Gift wrapping collection
     *
     * @return Magento_GiftWrapping_Model_Resource_Wrapping_Collection
     */
    public function getDesignCollection()
    {
        if (is_null($this->_designCollection)) {
            $this->_designCollection = $this->_wrappingCollection
                ->addStoreAttributesToResult($this->getStore()->getId())
                ->applyStatusFilter()
                ->applyWebsiteFilter($this->getStore()->getWebsiteId());
        }
        return $this->_designCollection;
    }

    /**
     * Return gift wrapping designs info
     *
     * @return Magento_Object
     */
    public function getDesignsInfo()
    {
        $data = array();
        foreach ($this->getDesignCollection()->getItems() as $item) {
            if ($this->getDisplayWrappingBothPrices()) {
                $temp['price_incl_tax'] = $this->calculatePrice($item, $item->getBasePrice(), true);
                $temp['price_excl_tax'] = $this->calculatePrice($item, $item->getBasePrice());
            } else {
                $temp['price'] = $this->calculatePrice($item, $item->getBasePrice(),
                    $this->getDisplayWrappingPriceInclTax());
            }
            $temp['path'] = $item->getImageUrl();
            $temp['design'] = $item->getDesign();
            $data[$item->getId()] = $temp;
        }
       return new Magento_Object($data);
    }

    /**
     * Prepare and return printed card info
     *
     * @return Magento_Object
     */
    public function getCardInfo()
    {
        $data = array();
        if ($this->getAllowPrintedCard()) {
            $price = $this->_giftWrappingData->getPrintedCardPrice($this->getStoreId());
             if ($this->getDisplayCardBothPrices()) {
                 $data['price_incl_tax'] = $this->calculatePrice(new Magento_Object(), $price, true);
                 $data['price_excl_tax'] = $this->calculatePrice(new Magento_Object(), $price);
             } else {
                $data['price'] = $this->calculatePrice(new Magento_Object(), $price,
                    $this->getDisplayCardPriceInclTax());
             }
        }
        return new Magento_Object($data);
    }

    /**
     * Calculate price
     *
     * @param Magento_Object $item
     * @param mixed $basePrice
     * @param bool $includeTax
     * @return string
     */
    public function calculatePrice($item, $basePrice, $includeTax = false)
    {
        $shippingAddress = $this->getQuote()->getShippingAddress();
        $billingAddress  = $this->getQuote()->getBillingAddress();

        $taxClass = $this->_giftWrappingData->getWrappingTaxClass($this->getStoreId());
        $item->setTaxClassId($taxClass);

        $price = $this->_giftWrappingData->getPrice(
            $item,
            $basePrice,
            $includeTax,
            $shippingAddress,
            $billingAddress
        );
        return $this->_coreData->currency($price, true, false);
    }

    /**
     * Check ability to display both prices for gift wrapping in shopping cart
     *
     * @return bool
     */
    public function getDisplayWrappingBothPrices()
    {
        return $this->_giftWrappingData
            ->displayCartWrappingBothPrices($this->getStoreId());
    }

    /**
     * Check ability to display prices including tax for gift wrapping in shopping cart
     *
     * @return bool
     */
    public function getDisplayWrappingPriceInclTax()
    {
        return $this->_giftWrappingData
            ->displayCartWrappingIncludeTaxPrice($this->getStoreId());
    }

    /**
     * Return quote id
     *
     * @return array
     */
    public function getEntityId()
    {
        return $this->getQuote()->getId();
    }
}
