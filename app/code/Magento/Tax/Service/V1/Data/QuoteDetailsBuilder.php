<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data;

/**
 * QuoteDetailsBuilder
 *
 * @method QuoteDetails create()
 */
class QuoteDetailsBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * QuoteDetails item builder
     *
     * @var \Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder
     */
    protected $itemBuilder;

    /**
     * Address builder
     *
     * @var \Magento\Customer\Service\V1\Data\AddressBuilder
     */
    protected $addressBuilder;

    /**
     * TaxClassKey builder
     *
     * @var \Magento\Tax\Service\V1\Data\TaxClassKeyBuilder
     */
    protected $taxClassKeyBuilder;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Service\Data\ObjectFactory $objectFactory
     * @param \Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder $itemBuilder
     * @param \Magento\Tax\Service\V1\Data\TaxClassKeyBuilder $taxClassKeyBuilder
     * @param \Magento\Customer\Service\V1\Data\AddressBuilder $addressBuilder
     */
    public function __construct(
        \Magento\Framework\Service\Data\ObjectFactory $objectFactory,
        \Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder $itemBuilder,
        \Magento\Tax\Service\V1\Data\TaxClassKeyBuilder $taxClassKeyBuilder,
        \Magento\Customer\Service\V1\Data\AddressBuilder $addressBuilder
    ) {
        parent::__construct($objectFactory);
        $this->itemBuilder = $itemBuilder;
        $this->taxClassKeyBuilder = $taxClassKeyBuilder;
        $this->addressBuilder = $addressBuilder;
    }

    /**
     * Convenience method to return item builder
     *
     * @return QuoteDetails\ItemBuilder
     */
    public function getItemBuilder()
    {
        return $this->itemBuilder;
    }

    /**
     * Convenience method to return address builder
     *
     * @return \Magento\Customer\Service\V1\Data\AddressBuilder
     */
    public function getAddressBuilder()
    {
        return $this->addressBuilder;
    }

    /**
     * Get tax class key builder
     *
     * @return TaxClassKeyBuilder
     */
    public function getTaxClassKeyBuilder()
    {
        return $this->taxClassKeyBuilder;
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
     * Set customer tax class key
     *
     * @param \Magento\Tax\Service\V1\Data\TaxClassKey $taxClassKey
     * @return $this
     */
    public function setCustomerTaxClassKey($taxClassKey)
    {
        return $this->_set(QuoteDetails::KEY_CUSTOMER_TAX_CLASS_KEY, $taxClassKey);
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

    /**
     * {@inheritdoc}
     */
    protected function _setDataValues(array $data)
    {
        if (array_key_exists(QuoteDetails::KEY_BILLING_ADDRESS, $data)) {
            $data[QuoteDetails::KEY_BILLING_ADDRESS] = $this->addressBuilder->populateWithArray(
                $data[QuoteDetails::KEY_BILLING_ADDRESS]
            )->create();
        }
        if (array_key_exists(QuoteDetails::KEY_SHIPPING_ADDRESS, $data)) {
            $data[QuoteDetails::KEY_SHIPPING_ADDRESS] = $this->addressBuilder->populateWithArray(
                $data[QuoteDetails::KEY_SHIPPING_ADDRESS]
            )->create();
        }
        if (array_key_exists(QuoteDetails::KEY_ITEMS, $data)) {
            $items = [];
            foreach ($data[QuoteDetails::KEY_ITEMS] as $itemArray) {
                $items[] = $this->itemBuilder->populateWithArray($itemArray)->create();
            }
            $data[QuoteDetails::KEY_ITEMS] = $items;
        }
        if (array_key_exists(QuoteDetails::KEY_CUSTOMER_TAX_CLASS_KEY, $data)) {
            $data[QuoteDetails::KEY_CUSTOMER_TAX_CLASS_KEY] = $this->taxClassKeyBuilder->populateWithArray(
                $data[QuoteDetails::KEY_CUSTOMER_TAX_CLASS_KEY]
            )->create();
        }
        return parent::_setDataValues($data);
    }
}
