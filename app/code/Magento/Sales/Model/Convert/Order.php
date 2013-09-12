<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order data convert model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Convert_Order extends Magento_Object
{
    /**
     * Converting order object to quote object
     *
     * @param   Magento_Sales_Model_Order $order
     * @return  Magento_Sales_Model_Quote
     */
    public function toQuote(Magento_Sales_Model_Order $order, $quote=null)
    {
        if (!($quote instanceof Magento_Sales_Model_Quote)) {
            $quote = Mage::getModel('Magento_Sales_Model_Quote');
        }

        $quote->setStoreId($order->getStoreId())
            ->setOrderId($order->getId());

        Mage::helper('Magento_Core_Helper_Data')->copyFieldset('sales_convert_order', 'to_quote', $order, $quote);

        Mage::dispatchEvent('sales_convert_order_to_quote', array('order'=>$order, 'quote'=>$quote));
        return $quote;
    }

    /**
     * Convert order to shipping address
     *
     * @param   Magento_Sales_Model_Order $order
     * @return  Magento_Sales_Model_Quote_Address
     */
    public function toQuoteShippingAddress(Magento_Sales_Model_Order $order)
    {
        $address = $this->addressToQuoteAddress($order->getShippingAddress());

        Mage::helper('Magento_Core_Helper_Data')->copyFieldset('sales_convert_order', 'to_quote_address', $order, $address);
        return $address;
    }

    /**
     * Convert order address to quote address
     *
     * @param   Magento_Sales_Model_Order_Address $address
     * @return  Magento_Sales_Model_Quote_Address
     */
    public function addressToQuoteAddress(Magento_Sales_Model_Order_Address $address)
    {
        $quoteAddress = Mage::getModel('Magento_Sales_Model_Quote_Address')
            ->setStoreId($address->getStoreId())
            ->setAddressType($address->getAddressType())
            ->setCustomerId($address->getCustomerId())
            ->setCustomerAddressId($address->getCustomerAddressId());

        Mage::helper('Magento_Core_Helper_Data')->copyFieldset('sales_convert_order_address', 'to_quote_address', $address, $quoteAddress);
        return $quoteAddress;
    }

    /**
     * Convert order payment to quote payment
     *
     * @param   Magento_Sales_Model_Order_Payment $payment
     * @return  Magento_Sales_Model_Quote_Payment
     */
    public function paymentToQuotePayment(Magento_Sales_Model_Order_Payment $payment, $quotePayment=null)
    {
        if (!($quotePayment instanceof Magento_Sales_Model_Quote_Payment)) {
            $quotePayment = Mage::getModel('Magento_Sales_Model_Quote_Payment');
        }

        $quotePayment->setStoreId($payment->getStoreId())
            ->setCustomerPaymentId($payment->getCustomerPaymentId());

        Mage::helper('Magento_Core_Helper_Data')->copyFieldset('sales_convert_order_payment', 'to_quote_payment', $payment, $quotePayment);
        return $quotePayment;
    }

    /**
     * Retrieve
     *
     * @param Magento_Sales_Model_Order_Item $item
     * @return unknown
     */
    public function itemToQuoteItem(Magento_Sales_Model_Order_Item $item)
    {
        $quoteItem = Mage::getModel('Magento_Sales_Model_Quote_Item')
            ->setStoreId($item->getOrder()->getStoreId())
            ->setQuoteItemId($item->getId())
            ->setProductId($item->getProductId())
            ->setParentProductId($item->getParentProductId());

        Mage::helper('Magento_Core_Helper_Data')->copyFieldset('sales_convert_order_item', 'to_quote_item', $item, $quoteItem);
        return $quoteItem;
    }

    /**
     * Convert order object to invoice
     *
     * @param   Magento_Sales_Model_Order $order
     * @return  Magento_Sales_Model_Order_Invoice
     */
    public function toInvoice(Magento_Sales_Model_Order $order)
    {
        $invoice = Mage::getModel('Magento_Sales_Model_Order_Invoice');
        $invoice->setOrder($order)
            ->setStoreId($order->getStoreId())
            ->setCustomerId($order->getCustomerId())
            ->setBillingAddressId($order->getBillingAddressId())
            ->setShippingAddressId($order->getShippingAddressId());

        Mage::helper('Magento_Core_Helper_Data')->copyFieldset('sales_convert_order', 'to_invoice', $order, $invoice);
        return $invoice;
    }

    /**
     * Convert order item object to invoice item
     *
     * @param   Magento_Sales_Model_Order_Item $item
     * @return  Magento_Sales_Model_Order_Invoice_Item
     */
    public function itemToInvoiceItem(Magento_Sales_Model_Order_Item $item)
    {
        $invoiceItem = Mage::getModel('Magento_Sales_Model_Order_Invoice_Item');
        $invoiceItem->setOrderItem($item)
            ->setProductId($item->getProductId());

        Mage::helper('Magento_Core_Helper_Data')->copyFieldset('sales_convert_order_item', 'to_invoice_item', $item, $invoiceItem);
        return $invoiceItem;
    }

    /**
     * Convert order object to Shipment
     *
     * @param   Magento_Sales_Model_Order $order
     * @return  Magento_Sales_Model_Order_Shipment
     */
    public function toShipment(Magento_Sales_Model_Order $order)
    {
        $shipment = Mage::getModel('Magento_Sales_Model_Order_Shipment');
        $shipment->setOrder($order)
            ->setStoreId($order->getStoreId())
            ->setCustomerId($order->getCustomerId())
            ->setBillingAddressId($order->getBillingAddressId())
            ->setShippingAddressId($order->getShippingAddressId());

        Mage::helper('Magento_Core_Helper_Data')->copyFieldset('sales_convert_order', 'to_shipment', $order, $shipment);
        return $shipment;
    }

    /**
     * Convert order item object to Shipment item
     *
     * @param   Magento_Sales_Model_Order_Item $item
     * @return  Magento_Sales_Model_Order_Shipment_Item
     */
    public function itemToShipmentItem(Magento_Sales_Model_Order_Item $item)
    {
        $shipmentItem = Mage::getModel('Magento_Sales_Model_Order_Shipment_Item');
        $shipmentItem->setOrderItem($item)
            ->setProductId($item->getProductId());

        Mage::helper('Magento_Core_Helper_Data')->copyFieldset('sales_convert_order_item', 'to_shipment_item', $item, $shipmentItem);
        return $shipmentItem;
    }

    /**
     * Convert order object to creditmemo
     *
     * @param   Magento_Sales_Model_Order $order
     * @return  Magento_Sales_Model_Order_Creditmemo
     */
    public function toCreditmemo(Magento_Sales_Model_Order $order)
    {
        $creditmemo = Mage::getModel('Magento_Sales_Model_Order_Creditmemo');
        $creditmemo->setOrder($order)
            ->setStoreId($order->getStoreId())
            ->setCustomerId($order->getCustomerId())
            ->setBillingAddressId($order->getBillingAddressId())
            ->setShippingAddressId($order->getShippingAddressId());

        Mage::helper('Magento_Core_Helper_Data')->copyFieldset('sales_convert_order', 'to_cm', $order, $creditmemo);
        return $creditmemo;
    }

    /**
     * Convert order item object to Creditmemo item
     *
     * @param   Magento_Sales_Model_Order_Item $item
     * @return  Magento_Sales_Model_Order_Creditmemo_Item
     */
    public function itemToCreditmemoItem(Magento_Sales_Model_Order_Item $item)
    {
        $creditmemoItem = Mage::getModel('Magento_Sales_Model_Order_Creditmemo_Item');
        $creditmemoItem->setOrderItem($item)
            ->setProductId($item->getProductId());

        Mage::helper('Magento_Core_Helper_Data')->copyFieldset('sales_convert_order_item', 'to_cm_item', $item, $creditmemoItem);
        return $creditmemoItem;
    }
}
