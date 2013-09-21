<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Shipping_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Shipping_Helper_Data
     */
    protected $_helper = null;

    protected function setUp()
    {
        $this->_helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Shipping_Helper_Data');
    }

    /**
     * @param string $modelName
     * @param string $getIdMethod
     * @param int $entityId
     * @param string $code
     * @param string $expected
     * @dataProvider getTrackingPopupUrlBySalesModelDataProvider
     */
    public function testGetTrackingPopupUrlBySalesModel($modelName, $getIdMethod, $entityId, $code, $expected)
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $constructArgs = array();
        if ('Magento_Sales_Model_Order_Shipment' == $modelName) {
            $orderFactory = $this->_getMockOrderFactory($code);
            $constructArgs['orderFactory'] = $orderFactory;
        } elseif ('Magento_Sales_Model_Order_Shipment_Track' == $modelName) {
            $shipmentFactory = $this->_getMockShipmentFactory($code);
            $constructArgs['shipmentFactory'] = $shipmentFactory;
        }

        $model = $objectManager->create($modelName, $constructArgs);
        $model->$getIdMethod($entityId);

        if ('Magento_Sales_Model_Order' == $modelName) {
            $model->setProtectCode($code);
        }

        $actual = $this->_helper->getTrackingPopupUrlBySalesModel($model);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param $code
     * @return Magento_Sales_Model_OrderFactory
     */
    protected function _getMockOrderFactory($code)
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $order = $objectManager->create('Magento_Sales_Model_Order');
        $order->setProtectCode($code);
        $orderFactory = $this->getMock('Magento_Sales_Model_OrderFactory', array('create'), array(), '', false);
        $orderFactory->expects($this->atLeastOnce())->method('create')->will($this->returnValue($order));
        return $orderFactory;
    }

    /**
     * @param $code
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMockShipmentFactory($code)
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $orderFactory = $this->_getMockOrderFactory($code);
        $shipmentConstructArgs = array('orderFactory' => $orderFactory);

        $shipment = $objectManager->create('Magento_Sales_Model_Order_Shipment', $shipmentConstructArgs);
        $shipmentFactory = $this->getMock(
            'Magento_Sales_Model_Order_ShipmentFactory', array('create'), array(), '', false
        );
        $shipmentFactory->expects($this->atLeastOnce())->method('create')->will($this->returnValue($shipment));
        return $shipmentFactory;
    }

    /**
     * @return array
     */
    public function getTrackingPopupUrlBySalesModelDataProvider()
    {
        return array(
            array(
                'Magento_Sales_Model_Order',
                'setId',
                42,
                'abc',
                'http://localhost/index.php/shipping/tracking/popup/hash/b3JkZXJfaWQ6NDI6YWJj/'
            ),
            array(
                'Magento_Sales_Model_Order_Shipment',
                'setId',
                42,
                'abc',
                'http://localhost/index.php/shipping/tracking/popup/hash/c2hpcF9pZDo0MjphYmM,/'
            ),
            array(
                'Magento_Sales_Model_Order_Shipment_Track',
                'setEntityId',
                42,
                'abc',
                'http://localhost/index.php/shipping/tracking/popup/hash/dHJhY2tfaWQ6NDI6YWJj/'
            )
        );
    }
}
