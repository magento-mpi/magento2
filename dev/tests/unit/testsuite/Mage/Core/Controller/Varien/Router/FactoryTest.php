<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class Mage_Core_Controller_Varien_Router_Factory
 */
class Mage_Core_Controller_Varien_Router_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**#@+
    * Test arguments
    */
    const CLASS_NAME  = 'TestClass';
    const AREA  = 'TestArea';
    const BASE_CONTROLLER  = 'TestBaseController';
    /**#@-*/

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Controller_Varien_Router_Factory
     */
    protected $_routerFactory;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager_Zend', array('get'), array(), '', false);
        $this->_routerFactory = new Mage_Core_Controller_Varien_Router_Factory($this->_objectManager);
    }

    public function testConstruct()
    {
        $this->assertAttributeInstanceOf('Magento_ObjectManager', '_objectManager', $this->_routerFactory);
    }

    public function testCreateRouterNoArguments()
    {
        $this->_objectManager->expects($this->once())
            ->method('get')
            ->with($this->equalTo(self::CLASS_NAME))
            ->will($this->returnValue('TestRouterInstance'));

        $this->assertEquals('TestRouterInstance', $this->_routerFactory->createRouter(self::CLASS_NAME));
    }

    public function testCreateRouterWithArguments()
    {
        $arguments = array(
            'areaCode'       => self::AREA,
            'baseController' => self::BASE_CONTROLLER,
        );

        $routerInfo = array(
            'area'            => self::AREA,
            'base_controller' => self::BASE_CONTROLLER,
        );

        $this->_objectManager->expects($this->once())
            ->method('get')
            ->with($this->equalTo(self::CLASS_NAME), $arguments)
            ->will($this->returnValue('TestRouterInstance'));

        $this->assertEquals('TestRouterInstance', $this->_routerFactory->createRouter(self::CLASS_NAME, $routerInfo));
    }
}
