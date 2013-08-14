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
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_OrderTest extends PHPUnit_Framework_TestCase
{
    public function testInitOrder()
    {
        $expectedData = array(
            'data' => null,
            Saas_PrintedTemplate_Model_Variable_Abstract_Entity::TAXES_GROUPED_BY_PERCENT_CACHE_KEY => 'taxes'
        );

        $billingAddrMock = $this
            ->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Address_Billing')
            ->disableOriginalConstructor()
            ->getMock();
        $shippingAddrMock = $this
            ->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Address_Shipping')
            ->disableOriginalConstructor()
            ->getMock();
        $paymentMock = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Payment')
            ->disableOriginalConstructor()
            ->getMock();

        $valueMap = array(
            array('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Address_Billing', $billingAddrMock),
            array('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Address_Shipping', $shippingAddrMock),
            array('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Payment', $paymentMock)
        );
        $mockItem = $this->getMockBuilder('Magento_Sales_Model_Order_Item')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', 'unsetData'))
            ->getMock();

        $helper = $this->getMockBuilder('Saas_PrintedTemplate_Helper_Data')
            ->setMethods(array('__'))
            ->disableOriginalConstructor()
            ->getMock();
        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        $modelOrder = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order')
            ->setMethods(array('_getHelper','_getMockData', '_getMockTaxes', 'getModel', '_getStoreConfig',
                '_createItemMock','addItem', '_getMockItems', 'addAddress', 'setPayment'))
            ->disableOriginalConstructor()
            ->getMock();
        $modelOrder->expects($this->exactly(2))
            ->method('addAddress');
        $modelOrder->expects($this->once())
            ->method('setPayment')
            ->with($paymentMock);
        $modelOrder->expects($this->once())
            ->method('_getMockTaxes')
            ->will($this->returnValue('taxes'));
        $modelOrder->expects($this->once())
            ->method('_getMockData')
            ->will($this->returnValue('data'));
        $modelOrder->expects($this->any())
            ->method('getModel')
            ->will($this->returnValueMap($valueMap));
        $modelOrder->expects($this->exactly(1))
            ->method('_getMockItems')
            ->will($this->returnValue(array($mockItem)));

        $reflection = new ReflectionClass(get_class($modelOrder));
        $method = $reflection->getMethod('_initOrder');
        $method->setAccessible(true);
        $modelOrder = $method->invokeArgs($modelOrder, array());

        $this->assertEquals($expectedData, $modelOrder->getData());
    }

    public function testGetMockTaxes()
    {
        $model = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Tax_ItemCollection')
            ->disableOriginalConstructor()
            ->getMock();
        $itemsCollection = new ReflectionClass(get_class($model));
        $method = $itemsCollection->getMethod('_getMockData');
        $method->setAccessible(true);
        $itemsTaxes = $method->invokeArgs($model, array());

        $model = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Tax_ShippingCollection')
            ->disableOriginalConstructor()
            ->getMock();
        $shipCollection = new ReflectionClass(get_class($model));
        $method = $shipCollection->getMethod('_getMockData');
        $method->setAccessible(true);
        $shippingTaxes = $method->invokeArgs($model, array());

        foreach ($itemsTaxes as &$item) {
            $item = new Magento_Object($item);
        }
        foreach ($shippingTaxes as &$item) {
            $item = new Magento_Object($item);
        }
        $expectedData = array('items_taxes' => $itemsTaxes, 'shipping_taxes' => $shippingTaxes);

        $valueMap = array(
            array('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Tax_ItemCollection', $itemsTaxes),
            array('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Tax_ShippingCollection', $shippingTaxes),
        );

        $modelOrder = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order')
            ->setMethods(array('getModel'))
            ->disableOriginalConstructor()
            ->getMock();
        $modelOrder->expects($this->any())
            ->method('getModel')
            ->will($this->returnValueMap($valueMap));

        $reflection = new ReflectionClass(get_class($modelOrder));
        $method = $reflection->getMethod('_getMockTaxes');
        $method->setAccessible(true);
        $result = $method->invokeArgs($modelOrder, array());

        $this->assertEquals($expectedData, $result);

        foreach ($result['items_taxes'] as $itemTax) {
            $this->assertSame($modelOrder, $itemTax->getOrder());
        }
        foreach ($result['shipping_taxes'] as $shippingTax) {
            $this->assertSame($modelOrder, $shippingTax->getOrder());
        }
    }

    public function testGetMockItems()
    {
        $valueMap = array(
            array('simple', $expectedData[] = $this->prepareItemMock('simple')),
            array('configurable', $expectedData[] = $this->prepareItemMock('configurable')),
            array('bundleDynamic', $expectedData[] = $this->prepareItemMock('bundleDynamic')),
            array('bundleFixed', $expectedData[] = $this->prepareItemMock('bundleFixed')),
        );

        $expectedData = array_merge($expectedData, array('configurable', 'bundleDynamic', 'bundleFixed'));

        $modelOrder = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order')
            ->setMethods(array('_createItemMock'))
            ->disableOriginalConstructor()
            ->getMock();
        $modelOrder->expects($this->any())
            ->method('_createItemMock')
            ->will($this->returnValueMap($valueMap));

        $reflection = new ReflectionClass(get_class($modelOrder));
        $method = $reflection->getMethod('_getMockItems');
        $method->setAccessible(true);
        $result = $method->invokeArgs($modelOrder, array());

        $this->assertEquals($expectedData, $result);
    }

    protected function prepareItemMock($type)
    {
        $mock = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Item_' . ucfirst($type))
            ->setMethods(array('getChildrenItems'))
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->any())
            ->method('getChildrenItems')
            ->will($this->returnValue(array($type)));

        return $mock;
    }
}
