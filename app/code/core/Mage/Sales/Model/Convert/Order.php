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
 
/**
 * Order data convert model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Dmitriy Soroka <dmitriy@varien.com> 
 */
class Mage_Sales_Model_Convert_Order extends Varien_Object
{
    /**
     * Converting order object to quote object
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  Mage_Sales_Model_Quote
     */
    public function toQuote(Mage_Sales_Model_Order $order, $quote=null)
    {
        if (!($quote instanceof Mage_Sales_Model_Quote)) {
            $quote = Mage::getModel('sales/quote');
        }
        
        $quote
            /**
             * Base Data
             */
            ->setStoreId($order->getStoreId())
            ->setOrderId($order->getId())
            
            /**
             * Customer data
             */
            ->setCustomerId($order->getCustomerId())
            ->setCustomerEmail($order->getCustomerEmail())
            ->setCustomerGroupId($order->getCustomerGroupId())
            ->setCustomerTaxClassId($order->getCustomerTaxClassId())
            ->setCustomerNote($quote->getCustomerNote())
            ->setCustomerNoteNotify($quote->getCustomerNoteNotify())
            
            /**
             * Currency data
             */
            ->setBaseCurrencyCode($order->getBaseCurrencyCode())
            ->setStoreCurrencyCode($order->getStoreCurrencyCode())
            ->setQuoteCurrencyCode($order->getOrderCurrencyCode())
            ->setStoreToBaseRate($order->getStoreToBaseRate())
            ->setStoreToQuoteRate($order->getStoreToOrderRate())
            
            /**
             * Totals data
             */
            ->setGrandTotal($order->getGrandTotal())
            
            /**
             * Another data
             */
            ->setCouponCode($order->getCouponCode())
            ->setGiftcertCode($order->getGiftcertCode())
            ->setAppliedRuleIds($order->getAppliedRuleIds());

        
        Mage::dispatchEvent('sales_convert_order_to_quote', array('order'=>$order, 'quote'=>$quote));
        return $quote;
    }
    
    public function addressToQuoteAddress(Mage_Sales_Model_Order_Address $orderAddress)
    {
        
    }
    
    public function paymentToQuotePayment()
    {
        
    }
    
    public function shippingToQuoteShipping()
    {
        
    }

    public function itemToQuoteItem()
    {
        
    }
    
    public function itemToInvoiceItem()
    {
        
    }
    
    public function itemToCreditmemoItem()
    {
        
    }
    
    public function itemToShipmentItem()
    {
        
    }    
}
