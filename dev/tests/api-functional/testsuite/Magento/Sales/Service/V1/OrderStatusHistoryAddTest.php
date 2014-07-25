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
use Magento\Sales\Service\V1\Data\OrderStatusHistory;

class OrderCommentAddTest extends WebapiAbstract
{
    const SERVICE_READ_NAME = 'salesOrderStatusHistoryAddServiceV1';
    const SERVICE_VERSION = 'V1';
    const ORDER_INCREMENT_ID = '100000001';

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
    public function testOrderCommentAdd()
    {
        $commentData = [
            'statusHistory' => [
                OrderStatusHistory::COMMENT => 'Hello',
                OrderStatusHistory::IS_CUSTOMER_NOTIFIED => true
            ]
        ];

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->objectManager->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId(self::ORDER_INCREMENT_ID);

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/orders/' . $order->getId() .'/comment',
                'httpMethod' => Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'info'
            ]
        ];

        $this->_webApiCall($serviceInfo, $commentData);

        //Verification

        $order->load($order->getId());

        $statusHistoryArray = $order->getAllStatusHistory();
        $statusHistory = end($statusHistoryArray);

        $this->assertEquals($commentData, $statusHistory->getData());
    }
}
