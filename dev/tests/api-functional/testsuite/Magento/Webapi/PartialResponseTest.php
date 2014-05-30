<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi;

use Magento\TestFramework\Helper\Customer as CustomerHelper;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Customer\Service\V1\CustomerAccountServiceTest;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\Webapi\Exception as HTTPExceptionCodes;

class PartialResponseTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    /** @var CustomerHelper */
    protected $customerHelper;

    /** @var CustomerAccountServiceInterface */
    protected $customerAccountService;

    protected function setUp()
    {
        $this->_markTestAsRestOnly('Partial response functionality available in REST mode only.');

        $this->customerAccountService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerAccountServiceInterface');
    }

    /**
     * @dataProvider customerDataProvider
     */
    public function testCustomer($filter, $expected)
    {
        $result = $this->_makeCall($filter);
        $this->assertEquals($expected, $result);
    }

    public function customerDataProvider()
    {
        return [
            ['customer[email]', ['customer' => ['email']]],
            [
                'customer[email],addresses[city]',
                [
                    'customer' => ['email'],
                    'addresses' => [
                        ['city' => CustomerHelper::ADDRESS_CITY1],
                        ['city' => CustomerHelper::ADDRESS_CITY2]
                    ]
                ]
            ],
            ['addresses[region[region_code]]',
                [
                    'addresses' => [
                        ['region' => ['region_code' => CustomerHelper::ADDRESS_REGION_CODE1]],
                        ['region' => ['region_code' => CustomerHelper::ADDRESS_REGION_CODE2]]
                    ]
                ]
            ]
        ];
    }

    public function testCustomerInvalidFilter()
    {
        try {
            $this->_makeCall('nonsense');
        } catch (\Exception $e) {
            $errorObj = $this->customerHelper->processRestExceptionResult($e);
            // @todo what message?
            // $this->assertEquals("<MESSAGE>", $errorObj['message']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_BAD_REQUEST, $e->getCode());
        }
    }

    protected function _makeCall($filter)
    {
        $customerData = $this->customerHelper->createSampleCustomer();

        $resourcePath = sprintf(
            '%s/%d?fields=%s',
            CustomerAccountServiceTest::RESOURCE_PATH,
            $customerData['id'],
            $filter
        );

        $serviceInfo = [
            'rest' => [
                'resourcePath' => $resourcePath,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ]
        ];

        return $this->_webApiCall($serviceInfo);
    }
}
