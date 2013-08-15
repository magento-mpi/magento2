<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class Magento_Core_Controller_Varien_Action_Factory
 */
class Magento_Core_Controller_Varien_Action_FactoryTest extends PHPUnit_Framework_TestCase
{
    /*
    * Test controller class name
    */
    const CONTROLLER_NAME  = 'TestController';

    /**
     * ObjectManager mock for tests
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * Test class instance
     *
     * @var Magento_Core_Controller_Varien_Action_Factory
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager');
    }

    public function testConstruct()
    {
        $this->_model = new Magento_Core_Controller_Varien_Action_Factory($this->_objectManager);
        $this->assertAttributeInstanceOf('Magento_ObjectManager', '_objectManager', $this->_model);
    }

    public function testCreateController()
    {
        $this->_objectManager->expects($this->at(1))
            ->method('create')
            ->with(self::CONTROLLER_NAME)
            ->will($this->returnValue('TestControllerInstance'));

        $this->_model = new Magento_Core_Controller_Varien_Action_Factory($this->_objectManager);
        $this->assertEquals('TestControllerInstance', $this->_model->createController(self::CONTROLLER_NAME));
    }
}
