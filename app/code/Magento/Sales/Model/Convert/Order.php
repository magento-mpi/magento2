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
namespace Magento\Sales\Model\Convert;

class Order extends \Magento\Object
{
    /**
     * Converting order object to quote object
     *
     * @param   \Magento\Sales\Model\Order $order
     * @return  \Magento\Sales\Model\Quote
     */
    public function toQuote(\Magento\Sales\Model\Order $order, $quote=null)
    {
        if (!($quote instanceof \Magento\Sales\Model\Quote)) {
            $quote = \Mage::getModel('Magento\Sales\Model\Quote');
        }

        $quote->setStoreId($order->getStoreId())
            ->setOrderId($order->getId());

        \Mage::helper('Magento\Core\Helper\Data')->copyFieldset('sales_convert_order', 'to_quote', $order, $quote);

        \Mage::dispatchEvent('sales_convert_order_to_quote', array('order'=>$order, 'quote'=>$quote));
        return $quote;
    }

    /**
     * Convert order to shipping address
     *
     * @param   \Magento\Sales\Model\Order $order
     * @return  \Magento\Sales\Model\Quote\Address
     */
    public function toQuoteShippingAddress(\Magento\Sales\Model\Order $order)
    {
        $address = $this->addressToQuoteAddress($order->getShippingAddress());

        \Mage::helper('Magento\Core\Helper\Data')->copyFieldset('sales_convert_order', 'to_quote_address', $order, $address);
        return $address;
    }

    /**
     * Convert order address to quote address
     *
     * @param   \Magento\Sales\Model\Order\Address $address
     * @return  \Magento\Sales\Model\Quote\Address
     */
    public function addressToQuoteAddress(\Magento\Sales\Model\Order\Address $address)
    {
        $quoteAddress = \Mage::getModel('Magento\Sales\Model\Quote\Address')
            ->setStoreId($address->getStoreId())
            ->setAddressType($address->getAddressType())
            ->setCustomerId($address->getCustomerId())
            ->setCustomerAddressId($address->getCustomerAddressId());

        \Mage::helper('Magento\Core\Helper\Data')->copyFieldset('sales_convert_order_address', 'to_quote_address', $address, $quoteAddress);
        return $quoteAddress;
    }

    /**
     * Convert order payment to quote payment
     *
     * @param   \Magento\Sales\Model\Order\Payment $payment
     * @return  \Magento\Sales\Model\Quote\Payment
     */
    public function paymentToQuotePayment(\Magento\Sales\Model\Order\Payment $payment, $quotePayment=null)
    {
        if (!($quotePayment instanceof \Magento\Sales\Model\Quote\Payment)) {
            $quotePayment = \Mage::getModel('Magento\Sales\Model\Quote\Payment');
        }

        $quotePayment->setStoreId($payment->getStoreId())
            ->setCustomerPaymentId($payment->getCustomerPaymentId());

        \Mage::helper('Magento\Core\Helper\Data')->copyFieldset('sales_convert_order_payment', 'to_quote_payment', $payment, $quotePayment);
        return $quotePayment;
    }

    /**
     * Retrieve
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return unknown
     */
    public function itemToQuoteItem(\Magento\Sales\Model\Order\Item $item)
    {
        $quoteItem = \Mage::getModel('Magento\Sales\Model\Quote\Item')
            ->setStoreId($item->getOrder()->getStoreId())
            ->setQuoteItemId($item->getId())
            ->setProductId($item->getProductId())
            ->setParentProductId($item->getParentProductId());

        \Mage::helper('Magento\Core\Helper\Data')->copyFieldset('sales_convert_order_item', 'to_quote_item', $item, $quoteItem);
        return $quoteItem;
    }

    /**
     * Convert order object to invoice
     *
     * @param   \Magento\Sales\Model\Order $order
     * @return  \Magento\Sales\Model\Order\Invoice
     */
    public function toInvoice(\Magento\Sales\Model\Order $order)
    {
        $invoice = \Mage::getModel('Magento\Sales\Model\Order\Invoice');
        $invoice->setOrder($order)
            ->setStoreId($order->getStoreId())
            ->setCustomerId($order->getCustomerId())
            ->setBillingAddressId($order->getBillingAddressId())
            ->setShippingAddressId($order->getShippingAddressId());

        \Mage::helper('Magento\Core\Helper\Data')->copyFieldset('sales_convert_order', 'to_invoice', $order, $invoice);
        return $invoice;
    }

    /**
     * Convert order item object to invoice item
     *
     * @param   \Magento\Sales\Model\Order\Item $item
     * @return  \Magento\Sales\Model\Order\Invoice\Item
     */
    public function itemToInvoiceItem(\Magento\Sales\Model\Order\Item $item)
    {
        $invoiceItem = \Mage::getModel('Magento\Sales\Model\Order\Invoice\Item');
        $invoiceItem->setOrderItem($item)
            ->setProductId($item->getProductId());

        \Mage::helper('Magento\Core\Helper\Data')->copyFieldset('sales_convert_order_item', 'to_invoice_item', $item, $invoiceItem);
        return $invoiceItem;
    }

    /**
     * Convert order object to Shipment
     *
     * @param   \Magento\Sales\Model\Order $order
     * @return  \Magento\Sales\Model\Order\Shipment
     */
    public function toShipment(\Magento\Sales\Model\Order $order)
    {
        $shipment = \Mage::getModel('Magento\Sales\Model\Order\Shipment');
        $shipment->setOrder($order)
            ->setStoreId($order->getStoreId())
            ->setCustomerId($order->getCustomerId())
            ->setBillingAddressId($order->getBillingAddressId())
            ->setShippingAddressId($order->getShippingAddressId());

        \Mage::helper('Magento\Core\Helper\Data')->copyFieldset('sales_convert_order', 'to_shipment', $order, $shipment);
        return $shipment;
    }

    /**
     * Convert order item object to Shipment item
     *
     * @param   \Magento\Sales\Model\Order\Item $item
     * @return  \Magento\Sales\Model\Order\Shipment\Item
     */
    public function itemToShipmentItem(\Magento\Sales\Model\Order\Item $item)
    {
        $shipmentItem = \Mage::getModel('Magento\Sales\Model\Order\Shipment\Item');
        $shipmentItem->setOrderItem($item)
            ->setProductId($item->getProductId());

        \Mage::helper('Magento\Core\Helper\Data')->copyFieldset('sales_convert_order_item', 'to_shipment_item', $item, $shipmentItem);
        return $shipmentItem;
    }

    /**
     * Convert order object to creditmemo
     *
     * @param   \Magento\Sales\Model\Order $order
     * @return  \Magento\Sales\Model\Order\Creditmemo
     */
    public function toCreditmemo(\Magento\Sales\Model\Order $order)
    {
        $creditmemo = \Mage::getModel('Magento\Sales\Model\Order\Creditmemo');
        $creditmemo->setOrder($order)
            ->setStoreId($order->getStoreId())
            ->setCustomerId($order->getCustomerId())
            ->setBillingAddressId($order->getBillingAddressId())
            ->setShippingAddressId($order->getShippingAddressId());

        \Mage::helper('Magento\Core\Helper\Data')->copyFieldset('sales_convert_order', 'to_cm', $order, $creditmemo);
        return $creditmemo;
    }

    /**
     * Convert order item object to Creditmemo item
     *
     * @param   \Magento\Sales\Model\Order\Item $item
     * @return  \Magento\Sales\Model\Order\Creditmemo\Item
     */
    public function itemToCreditmemoItem(\Magento\Sales\Model\Order\Item $item)
    {
        $creditmemoItem = \Mage::getModel('Magento\Sales\Model\Order\Creditmemo\Item');
        $creditmemoItem->setOrderItem($item)
            ->setProductId($item->getProductId());

        \Mage::helper('Magento\Core\Helper\Data')->copyFieldset('sales_convert_order_item', 'to_cm_item', $item, $creditmemoItem);
        return $creditmemoItem;
    }
}
