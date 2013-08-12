<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_ShipmentTest extends PHPUnit_Framework_TestCase
{
    public function testSetOrder()
    {
        $order = $this->getMockBuilder('Magento_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->setMethods(array('addItem'))
            ->getMock();
        $order->setId(1);
        $order->setStoreId(2);

        $item = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Shipment_Item_Configurable')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', 'unsetData'))
            ->getMock();

        $modelShipment = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Shipment')
            ->setMethods(array('_getHelper','_getMockData', 'getModel', '_createItemMock','addItem', '_getMockItems'))
            ->disableOriginalConstructor()
            ->getMock();
        $modelShipment->expects($this->once())
            ->method('_getMockData')
            ->will($this->returnValue('data'));
        $modelShipment->expects($this->exactly(1))
            ->method('_getMockItems')
            ->will($this->returnValue(array($item)));

        $result = $modelShipment->setOrder($order);

        $this->assertEquals($order, $result->getOrder());
    }

    public function testGetMockItems()
    {
        $valueMap = array(
            array('configurable', $expectedData[] = $this->prepareItemMock('configurable')),
            array('bundleDynamic', $expectedData[] = $this->prepareItemMock('bundleDynamic')),
        );

        $expectedData = array_merge($expectedData, array('bundleDynamic'));

        $modelShipment = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Shipment')
            ->setMethods(array('_createItemMock'))
            ->disableOriginalConstructor()
            ->getMock();
        $modelShipment->expects($this->any())
            ->method('_createItemMock')
            ->will($this->returnValueMap($valueMap));

        $reflection = new ReflectionClass(get_class($modelShipment));
        $method = $reflection->getMethod('_getMockItems');
        $method->setAccessible(true);
        $result = $method->invokeArgs($modelShipment, array());

        $this->assertEquals($expectedData, $result);
    }

    protected function prepareItemMock($type)
    {
        $mock = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Shipment_Item_'.ucfirst($type))
            ->setMethods(array('getChildrenItems'))
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->any())
            ->method('getChildrenItems')
            ->will($this->returnValue(array($type)));

        return $mock;
    }
}
