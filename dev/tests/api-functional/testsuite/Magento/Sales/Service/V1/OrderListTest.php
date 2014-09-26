<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;

/**
 * Class OrderListTest
 * @package Magento\Sales\Service\V1
 */
class OrderListTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/orders';

    const SERVICE_READ_NAME = 'salesOrderReadV1';

    const SERVICE_VERSION = 'V1';

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/order.php
     */
    public function testOrderList()
    {
        /** @var $searchCriteriaBuilder  \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->create(
            'Magento\Framework\Service\V1\Data\SearchCriteriaBuilder'
        );

        /** @var $filterBuilder  \Magento\Framework\Service\V1\Data\FilterBuilder */
        $filterBuilder = $this->objectManager->create(
            'Magento\Framework\Service\V1\Data\FilterBuilder'
        );

        $searchCriteriaBuilder->addFilter(
            [
                $filterBuilder
                    ->setField('status')
                    ->setValue('processing')
                    ->create()
            ]
        );
        $searchData = $searchCriteriaBuilder->create()->__toArray();

        $requestData = ['searchCriteria' => $searchData];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($requestData),
                'httpMethod' => Config::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'search'
            ]
        ];

        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertArrayHasKey('items', $result);
        $this->assertCount(1, $result['items']);
    }
}
