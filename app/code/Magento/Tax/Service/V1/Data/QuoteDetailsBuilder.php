<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data;

use Magento\Framework\Api\AttributeDataBuilder;
use Magento\Framework\Api\MetadataServiceInterface;
use Magento\Customer\Api\Data\AddressInterface as CustomerAddress;
use Magento\Customer\Api\Data\AddressDataBuilder as CustomerAddressBuilder;

/**
 * QuoteDetailsBuilder
 *
 * @method QuoteDetails create()
 */
class QuoteDetailsBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
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
     * @var CustomerAddressBuilder
     */
    protected $customerAddressBuilder;

    /**
     * TaxClassKey builder
     *
     * @var \Magento\Tax\Service\V1\Data\TaxClassKeyBuilder
     */
    protected $taxClassKeyBuilder;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Api\ObjectFactory $objectFactory
     * @param AttributeDataBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     * @param \Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder $itemBuilder
     * @param \Magento\Tax\Service\V1\Data\TaxClassKeyBuilder $taxClassKeyBuilder
     * @param CustomerAddressBuilder $customerAddressBuilder
     */
    public function __construct(
        \Magento\Framework\Api\ObjectFactory $objectFactory,
        AttributeDataBuilder $valueBuilder,
        MetadataServiceInterface $metadataService,
        \Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder $itemBuilder,
        \Magento\Tax\Service\V1\Data\TaxClassKeyBuilder $taxClassKeyBuilder,
        CustomerAddressBuilder $customerAddressBuilder
    ) {
        parent::__construct($objectFactory, $valueBuilder, $metadataService);
        $this->itemBuilder = $itemBuilder;
        $this->taxClassKeyBuilder = $taxClassKeyBuilder;
        $this->customerAddressBuilder = $customerAddressBuilder;
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
     * @param CustomerAddress $address
     * @return $this
     */
    public function setBillingAddress($address)
    {
        return $this->_set(QuoteDetails::KEY_BILLING_ADDRESS, $address);
    }

    /**
     * Set customer shipping address
     *
     * @param CustomerAddress $address
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
     * Set customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->_set(QuoteDetails::KEY_CUSTOMER_ID, $customerId);
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
     * Set quote items
     *
     * @param int $customerTaxClassId
     * @return $this
     */
    public function setCustomerTaxClassId($customerTaxClassId)
    {
        return $this->_set(QuoteDetails::CUSTOMER_TAX_CLASS_ID, $customerTaxClassId);
    }

    /**
     * {@inheritdoc}
     */
    protected function _setDataValues(array $data)
    {
        if (array_key_exists(QuoteDetails::KEY_BILLING_ADDRESS, $data)) {
            $data[QuoteDetails::KEY_BILLING_ADDRESS] = $this->customerAddressBuilder->populateWithArray(
                $data[QuoteDetails::KEY_BILLING_ADDRESS]
            )->create();
        }
        if (array_key_exists(QuoteDetails::KEY_SHIPPING_ADDRESS, $data)) {
            $data[QuoteDetails::KEY_SHIPPING_ADDRESS] = $this->customerAddressBuilder->populateWithArray(
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
