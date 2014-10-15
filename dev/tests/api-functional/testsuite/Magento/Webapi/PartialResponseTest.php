<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Service\V1\CustomerAccountServiceTest;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Helper\Customer as CustomerHelper;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class PartialResponseTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    /** @var CustomerHelper */
    protected $customerHelper;

    /** @var CustomerAccountServiceInterface */
    protected $customerAccountService;

    /** @var string */
    protected $customerData;

    protected function setUp()
    {
        $this->_markTestAsRestOnly('Partial response functionality available in REST mode only.');

        $this->customerAccountService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerAccountServiceInterface');

        $this->customerHelper = Bootstrap::getObjectManager()
            ->get('Magento\TestFramework\Helper\Customer');

        $this->customerData = $this->customerHelper->createSampleCustomer();
    }

    public function testCustomerWithEmailFilter()
    {
        $filter = 'customer[email]';
        $expected = ['customer' => ['email' => $this->customerData['email']]];
        $result = $this->_getCustomerWithFilter($filter, $this->customerData['id']);
        $this->assertEquals($expected, $result);
    }

    public function testCustomerWithEmailAndAddressFilter()
    {
        $filter = 'customer[email],addresses[city]';
        $expected = [
            'customer' => [
                'email' => $this->customerData['email']
            ],
            'addresses' => [
                ['city' => CustomerHelper::ADDRESS_CITY1],
                ['city' => CustomerHelper::ADDRESS_CITY2]
            ]
        ];
        $result = $this->_getCustomerWithFilter($filter, $this->customerData['id']);
        $this->assertEquals($expected, $result);
    }

    public function testCustomerWithNestedAddressFilter()
    {
        $filter = 'addresses[region[region_code]]';
        $expected = [
            'addresses' => [
                ['region' => ['region_code' => CustomerHelper::ADDRESS_REGION_CODE1]],
                ['region' => ['region_code' => CustomerHelper::ADDRESS_REGION_CODE2]]
            ]
        ];
        $result = $this->_getCustomerWithFilter($filter, $this->customerData['id']);
        $this->assertEquals($expected, $result);
    }

    public function testCustomerInvalidFilter()
    {
        try {
            $result = $this->_getCustomerWithFilter('invalid', $this->customerData['id']);
            $this->assertEmpty($result);
        } catch (\Exception $e) {
            $this->fail('Invalid filter was not expected to result in an HTTP error : ' . $e->getCode());
        }
    }

    public function testFilterForCustomerApiWithSimpleResponse()
    {
        try {
            $result = $this->_getCustomerWithFilter('customers', $this->customerData['id'], '/permissions/delete');
            //assert if filter is ignored and a normal response is returned
            $this->assertTrue($result);
        } catch (\Exception $e) {
            $this->fail('Invalid filter was not expected to result in an HTTP error : ' . $e->getCode());
        }
    }

    protected function _getCustomerWithFilter($filter, $customerId, $path = '')
    {
        $resourcePath = sprintf(
            '%s/%d%s?fields=%s',
            CustomerAccountServiceTest::RESOURCE_PATH,
            $customerId,
            $path,
            $filter
        );

        $serviceInfo = [
            'rest' => [
                'resourcePath' => $resourcePath,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ]
        ];

        return $this->_webApiCall($serviceInfo);
    }
}
