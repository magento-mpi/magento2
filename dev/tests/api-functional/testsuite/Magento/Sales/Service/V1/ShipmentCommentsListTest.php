<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class ShipmentCommentsListTest
 */
class ShipmentCommentsListTest extends WebapiAbstract
{
    const SERVICE_NAME = 'salesShipmentManagementV1';

    const SERVICE_VERSION = 'V1';

    /**
     * @magentoApiDataFixture Magento/Sales/_files/shipment.php
     */
    public function testShipmentCommentsList()
    {
        $comment = 'Test comment';
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Sales\Model\Resource\Order\Shipment\Collection $shipmentCollection */
        $shipmentCollection = $objectManager->get('Magento\Sales\Model\Resource\Order\Shipment\Collection');
        $shipment = $shipmentCollection->getFirstItem();
        $shipmentComment = $objectManager->get('Magento\Sales\Model\Order\Shipment\Comment');
        $shipmentComment->setComment($comment);
        $shipmentComment->setParentId($shipment->getId());
        $shipmentComment->save();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/shipment/' . $shipment->getId() . '/comments',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getCommentsList'
            ]
        ];
        $requestData = ['id' => $shipment->getId()];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        // TODO Test fails, due to the inability of the framework API to handle data collection
        foreach ($result['items'] as $item) {
            /** @var \Magento\Sales\Model\Order\Shipment\Comment $shipmentHistoryStatus */
            $shipmentHistoryStatus = $objectManager->get('Magento\Sales\Model\Order\Shipment\Comment')
                ->load($item['entity_id']);
            $this->assertEquals($shipmentHistoryStatus->getComment(), $item['comment']);
        }
    }
}
