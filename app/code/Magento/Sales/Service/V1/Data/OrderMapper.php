<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

/**
 * Class OrderMapper
 */
class OrderMapper
{
    /**
     * @var OrderBuilder
     */
    protected $orderBuilder;

    /**
     * @var OrderItemMapper
     */
    protected $orderItemMapper;

    /**
     * @var OrderPaymentMapper
     */
    protected $orderPaymentMapper;

    /**
     * @var OrderAddressMapper
     */
    protected $orderAddressMapper;

    /**
     * @param OrderBuilder $orderBuilder
     * @param OrderItemMapper $orderItemMapper
     * @param OrderPaymentMapper $orderPaymentMapper
     * @param OrderAddressMapper $orderAddressMapper
     */
    public function __construct(
        OrderBuilder $orderBuilder,
        OrderItemMapper $orderItemMapper,
        OrderPaymentMapper $orderPaymentMapper,
        OrderAddressMapper $orderAddressMapper
    ) {
        $this->orderBuilder = $orderBuilder;
        $this->orderItemMapper = $orderItemMapper;
        $this->orderPaymentMapper = $orderPaymentMapper;
        $this->orderAddressMapper = $orderAddressMapper;
    }

    /**
     * Returns array of items
     *
     * @param \Magento\Sales\Model\Order $object
     * @return OrderItem[]
     */
    protected function getItems(\Magento\Sales\Model\Order $object)
    {
        $items = [];
        foreach ($object->getItemsCollection() as $item) {
            $items[] = $this->orderItemMapper->extractDto($item);
        }
        return $items;
    }

    /**
     * Returns array of payments
     *
     * @param \Magento\Sales\Model\Order $object
     * @return OrderPayment[]
     */
    protected function getPayments(\Magento\Sales\Model\Order $object)
    {
        $payments = [];
        foreach ($object->getPaymentsCollection() as $payment) {
            $payments[] = $this->orderPaymentMapper->extractDto($payment);
        }
        return $payments;
    }

    /**
     * Return billing address
     *
     * @param \Magento\Sales\Model\Order $object
     * @return OrderAddress|null
     */
    protected function getBillingAddress(\Magento\Sales\Model\Order $object)
    {
        $billingAddress = null;
        if ($object->getBillingAddress()) {
            $billingAddress = $this->orderAddressMapper->extractDto($object->getBillingAddress());
        }
        return $billingAddress;
    }

    /**
     * Returns shipping address
     *
     * @param \Magento\Sales\Model\Order $object
     * @return OrderAddress|null
     */
    protected function getShippingAddress(\Magento\Sales\Model\Order $object)
    {
        $shippingAddress = null;
        if ($object->getShippingAddress()) {
            $shippingAddress = $this->orderAddressMapper->extractDto($object->getShippingAddress());
        }
        return $shippingAddress;
    }

    /**
     * @param \Magento\Sales\Model\Order $object
     * @return \Magento\Sales\Service\V1\Data\Order
     */
    public function extractDto(\Magento\Sales\Model\Order $object)
    {
        $this->orderBuilder->populateWithArray($object->getData());
        $this->orderBuilder->setItems($this->getItems($object));
        $this->orderBuilder->setPayments($this->getPayments($object));
        $this->orderBuilder->setBillingAddress($this->getBillingAddress($object));
        $this->orderBuilder->setShippingAddress($this->getShippingAddress($object));
        return $this->orderBuilder->create();
    }
}
