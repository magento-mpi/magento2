<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Test_Event_Magento.
 */
class Magento_Test_Event_MagentoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Event_Magento
     */
    protected $_object;

    /**
     * @var Magento_Test_EventManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManager;

    protected function setUp()
    {
        $this->_eventManager = $this->getMock('Magento_Test_EventManager', array('fireEvent'), array(array()));
        $this->_object = new Magento_Test_Event_Magento($this->_eventManager);
    }

    protected function tearDown()
    {
        Magento_Test_Event_Magento::setDefaultEventManager(null);
    }

    public function testConstructorDefaultEventManager()
    {
        Magento_Test_Event_Magento::setDefaultEventManager($this->_eventManager);
        $this->_object = new Magento_Test_Event_Magento();
        $this->testInitFrontControllerBefore();
    }

    /**
     * @dataProvider constructorExceptionDataProvider
     * @expectedException Magento_Exception
     * @param mixed $eventManager
     */
    public function testConstructorException($eventManager)
    {
        new Magento_Test_Event_Magento($eventManager);
    }

    public function constructorExceptionDataProvider()
    {
        return array(
            'no event manager'     => array(null),
            'not an event manager' => array(new stdClass()),
        );
    }

    public function testInitFrontControllerBefore()
    {
        $this->_eventManager
            ->expects($this->once())
            ->method('fireEvent')
            ->with('initFrontControllerBefore')
        ;
        $this->_object->initFrontControllerBefore();
    }
}
