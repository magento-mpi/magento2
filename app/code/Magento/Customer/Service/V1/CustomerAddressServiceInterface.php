<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;
use Magento\Customer\Service\Entity\V1\AggregateException;
use Magento\Customer\Service\Entity\V1\Exception;

/**
 * Manipulate Customer Address Entities *
 */
interface CustomerAddressServiceInterface
{
    /**
     * Retrieve all Customer Addresses
     *
     * @param int $customerId,
     * @return \Magento\Customer\Service\V1\Dto\Address[]
     * @throws Exception
     */
    public function getAddresses($customerId);

    /**
     * Retrieve default billing address
     *
     * @param int $customerId
     * @return \Magento\Customer\Service\V1\Dto\Address
     * @throws Exception
     */
    public function getDefaultBillingAddress($customerId);

    /**
     * Retrieve default shipping address
     *
     * @param int $customerId
     * @return \Magento\Customer\Service\V1\Dto\Address
     * @throws Exception
     */
    public function getDefaultShippingAddress($customerId);

    /**
     * Retrieve address by id
     *
     * @param int $customerId
     * @param int $addressId
     * @return \Magento\Customer\Service\V1\Dto\Address
     * @throws Exception
     */
    public function getAddressById($customerId, $addressId);

    /**
     * Removes an address by id.
     *
     * @param int $customerId
     * @param int $addressId
     * @throws Exception if the address does not belong to the given customer
     */
    public function deleteAddressFromCustomer($customerId, $addressId);

    /**
     * Insert and/or update a list of addresses.
     *
     * This will add the addresses to the provided customerId.
     * Only one address can be the default billing or shipping
     * so if more than one is set, or if one was already set
     * then the last address in this provided list will take
     * over as the new default.
     *
     * This doesn't support partial updates to addresses, meaning
     * that a full set of data must be provided with each Address
     *
     * @param int                 $customerId
     * @param \Magento\Customer\Service\V1\Dto\Address[] $addresses
     *
     * @throws AggregateException if there are validation errors.
     * @throws Exception If customerId is not found or other error occurs.
     * @return int[] address ids
     */
    public function saveAddresses($customerId, array $addresses);

}
