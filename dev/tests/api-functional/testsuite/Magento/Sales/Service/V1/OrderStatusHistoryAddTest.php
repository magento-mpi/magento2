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
use Magento\Sales\Service\V1\Data\OrderStatusHistoryBuilder;

/**
 * Class OrderCommentAddTest
 * @package Magento\Sales\Service\V1
 */
class OrderStatusHistoryAddTest extends WebapiAbstract
{
    const SERVICE_READ_NAME = 'salesOrderWriteV1';

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
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->objectManager->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId(self::ORDER_INCREMENT_ID);

        $commentData = [
            OrderStatusHistory::COMMENT => 'Hello',
            OrderStatusHistory::ENTITY_ID => null,
            OrderStatusHistory::IS_CUSTOMER_NOTIFIED => true,
            OrderStatusHistory::CREATED_AT => null,
            OrderStatusHistory::PARENT_ID => $order->getId(),
            OrderStatusHistory::ENTITY_NAME => null,
            OrderStatusHistory::STATUS => null,
            OrderStatusHistory::IS_VISIBLE_ON_FRONT => true,
        ];


        $requestData = ['id'=> $order->getId(), 'statusHistory' => $commentData];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/order/' . $order->getId() . '/comment',
                'httpMethod' => Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'statusHistoryAdd'
            ]
        ];

        $this->_webApiCall($serviceInfo, $requestData);

        //Verification
        $statusHistoryComment = $order->load($order->getId())->getAllStatusHistory()[0];

        foreach ($commentData as $key => $value) {
            $this->assertEquals($commentData[OrderStatusHistory::COMMENT], $statusHistoryComment->getComment());
            $this->assertEquals($commentData[OrderStatusHistory::PARENT_ID], $statusHistoryComment->getParentId());
            $this->assertEquals(
                $commentData[OrderStatusHistory::IS_CUSTOMER_NOTIFIED], $statusHistoryComment->getIsCustomerNotified()
            );
            $this->assertEquals(
                $commentData[OrderStatusHistory::IS_VISIBLE_ON_FRONT], $statusHistoryComment->getIsVisibleOnFront()
            );
        }
    }
}
