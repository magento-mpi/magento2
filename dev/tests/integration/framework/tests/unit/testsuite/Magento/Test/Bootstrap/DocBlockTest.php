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
 * Test class for Magento_TestFramework_Bootstrap_DocBlock.
 */
class Magento_Test_Bootstrap_DocBlockTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Bootstrap_DocBlock
     */
    protected $_object;

    /**
     * @var Magento_TestFramework_Application|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_application;

    protected function setUp()
    {
        $this->_object = new Magento_TestFramework_Bootstrap_DocBlock(__DIR__);
        $this->_application = $this->getMock('Magento_TestFramework_Application', array(), array(), '', false);
    }

    protected function tearDown()
    {
        $this->_object = null;
        $this->_application = null;
    }

    /**
     * Setup expectation of inability to instantiate an event listener without passing the event manager instance
     *
     * @param string $listenerClass
     * @param string $expectedExceptionMsg
     */
    protected function _expectNoListenerCreation($listenerClass, $expectedExceptionMsg)
    {
        try {
            new $listenerClass();
            $this->fail("Inability to instantiate the event listener '$listenerClass' is expected.");
        } catch (\Magento\Exception $e) {
            $this->assertEquals($expectedExceptionMsg, $e->getMessage());
        }
    }

    public function testRegisterAnnotations()
    {
        $this->_expectNoListenerCreation(
            'Magento_TestFramework_Event_PhpUnit', 'Instance of the event manager is required.');
        $this->_expectNoListenerCreation(
            'Magento_TestFramework_Event_Magento', 'Instance of the "Magento_TestFramework_EventManager" is expected.'
        );
        $this->_object->registerAnnotations($this->_application);
        new Magento_TestFramework_Event_PhpUnit();
        new Magento_TestFramework_Event_Magento();
    }
}
