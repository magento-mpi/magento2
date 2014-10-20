<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Integration\Model\Oauth\Token as TokenModel;
use Magento\TestFramework\Helper\Bootstrap;

class CustomerAddressServiceMeTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    /** @var $token TokenModel */
    private $token;

    protected function setUp()
    {
        $this->_markTestAsRestOnly();

        // get token
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/integration/customer/token',
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST
            ]
        ];
        $requestData = ['username' => 'customer@example.com', 'password' => 'password'];
        $this->token = $this->_webApiCall($serviceInfo, $requestData);

        parent::setUp();
    }

    /**
     * Ensure that fixture customer and his addresses are deleted.
     */
    protected function tearDown()
    {
        /** @var \Magento\Framework\Registry $registry */
        $registry = Bootstrap::getObjectManager()->get('Magento\Framework\Registry');
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);
        parent::tearDown();
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
                'resourcePath' => "/V1/customers/me/addresses",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET,
                'token' => $this->token
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
                'resourcePath' => "/V1/customers/me/billingAddress",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET,
                'token' => $this->token
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
                'resourcePath' => "/V1/customers/me/shippingAddress",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET,
                'token' => $this->token
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
            'company' => 'CompanyName',
            'postcode' => '75477',
            'telephone' => '3468676',
            'street' => ['Green str, 67'],
            'id' => 1,
            'default_billing' => true,
            'default_shipping' => true,
            'customer_id' => '1',
            'region' => ['region' => 'Alabama', 'region_id' => 1, 'region_code' => 'AL'],
            'fax' => null,
            'middlename' => null,
            'prefix' => null,
            'suffix' => null,
            'vat_id' => null,
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
            'id' => 2,
            'default_billing' => false,
            'default_shipping' => false,
            'customer_id' => '1',
            'region' => ['region' => 'Alabama', 'region_id' => 1, 'region_code' => 'AL'],
            'fax' => null,
            'middlename' => null,
            'prefix' => null,
            'suffix' => null,
            'vat_id' => null,
        ];
    }
}
