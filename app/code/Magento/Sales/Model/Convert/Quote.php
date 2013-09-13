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
 * Quote data convert model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Convert_Quote extends Magento_Object
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
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Data $coreData
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Data $coreData,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        $this->_coreData = $coreData;
        parent::__construct($data);
    }

    /**
     * Convert quote model to order model
     *
     * @param   Magento_Sales_Model_Quote $quote
     * @return  Magento_Sales_Model_Order
     */
    public function toOrder(Magento_Sales_Model_Quote $quote, $order=null)
    {
        if (!($order instanceof Magento_Sales_Model_Order)) {
            $order = Mage::getModel('Magento_Sales_Model_Order');
        }
        /* @var $order Magento_Sales_Model_Order */

        $order->setIncrementId($quote->getReservedOrderId())
            ->setStoreId($quote->getStoreId())
            ->setQuoteId($quote->getId())
            ->setQuote($quote)
            ->setCustomer($quote->getCustomer());

        $this->_coreData->copyFieldsetToTarget('sales_convert_quote', 'to_order', $quote, $order);
        $this->_eventManager->dispatch('sales_convert_quote_to_order', array('order'=>$order, 'quote'=>$quote));
        return $order;
    }

    /**
     * Convert quote address model to order
     *
     * @param   Magento_Sales_Model_Quote $quote
     * @return  Magento_Sales_Model_Order
     */
    public function addressToOrder(Magento_Sales_Model_Quote_Address $address, $order=null)
    {
        if (!($order instanceof Magento_Sales_Model_Order)) {
            $order = $this->toOrder($address->getQuote());
        }

        $this->_coreData->copyFieldsetToTarget(
            'sales_convert_quote_address',
            'to_order',
            $address,
            $order
        );

        $this->_eventManager->dispatch('sales_convert_quote_address_to_order', array('address'=>$address, 'order'=>$order));
        return $order;
    }

    /**
     * Convert quote address to order address
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  Magento_Sales_Model_Order_Address
     */
    public function addressToOrderAddress(Magento_Sales_Model_Quote_Address $address)
    {
        $orderAddress = Mage::getModel('Magento_Sales_Model_Order_Address')
            ->setStoreId($address->getStoreId())
            ->setAddressType($address->getAddressType())
            ->setCustomerId($address->getCustomerId())
            ->setCustomerAddressId($address->getCustomerAddressId());

        $this->_coreData->copyFieldsetToTarget(
            'sales_convert_quote_address',
            'to_order_address',
            $address,
            $orderAddress
        );

        $this->_eventManager->dispatch('sales_convert_quote_address_to_order_address',
            array('address' => $address, 'order_address' => $orderAddress));

        return $orderAddress;
    }

    /**
     * Convert quote payment to order payment
     *
     * @param   Magento_Sales_Model_Quote_Payment $payment
     * @return  Magento_Sales_Model_Quote_Payment
     */
    public function paymentToOrderPayment(Magento_Sales_Model_Quote_Payment $payment)
    {
        $orderPayment = Mage::getModel('Magento_Sales_Model_Order_Payment')
            ->setStoreId($payment->getStoreId())
            ->setCustomerPaymentId($payment->getCustomerPaymentId());

        $this->_coreData->copyFieldsetToTarget(
            'sales_convert_quote_payment',
            'to_order_payment',
            $payment,
            $orderPayment
        );

        $this->_eventManager->dispatch('sales_convert_quote_payment_to_order_payment',
            array('order_payment' => $orderPayment, 'quote_payment' => $payment));

        return $orderPayment;
    }

    /**
     * Convert quote item to order item
     *
     * @param   Magento_Sales_Model_Quote_Item_Abstract $item
     * @return  Magento_Sales_Model_Order_Item
     */
    public function itemToOrderItem(Magento_Sales_Model_Quote_Item_Abstract $item)
    {
        $orderItem = Mage::getModel('Magento_Sales_Model_Order_Item')
            ->setStoreId($item->getStoreId())
            ->setQuoteItemId($item->getId())
            ->setQuoteParentItemId($item->getParentItemId())
            ->setProductId($item->getProductId())
            ->setProductType($item->getProductType())
            ->setQtyBackordered($item->getBackorders())
            ->setProduct($item->getProduct())
            ->setBaseOriginalPrice($item->getBaseOriginalPrice())
        ;

        $options = $item->getProductOrderOptions();
        if (!$options) {
            $options = $item->getProduct()->getTypeInstance()->getOrderOptions($item->getProduct());
        }
        $orderItem->setProductOptions($options);
        $this->_coreData->copyFieldsetToTarget(
            'sales_convert_quote_item',
            'to_order_item',
            $item,
            $orderItem
        );

        if ($item->getParentItem()) {
            $orderItem->setQtyOrdered($orderItem->getQtyOrdered()*$item->getParentItem()->getQty());
        }

        if (!$item->getNoDiscount()) {
            $this->_coreData->copyFieldsetToTarget(
                'sales_convert_quote_item',
                'to_order_item_discount',
                $item,
                $orderItem
            );
        }

        $this->_eventManager->dispatch('sales_convert_quote_item_to_order_item',
            array('order_item'=>$orderItem, 'item'=>$item)
        );
        return $orderItem;
    }
}
