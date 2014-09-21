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

    /**
     * @magentoApiDataFixture Magento/Sales/_files/order.php
     */
    protected function prepareRma()
    {
        $order = $this->objectManager->get('Magento\Sales\Model\Order')->load(1);
        /** @var \Magento\Rma\Service\V1\Data\RmaBuilder $orderBuilder */
        $rmaBuilder = $this->objectManager->get('Magento\Rma\Service\V1\Data\RmaBuilder');
        $rmaBuilder->populateWithArray($this->getDataStructure('Magento\Rma\Service\V1\Data\Rma'));
        $rmaBuilder->setOrderId($order->getId());
        $rmaBuilder->setIncrementId(28);
        return $rmaBuilder->create()->__toArray();
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

    public function testCreate()
    {
        $rma = $this->prepareRma();

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
        $result = $this->_webApiCall($serviceInfo, ['rmaDataObject' => $rma]);
        $this->assertTrue($result);
        $model = $this->objectManager->get('Magento\Rma\Model\Rma');
        $model->load($rma['increment_id'], 'increment_id');
        $this->assertTrue((bool)$model->getId());
    }

    /**
     * @magentoApiDataFixture Magento/Rma/_files/rma.php
     */
    public function testUpdate()
    {
        $rma = [
            'customer_custom_email' => 'email@example.com'
        ];

        $model = $this->objectManager->get('Magento\Rma\Model\Rma');
        $model->load('1', 'increment_id');
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/returns/' . $model->getId(),
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'update'
            ]
        ];
        $this->_webApiCall($serviceInfo, ['rmaDataObject' => $rma]);
        $actualRma = $this->objectManager->get('Magento\Rma\Model\Rma')->load($model->getId());
        $customerCustomEmail = $actualRma->getCustomerCustomEmail();
        $this->assertEquals('email@example.com', $customerCustomEmail->getData('customer_custom_email'));
    }
}
