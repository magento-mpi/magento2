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
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Item_SimpleTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $helper = $this->getMockBuilder('Saas_PrintedTemplate_Helper_Data')
            ->setMethods(array('__'))
            ->getMock();
        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        $model = $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Item_Simple')
            ->setMethods(array('_getHelper'))
            ->disableOriginalConstructor()
            ->getMock();
        $model->expects($this->any())
            ->method('_getHelper')
            ->will($this->returnValue($helper));

        $this->assertEmpty($model->getData());

        $reflection = new ReflectionClass(get_class($model));
        $method = $reflection->getMethod('_construct');
        $method->setAccessible(true);
        $method->invokeArgs($model, array());

        $this->assertNotEmpty($model->getData());
    }
}
