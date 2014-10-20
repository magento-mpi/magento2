<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Address\Billing;

use \Magento\TestFramework\TestCase\WebapiAbstract;
use \Magento\Webapi\Model\Rest\Config as RestConfig;
use \Magento\Checkout\Service\V1\Data\Cart\Address;
use \Magento\Checkout\Service\V1\Data\Cart\Address\Region;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'checkoutAddressBillingReadServiceV1';
    const RESOURCE_PATH = '/V1/carts/';

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     */
    public function testGetAddress()
    {
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('test_order_1', 'reserved_order_id');

        /** @var \Magento\Sales\Model\Quote\Address $address */
        $address = $quote->getBillingAddress();

        $data = [
            Address::KEY_COUNTRY_ID => $address->getCountryId(),
            Address::KEY_ID => (int)$address->getId(),
            Address::KEY_CUSTOMER_ID => $address->getCustomerId(),
            Address::KEY_REGION => array(
                Region::REGION => $address->getRegion(),
                Region::REGION_ID => $address->getRegionId(),
                Region::REGION_CODE => $address->getRegionCode()
            ),
            Address::KEY_STREET => $address->getStreet(),
            Address::KEY_COMPANY => $address->getCompany(),
            Address::KEY_TELEPHONE => $address->getTelephone(),
            Address::KEY_FAX => $address->getFax(),
            Address::KEY_POSTCODE => $address->getPostcode(),
            Address::KEY_CITY => $address->getCity(),
            Address::KEY_FIRSTNAME => $address->getFirstname(),
            Address::KEY_LASTNAME => $address->getLastname(),
            Address::KEY_MIDDLENAME => $address->getMiddlename(),
            Address::KEY_PREFIX => $address->getPrefix(),
            Address::KEY_SUFFIX => $address->getSuffix(),
            Address::KEY_EMAIL => $address->getEmail(),
            Address::KEY_VAT_ID => $address->getVatId(),
            Address::CUSTOM_ATTRIBUTES_KEY => array(['attribute_code' => 'disable_auto_group_change', 'value' => null])
        ];

        $cartId = $quote->getId();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/billing-address',
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ),
            'soap' => array(
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetAddress',
            ),
        );

        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            unset($data[Address::KEY_PREFIX]);
            unset($data[Address::KEY_SUFFIX]);
            unset($data[Address::KEY_MIDDLENAME]);
            unset($data[Address::KEY_FAX]);
            unset($data[Address::KEY_VAT_ID]);
        }

        $requestData = ["cartId" => $cartId];
        $this->assertEquals($data, $this->_webApiCall($serviceInfo, $requestData));
    }
}
