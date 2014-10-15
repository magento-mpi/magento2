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
use Magento\Sales\Service\V1\Data\Order;

class OrderCreateTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/order';

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
    protected function prepareOrder()
    {
        /** @var \Magento\Sales\Service\V1\Data\OrderBuilder $orderBuilder */
        $orderBuilder = $this->objectManager->get('Magento\Sales\Service\V1\Data\OrderBuilder');
        /** @var \Magento\Sales\Service\V1\Data\OrderItemBuilder $orderItemBuilder */
        $orderItemBuilder = $this->objectManager->get('Magento\Sales\Service\V1\Data\OrderItemBuilder');
        /** @var \Magento\Sales\Service\V1\Data\OrderPaymentBuilder $orderPaymentBuilder */
        $orderPaymentBuilder = $this->objectManager->get('Magento\Sales\Service\V1\Data\OrderPaymentBuilder');
        /** @var \Magento\Sales\Service\V1\Data\OrderAddressBuilder $orderAddressBuilder */
        $orderAddressBuilder = $this->objectManager->get('Magento\Sales\Service\V1\Data\OrderAddressBuilder');

        $orderBuilder->populateWithArray($this->getDataStructure('Magento\Sales\Service\V1\Data\Order'));
        $orderItemBuilder->populateWithArray($this->getDataStructure('Magento\Sales\Service\V1\Data\OrderItem'));
        $orderPaymentBuilder->populateWithArray($this->getDataStructure('Magento\Sales\Service\V1\Data\OrderPayment'));
        $orderAddressBuilder->populateWithArray($this->getDataStructure('Magento\Sales\Service\V1\Data\OrderAddress'));

        $email = uniqid() . 'email@example.com';
        $orderItemBuilder->setSku('sku#1');
        $orderPaymentBuilder->setCcLast4('4444');
        $orderPaymentBuilder->setMethod('checkmo');
        $orderPaymentBuilder->setAdditionalInformation([]);
        $orderBuilder->setCustomerEmail($email);
        $orderBuilder->setBaseGrandTotal(100);
        $orderBuilder->setGrandTotal(100);
        $orderBuilder->setItems([$orderItemBuilder->create()->__toArray()]);
        $orderBuilder->setPayments([$orderPaymentBuilder->create()->__toArray()]);
        $orderAddressBuilder->setCity('City');
        $orderAddressBuilder->setPostcode('12345');
        $orderAddressBuilder->setLastname('Last Name');
        $orderAddressBuilder->setFirstname('First Name');
        $orderAddressBuilder->setTelephone('+00(000)-123-45-57');
        $orderAddressBuilder->setStreet('Street');
        $orderAddressBuilder->setCountryId(1);
        $orderAddressBuilder->setAddressType('billing');
        $orderBuilder->setBillingAddress($orderAddressBuilder->create()->__toArray());
        $orderAddressBuilder->populateWithArray($this->getDataStructure('Magento\Sales\Service\V1\Data\OrderAddress'));
        $orderAddressBuilder->setCity('City');
        $orderAddressBuilder->setPostcode('12345');
        $orderAddressBuilder->setLastname('Last Name');
        $orderAddressBuilder->setFirstname('First Name');
        $orderAddressBuilder->setTelephone('+00(000)-123-45-57');
        $orderAddressBuilder->setStreet('Street');
        $orderAddressBuilder->setCountryId(1);
        $orderAddressBuilder->setAddressType('shipping');
        $orderBuilder->setShippingAddress($orderAddressBuilder->create()->__toArray());
        return $orderBuilder->create()->__toArray();

    }

    protected function getDataStructure($className)
    {
        $refClass = new \ReflectionClass ($className);
        $constants = $refClass->getConstants();
        $data = array_fill_keys($constants, null);
        unset($data['custom_attributes']);
        return $data;
    }

    public function testOrderCreate()
    {
        $order = $this->prepareOrder();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'create'
            ]
        ];
        $this->assertTrue($this->_webApiCall($serviceInfo, ['orderDataObject' => $order]));
        /** @var \Magento\Sales\Model\Order $model */
        $model = $this->objectManager->get('Magento\Sales\Model\Order');
        $model->load($order['customer_email'], 'customer_email');
        $this->assertTrue((bool)$model->getId());
        $this->assertEquals($order['base_grand_total'], $model->getBaseGrandTotal());
        $this->assertEquals($order['grand_total'], $model->getGrandTotal());
    }
}
