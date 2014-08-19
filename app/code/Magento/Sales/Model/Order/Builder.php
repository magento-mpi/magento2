<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Order;

//use Magento\Sales\Model\AdminOrder\Create as OrderCreator;

use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\Customer;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Quote;
use Magento\Catalog\Model\Product;
use Magento\Webapi\Exception;

class Builder
{
    protected $billingAddress;
    protected $shippingAddress;
    protected $items;
    protected $payments;
    /**
     * @var \Magento\Sales\Model\Order\Customer
     */
    protected $customer;

    protected $quoteId;        //quote_id
    protected $appliedRuleIds; //'applied_rule_ids',
    protected $isVirtual;      //'is_virtual',
    protected $remoteIp;       //'remote_ip',


    protected $baseSubtotal; //'base_subtotal',
    protected $subtotal; //'subtotal',
    protected $baseGrandTotal; //'base_grand_total',
    protected $grandTotal; //'grand_total',

    protected $baseCurrencyCode; //'base_currency_code',
    protected $globalCurrencyCode; //'global_currency_code',
    protected $storeCurrencyCode; //'store_currency_code'
    protected $storeId; //'store_id',
    protected $storeToBaseRate; //'store_to_base_rate',
    protected $baseToGlobalRate; //'base_to_global_rate',

    protected $couponCode;     //'coupon_code',

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    public function __construct(
        OrderFactory $orderFactory
    ) {
        $this->orderFactory = $orderFactory;
    }

    /**
     * @param Customer $customer
     * @return $this
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @param Address $address
     * @return $this
     */
    public function setShippingAddress(Address $address)
    {
        $this->shippingAddress = $address;
        return $this;
    }

    /**
     * @param Address $address
     * @return $this
     */
    public function setBillingAddress(Address $address)
    {
        $this->billingAddress = $address;
        return $this;
    }

    /**
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId)
    {
        $this->quoteId = $quoteId;
        return $this;
    }

    /**
     * @param array $appliedRuleIds
     * @return $this
     */
    public function setAppliedRuleIds($appliedRuleIds)
    {
        $this->appliedRuleIds = $appliedRuleIds;
        return $this;
    }

    /**
     * @param $isVirtual
     * @return $this
     */
    public function setIsVirtual($isVirtual)
    {
        $this->isVirtual = $isVirtual;
        return $this;
    }

    /**
     * @param $remoteIp
     * @return $this
     */
    public function setRemoteIp($remoteIp)
    {
        $this->remoteIp = $remoteIp;
        return $this;
    }

    /**
     * @param $baseSubtotal
     * @return $this
     */
    public function setBaseSubtotal($baseSubtotal)
    {
        $this->baseSubtotal = $baseSubtotal;
        return $this;
    }

    /**
     * @param $subtotal
     * @return $this
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
        return $this;
    }

    /**
     * @param $baseGrandTotal
     * @return $this
     */
    public function setBaseGrandTotal($baseGrandTotal)
    {
        $this->baseGrandTotal = $baseGrandTotal;
        return $this;
    }

    /**
     * @param $grandTotal
     * @return $this
     */
    public function setGrandTotal($grandTotal)
    {
        $this->grandTotal = $grandTotal;
        return $this;
    }

    /**
     * @param $baseCurrencyCode
     * @return $this
     */
    public function setBaseCurrencyCode($baseCurrencyCode)
    {
        $this->baseCurrencyCode = $baseCurrencyCode;
        return $this;
    }

    /**
     * @param $globalCurrencyCode
     * @return $this
     */
    public function setGlobalCurrencyCode($globalCurrencyCode)
    {
        $this->globalCurrencyCode = $globalCurrencyCode;
        return $this;
    }

    /**
     * @param $storeCurrencyCode
     * @return $this
     */
    public function setStoreCurrencyCode($storeCurrencyCode)
    {
        $this->storeCurrencyCode = $storeCurrencyCode;
        return $this;
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * @param $storeToBaseRate
     * @return $this
     */
    public function setStoreToBaseRate($storeToBaseRate)
    {
        $this->storeToBaseRate = $storeToBaseRate;
        return $this;
    }

    /**
     * @param $baseToGlobalRate
     * @return $this
     */
    public function setBaseToGlobalRate($baseToGlobalRate)
    {
        $this->baseToGlobalRate = $baseToGlobalRate;
        return $this;
    }

    /**
     * @param $couponCode
     * @return $this
     */
    public function setCouponCode($couponCode)
    {
        $this->couponCode = $couponCode;
        return $this;
    }

    /**
     * @param Item[] $items
     * @return $this
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @param Payment[] $payments
     * @return $this
     */
    public function setPayments($payments)
    {
        $this->payments = $payments;
        return $this;
    }

    public function create()
    {
        /**@var $order \Magento\Sales\Model\Order */
        $order = $this->orderFactory->create([
            'data' => [
                'quote_id' => $this->quoteId,
                'applied_rule_ids' => $this->appliedRuleIds,
                'is_virtual' => $this->isVirtual,
                'remote_ip' => $this->remoteIp,
                'base_subtotal' => $this->baseSubtotal,
                'subtotal' => $this->subtotal,
                'base_grand_total' => $this->baseGrandTotal,
                'grand_total' => $this->grandTotal,
                'base_currency_code' => $this->baseCurrencyCode,
                'global_currency_code' => $this->globalCurrencyCode,
                'store_currency_code' => $this->storeCurrencyCode,
                'store_id' => $this->storeId,
                'store_to_base_rate' => $this->storeToBaseRate,
                'base_to_global_rate' => $this->baseToGlobalRate,
                'coupon_code' => $this->couponCode,
                'customer_dob' => $this->customer->getDob(),
                'customer_email' => $this->customer->getEmail(),
                'customer_firstname' => $this->customer->getFirstName(),
                'customer_gender' => $this->customer->getGender(),
                'customer_group_id' => $this->customer->getGroupId(),
                'customer_id' => $this->customer->getId(),
                'customer_is_guest' => $this->customer->getIsGuest(),
                'customer_lastname' => $this->customer->getLastName(),
                'customer_middlename' => $this->customer->getMiddleName(),
                'customer_note' => $this->customer->getNote(),
                'customer_note_notify' => $this->customer->getNoteNotify(),
                'customer_prefix' => $this->customer->getPrefix(),
                'customer_suffix' => $this->customer->getSuffix(),
                'customer_taxvat' => $this->customer->getTaxvat()
            ]
        ]);
        $order->setBillingAddress($this->billingAddress)
            ->setShippingAddress($this->shippingAddress);
        foreach ($this->items as $item) {
            if ($item instanceof Item) {
                $order->addItem($item);
            } else {
                throw new \Exception('...');
            }
        }
        foreach ($this->payments as $payment) {
            if ($payment instanceof Payment) {
                $order->addPayment($payment);
            } else {
                throw new \Exception('...');
            }
        }
        return $order;
    }
}