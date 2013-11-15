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

namespace Magento\App;

/**
 * Test class \Magento\App\ActionFactory
 */
class ActionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /*
    * Test controller class name
    */
    const CONTROLLER_NAME  = 'TestController';

    /**
     * ObjectManager mock for tests
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * Test class instance
     *
     * @var \Magento\App\ActionFactory
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
    }

    public function testConstruct()
    {
        $this->_model = new \Magento\App\ActionFactory($this->_objectManager);
        $this->assertAttributeInstanceOf('Magento\ObjectManager', '_objectManager', $this->_model);
    }

    public function testCreateController()
    {
        $this->_objectManager->expects($this->at(1))
            ->method('create')
            ->with(self::CONTROLLER_NAME)
            ->will($this->returnValue('TestControllerInstance'));

        $this->_model = new \Magento\App\ActionFactory($this->_objectManager);
        $this->assertEquals('TestControllerInstance', $this->_model->createController(self::CONTROLLER_NAME));
    }
}
