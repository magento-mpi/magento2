<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Gift wrapping checkout process options block
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftWrapping_Block_Checkout_Options extends Mage_Core_Block_Template
{
    protected $_designCollection;

    protected $_giftWrappingAvailable = false;

    /**
     * Gift wrapping collection
     *
     * @return Enterprise_GiftWrapping_Model_Resource_Wrapping_Collection
     */
    public function getDesignCollection()
    {
        if (is_null($this->_designCollection)) {
            $store = Mage::app()->getStore();
            $this->_designCollection = Mage::getModel('Enterprise_GiftWrapping_Model_Wrapping')->getCollection()
                ->addStoreAttributesToResult($store->getId())
                ->applyStatusFilter()
                ->applyWebsiteFilter($store->getWebsiteId());
        }
        return $this->_designCollection;
    }

    /**
     * Select element for choosing gift wrapping design
     *
     * @return array
     */
    public function getDesignSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Mage_Core_Block_Html_Select')
            ->setData(array(
                'id'    => 'giftwrapping_{{id}}',
                'class' => 'select'
            ))
            ->setName('giftwrapping[{{id}}][design]')
            ->setOptions($this->getDesignCollection()->toOptionArray());
        return $select->getHtml();
    }

    /**
     * Get quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * Calculate including tax price
     *
     * @param Varien_Object $item
     * @param mixed $basePrice
     * @param Mage_Sales_Model_Quote_Address $shippingAddress
     * @param bool $includeTax
     * @return string
     */
    public function calculatePrice($item, $basePrice, $shippingAddress, $includeTax = false)
    {
        $billingAddress = $this->_getQuote()->getBillingAddress();
        $taxClass = Mage::helper('Enterprise_GiftWrapping_Helper_Data')->getWrappingTaxClass();
        $item->setTaxClassId($taxClass);

        $price = Mage::helper('Enterprise_GiftWrapping_Helper_Data')->getPrice($item, $basePrice, $includeTax, $shippingAddress,
            $billingAddress
        );
        return Mage::helper('Mage_Core_Helper_Data')->currency($price, true, false);
    }

    /**
     * Return gift wrapping designs info
     *
     * @return Varien_Object
     */
    public function getDesignsInfo()
    {
        $data = array();
        foreach ($this->getDesignCollection()->getItems() as $item) {
            $temp = array();
            foreach ($this->_getQuote()->getAllShippingAddresses() as $address) {
                $entityId = $this->_getQuote()->getIsMultiShipping() ? $address->getId() : $this->_getQuote()->getId();
                if ($this->getDisplayWrappingBothPrices()) {
                    $temp[$entityId]['price_incl_tax'] = $this->calculatePrice(
                        $item,
                        $item->getBasePrice(),
                        $address,
                        true
                     );
                    $temp[$entityId]['price_excl_tax'] = $this->calculatePrice(
                        $item,
                        $item->getBasePrice(),
                        $address
                    );
                } else {
                    $temp[$entityId]['price'] = $this->calculatePrice(
                        $item,
                        $item->getBasePrice(),
                        $address,
                        $this->getDisplayWrappingIncludeTaxPrice()
                    );
                }
            }
            $temp['path'] = $item->getImageUrl();
            $data[$item->getId()] = $temp;
        }
       return new Varien_Object($data);
    }

    /**
     * Prepare and return quote items info
     *
     * @return Varien_Object
     */
    public function getItemsInfo()
    {
        $data = array();
        if ($this->_getQuote()->getIsMultiShipping()) {
            foreach ($this->_getQuote()->getAllShippingAddresses() as $address) {
                $this->_processItems($address->getAllItems(), $address, $data);
            }
        } else {
            $this->_processItems($this->_getQuote()->getAllItems(), $this->_getQuote()->getShippingAddress(), $data);
        }
        return new Varien_Object($data);
    }

    /**
     * Process items
     *
     * @param array $items
     * @param Mage_Sales_Model_Quote_Address $shippingAddress
     * @param array $data
     * @return array
     */
    protected function _processItems($items, $shippingAddress, &$data)
    {
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $allowed = $item->getProduct()->getGiftWrappingAvailable();
            if (Mage::helper('Enterprise_GiftWrapping_Helper_Data')->isGiftWrappingAvailableForProduct($allowed)
                && !$item->getIsVirtual()) {
                $temp = array();
                if ($price = $item->getProduct()->getGiftWrappingPrice()) {
                    if ($this->getDisplayWrappingBothPrices()) {
                        $temp['price_incl_tax'] = $this->calculatePrice(
                            new Varien_Object(),
                            $price,
                            $shippingAddress,
                            true
                        );
                        $temp['price_excl_tax'] = $this->calculatePrice(
                            new Varien_Object(),
                            $price,
                            $shippingAddress
                        );
                    } else {
                        $temp['price'] = $this->calculatePrice(
                            new Varien_Object(),
                            $price,
                            $shippingAddress,
                            $this->getDisplayWrappingIncludeTaxPrice()
                        );
                    }
                }
                $data[$item->getId()] = $temp;
            }
        }
        return $data;
    }

    /**
     * Prepare and return printed card info
     *
     * @return Varien_Object
     */
    public function getCardInfo()
    {
        $data = array();
        if ($this->getAllowPrintedCard()) {
            $price = Mage::helper('Enterprise_GiftWrapping_Helper_Data')->getPrintedCardPrice();
            foreach ($this->_getQuote()->getAllShippingAddresses() as $address) {
                $entityId = $this->_getQuote()->getIsMultiShipping()
                    ? $address->getId()
                    : $this->_getQuote()->getId();

                if ($this->getDisplayCardBothPrices()) {
                    $data[$entityId]['price_incl_tax'] = $this->calculatePrice(
                        new Varien_Object(),
                        $price,
                        $address,
                        true
                    );
                    $data[$entityId]['price_excl_tax'] = $this->calculatePrice(
                        new Varien_Object(),
                        $price,
                        $address
                    );
                } else {
                    $data[$entityId]['price'] = $this->calculatePrice(
                        new Varien_Object(),
                        $price,
                        $address,
                        $this->getDisplayCardIncludeTaxPrice()
                    );
                }
            }
        }
        return new Varien_Object($data);
    }

    /**
     * Check display both prices for gift wrapping
     *
     * @return bool
     */
    public function getDisplayWrappingBothPrices()
    {
        return Mage::helper('Enterprise_GiftWrapping_Helper_Data')->displayCartWrappingBothPrices();
    }

    /**
     * Check display both prices for printed card
     *
     * @return bool
     */
    public function getDisplayCardBothPrices()
    {
        return Mage::helper('Enterprise_GiftWrapping_Helper_Data')->displayCartCardBothPrices();
    }

    /**
     * Check display prices including tax for gift wrapping
     *
     * @return bool
     */
    public function getDisplayWrappingIncludeTaxPrice()
    {
        return Mage::helper('Enterprise_GiftWrapping_Helper_Data')->displayCartWrappingIncludeTaxPrice();
    }

    /**
     * Check display price including tax for printed card
     *
     * @return bool
     */
    public function getDisplayCardIncludeTaxPrice()
    {
        return Mage::helper('Enterprise_GiftWrapping_Helper_Data')->displayCartCardIncludeTaxPrice();
    }

    /**
     * Check allow printed card
     *
     * @return bool
     */
    public function getAllowPrintedCard()
    {
        return Mage::helper('Enterprise_GiftWrapping_Helper_Data')->allowPrintedCard();
    }

    /**
     * Check allow gift receipt
     *
     * @return bool
     */
    public function getAllowGiftReceipt()
    {
        return Mage::helper('Enterprise_GiftWrapping_Helper_Data')->allowGiftReceipt();
    }

    /**
     * Check allow gift wrapping on order level
     *
     * @return bool
     */
    public function getAllowForOrder()
    {
        return Mage::helper('Enterprise_GiftWrapping_Helper_Data')->isGiftWrappingAvailableForOrder();
    }

    /**
     * Check allow gift wrapping on order items
     *
     * @return bool
     */
    public function getAllowForItems()
    {
        return Mage::helper('Enterprise_GiftWrapping_Helper_Data')->isGiftWrappingAvailableForItems();
    }

    /**
     * Check allow gift wrapping for order
     *
     * @return bool
     */
    public function canDisplayGiftWrapping()
    {
        $cartItems      = Mage::getModel('Mage_Checkout_Model_Cart')->getItems();
        $productModel   = Mage::getModel('Mage_Catalog_Model_Product');
        foreach ($cartItems as $item) {
            $product = $productModel->load($item->getProductId());
            if ($product->getGiftWrappingAvailable()) {
                $this->_giftWrappingAvailable = true;
                continue;
            }
        }

        $canDisplay = $this->getAllowForOrder()
            || $this->getAllowForItems()
            || $this->getAllowPrintedCard()
            || $this->getAllowGiftReceipt()
            || $this->_giftWrappingAvailable;
        return $canDisplay;
    }

    /**
     * Get design collection count
     *
     * @return int
     */
    public function getDesignCollectionCount()
    {
        return count($this->getDesignCollection());
    }
}
