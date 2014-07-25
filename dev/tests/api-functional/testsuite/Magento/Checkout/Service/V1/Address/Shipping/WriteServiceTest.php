<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Address\Shipping;

use \Magento\TestFramework\TestCase\WebapiAbstract;
use \Magento\Webapi\Model\Rest\Config as RestConfig;

class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'checkoutAddressShippingWriteServiceV1';
    const RESOURCE_PATH = '/V1/carts/';

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Checkout\Service\V1\Data\Cart\AddressBuilder
     */
    protected $builder;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->builder = $this->objectManager->create('\Magento\Checkout\Service\V1\Data\Cart\AddressBuilder');
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address.php
     */
    public function testGetAddress()
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('test_order_1', 'reserved_order_id');

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . $quote->getId() . '/shipping-address',
                'httpMethod' => RestConfig::HTTP_METHOD_POST,
            ),
            'soap' => array(
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'SetAddress',
            ),
        );


        $addressData = [
            'firstname' => 'Slava',
            'lastname' => 'Ukrayini',
            'email' => 'cat@dog.com',
            'company' => 'eBay Inc',
            'street' => ['VeryLargeStreet', 'Tiny House 18'],
            'city' => 'TheSmartestCity',
            'region' => [
                'region_id' => 12,
                'region' => 'California',
                'region_code' => 'CA',
            ],
            'postcode' => '0985432',
            'country_id' => 'US',
            'telephone' => '88776655',
            'fax' => '44332255',
        ];
        $requestData = [
            "cartId" => $quote->getId(),
            'addressData' => $addressData
        ];

        $result = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertTrue($result);

        //reset $quote to reload data
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('test_order_1', 'reserved_order_id');
        $savedData  = $quote->getShippingAddress()->getData();
        //custom checks for street, region and address_type
        foreach ($addressData['street'] as $streetLine) {
            $this->assertContains($streetLine, $quote->getShippingAddress()->getStreet());
        }
        unset($addressData['street']);
        $this->assertEquals($addressData['region']['region_id'], $savedData['region_id']);
        $this->assertEquals($addressData['region']['region'], $savedData['region']);
        unset($addressData['region']);
        $this->assertEquals('shipping', $savedData['address_type']);
        //check the rest of fields
        foreach ($addressData as $key => $value) {
            $this->assertEquals($value, $savedData[$key]);
        }
    }
}
