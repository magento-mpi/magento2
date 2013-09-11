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
namespace Magento\GiftWrapping\Block\Adminhtml\Order\Create;

class AbstractCreate
    extends \Magento\Adminhtml\Block\Sales\Order\Create\AbstractCreate
{
    protected $_designCollection;

    /**
     * Gift wrapping collection
     *
     * @return \Magento\GiftWrapping\Model\Resource\Wrapping\Collection
     */
    public function getDesignCollection()
    {
        if (is_null($this->_designCollection)) {
            $this->_designCollection = \Mage::getModel('Magento\GiftWrapping\Model\Wrapping')->getCollection()
                ->addStoreAttributesToResult($this->getStore()->getId())
                ->applyStatusFilter()
                ->applyWebsiteFilter($this->getStore()->getWebsiteId());
        }
        return $this->_designCollection;
    }

    /**
     * Return gift wrapping designs info
     *
     * @return \Magento\Object
     */
    public function getDesignsInfo()
    {
        $data = array();
        foreach ($this->getDesignCollection()->getItems() as $item) {
            if ($this->getDisplayWrappingBothPrices()) {
                $temp['price_incl_tax'] = $this->calculatePrice($item, $item->getBasePrice(), true);
                $temp['price_excl_tax'] = $this->calculatePrice($item, $item->getBasePrice());
            } else {
                $temp['price'] = $this->calculatePrice($item, $item->getBasePrice(), $this->getDisplayWrappingPriceInclTax());
            }
            $temp['path'] = $item->getImageUrl();
            $temp['design'] = $item->getDesign();
            $data[$item->getId()] = $temp;
        }
       return new \Magento\Object($data);
    }

    /**
     * Prepare and return printed card info
     *
     * @return \Magento\Object
     */
    public function getCardInfo()
    {
        $data = array();
        if ($this->getAllowPrintedCard()) {
            $price = \Mage::helper('Magento\GiftWrapping\Helper\Data')->getPrintedCardPrice($this->getStoreId());
             if ($this->getDisplayCardBothPrices()) {
                 $data['price_incl_tax'] = $this->calculatePrice(new \Magento\Object(), $price, true);
                 $data['price_excl_tax'] = $this->calculatePrice(new \Magento\Object(), $price);
             } else {
                $data['price'] = $this->calculatePrice(new \Magento\Object(), $price, $this->getDisplayCardPriceInclTax());
             }
        }
        return new \Magento\Object($data);
    }

    /**
     * Calculate price
     *
     * @param \Magento\Object $item
     * @param mixed $basePrice
     * @param bool $includeTax
     * @return string
     */
    public function calculatePrice($item, $basePrice, $includeTax = false)
    {
        $shippingAddress = $this->getQuote()->getShippingAddress();
        $billingAddress  = $this->getQuote()->getBillingAddress();

        $taxClass = \Mage::helper('Magento\GiftWrapping\Helper\Data')->getWrappingTaxClass($this->getStoreId());
        $item->setTaxClassId($taxClass);

        $price = \Mage::helper('Magento\GiftWrapping\Helper\Data')->getPrice(
            $item,
            $basePrice,
            $includeTax,
            $shippingAddress,
            $billingAddress
        );
        return \Mage::helper('Magento\Core\Helper\Data')->currency($price, true, false);
    }

    /**
     * Check ability to display both prices for gift wrapping in shopping cart
     *
     * @return bool
     */
    public function getDisplayWrappingBothPrices()
    {
        return \Mage::helper('Magento\GiftWrapping\Helper\Data')
            ->displayCartWrappingBothPrices($this->getStoreId());
    }

    /**
     * Check ability to display prices including tax for gift wrapping in shopping cart
     *
     * @return bool
     */
    public function getDisplayWrappingPriceInclTax()
    {
        return \Mage::helper('Magento\GiftWrapping\Helper\Data')
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
