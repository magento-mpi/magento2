<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Sales_Model_Quote_Address_Total_Subtotal extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Collect address subtotal
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Quote_Address_Total_Subtotal
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $address->setTotalQty(0);

        $baseVirtualAmount = $virtualAmount = 0;

        /**
         * Process address items
         */
        $items = $this->_getAddressItems($address);
        foreach ($items as $item) {
            if ($this->_initItem($address, $item) && $item->getQty() > 0) {
                /**
                 * Separatly calculate subtotal only for virtual products
                 */
                if ($item->getProduct()->isVirtual()) {
                    $virtualAmount += $item->getRowTotal();
                    $baseVirtualAmount += $item->getBaseRowTotal();
                }
            }
            else {
                $this->_removeItem($address, $item);
            }
        }

        $address->setBaseVirtualAmount($baseVirtualAmount);
        $address->setVirtualAmount($virtualAmount);

        /**
         * Initialize grand totals
         */
        Mage::helper('Mage_Sales_Helper_Data')->checkQuoteAmount($address->getQuote(), $address->getSubtotal());
        Mage::helper('Mage_Sales_Helper_Data')->checkQuoteAmount($address->getQuote(), $address->getBaseSubtotal());
        return $this;
    }

    /**
     * Address item initialization
     *
     * @param  $item
     * @return bool
     */
    protected function _initItem($address, $item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $quoteItem = $item->getAddress()->getQuote()->getItemById($item->getQuoteItemId());
        }
        else {
            $quoteItem = $item;
        }
        $product = $quoteItem->getProduct();
        $product->setCustomerGroupId($quoteItem->getQuote()->getCustomerGroupId());

        /**
         * Quote super mode flag mean what we work with quote without restriction
         */
        if ($item->getQuote()->getIsSuperMode()) {
            if (!$product) {
                return false;
            }
        }
        else {
            if (!$product || !$product->isVisibleInCatalog()) {
                return false;
            }
        }

        if ($quoteItem->getParentItem() && $quoteItem->isChildrenCalculated()) {
            $finalPrice = $quoteItem->getParentItem()->getProduct()->getPriceModel()->getChildFinalPrice(
               $quoteItem->getParentItem()->getProduct(),
               $quoteItem->getParentItem()->getQty(),
               $quoteItem->getProduct(),
               $quoteItem->getQty()
            );
            $item->setPrice($finalPrice)
                ->setBaseOriginalPrice($finalPrice);
            $item->calcRowTotal();
        } else if (!$quoteItem->getParentItem()) {
            $finalPrice = $product->getFinalPrice($quoteItem->getQty());
            $item->setPrice($finalPrice)
                ->setBaseOriginalPrice($finalPrice);
            $item->calcRowTotal();
            $this->_addAmount($item->getRowTotal());
            $this->_addBaseAmount($item->getBaseRowTotal());
            $address->setTotalQty($address->getTotalQty() + $item->getQty());
        }

        return true;
    }

    /**
     * Remove item
     *
     * @param  $address
     * @param  $item
     * @return Mage_Sales_Model_Quote_Address_Total_Subtotal
     */
    protected function _removeItem($address, $item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Item) {
            $address->removeItem($item->getId());
            if ($address->getQuote()) {
                $address->getQuote()->removeItem($item->getId());
            }
        }
        elseif ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $address->removeItem($item->getId());
            if ($address->getQuote()) {
                $address->getQuote()->removeItem($item->getQuoteItemId());
            }
        }

        return $this;
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Quote_Address_Total_Subtotal
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $address->addTotal(array(
            'code'  => $this->getCode(),
            'title' => __('Subtotal'),
            'value' => $address->getSubtotal()
        ));
        return $this;
    }

    /**
     * Get Subtotal label
     *
     * @return string
     */
    public function getLabel()
    {
        return __('Subtotal');
    }
}
