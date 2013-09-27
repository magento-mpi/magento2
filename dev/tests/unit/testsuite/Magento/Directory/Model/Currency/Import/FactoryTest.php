<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Directory_Model_Currency_Import_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Directory_Model_Currency_Import_Factory
     */
    protected $_model;

    /**
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var Magento_Directory_Model_Currency_Import_Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_importConfig;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager');
        $this->_importConfig = $this->getMock(
            'Magento_Directory_Model_Currency_Import_Config', array(), array(), '', false
        );
        $this->_model = new Magento_Directory_Model_Currency_Import_Factory(
            $this->_objectManager, $this->_importConfig
        );
    }

    public function testCreate()
    {
        $expectedResult = $this->getMock('Magento_Directory_Model_Currency_Import_Interface');
        $this->_importConfig
            ->expects($this->once())
            ->method('getServiceClass')
            ->with('test')
            ->will($this->returnValue('Test_Class'))
        ;
        $this->_objectManager
            ->expects($this->once())
            ->method('create')
            ->with('Test_Class', array('argument' => 'value'))
            ->will($this->returnValue($expectedResult))
        ;
        $actualResult = $this->_model->create('test', array('argument' => 'value'));
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Currency import service 'test' is not defined
     */
    public function testCreateUndefinedServiceClass()
    {
        $this->_importConfig
            ->expects($this->once())
            ->method('getServiceClass')
            ->with('test')
            ->will($this->returnValue(null))
        ;
        $this->_objectManager->expects($this->never())->method('create');
        $this->_model->create('test');
    }

    /**
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage Class 'stdClass' has to implement Magento_Directory_Model_Currency_Import_Interface
     */
    public function testCreateIrrelevantServiceClass()
    {
        $this->_importConfig
            ->expects($this->once())
            ->method('getServiceClass')
            ->with('test')
            ->will($this->returnValue('stdClass'))
        ;
        $this->_objectManager
            ->expects($this->once())
            ->method('create')
            ->with('stdClass')
            ->will($this->returnValue(new stdClass()))
        ;
        $this->_model->create('test');
    }
}
