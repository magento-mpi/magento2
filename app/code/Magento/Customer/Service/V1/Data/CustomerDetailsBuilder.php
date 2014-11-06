<?php
/**
 * CustomerDetailsBuilder class
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

use Magento\Framework\Api\ExtensibleObjectBuilder;
use Magento\Framework\Api\AttributeValueBuilder;
use Magento\Framework\Api\MetadataServiceInterface;

/**
 * Class CustomerDetailsBuilder
 */
class CustomerDetailsBuilder extends ExtensibleObjectBuilder
{
    /**
     * Customer builder
     *
     * @var \Magento\Customer\Service\V1\Data\CustomerBuilder
     */
    protected $_customerBuilder;

    /**
     * Address builder
     *
     * @var \Magento\Customer\Service\V1\Data\AddressBuilder
     */
    protected $_addressBuilder;

    /**
     * @param \Magento\Framework\Api\ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     * @param CustomerBuilder $customerBuilder
     * @param AddressBuilder $addressBuilder
     */
    public function __construct(
        \Magento\Framework\Api\ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
        MetadataServiceInterface $metadataService,
        \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder,
        \Magento\Customer\Service\V1\Data\AddressBuilder $addressBuilder
    ) {
        parent::__construct($objectFactory, $valueBuilder, $metadataService);
        $this->_customerBuilder = $customerBuilder;
        $this->_addressBuilder = $addressBuilder;
    }

    /**
     * {@inheritdoc}
     */
    protected function _setDataValues(array $data)
    {
        $newData = array();
        if (isset($data[CustomerDetails::KEY_CUSTOMER])) {
            $newData[CustomerDetails::KEY_CUSTOMER] = $this->_customerBuilder->populateWithArray(
                $data[CustomerDetails::KEY_CUSTOMER]
            )->create();
        }

        if (isset($data[CustomerDetails::KEY_ADDRESSES])) {
            $newData[CustomerDetails::KEY_ADDRESSES] = array();
            $addresses = $data[CustomerDetails::KEY_ADDRESSES];
            foreach ($addresses as $address) {
                $newData[CustomerDetails::KEY_ADDRESSES][] = $this->_addressBuilder->populateWithArray(
                    $address
                )->create();
            }
        }

        return parent::_setDataValues($newData);
    }

    /**
     * Set customer
     *
     * @param \Magento\Customer\Service\V1\Data\Customer $customer
     * @return $this
     */
    public function setCustomer(Customer $customer)
    {
        return $this->_set(CustomerDetails::KEY_CUSTOMER, $customer);
    }

    /**
     * Set addresses
     *
     * @param \Magento\Customer\Service\V1\Data\Address[]|null $addresses
     * @return $this
     */
    public function setAddresses($addresses)
    {
        return $this->_set(CustomerDetails::KEY_ADDRESSES, $addresses);
    }

    /**
     * Builds the entity.
     *
     * @return \Magento\Customer\Service\V1\Data\CustomerDetails
     */
    public function create()
    {
        if (!isset($this->data[CustomerDetails::KEY_CUSTOMER])) {
            $this->data[CustomerDetails::KEY_CUSTOMER] = $this->_customerBuilder->create();
        }
        if (!isset($this->data[CustomerDetails::KEY_ADDRESSES])) {
            $this->data[CustomerDetails::KEY_ADDRESSES] = null;
        }
        return parent::create();
    }
}
