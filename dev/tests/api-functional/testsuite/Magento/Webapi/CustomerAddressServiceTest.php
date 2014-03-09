<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi;

use Magento\TestFramework\Helper\Bootstrap;

class CustomerAddressServiceTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SOAP_SERVICE_NAME = 'customerCustomerAddressServiceV1';
    const SOAP_SERVICE_VERSION = 'V1';

    /** @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface */
    protected $customerAddressService;

    /** @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface */
    protected $customerService;

    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->customerService = $objectManager->get('Magento\Customer\Service\V1\CustomerAccountServiceInterface');
        $this->customerAddressService = $objectManager->get(
            'Magento\Customer\Service\V1\CustomerAddressServiceInterface'
        );
        //Temporarily adding it here to avoid making changes to tear down
        $this->_markTestAsRestOnly();
        parent::setUp();
    }

    /**
     * Ensure that fixture customer and his addresses are deleted.
     */
    protected function tearDown()
    {
        try {
            $fixtureFirstAddressId = 1;
            $this->customerAddressService->deleteAddress($fixtureFirstAddressId);
        } catch (\Magento\Exception\NoSuchEntityException $e) {
            /** First address fixture was not used */
        }
        try {
            $fixtureSecondAddressId = 2;
            $this->customerAddressService->deleteAddress($fixtureSecondAddressId);
        } catch (\Magento\Exception\NoSuchEntityException $e) {
            /** Second address fixture was not used */
        }
        try {
            $fixtureCustomerId = 1;
            $this->customerService->deleteCustomer($fixtureCustomerId);
        } catch (\Magento\Exception\NoSuchEntityException $e) {
            /** Customer fixture was not used */
        }
        parent::tearDown();
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoApiDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testGetAddress()
    {
        $fixtureAddressId = 1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/customer/address/{$fixtureAddressId}",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SOAP_SERVICE_NAME,
                'serviceVersion' => self::SOAP_SERVICE_VERSION,
                'operation' => self::SOAP_SERVICE_NAME . 'GetAddress'
            ]
        ];
        $requestData = ['addressId' => $fixtureAddressId];
        $addressData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($this->getFirstFixtureAddressData(), $addressData, "Address data is invalid.");
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoApiDataFixture Magento/Customer/_files/customer_two_addresses.php
     */
    public function testGetAddresses()
    {
        $fixtureCustomerId = 1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/customer/$fixtureCustomerId/address",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SOAP_SERVICE_NAME,
                'serviceVersion' => self::SOAP_SERVICE_VERSION,
                'operation' => self::SOAP_SERVICE_NAME . 'GetAddresses'
            ]
        ];
        $requestData = ['customerId' => $fixtureCustomerId];
        $addressesData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(
            [$this->getFirstFixtureAddressData(), $this->getSecondFixtureAddressData()],
            $addressesData,
            "Addresses list is invalid."
        );
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoApiDataFixture Magento/Customer/_files/customer_two_addresses.php
     */
    public function testGetDefaultBillingAddress()
    {
        $fixtureCustomerId = 1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/customer/$fixtureCustomerId/address/DefaultBilling",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SOAP_SERVICE_NAME,
                'serviceVersion' => self::SOAP_SERVICE_VERSION,
                'operation' => self::SOAP_SERVICE_NAME . 'GetDefaultBillingAddress'
            ]
        ];
        $requestData = ['customerId' => $fixtureCustomerId];
        $addressData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(
            $this->getFirstFixtureAddressData(),
            $addressData,
            "Default billing address data is invalid."
        );
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoApiDataFixture Magento/Customer/_files/customer_two_addresses.php
     */
    public function testGetDefaultShippingAddress()
    {
        $fixtureCustomerId = 1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/customer/$fixtureCustomerId/address/DefaultBilling",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SOAP_SERVICE_NAME,
                'serviceVersion' => self::SOAP_SERVICE_VERSION,
                'operation' => self::SOAP_SERVICE_NAME . 'GetDefaultShippingAddress'
            ]
        ];
        $requestData = ['customerId' => $fixtureCustomerId];
        $addressData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(
            $this->getFirstFixtureAddressData(),
            $addressData,
            "Default shipping address data is invalid."
        );
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoApiDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testDeleteAddress()
    {
        $fixtureAddressId = 1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/customer/address/{$fixtureAddressId}",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SOAP_SERVICE_NAME,
                'serviceVersion' => self::SOAP_SERVICE_VERSION,
                'operation' => self::SOAP_SERVICE_NAME . 'GetDefaultShippingAddress'
            ]
        ];
        $requestData = ['addressId' => $fixtureAddressId];
        $this->_webApiCall($serviceInfo, $requestData);

        $this->setExpectedException('Magento\Exception\NoSuchEntityException', 'No such entity with addressId = 1');
        $this->customerAddressService->getAddress($fixtureAddressId);
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     */
    public function testCreateAddress()
    {
        $customerFixtureId = 1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => "/V1/customer/{$customerFixtureId}/address",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SOAP_SERVICE_NAME,
                'serviceVersion' => self::SOAP_SERVICE_VERSION,
                'operation' => self::SOAP_SERVICE_NAME . 'SaveAddresses'
            ]
        ];
        $firstAddressData = $this->getFirstFixtureAddressData();
        unset($firstAddressData['id']);
        $secondAddressData = $this->getFirstFixtureAddressData();
        unset($secondAddressData['id']);

        $requestData = ['customerId' => $customerFixtureId,'addresses' => [$firstAddressData, $secondAddressData]];
        $createdAddressesIds = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertCount(2, $createdAddressesIds, "IDs for some of created addresses are missing.");

        $firstAddressStoredData = $this->customerAddressService->getAddress($createdAddressesIds[0])->__toArray();
        unset($firstAddressStoredData['id']);
        $this->assertEquals($firstAddressData, $firstAddressStoredData, "First address was stored incorrectly.");

        $secondAddressStoredData = $this->customerAddressService->getAddress($createdAddressesIds[1])->__toArray();
        unset($secondAddressStoredData['id']);
        $this->assertEquals($secondAddressData, $secondAddressStoredData, "Second address was stored incorrectly.");
    }

    /**
     * Retrieve data of the first fixture address.
     *
     * @return array
     */
    protected function getFirstFixtureAddressData()
    {
        return [
            'firstname' => 'John',
            'lastname' => 'Smith',
            'city' => 'CityM',
            'country_id' => 'US',
            'postcode' => '75477',
            'telephone' => '3468676',
            'street' => ['Green str, 67'],
            'id' => '1',
            'default_billing' => true,
            'default_shipping' => true,
            'customer_id' => '1',
            'region' => ['region' => 'Alabama', 'region_id' => 1, 'region_code' => 'AL'],
        ];
    }

    /**
     * Retrieve data of the second fixture address.
     *
     * @return array
     */
    protected function getSecondFixtureAddressData()
    {
        return [
            'firstname' => 'John',
            'lastname' => 'Smith',
            'city' => 'CityX',
            'country_id' => 'US',
            'postcode' => '47676',
            'telephone' => '3234676',
            'street' => ['Black str, 48',],
            'id' => '2',
            'default_billing' => false,
            'default_shipping' => false,
            'customer_id' => '1',
            'region' => ['region' => 'Alabama', 'region_id' => 1, 'region_code' => 'AL'],
        ];
    }
}
