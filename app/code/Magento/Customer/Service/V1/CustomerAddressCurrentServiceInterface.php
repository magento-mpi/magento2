<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

/**
 * Interface CustomerAddressCurrentServiceInterface
 */
interface CustomerAddressCurrentServiceInterface
{
    /**
     * Returns all addresses for current customer
     *
     * @return array|Dto\Address[]
     */
    public function getCustomerAddresses();

    /**
     * Returns default billing address form current customer
     *
     * @return Dto\Address|null
     */
    public function getDefaultBillingAddress();

    /**
     * Returns default shipping address for current customer
     *
     * @return Dto\Address|null
     */
    public function getDefaultShippingAddress();
}
