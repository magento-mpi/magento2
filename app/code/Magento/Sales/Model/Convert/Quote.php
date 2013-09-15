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
namespace Magento\Sales\Model\Convert;

class Quote extends \Magento\Object
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
     * @param   \Magento\Sales\Model\Quote $quote
     * @return  \Magento\Sales\Model\Order
     */
    public function toOrder(\Magento\Sales\Model\Quote $quote, $order=null)
    {
        if (!($order instanceof \Magento\Sales\Model\Order)) {
            $order = \Mage::getModel('Magento\Sales\Model\Order');
        }
        /* @var $order \Magento\Sales\Model\Order */

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
     * @param   \Magento\Sales\Model\Quote $quote
     * @return  \Magento\Sales\Model\Order
     */
    public function addressToOrder(\Magento\Sales\Model\Quote\Address $address, $order=null)
    {
        if (!($order instanceof \Magento\Sales\Model\Order)) {
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
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\Sales\Model\Order\Address
     */
    public function addressToOrderAddress(\Magento\Sales\Model\Quote\Address $address)
    {
        $orderAddress = \Mage::getModel('Magento\Sales\Model\Order\Address')
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
     * @param   \Magento\Sales\Model\Quote\Payment $payment
     * @return  \Magento\Sales\Model\Quote\Payment
     */
    public function paymentToOrderPayment(\Magento\Sales\Model\Quote\Payment $payment)
    {
        $orderPayment = \Mage::getModel('Magento\Sales\Model\Order\Payment')
            ->setStoreId($payment->getStoreId())
            ->setCustomerPaymentId($payment->getCustomerPaymentId());

        $this->_coreData->copyFieldsetToTarget(
            'sales_convert_quote_payment',
            'to_order_payment',
            $payment,
            $orderPayment
        );

        return $orderPayment;
    }

    /**
     * Convert quote item to order item
     *
     * @param   \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @return  \Magento\Sales\Model\Order\Item
     */
    public function itemToOrderItem(\Magento\Sales\Model\Quote\Item\AbstractItem $item)
    {
        $orderItem = \Mage::getModel('Magento\Sales\Model\Order\Item')
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

        return $orderItem;
    }
}
