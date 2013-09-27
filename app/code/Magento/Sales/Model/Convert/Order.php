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
 */
class Magento_Sales_Model_Convert_Order extends Magento_Object
{
    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @var Magento_Sales_Model_QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var Magento_Sales_Model_Quote_AddressFactory
     */
    protected $_quoteAddressFactory;

    /**
     * @var Magento_Sales_Model_Quote_PaymentFactory
     */
    protected $_quotePaymentFactory;

    /**
     * @var Magento_Sales_Model_Quote_ItemFactory
     */
    protected $_quoteItemFactory;

    /**
     * @var Magento_Sales_Model_Order_Invoice
     */
    protected $_orderInvoiceFactory;

    /**
     * @var Magento_Sales_Model_Order_Invoice_ItemFactory
     */
    protected $_invoiceItemFactory;

    /**
     * @var Magento_Sales_Model_Order_ShipmentFactory
     */
    protected $_orderShipmentFactory;

    /**
     * @var Magento_Sales_Model_Order_CreditmemoFactory
     */
    protected $_creditmemoFactory;

    /**
     * @var Magento_Sales_Model_Order_Creditmemo_ItemFactory
     */
    protected $_creditmemoItemFactory;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Sales_Model_QuoteFactory $quoteFactory
     * @param Magento_Sales_Model_Quote_AddressFactory $quoteAddressFactory
     * @param Magento_Sales_Model_Quote_PaymentFactory $quotePaymentFactory
     * @param Magento_Sales_Model_Quote_ItemFactory $quoteItemFactory
     * @param Magento_Sales_Model_Order_InvoiceFactory $orderInvoiceFactory
     * @param Magento_Sales_Model_Order_Invoice_ItemFactory $invoiceItemFactory
     * @param Magento_Sales_Model_Order_ShipmentFactory $orderShipmentFactory
     * @param Magento_Sales_Model_Order_Shipment_ItemFactory $shipmentItemFactory
     * @param Magento_Sales_Model_Order_CreditmemoFactory $creditmemoFactory
     * @param Magento_Sales_Model_Order_Creditmemo_ItemFactory $creditmemoItemFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Sales_Model_QuoteFactory $quoteFactory,
        Magento_Sales_Model_Quote_AddressFactory $quoteAddressFactory,
        Magento_Sales_Model_Quote_PaymentFactory $quotePaymentFactory,
        Magento_Sales_Model_Quote_ItemFactory $quoteItemFactory,
        Magento_Sales_Model_Order_InvoiceFactory $orderInvoiceFactory,
        Magento_Sales_Model_Order_Invoice_ItemFactory $invoiceItemFactory,
        Magento_Sales_Model_Order_ShipmentFactory $orderShipmentFactory,
        Magento_Sales_Model_Order_Shipment_ItemFactory $shipmentItemFactory,
        Magento_Sales_Model_Order_CreditmemoFactory $creditmemoFactory,
        Magento_Sales_Model_Order_Creditmemo_ItemFactory $creditmemoItemFactory,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        $this->_coreData = $coreData;
        $this->_quoteFactory = $quoteFactory;
        $this->_quoteAddressFactory = $quoteAddressFactory;
        $this->_quotePaymentFactory = $quotePaymentFactory;
        $this->_quoteItemFactory = $quoteItemFactory;
        $this->_orderInvoiceFactory = $orderInvoiceFactory;
        $this->_invoiceItemFactory = $invoiceItemFactory;
        $this->_orderShipmentFactory = $orderShipmentFactory;
        $this->_shipmentItemFactory = $shipmentItemFactory;
        $this->_creditmemoFactory = $creditmemoFactory;
        $this->_creditmemoItemFactory = $creditmemoItemFactory;
        parent::__construct($data);
    }

    /**
     * Converting order object to quote object
     *
     * @param Magento_Sales_Model_Order $order
     * @param null|Magento_Sales_Model_Quote $quote
     * @return Magento_Sales_Model_Quote
     */
    public function toQuote(Magento_Sales_Model_Order $order, $quote = null)
    {
        if (!($quote instanceof Magento_Sales_Model_Quote)) {
            $quote = $this->_quoteFactory->create();
        }

        $quote->setStoreId($order->getStoreId())
            ->setOrderId($order->getId());

        $this->_coreData->copyFieldsetToTarget('sales_convert_order', 'to_quote', $order, $quote);

        $this->_eventManager->dispatch('sales_convert_order_to_quote', array('order' => $order, 'quote' => $quote));
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

        $this->_coreData->copyFieldsetToTarget('sales_convert_order', 'to_quote_address', $order, $address);
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
        $quoteAddress = $this->_quoteAddressFactory->create()
            ->setStoreId($address->getStoreId())
            ->setAddressType($address->getAddressType())
            ->setCustomerId($address->getCustomerId())
            ->setCustomerAddressId($address->getCustomerAddressId());

        $this->_coreData->copyFieldsetToTarget(
            'sales_convert_order_address',
            'to_quote_address',
            $address,
            $quoteAddress
        );
        return $quoteAddress;
    }

    /**
     * Convert order payment to quote payment
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @param null|Magento_Sales_Model_Quote_Payment $quotePayment
     * @return Magento_Sales_Model_Quote_Payment
     */
    public function paymentToQuotePayment(Magento_Sales_Model_Order_Payment $payment, $quotePayment = null)
    {
        if (!($quotePayment instanceof Magento_Sales_Model_Quote_Payment)) {
            $quotePayment = $this->_quotePaymentFactory->create();
        }

        $quotePayment->setStoreId($payment->getStoreId())
            ->setCustomerPaymentId($payment->getCustomerPaymentId());

        $this->_coreData->copyFieldsetToTarget(
            'sales_convert_order_payment',
            'to_quote_payment',
            $payment,
            $quotePayment
        );
        return $quotePayment;
    }

    /**
     * Retrieve
     *
     * @param Magento_Sales_Model_Order_Item $item
     * @return Magento_Sales_Model_Quote_Item
     */
    public function itemToQuoteItem(Magento_Sales_Model_Order_Item $item)
    {
        $quoteItem = $this->_quoteItemFactory->create()
            ->setStoreId($item->getOrder()->getStoreId())
            ->setQuoteItemId($item->getId())
            ->setProductId($item->getProductId())
            ->setParentProductId($item->getParentProductId());

        $this->_coreData->copyFieldsetToTarget('sales_convert_order_item', 'to_quote_item', $item, $quoteItem);
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
        $invoice = $this->_orderInvoiceFactory->create();
        $invoice->setOrder($order)
            ->setStoreId($order->getStoreId())
            ->setCustomerId($order->getCustomerId())
            ->setBillingAddressId($order->getBillingAddressId())
            ->setShippingAddressId($order->getShippingAddressId());

        $this->_coreData->copyFieldsetToTarget('sales_convert_order', 'to_invoice', $order, $invoice);
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
        $invoiceItem = $this->_invoiceItemFactory->create();
        $invoiceItem->setOrderItem($item)
            ->setProductId($item->getProductId());

        $this->_coreData->copyFieldsetToTarget('sales_convert_order_item', 'to_invoice_item', $item, $invoiceItem);
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
        $shipment = $this->_orderShipmentFactory->create();
        $shipment->setOrder($order)
            ->setStoreId($order->getStoreId())
            ->setCustomerId($order->getCustomerId())
            ->setBillingAddressId($order->getBillingAddressId())
            ->setShippingAddressId($order->getShippingAddressId());

        $this->_coreData->copyFieldsetToTarget('sales_convert_order', 'to_shipment', $order, $shipment);
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
        $shipmentItem = $this->_shipmentItemFactory->create();
        $shipmentItem->setOrderItem($item)
            ->setProductId($item->getProductId());

        $this->_coreData->copyFieldsetToTarget('sales_convert_order_item', 'to_shipment_item', $item, $shipmentItem);
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
        $creditmemo = $this->_creditmemoFactory->create();
        $creditmemo->setOrder($order)
            ->setStoreId($order->getStoreId())
            ->setCustomerId($order->getCustomerId())
            ->setBillingAddressId($order->getBillingAddressId())
            ->setShippingAddressId($order->getShippingAddressId());

        $this->_coreData->copyFieldsetToTarget('sales_convert_order', 'to_cm', $order, $creditmemo);
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
        $creditmemoItem = $this->_creditmemoItemFactory->create();
        $creditmemoItem->setOrderItem($item)
            ->setProductId($item->getProductId());

        $this->_coreData->copyFieldsetToTarget('sales_convert_order_item', 'to_cm_item', $item, $creditmemoItem);
        return $creditmemoItem;
    }
}
