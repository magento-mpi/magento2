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
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    protected function prepareRma()
    {
        $order = $this->objectManager->get('Magento\Sales\Model\Order')->load(1);
        /** @var \Magento\Sales\Service\V1\Data\OrderItemBuilder $orderItemBuilder */
        $rmaItemBuilder = $this->objectManager->get('Magento\Rma\Service\V1\Data\ItemBuilder');
        $rmaItemBuilder->populateWithArray($this->getDataStructure('Magento\Rma\Service\V1\Data\Item'));

        $rmaItemBuilder->setId(333);
        $orderItem = current($order->getAllItems());
        $rmaItemBuilder->setOrderItemId($orderItem->getId());
        $rmaItemBuilder->setQtyRequested($orderItem->getQtyOrdered());
        $rmaItemBuilder->setQtyAuthorized(0);
        $rmaItemBuilder->setQtyReturned(0);
        $rmaItemBuilder->setQtyApproved(1);
        $rmaItemBuilder->setReason(1);
        $rmaItemBuilder->setCondition(2);
        $rmaItemBuilder->setResolution(3);
        $rmaItemBuilder->setStatus('pending');
        $rmaItem = $rmaItemBuilder->create()->__toArray();
        /** @var \Magento\Rma\Service\V1\Data\RmaBuilder $orderBuilder */
        $rmaBuilder = $this->objectManager->get('Magento\Rma\Service\V1\Data\RmaBuilder');
        $rmaBuilder->populateWithArray($this->getDataStructure('Magento\Rma\Service\V1\Data\Rma'));

        $rmaBuilder->setEntityId(12);
        $rmaBuilder->setOrderId($order->getId());
        $rmaBuilder->setOrderIncrementId($order->getIncrementId());
        $rmaBuilder->setIncrementId(28);
        $rmaBuilder->setStoreId(1);
        $rmaBuilder->setCustomerCustomEmail('customer@custom.com');
        $rmaBuilder->setCustomerId(1);
        $rmaBuilder->setItems([$rmaItem]);
        $rmaBuilder->setStatus('pending');
        $rma = $rmaBuilder->create()->__toArray();
        return $rma;
    }

    /**
     * @param string $className
     *
     * @return array
     */
    protected function getDataStructure($className)
    {
        $refClass = new \ReflectionClass ($className);
        $constants = $refClass->getConstants();
        $data = array_fill_keys($constants, null);
        unset($data['custom_attributes']);
        return $data;
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/shipment.php
     */
    public function testCreate()
    {
        $rma = $this->prepareRma();

        $requestData = ['rmaDataObject' => $rma];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/returns' . '/?' . http_build_query($requestData),
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
        $model = $this->objectManager->get('Magento\Rma\Model\Rma');
        $model->load($rma->getIncrementId(), 'increment_id');
        $this->assertTrue((bool)$model->getId());
    }

//    /**
//     * @magentoApiDataFixture Magento/Rma/_files/rma.php
//     */
//    public function testUpdate()
//    {
//        $rma = $this->prepareRma();
//        $rma->setCustomerCustomEmail('email@example.com');
//        $rma->create();
//        $model = $this->objectManager->get('Magento\Rma\Model\Rma');
//        $model->load('1', 'increment_id');
//        $serviceInfo = [
//            'rest' => [
//                'resourcePath' => '/V1/returns/' . $model->getId(),
//                'httpMethod' => RestConfig::HTTP_METHOD_PUT
//            ],
//            'soap' => [
//                'service' => self::SERVICE_NAME,
//                'serviceVersion' => self::SERVICE_VERSION,
//                'operation' => self::SERVICE_NAME . 'update'
//            ]
//        ];
//        $this->_webApiCall($serviceInfo, ['rmaDataObject' => $rma]);
//        $actualRma = $this->objectManager->get('Magento\Rma\Model\Rma')->load($model->getId());
//        $customerCustomEmail = $actualRma->getCustomerCustomEmail();
//        $this->assertEquals('email@example.com', $customerCustomEmail->getData('customer_custom_email'));
//    }
}
