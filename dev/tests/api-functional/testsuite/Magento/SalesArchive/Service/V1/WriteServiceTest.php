<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class WriteServiceTest extends WebapiAbstract
{
    /**#@+
     * Constants defined for Web Api call
     */
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'salesArchiveWriteServiceV1';
    /**#@-*/

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @magentoApiDataFixture Magento/Sales/_files/order.php
     */
    protected function setUp()
    {
        $this->markTestSkipped('Skipped');
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $config = $this->objectManager->get('Magento\Framework\App\Config\MutableScopeConfigInterface');
        $config->setValue(\Magento\SalesArchive\Model\Config::XML_PATH_ARCHIVE_ACTIVE, 1);
        $config->setValue(\Magento\SalesArchive\Model\Config::XML_PATH_ARCHIVE_ORDER_STATUSES, 'closed,canceled');
    }

    public function testGetList()
    {
        $order = $this->objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId('100000001');
        $order->setStatus('canceled');
        $order->save();

        /** @var $searchCriteriaBuilder  \Magento\Framework\Data\SearchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->create(
            'Magento\Framework\Data\SearchCriteriaBuilder'
        );

        /** @var $filterBuilder  \Magento\Framework\Service\V1\Data\FilterBuilder */
        $filterBuilder = $this->objectManager->create(
            'Magento\Framework\Service\V1\Data\FilterBuilder'
        );

        $searchCriteriaBuilder->addFilter(
            [
                $filterBuilder
                    ->setField('status')
                    ->setValue('canceled')
                    ->create()
            ]
        );
        $searchData = $searchCriteriaBuilder->create()->__toArray();

        $requestData = ['searchCriteria' => $searchData];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/archived-orders/?' . http_build_query($requestData),
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getList'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertArrayHasKey('items', $result);
        $this->assertCount(1, $result['items']);
    }

    public function testMoveOrdersToArchive()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/archived-orders/',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'moveOrdersToArchive'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo);
        $this->assertTrue($result);
    }

    public function testRemoveOrderFromArchiveById()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId('100000001');
        $requestData = ['id' => $order->getId()];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/archived-orders/' . $order->getId(),
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'removeOrderFromArchiveById'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($result);
    }
}
