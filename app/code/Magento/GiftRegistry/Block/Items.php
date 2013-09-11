<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Front end helper block to show giftregistry items
 */
namespace Magento\GiftRegistry\Block;

class Items extends \Magento\Checkout\Block\Cart
{

    /**
     * Return list of gift registry items
     *
     * @return array
     */
    public function getItems()
    {
        if (!$this->hasItemCollection()) {
            if (!$this->getEntity()) {
                return array();
            }
            $collection = \Mage::getModel('Magento\GiftRegistry\Model\Item')->getCollection()
                ->addRegistryFilter($this->getEntity()->getId());

            $quoteItemsCollection = array();
            $quote = \Mage::getModel('Magento\Sales\Model\Quote')->setItemCount(true);
            $emptyQuoteItem = \Mage::getModel('Magento\Sales\Model\Quote\Item');
            foreach ($collection as $item) {
                $product = $item->getProduct();
                $remainingQty = $item->getQty() - $item->getQtyFulfilled();
                if ($remainingQty < 0) {
                    $remainingQty = 0;
                }
                // Create a new qoute item and import data from gift registry item to it
                $quoteItem = clone $emptyQuoteItem;
                $quoteItem->addData($item->getData())
                    ->setQuote($quote)
                    ->setProduct($product)
                    ->setRemainingQty($remainingQty)
                    ->setOptions($item->getOptions());

                $product->setCustomOptions($item->getOptionsByCode());
                if (\Mage::helper('Magento\Catalog\Helper\Data')->canApplyMsrp($product)) {
                    $quoteItem->setCanApplyMsrp(true);
                    $product->setRealPriceHtml(
                        \Mage::app()->getStore()->formatPrice(\Mage::app()->getStore()->convertPrice(
                            \Mage::helper('Magento\Tax\Helper\Data')->getPrice($product, $product->getFinalPrice(), true)
                        ))
                    );
                    $product->setAddToCartUrl($this->helper('Magento\Checkout\Helper\Cart')->getAddUrl($product));
                } else {
                    $quoteItem->setGiftRegistryPrice($product->getFinalPrice());
                    $quoteItem->setCanApplyMsrp(false);
                }

                $quoteItemsCollection[] = $quoteItem;
            }

            $this->setData('item_collection', $quoteItemsCollection);
        }
        return $this->_getData('item_collection');
    }

    /**
     * Return current gift registry entity
     *
     * @return \Magento\GiftRegistry\Model\Resource\Item\Collection
     */
    public function getEntity()
    {
         if (!$this->hasEntity()) {
            $this->setData('entity', \Mage::registry('current_entity'));
        }
        return $this->_getData('entity');
    }

    /**
     * Return "add to cart" url
     *
     * @param \Magento\GiftRegistry\Model\Item $item
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/addToCart', array('_current' => true));
    }

    /**
     * Return update action form url
     *
     * @return string
     */
    public function getActionUpdateUrl()
    {
        return $this->getUrl('*/*/updateItems', array('_current' => true));
    }

    /**
     * Return back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('giftregistry');
    }

}
