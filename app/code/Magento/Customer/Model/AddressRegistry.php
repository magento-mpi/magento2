<?php
/**
 * {license_notice}

 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model;

use Magento\Exception\NoSuchEntityException;

/**
 * Class AddressRegistry
 */
class AddressRegistry
{
    /**
     * @var Address[]
     */
    protected $registry = [];

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $addressFactory;

    /**
     * @param AddressFactory $addressFactory
     */
    public function __construct(\Magento\Customer\Model\AddressFactory $addressFactory)
    {
        $this->addressFactory = $addressFactory;
    }

    /**
     * Get instance of the Address Model identified by id
     *
     * @param int $addressId
     * @return Address
     * @throws \Magento\Exception\NoSuchEntityException
     */
    public function retrieve($addressId)
    {
        if (isset($this->registry[$addressId])) {
            return $this->registry[$addressId];
        }
        $address = $this->addressFactory->create();
        $address->load($addressId);
        if (!$address->getId()) {
            throw new \Magento\Exception\NoSuchEntityException('addressId', $addressId);
        }
        $this->registry[$addressId] = $address;
        return $address;
    }

    /**
     * Remove an instance of the Address Model from the registry
     *
     * @param int $addressId
     * @return void
     */
    public function remove($addressId)
    {
        unset($this->registry[$addressId]);
    }
}