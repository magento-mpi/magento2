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

        \Mage::helper('Magento\Core\Helper\Data')->copyFieldset('sales_convert_quote', 'to_order', $quote, $order);
        \Mage::dispatchEvent('sales_convert_quote_to_order', array('order'=>$order, 'quote'=>$quote));
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

        \Mage::helper('Magento\Core\Helper\Data')->copyFieldset(
            'sales_convert_quote_address',
            'to_order',
            $address,
            $order
        );

        \Mage::dispatchEvent('sales_convert_quote_address_to_order', array('address'=>$address, 'order'=>$order));
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

        \Mage::helper('Magento\Core\Helper\Data')->copyFieldset(
            'sales_convert_quote_address',
            'to_order_address',
            $address,
            $orderAddress
        );

        \Mage::dispatchEvent('sales_convert_quote_address_to_order_address',
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

        \Mage::helper('Magento\Core\Helper\Data')->copyFieldset(
            'sales_convert_quote_payment',
            'to_order_payment',
            $payment,
            $orderPayment
        );

        \Mage::dispatchEvent('sales_convert_quote_payment_to_order_payment',
            array('order_payment' => $orderPayment, 'quote_payment' => $payment));

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
        \Mage::helper('Magento\Core\Helper\Data')->copyFieldset(
            'sales_convert_quote_item',
            'to_order_item',
            $item,
            $orderItem
        );

        if ($item->getParentItem()) {
            $orderItem->setQtyOrdered($orderItem->getQtyOrdered()*$item->getParentItem()->getQty());
        }

        if (!$item->getNoDiscount()) {
            \Mage::helper('Magento\Core\Helper\Data')->copyFieldset(
                'sales_convert_quote_item',
                'to_order_item_discount',
                $item,
                $orderItem
            );
        }

        \Mage::dispatchEvent('sales_convert_quote_item_to_order_item',
            array('order_item'=>$orderItem, 'item'=>$item)
        );
        return $orderItem;
    }
}
