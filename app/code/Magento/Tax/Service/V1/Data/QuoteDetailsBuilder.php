<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data;

class QuoteDetailsBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * QuoteDetails item builder
     *
     * @var \Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder
     */
    protected $itemBuilder;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Service\Data\ObjectFactory $objectFactory
     * @param \Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder $itemBuilder
     */
    public function __construct(
        \Magento\Framework\Service\Data\ObjectFactory $objectFactory,
        \Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder $itemBuilder
    ) {
        parent::__construct($objectFactory);
        $this->itemBuilder = $itemBuilder;
    }

    /**
     * Set customer billing address
     *
     * @param \Magento\Customer\Service\V1\Data\Address $address
     * @return $this
     */
    public function setBillingAddress($address)
    {
        return $this->_set(QuoteDetails::KEY_BILLING_ADDRESS, $address);
    }

    /**
     * Set customer shipping address
     *
     * @param \Magento\Customer\Service\V1\Data\Address $address
     * @return $this
     */
    public function setShippingAddress($address)
    {
        return $this->_set(QuoteDetails::KEY_SHIPPING_ADDRESS, $address);
    }

    /**
     * Set customer tax class id
     *
     * @param int $taxClassId
     * @return $this
     */
    public function setTaxClassId($taxClassId)
    {
        return $this->_set(QuoteDetails::KEY_TAX_CLASS_ID, $taxClassId);
    }

    /**
     * Set customer
     *
     * @param \Magento\Customer\Service\V1\Data\Customer $customer
     * @return $this
     */
    public function setCustomer($customer)
    {
        return $this->_set(QuoteDetails::KEY_CUSTOMER, $customer);
    }

    /**
     * Set customer group
     *
     * @param \Magento\Customer\Service\V1\Data\CustomerGroup $customerGroup
     * @return $this
     */
    public function setCustomerGroup($customerGroup)
    {
        return $this->_set(QuoteDetails::KEY_CUSTOMER_GROUP, $customerGroup);
    }

    /**
     * Set quote items
     *
     * @param \Magento\Tax\Service\V1\Data\QuoteDetails\Item[]|null $items
     * @return $this
     */
    public function setItems($items)
    {
        return $this->_set(QuoteDetails::KEY_ITEMS, $items);
    }
}
