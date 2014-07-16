<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Service\V1;

use Magento\Customer\Model\Address as CustomerAddressModel;
use Magento\Customer\Model\Address\Converter as AddressConverter;
use Magento\Customer\Model\AddressRegistry;
use Magento\Customer\Service\V1\CustomerAddressServiceInterface;

class CustomerAddressServicePlugin
{
    /**
     * @var AddressRegistry
     */
    private $addressRegistry;

    /**
     * @var AddressConverter
     */
    private $addressConverter;

    /**
     * @param AddressRegistry $addressRegistry
     * @param AddressConverter $addressConverter
     */
    public function __construct(
        AddressRegistry $addressRegistry,
        AddressConverter $addressConverter
    ) {
        $this->addressRegistry = $addressRegistry;
        $this->addressConverter = $addressConverter;
    }

    /**
     * Skip customer check if address data must be taken from gift registry
     *
     * @param CustomerAddressServiceInterface $subject
     * @param callable $proceed
     * @param int|string $addressId
     * @return \Magento\Customer\Service\V1\Data\Address
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetAddress(
        CustomerAddressServiceInterface $subject,
        \Closure $proceed,
        $addressId
    ) {
        if (is_string($addressId) && strpos($addressId, \Magento\GiftRegistry\Helper\Data::ADDRESS_PREFIX) === 0) {
            $address = $this->addressRegistry->retrieve($addressId);
            $address->setData('is_default_shipping', false);
            $address->setData('is_default_billing', false);
            return $this->addressConverter->createAddressFromModel(
                $address,
                null,
                null
            );
        } else {
            return $proceed($addressId);
        }
    }
}
