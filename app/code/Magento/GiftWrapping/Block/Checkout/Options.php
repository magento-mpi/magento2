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
 * Gift wrapping checkout process options block
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Block\Checkout;

class Options extends \Magento\Core\Block\Template
{
    protected $_designCollection;

    protected $_giftWrappingAvailable = false;

    /**
     * Gift wrapping collection
     *
     * @return \Magento\GiftWrapping\Model\Resource\Wrapping\Collection
     */
    public function getDesignCollection()
    {
        if (is_null($this->_designCollection)) {
            $store = \Mage::app()->getStore();
            $this->_designCollection = \Mage::getModel('\Magento\GiftWrapping\Model\Wrapping')->getCollection()
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
        $select = $this->getLayout()->createBlock('\Magento\Core\Block\Html\Select')
            ->setData(array(
            'id'    => 'giftwrapping-${_id_}',
            'class' => 'select'
        ))
            ->setName('giftwrapping[${_id_}][design]')
            ->setExtraParams('data-addr-id="${_blockId_}"')
            ->setOptions($this->getDesignCollection()->toOptionArray());
        return $select->getHtml();
    }

    /**
     * Get quote instance
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote();
    }

    /**
     * Calculate including tax price
     *
     * @param \Magento\Object $item
     * @param mixed $basePrice
     * @param \Magento\Sales\Model\Quote\Address $shippingAddress
     * @param bool $includeTax
     * @return string
     */
    public function calculatePrice($item, $basePrice, $shippingAddress, $includeTax = false)
    {
        $billingAddress = $this->getQuote()->getBillingAddress();
        $taxClass = \Mage::helper('Magento\GiftWrapping\Helper\Data')->getWrappingTaxClass();
        $item->setTaxClassId($taxClass);

        $price = \Mage::helper('Magento\GiftWrapping\Helper\Data')->getPrice($item, $basePrice, $includeTax, $shippingAddress,
            $billingAddress
        );
        return \Mage::helper('Magento\Core\Helper\Data')->currency($price, true, false);
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
            $temp = array();
            foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
                $entityId = $this->getQuote()->getIsMultiShipping() ? $address->getId() : $this->getQuote()->getId();
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
        return new \Magento\Object($data);
    }

    /**
     * Prepare and return quote items info
     *
     * @return \Magento\Object
     */
    public function getItemsInfo()
    {
        $data = array();
        if ($this->getQuote()->getIsMultiShipping()) {
            foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
                $this->_processItems($address->getAllItems(), $address, $data);
            }
        } else {
            $this->_processItems($this->getQuote()->getAllItems(), $this->getQuote()->getShippingAddress(), $data);
        }
        return new \Magento\Object($data);
    }

    /**
     * Process items
     *
     * @param array $items
     * @param \Magento\Sales\Model\Quote\Address $shippingAddress
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
            if (\Mage::helper('Magento\GiftWrapping\Helper\Data')->isGiftWrappingAvailableForProduct($allowed)
                && !$item->getIsVirtual()) {
                $temp = array();
                if ($price = $item->getProduct()->getGiftWrappingPrice()) {
                    if ($this->getDisplayWrappingBothPrices()) {
                        $temp['price_incl_tax'] = $this->calculatePrice(
                            new \Magento\Object(),
                            $price,
                            $shippingAddress,
                            true
                        );
                        $temp['price_excl_tax'] = $this->calculatePrice(
                            new \Magento\Object(),
                            $price,
                            $shippingAddress
                        );
                    } else {
                        $temp['price'] = $this->calculatePrice(
                            new \Magento\Object(),
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
     * @return \Magento\Object
     */
    public function getCardInfo()
    {
        $data = array();
        if ($this->getAllowPrintedCard()) {
            $price = \Mage::helper('Magento\GiftWrapping\Helper\Data')->getPrintedCardPrice();
            foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
                $entityId = $this->getQuote()->getIsMultiShipping()
                    ? $address->getId()
                    : $this->getQuote()->getId();

                if ($this->getDisplayCardBothPrices()) {
                    $data[$entityId]['price_incl_tax'] = $this->calculatePrice(
                        new \Magento\Object(),
                        $price,
                        $address,
                        true
                    );
                    $data[$entityId]['price_excl_tax'] = $this->calculatePrice(
                        new \Magento\Object(),
                        $price,
                        $address
                    );
                } else {
                    $data[$entityId]['price'] = $this->calculatePrice(
                        new \Magento\Object(),
                        $price,
                        $address,
                        $this->getDisplayCardIncludeTaxPrice()
                    );
                }
            }
        }
        return new \Magento\Object($data);
    }

    /**
     * Check display both prices for gift wrapping
     *
     * @return bool
     */
    public function getDisplayWrappingBothPrices()
    {
        return \Mage::helper('Magento\GiftWrapping\Helper\Data')->displayCartWrappingBothPrices();
    }

    /**
     * Check display both prices for printed card
     *
     * @return bool
     */
    public function getDisplayCardBothPrices()
    {
        return \Mage::helper('Magento\GiftWrapping\Helper\Data')->displayCartCardBothPrices();
    }

    /**
     * Check display prices including tax for gift wrapping
     *
     * @return bool
     */
    public function getDisplayWrappingIncludeTaxPrice()
    {
        return \Mage::helper('Magento\GiftWrapping\Helper\Data')->displayCartWrappingIncludeTaxPrice();
    }

    /**
     * Check display price including tax for printed card
     *
     * @return bool
     */
    public function getDisplayCardIncludeTaxPrice()
    {
        return \Mage::helper('Magento\GiftWrapping\Helper\Data')->displayCartCardIncludeTaxPrice();
    }

    /**
     * Check allow printed card
     *
     * @return bool
     */
    public function getAllowPrintedCard()
    {
        return \Mage::helper('Magento\GiftWrapping\Helper\Data')->allowPrintedCard();
    }

    /**
     * Check allow gift receipt
     *
     * @return bool
     */
    public function getAllowGiftReceipt()
    {
        return \Mage::helper('Magento\GiftWrapping\Helper\Data')->allowGiftReceipt();
    }

    /**
     * Check allow gift wrapping on order level
     *
     * @return bool
     */
    public function getAllowForOrder()
    {
        return \Mage::helper('Magento\GiftWrapping\Helper\Data')->isGiftWrappingAvailableForOrder();
    }

    /**
     * Check allow gift wrapping on order items
     *
     * @return bool
     */
    public function getAllowForItems()
    {
        return \Mage::helper('Magento\GiftWrapping\Helper\Data')->isGiftWrappingAvailableForItems();
    }

    /**
     * Check allow gift wrapping for order
     *
     * @return bool
     */
    public function canDisplayGiftWrapping()
    {
        $cartItems      = \Mage::getModel('\Magento\Checkout\Model\Cart')->getItems();
        $productModel   = \Mage::getModel('\Magento\Catalog\Model\Product');
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
     * Determines if gift wrapping is available for any product in this checkout
     *
     * @return bool
     */
    public function getGiftWrappingAvailable()
    {
        return $this->_giftWrappingAvailable;
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
