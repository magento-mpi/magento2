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
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_InvoiceTest extends PHPUnit_Framework_TestCase
{
    public function testSetOrder()
    {
        $order = $this->getMockBuilder('Magento_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->setMethods(array('addItem'))
            ->getMock();
        $order->setId(1);
        $order->setStoreId(2);

        $mockItem = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Invoice_Item_Configurable')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', 'unsetData'))
            ->getMock();

        $modelInvoice = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Invoice')
            ->setMethods(array('_getHelper','_getMockData', '_getMockTaxes', 'getModel', '_getStoreConfig',
                '_createItemMock','addItem', '_getMockItems', 'addAddress', 'setPayment'))
            ->disableOriginalConstructor()
            ->getMock();
        $modelInvoice->expects($this->once())
            ->method('_getMockData')
            ->will($this->returnValue('data'));
        $modelInvoice->expects($this->exactly(1))
            ->method('_getMockItems')
            ->will($this->returnValue(array($mockItem)));

        $result = $modelInvoice->setOrder($order);

        $this->assertEquals($order, $result->getOrder());
    }

    public function testGetMockItems()
    {
        $valueMap = array(
            array('configurable', $expectedData[] = $this->prepareItemMock('configurable')),
            array('bundleDynamic', $expectedData[] = $this->prepareItemMock('bundleDynamic')),
            array('bundleFixed', $expectedData[] = $this->prepareItemMock('bundleFixed')),
        );

        $expectedData = array_merge($expectedData, array('bundleDynamic', 'bundleFixed'));

        $modelInvoice = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Invoice')
            ->setMethods(array('_createItemMock'))
            ->disableOriginalConstructor()
            ->getMock();
        $modelInvoice->expects($this->any())
            ->method('_createItemMock')
            ->will($this->returnValueMap($valueMap));

        $reflection = new ReflectionClass(get_class($modelInvoice));
        $method = $reflection->getMethod('_getMockItems');
        $method->setAccessible(true);
        $result = $method->invokeArgs($modelInvoice, array());

        $this->assertEquals($expectedData, $result);
    }

    protected function prepareItemMock($type)
    {
        $mock = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Invoice_Item_'.ucfirst($type))
            ->setMethods(array('getChildrenItems'))
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->any())
            ->method('getChildrenItems')
            ->will($this->returnValue(array($type)));

        return $mock;
    }
}
