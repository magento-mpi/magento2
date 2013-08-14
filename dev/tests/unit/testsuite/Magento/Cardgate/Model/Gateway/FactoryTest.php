<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cardgate
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Cardgate_Model_Gateway_Factory
 */
class Magento_Cardgate_Model_Gateway_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Cardgate_Model_Gateway_Factory
     */
    protected $_baseModel;

    /**
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * Set up model
     */
    public function _createModel()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $this->_baseModel = new Magento_Cardgate_Model_Gateway_Factory($this->_objectManager);
    }

    /**
     * @test
     */
    public function testCreate()
    {
        $this->_createModel();

        $testModel = $this->getMock('Magento_Cardgate_Model_Gateway_Dummy', array(), array(), '', false);

        $this->_objectManager->expects($this->once())->method('create')->will($this->returnValue($testModel));

        $this->assertEquals($testModel, $this->_baseModel->create('dummy'));
    }

    /**
     * @test
     */
    public function testCreateNotInstance()
    {
        $this->_createModel();

        $testModel = $this->getMock('Magento_Cardgate_Model_Gateway_Factory', array(), array(), '', false);

        $this->_objectManager->expects($this->once())->method('create')->will($this->returnValue($testModel));

        $this->setExpectedException('InvalidArgumentException',
            'Invalid Model Name: Magento_Cardgate_Model_Gateway_Factory '
            . 'is not instance of Magento_Cardgate_Model_Gateway_Abstract.');
        $this->_baseModel->create('factory');
    }
}
