<?php
/**
 * CustomerDetailsBuilder class
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

use Magento\Service\Entity\AbstractDtoBuilder;

class CustomerDetailsBuilder extends AbstractDtoBuilder
{
    /**
     * Customer builder
     *
     * @var CustomerBuilder
     */
    protected $_customerBuilder;

    /**
     * Address builder
     *
     * @var AddressBuilder
     */
    protected $_addressBuilder;

    /**
     * Constructor
     *
     * @param CustomerBuilder $customerBuilder
     * @param AddressBuilder $addressBuilder
     */
    public function __construct(
        CustomerBuilder $customerBuilder,
        AddressBuilder $addressBuilder
    ) {
        parent::__construct();
        $this->_customerBuilder = $customerBuilder;
        $this->_addressBuilder = $addressBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function populateWithArray(array $data)
    {
        $newData = [];
        if (isset($data[CustomerDetails::KEY_CUSTOMER])) {
            $newData[CustomerDetails::KEY_CUSTOMER] = $this->_customerBuilder
                ->populateWithArray($data[CustomerDetails::KEY_CUSTOMER])
                ->create();
        }

        if (isset($data[CustomerDetails::KEY_ADDRESSES])) {
            $newData[CustomerDetails::KEY_ADDRESSES] = [];
            $addresses = $data[CustomerDetails::KEY_ADDRESSES];
            foreach ($addresses as $address) {
                $newData[CustomerDetails::KEY_ADDRESSES][] = $this->_addressBuilder
                    ->populateWithArray($address)
                    ->create();
            }
        }

        return parent::populateWithArray($newData);
    }

    /**
     * Set customer
     *
     * @param Customer $customer
     * @return $this
     */
    public function setCustomer(Customer $customer)
    {
        return $this->_set(CustomerDetails::KEY_CUSTOMER, $customer);
    }

    /**
     * Set addresses
     *
     * @param Address[]|null $addresses
     * @return $this
     */
    public function setAddresses($addresses)
    {
        return $this->_set(CustomerDetails::KEY_ADDRESSES, $addresses);
    }

    /**
     * Builds the entity.
     *
     * @return CustomerDetails
     */
    public function create()
    {
        if (!isset($this->_data[CustomerDetails::KEY_CUSTOMER])) {
            $this->_data[CustomerDetails::KEY_CUSTOMER] = $this->_customerBuilder->create();
        }
        if (!isset($this->_data[CustomerDetails::KEY_ADDRESSES])) {
            $this->_data[CustomerDetails::KEY_ADDRESSES] = null;
        }
        return parent::create();
    }
}
