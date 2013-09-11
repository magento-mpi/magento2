<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Sales\Model\Quote\Address\Total;

class Discount extends \Magento\Sales\Model\Quote\Address\Total\AbstractTotal
{
    public function collect(\Magento\Sales\Model\Quote\Address $address)
    {
        $quote = $address->getQuote();
        $eventArgs = array(
            'website_id'=>Mage::app()->getStore($quote->getStoreId())->getWebsiteId(),
            'customer_group_id'=>$quote->getCustomerGroupId(),
            'coupon_code'=>$quote->getCouponCode(),
        );

        $address->setFreeShipping(0);
        $totalDiscountAmount = 0;
        $subtotalWithDiscount= 0;
        $baseTotalDiscountAmount = 0;
        $baseSubtotalWithDiscount= 0;

        $items = $address->getAllItems();
        if (!count($items)) {
            $address->setDiscountAmount($totalDiscountAmount);
            $address->setSubtotalWithDiscount($subtotalWithDiscount);
            $address->setBaseDiscountAmount($baseTotalDiscountAmount);
            $address->setBaseSubtotalWithDiscount($baseSubtotalWithDiscount);
            return $this;
        }

        $hasDiscount = false;
        foreach ($items as $item) {
            if ($item->getNoDiscount()) {
                $item->setDiscountAmount(0);
                $item->setBaseDiscountAmount(0);
                $item->setRowTotalWithDiscount($item->getRowTotal());
                $item->setBaseRowTotalWithDiscount($item->getRowTotal());

                $subtotalWithDiscount+=$item->getRowTotal();
                $baseSubtotalWithDiscount+=$item->getBaseRowTotal();
            }
            else {
                /**
                 * Child item discount we calculate for parent
                 */
                if ($item->getParentItemId()) {
                    continue;
                }

                /**
                 * Composite item discount calculation
                 */

                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $eventArgs['item'] = $child;
                        \Mage::dispatchEvent('sales_quote_address_discount_item', $eventArgs);

                        if ($child->getDiscountAmount() || $child->getFreeShipping()) {
                            $hasDiscount = true;
                        }

                        /**
                         * Parent free shipping we apply to all children
                         */
                        if ($item->getFreeShipping()) {
                            $child->setFreeShipping($item->getFreeShipping());
                        }

                        /**
                         * @todo Parent discount we apply for all children without discount
                         */
                        if (!$child->getDiscountAmount() && $item->getDiscountPercent()) {

                        }
                        $totalDiscountAmount += $child->getDiscountAmount();//*$item->getQty();
                        $baseTotalDiscountAmount += $child->getBaseDiscountAmount();//*$item->getQty();

                        $child->setRowTotalWithDiscount($child->getRowTotal()-$child->getDiscountAmount());
                        $child->setBaseRowTotalWithDiscount($child->getBaseRowTotal()-$child->getBaseDiscountAmount());

                        $subtotalWithDiscount+=$child->getRowTotalWithDiscount();
                        $baseSubtotalWithDiscount+=$child->getBaseRowTotalWithDiscount();
                    }
                }
                else {
                    $eventArgs['item'] = $item;
                    \Mage::dispatchEvent('sales_quote_address_discount_item', $eventArgs);

                    if ($item->getDiscountAmount() || $item->getFreeShipping()) {
                        $hasDiscount = true;
                    }
                    $totalDiscountAmount += $item->getDiscountAmount();
                    $baseTotalDiscountAmount += $item->getBaseDiscountAmount();

                    $item->setRowTotalWithDiscount($item->getRowTotal()-$item->getDiscountAmount());
                    $item->setBaseRowTotalWithDiscount($item->getBaseRowTotal()-$item->getBaseDiscountAmount());

                    $subtotalWithDiscount+=$item->getRowTotalWithDiscount();
                    $baseSubtotalWithDiscount+=$item->getBaseRowTotalWithDiscount();
                }
            }
        }
        $address->setDiscountAmount($totalDiscountAmount);
        $address->setSubtotalWithDiscount($subtotalWithDiscount);
        $address->setBaseDiscountAmount($baseTotalDiscountAmount);
        $address->setBaseSubtotalWithDiscount($baseSubtotalWithDiscount);

        $address->setGrandTotal($address->getGrandTotal() - $address->getDiscountAmount());
        $address->setBaseGrandTotal($address->getBaseGrandTotal()-$address->getBaseDiscountAmount());
        return $this;
    }

    public function fetch(\Magento\Sales\Model\Quote\Address $address)
    {
        $amount = $address->getDiscountAmount();
        if ($amount!=0) {
            $title = __('Discount');
            $code = $address->getCouponCode();
            if (strlen($code)) {
                $title = __('Discount (%1)', $code);
            }
            $address->addTotal(array(
                'code'=>$this->getCode(),
                'title'=>$title,
                'value'=>-$amount
            ));
        }
        return $this;
    }

}
