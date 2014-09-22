<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\Rma\Service\V1\Data\Rma;
use Magento\Rma\Service\V1\Data\Item;

/**
 * Class RmaWriteTest
 * @package Magento\Rma\Service\V1
 */
class RmaWriteTest extends WebapiAbstract
{
    /**#@+
     * Constants defined for Web Api call
     */
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'rmaRmaWriteV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture Magento/Sales/_files/shipment.php
     */
    public function testCreate()
    {
        $rma = $this->getNewRmaData();

        $requestData = ['rmaDataObject' => $rma];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/returns',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'create'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($result);
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/shipment.php
     */
    public function testUpdate()
    {
        $rmaData = $this->getNewRmaData();

        $requestData = ['rmaDataObject' => $rmaData];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/returns',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'create'
            ]
        ];
        $this->_webApiCall($serviceInfo, $requestData);

        $rma = $this->getRmaFixture();

        $requestData = ['rmaDataObject' => $this->getRequestForUpdateRma($rma)];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/returns/' . $rma->getId(),
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'update'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($result);
    }

    /**
     * @return array
     */
    private function getNewRmaData()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $collection = $objectManager->create('Magento\Sales\Model\Resource\Order\Collection');
        $collection->setOrder('entity_id')
            ->setPageSize(1)
            ->load();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $collection->fetchItem();
        $items = $order->getItemsCollection();

        $request = [
            Rma::ORDER_ID => $order->getId(),
            Rma::ITEMS => []
        ];

        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($items as $item) {
            $request[Rma::ITEMS][] = [
                Item::ORDER_ITEM_ID => $item->getId(),
                Item::QTY_REQUESTED => 1,
                Item::CONDITION => 7,
                Item::REASON => 9,
                Item::RESOLUTION => 4,
                Item::STATUS => 'pending'
            ];
            $item->setProductType('simple');
            $item->setQtyShipped($item->getQtyOrdered());
            $item->save();
        }

        $order->save();

        return $request;
    }

    /**
     * Return last created Rma fixture
     *
     * @return \Magento\Rma\Model\Rma
     */
    private function getRmaFixture()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $collection = $objectManager->create('Magento\Rma\Model\Resource\Rma\Collection');
        $collection->setOrder('entity_id')
            ->setPageSize(1)
            ->load();
        return $collection->fetchItem();
    }

    /**
     * @param \Magento\Rma\Model\Rma $rma
     * @return array
     */
    private function getRequestForUpdateRma(\Magento\Rma\Model\Rma $rma)
    {
        $request = [
            Rma::ORDER_ID => $rma->getOrderId(),
            Rma::ITEMS => []
        ];

        /** @var \Magento\Rma\Model\Item $item */
        foreach ($rma->getItemsForDisplay()as $item) {
            $request[Rma::ITEMS][] = [
                Item::ID => $item->getId(),
                Item::ORDER_ITEM_ID => $item->getOrderItemId(),
                Item::QTY_AUTHORIZED => 1,
                Item::CONDITION => 7,
                Item::REASON => 9,
                Item::RESOLUTION => 4,
                Item::STATUS => 'authorized'
            ];
        }
        return $request;
    }
}
