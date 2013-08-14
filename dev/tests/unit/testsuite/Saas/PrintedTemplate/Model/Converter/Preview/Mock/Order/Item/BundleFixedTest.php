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
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Item_BundleFixedTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $helper = $this->getMockBuilder('Saas_PrintedTemplate_Helper_Data')
            ->setMethods(array('__'))
            ->disableOriginalConstructor()
            ->getMock();
        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        $mockItem = $this->getMockBuilder('Magento_Sales_Model_Order_Item')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', 'unsetData'))
            ->getMock();

        $modelFixed =  $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Item_BundleFixed')
            ->setMethods(array('getModel', '_getHelper', '_getResource'))
            ->disableOriginalConstructor()
            ->getMock();
        $modelFixed->expects($this->any())
            ->method('_getHelper')
            ->will($this->returnValue($helper));
        $modelFixed->expects($this->any())
            ->method('getModel')
            ->will($this->returnValue($mockItem));
        $resource = $this->getMock('Magento_Sales_Model_Resource_Order_Item', array(), array(), '', false);
        $modelFixed->expects($this->any())->method('_getResource')->will($this->returnValue($resource));

        $this->assertEmpty($modelFixed->getData());
        $this->assertEmpty($mockItem->getData());

        $reflection = new ReflectionClass(get_class($modelFixed));
        $method = $reflection->getMethod('_construct');
        $method->setAccessible(true);
        $method->invokeArgs($modelFixed, array());

        $this->assertNotEmpty($modelFixed->getData());
        $this->assertNotEmpty($mockItem->getData());
        $this->assertSame($modelFixed, $mockItem->getParentItem());
    }
}
