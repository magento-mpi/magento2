<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model;

use Magento\Sales\Model\Order\Builder as OrderBuilder;
use Magento\Sales\Model\Order\ItemConverter;
use Magento\Sales\Model\Order\PaymentConverter;
use Magento\Sales\Model\Order\AddressConverter;
use Magento\Sales\Model\Order\Customer\Builder as CustomerBuilder;
use Magento\Sales\Service\V1\Data\Order as OrderData;

/**
 * Converter class for \Magento\Sales\Model\Order
 */
class OrderConverter
{
    /**
     * @var OrderBuilder
     */
    protected $orderBuilder;

    /**
     * @var ItemConverter
     */
    protected $itemConverter;

    /**
     * @var PaymentConverter
     */
    protected $paymentConverter;

    /**
     * @var AddressConverter
     */
    protected $addressConverter;

    /**
     * @var CustomerBuilder
     */
    protected $customerBuilder;

    /**
     * @param OrderBuilder $orderBuilder
     * @param ItemConverter $itemConverter
     * @param PaymentConverter $paymentConverter
     * @param AddressConverter $addressConverter
     * @param CustomerBuilder $customerBuilder
     */
    public function __construct(
        OrderBuilder $orderBuilder,
        ItemConverter $itemConverter,
        PaymentConverter $paymentConverter,
        AddressConverter $addressConverter,
        CustomerBuilder $customerBuilder
    ) {
        $this->orderBuilder = $orderBuilder;
        $this->itemConverter = $itemConverter;
        $this->paymentConverter = $paymentConverter;
        $this->addressConverter = $addressConverter;
        $this->customerBuilder = $customerBuilder;
    }

    /**
     * Get Order Customer
     *
     * @param OrderData $dataObject
     * @return Order\Customer
     */
    protected function getCustomer(OrderData $dataObject)
    {
        $this->customerBuilder->setDob($dataObject->getCustomerDob())
            ->setEmail($dataObject->getCustomerEmail())
            ->setFirstName($dataObject->getCustomerFirstname())
            ->setGender($dataObject->getCustomerGender())
            ->setGroupId($dataObject->getCustomerGroupId())
            ->setId($dataObject->getCustomerId())
            ->setIsGuest($dataObject->getCustomerIsGuest())
            ->setLastName($dataObject->getCustomerLastname())
            ->setMiddleName($dataObject->getCustomerMiddlename())
            ->setNote($dataObject->getCustomerNote())
            ->setNoteNotify($dataObject->getCustomerNoteNotify())
            ->setPrefix($dataObject->getCustomerPrefix())
            ->setSuffix($dataObject->getCustomerSuffix())
            ->setTaxvat($dataObject->getCustomerTaxvat());
        return $this->customerBuilder->create();
    }

    /**
     * Get Order Items
     *
     * @param OrderData $dataObject
     * @return array
     */
    protected function getItems(OrderData $dataObject)
    {
        $items = [];
        foreach ($dataObject->getItems() as $item) {
            $items[] = $this->itemConverter->getModel($item);
        }
        return $items;
    }

    /**
     * Get Order Payments
     *
     * @param OrderData $dataObject
     * @return array
     */
    protected function getPayments(OrderData $dataObject)
    {
        $payments = [];
        foreach ($dataObject->getPayments() as $payment) {
            $payments[] = $this->paymentConverter->getModel($payment);
        }
        return $payments;
    }

    /**
     * Get Order Model
     *
     * @param OrderData $dataObject
     * @return Order
     * @throws \Exception
     */
    public function getModel(OrderData $dataObject)
    {
        $this->orderBuilder->setCustomer($this->getCustomer($dataObject))
            ->setQuoteId($dataObject->getQuoteId())
            ->setAppliedRuleIds($dataObject->getAppliedRuleIds())
            ->setIsVirtual($dataObject->getIsVirtual())
            ->setRemoteIp($dataObject->getRemoteIp())
            ->setBaseSubtotal($dataObject->getBaseSubtotal())
            ->setSubtotal($dataObject->getSubtotal())
            ->setBaseGrandTotal($dataObject->getBaseGrandTotal())
            ->setGrandTotal($dataObject->getGrandTotal())
            ->setBaseCurrencyCode($dataObject->getBaseCurrencyCode())
            ->setGlobalCurrencyCode($dataObject->getGlobalCurrencyCode())
            ->setStoreCurrencyCode($dataObject->getStoreCurrencyCode())
            ->setStoreId($dataObject->getStoreId())
            ->setStoreToBaseRate($dataObject->getStoreToBaseRate())
            ->setBaseToGlobalRate($dataObject->getBaseToGlobalRate())
            ->setCouponCode($dataObject->getCouponCode())
            ->setBillingAddress($this->addressConverter->getModel($dataObject->getBillingAddress()))
            ->setShippingAddress($this->addressConverter->getModel($dataObject->getShippingAddress()))
            ->setPayments($this->getPayments($dataObject))
            ->setItems($this->getItems($dataObject));
        return $this->orderBuilder->create();
    }
}
