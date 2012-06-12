<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Testlink_ListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Testlink_Listener
     */
    protected $_listenerMock;
    protected $_test;

    protected function setUp()
    {
        $this->_listenerMock = $this->getMock('Mage_Testlink_Listener',
                array("instantiateObservers", "notifyObservers"),
                array("Test Project", "url", "key", "testplan", "build"), "", true);
        $this->_listenerMock->expects($this->any())
                           ->method('instantiateObservers')
                ->will($this->returnValue('null'));
    }

    protected function tearDown()
    {

    }

    /**
     * @covers Mage_Testlink_Listener::getProject
     */
    public function testGetProjectName()
    {
        $this->assertEquals($this->_listenerMock->getProject(), "Test Project");
    }

    /**
     * @covers Mage_Testlink_Listener::getTestPlan
     */
    public function testGetTestPlan()
    {
        $this->assertEquals($this->_listenerMock->getTestPlan(), "testplan");
    }

    /**
     * @covers Mage_Testlink_Listener::getBuild
     */
    public function testGetBuild()
    {
        $this->assertEquals($this->_listenerMock->getBuild(), "build");
    }

    /**
     * @covers Mage_Testlink_Listener::getCurrentTest
     */
    public function testGetCurrentTest()
    {
        $test = new Mage_Selenium_TestCase();
        $this->_listenerMock->addError($test, new Exception(), time());
        $this->assertEquals($this->_listenerMock->getCurrentTest(), $test);
    }

    /**
     * @covers Mage_Testlink_Listener::addError
     */
    public function testAddError()
    {
        $test = new Mage_Selenium_TestCase();
        $this->_listenerMock->expects($this->once())->method('notifyObservers');
        $this->_listenerMock->addError($test, new Exception("TestException"), time());
    }

    /**
     * @covers Mage_Testlink_Listener::addFailure
     */
    public function testAddFailure()
    {
        $test = new Mage_Selenium_TestCase();
        $this->_listenerMock->expects($this->once())->method('notifyObservers');
        $this->_listenerMock->addFailure($test, new PHPUnit_Framework_AssertionFailedError("TestException"), time());
    }

    /**
     * @covers Mage_Testlink_Listener::addIncompleteTest
     */
    public function testAddIncompleteTest()
    {
        $test = new Mage_Selenium_TestCase();
        $this->_listenerMock->expects($this->once())->method('notifyObservers');
        $this->_listenerMock->addIncompleteTest($test, new Exception("TestException"), time());
    }

    /**
     * @covers Mage_Testlink_Listener::addSkippedTest
     */
    public function testAddSkippedTest()
    {
        $test = new Mage_Selenium_TestCase();
        $this->_listenerMock->expects($this->once())->method('notifyObservers');
        $this->_listenerMock->addSkippedTest($test, new Exception("TestException"), time());
    }

    /**
     * @covers Mage_Testlink_Listener::startTestSuite
     */
    public function testStartTestSuite()
    {
        $this->_listenerMock->expects($this->once())->method('notifyObservers');
        $this->_listenerMock->startTestSuite(new PHPUnit_Framework_TestSuite());
    }

    /**
     * @covers Mage_Testlink_Listener::endTestSuite
     */
    public function testEndTestSuite()
    {
        $this->_listenerMock->expects($this->once())->method('notifyObservers');
        $this->_listenerMock->endTestSuite(new PHPUnit_Framework_TestSuite());
    }

    /**
     * @covers Mage_Testlink_Listener::startTest
     */
    public function testStartTest()
    {
        $test = new Mage_Selenium_TestCase();
        $this->_listenerMock->expects($this->once())->method('notifyObservers');
        $this->_listenerMock->startTest($test);
    }

    /**
     * @covers Mage_Testlink_Listener::endTest
     */
    public function testEndTest()
    {
        $test = new Mage_Selenium_TestCase();
        $this->_listenerMock->expects($this->once())->method('notifyObservers');
        $this->_listenerMock->endTest($test, time());
    }
}