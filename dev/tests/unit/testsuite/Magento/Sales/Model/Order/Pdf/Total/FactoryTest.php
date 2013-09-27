<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Model_Order_Pdf_Total_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Sales_Model_Order_Pdf_Total_Factory
     */
    protected $_factory;

    public function setUp()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $this->_factory = new Magento_Sales_Model_Order_Pdf_Total_Factory($this->_objectManager);
    }

    /**
     * @param mixed $class
     * @param array $arguments
     * @param string $expectedClassName
     * @dataProvider createDataProvider
     */
    public function testCreate($class, $arguments, $expectedClassName)
    {
        $createdModel = $this->getMock('Magento_Sales_Model_Order_Pdf_Total_Default', array(), array(),
            (string) $class, false);
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with($expectedClassName, $arguments)
            ->will($this->returnValue($createdModel));

        $actual = $this->_factory->create($class, $arguments);
        $this->assertSame($createdModel, $actual);
    }

    /**
     * @return array
     */
    public static function createDataProvider()
    {
        return array(
            'default model' => array(
                null, array('param1', 'param2'),
                'Magento_Sales_Model_Order_Pdf_Total_Default',
            ),
            'custom model' => array(
                'custom_class', array('param1', 'param2'),
                'custom_class',
            ),
        );
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage The PDF total model TEST must be or extend Magento_Sales_Model_Order_Pdf_Total_Default.
     */
    public function testCreateException()
    {
        $this->_factory->create('TEST');
    }
}
