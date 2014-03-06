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

/**
 * Class CustomerDetailsBuilder
 */
class CustomerDetailsBuilder extends AbstractDtoBuilder
{
    /**
     * Customer builder
     *
     * @var \Magento\Customer\Service\V1\Dto\CustomerBuilder
     */
    protected $_customerBuilder;

    /**
     * Address builder
     *
     * @var \Magento\Customer\Service\V1\Dto\AddressBuilder
     */
    protected $_addressBuilder;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerBuilder
     * @param \Magento\Customer\Service\V1\Dto\AddressBuilder $addressBuilder
     */
    public function __construct(
        \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerBuilder,
        \Magento\Customer\Service\V1\Dto\AddressBuilder $addressBuilder
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
     * @param \Magento\Customer\Service\V1\Dto\Customer $customer
     * @return $this
     */
    public function setCustomer(Customer $customer)
    {
        return $this->_set(CustomerDetails::KEY_CUSTOMER, $customer);
    }

    /**
     * Set addresses
     *
     * @param \Magento\Customer\Service\V1\Dto\Address[]|null $addresses
     * @return $this
     */
    public function setAddresses($addresses)
    {
        return $this->_set(CustomerDetails::KEY_ADDRESSES, $addresses);
    }

    /**
     * Builds the entity.
     *
     * @return \Magento\Customer\Service\V1\Dto\CustomerDetails
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
