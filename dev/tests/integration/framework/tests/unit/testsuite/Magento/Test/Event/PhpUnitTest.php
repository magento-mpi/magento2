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
 * Test class for Magento_TestFramework_Event_PhpUnit.
 */
class Magento_Test_Event_PhpUnitTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Event_PhpUnit
     */
    protected $_object;

    /**
     * @var Magento_TestFramework_EventManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManager;

    protected function setUp()
    {
        $this->_eventManager = $this->getMock('Magento_TestFramework_EventManager', array('fireEvent'), array(array()));
        $this->_object = new Magento_TestFramework_Event_PhpUnit($this->_eventManager);
    }

    protected function tearDown()
    {
        Magento_TestFramework_Event_PhpUnit::setDefaultEventManager(null);
    }

    public function testConstructorDefaultEventManager()
    {
        Magento_TestFramework_Event_PhpUnit::setDefaultEventManager($this->_eventManager);
        $this->_object = new Magento_TestFramework_Event_PhpUnit();
        $this->testStartTestSuiteFireEvent();
    }

    /**
     * @expectedException Magento_Exception
     */
    public function testConstructorException()
    {
        new Magento_TestFramework_Event_Magento();
    }

    /**
     * @param string $method
     * @dataProvider doNotFireEventDataProvider
     */
    public function testDoNotFireEvent($method)
    {
        $this->_eventManager
            ->expects($this->never())
            ->method('fireEvent')
        ;
        $this->_object->$method($this, new PHPUnit_Framework_AssertionFailedError, 0);
    }

    public function doNotFireEventDataProvider()
    {
        return array(
            'method "addError"'          => array('addError'),
            'method "addFailure"'        => array('addFailure'),
            'method "addIncompleteTest"' => array('addIncompleteTest'),
            'method "addSkippedTest"'    => array('addSkippedTest'),
        );
    }

    public function testStartTestSuiteFireEvent()
    {
        $this->_eventManager
            ->expects($this->once())
            ->method('fireEvent')
            ->with('startTestSuite')
        ;
        $this->_object->startTestSuite(new PHPUnit_Framework_TestSuite);
    }

    public function testStartTestSuiteDoNotFireEvent()
    {
        $this->_eventManager
            ->expects($this->never())
            ->method('fireEvent')
        ;
        $this->_object->startTestSuite(new PHPUnit_Framework_TestSuite_DataProvider);
    }

    public function testEndTestSuiteFireEvent()
    {
        $this->_eventManager
            ->expects($this->once())
            ->method('fireEvent')
            ->with('endTestSuite')
        ;
        $this->_object->endTestSuite(new PHPUnit_Framework_TestSuite);
    }

    public function testEndTestSuiteDoNotFireEvent()
    {
        $this->_eventManager
            ->expects($this->never())
            ->method('fireEvent')
        ;
        $this->_object->endTestSuite(new PHPUnit_Framework_TestSuite_DataProvider);
    }

    public function testStartTestFireEvent()
    {
        $this->_eventManager
            ->expects($this->once())
            ->method('fireEvent')
            ->with('startTest')
        ;
        $this->_object->startTest($this);
    }

    public function testStartTestDoNotFireEvent()
    {
        $this->_eventManager
            ->expects($this->never())
            ->method('fireEvent')
        ;
        $this->_object->startTest(new PHPUnit_Framework_Warning);
        $this->_object->startTest($this->getMock('PHPUnit_Framework_Test'));
    }

    public function testEndTestFireEvent()
    {
        $this->_eventManager
            ->expects($this->once())
            ->method('fireEvent')
            ->with('endTest')
        ;
        $this->_object->endTest($this, 0);
    }

    public function testEndTestDoNotFireEvent()
    {
        $this->_eventManager
            ->expects($this->never())
            ->method('fireEvent')
        ;
        $this->_object->endTest(new PHPUnit_Framework_Warning, 0);
        $this->_object->endTest($this->getMock('PHPUnit_Framework_Test'), 0);
    }
}
