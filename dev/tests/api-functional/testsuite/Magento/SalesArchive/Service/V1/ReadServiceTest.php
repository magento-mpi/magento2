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

/**
 * Class ArchiveListTest
 * @package Magento\SalesArchive\Service\V1
 */
class ReadServiceTest extends WebapiAbstract
{
    /**#@+
     * Constants defined for Web Api call
     */
    const SERVICE_NAME = 'salesArchiveReadServiceV1';
    const SERVICE_VERSION = 'V1';
    /**#@-*/

    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    private $objectManager;

    /**
     * @magentoApiDataFixture Magento/Sales/_files/order.php
     */
    public function setUp()
    {
        $this->markTestSkipped('Skipped');
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $config = $this->objectManager->get('Magento\Framework\App\Config\MutableScopeConfigInterface');
        $config->setValue(\Magento\SalesArchive\Model\Config::XML_PATH_ARCHIVE_ACTIVE, 1);
        $config->setValue(\Magento\SalesArchive\Model\Config::XML_PATH_ARCHIVE_ORDER_STATUSES, 'closed,canceled');
    }

    public function testGetOrderInfo()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId('100000001');
        $order->setStatus('canceled');
        $order->save();
        $requestData = ['id' => $order->getId()];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/archived-orders/' . $order->getId(),
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getOrderInfo'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertArrayHasKey('entity_id', $result);
        $this->assertCount(16, $result);
    }
}
