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
 * Test class for Magento_TestFramework_Event_Magento.
 */
class Magento_Test_Event_MagentoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Event_Magento
     */
    protected $_object;

    /**
     * @var Magento_TestFramework_EventManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManager;

    protected function setUp()
    {
        $this->_eventManager = $this->getMock('Magento_TestFramework_EventManager', array('fireEvent'), array(array()));
        $this->_object = new Magento_TestFramework_Event_Magento($this->_eventManager);
    }

    protected function tearDown()
    {
        Magento_TestFramework_Event_Magento::setDefaultEventManager(null);
    }

    public function testConstructorDefaultEventManager()
    {
        Magento_TestFramework_Event_Magento::setDefaultEventManager($this->_eventManager);
        $this->_object = new Magento_TestFramework_Event_Magento();
        $this->testInitStoreAfter();
    }

    /**
     * @dataProvider constructorExceptionDataProvider
     * @expectedException Magento_Exception
     * @param mixed $eventManager
     */
    public function testConstructorException($eventManager)
    {
        new Magento_TestFramework_Event_Magento($eventManager);
    }

    public function constructorExceptionDataProvider()
    {
        return array(
            'no event manager'     => array(null),
            'not an event manager' => array(new stdClass()),
        );
    }

    public function testInitStoreAfter()
    {
        $this->_eventManager
            ->expects($this->once())
            ->method('fireEvent')
            ->with('initStoreAfter')
        ;
        $this->_object->initStoreAfter();
    }
}
