<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Address\Shipping;

use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Checkout\Service\V1\Data\Cart\AddressBuilder;
use \Magento\Checkout\Service\V1\Data\Cart\Address;
use \Magento\Customer\Service\V1\Data\Region;

class ReadService implements ReadServiceInterface
{
    /**
     * @var \Magento\Checkout\Service\V1\QuoteLoader
     */
    protected $quoteLoader;

    /**
     * @var AddressBuilder
     */
    protected $addressBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
     * @param AddressBuilder $addressBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader,
        AddressBuilder $addressBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->quoteLoader = $quoteLoader;
        $this->addressBuilder = $addressBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress($cartId)
    {
        $storeId = $this->storeManager->getStore()->getId();

        /** @var  \Magento\Sales\Model\Quote\Address $address */
        $address = $this->quoteLoader->load($cartId, $storeId)->getShippingAddress();

        $data = [
            Address::KEY_COUNTRY_ID => $address->getCountryId(),
            Address::KEY_ID => $address->getId(),
            Address::KEY_CUSTOMER_ID => $address->getCustomerId(),
            Address::KEY_REGION => array(
                Region::KEY_REGION => $address->getRegion(),
                Region::KEY_REGION_ID => $address->getRegionId(),
                Region::KEY_REGION_CODE => $address->getRegionCode()
            ),
            Address::KEY_STREET => $address->getStreet(),
            Address::KEY_COMPANY => $address->getCompany(),
            Address::KEY_TELEPHONE => $address->getTelephone(),
            Address::KEY_FAX => $address->getFax(),
            Address::KEY_POSTCODE => $address->getPostcode(),
            Address::KEY_FIRSTNAME => $address->getFirstname(),
            Address::KEY_LASTNAME => $address->getLastname(),
            Address::KEY_MIDDLENAME => $address->getMiddlename(),
            Address::KEY_PREFIX => $address->getPrefix(),
            Address::KEY_SUFFIX => $address->getSuffix()
        ];

        return $this->addressBuilder->populateWithArray($data)->create();
    }
}
