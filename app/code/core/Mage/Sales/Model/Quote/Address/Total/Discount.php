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
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Sales_Model_Quote_Address_Total_Discount
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $quote = $address->getQuote();
        $eventArgs = array(
            'website_id'=>Mage::app()->getStore($quote->getStoreId())->getWebsiteId(),
            'customer_group_id'=>$quote->getCustomerGroupId(),
            'coupon_code'=>$quote->getCouponCode(),
        );

        $address->setDiscountAmount(0);
        $address->setSubtotalWithDiscount(0);
        $address->setFreeShipping(0);

        $totalDiscountAmount = 0;
        $subtotalWithDiscount= 0;
        foreach ($address->getAllItems() as $item) {
            if ($item->getNoDiscount()) {
                $item->setDiscountAmount(0);
                $item->setRowTotalWithDiscount($item->getRowTotal());
                $subtotalWithDiscount+=$item->getRowTotal();
            }
            else {
                $eventArgs['item'] = $item;
                Mage::dispatchEvent('sales_quote_address_discount_item', $eventArgs);

            	$totalDiscountAmount += $item->getDiscountAmount();
            	$item->setRowTotalWithDiscount($item->getRowTotal()-$item->getDiscountAmount());
            	$subtotalWithDiscount+=$item->getRowTotalWithDiscount();
            }
        }
        $address->setSubtotalWithDiscount($subtotalWithDiscount);
        $address->setDiscountAmount($totalDiscountAmount);

        $address->setGrandTotal($address->getGrandTotal() - $address->getDiscountAmount());

        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getDiscountAmount();
        if ($amount!=0) {
            $title = Mage::helper('sales')->__('Discount');
            if ($code = $address->getQuote()->getCouponCode()) {
                $title = Mage::helper('sales')->__('Discount (%s)', $code);
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