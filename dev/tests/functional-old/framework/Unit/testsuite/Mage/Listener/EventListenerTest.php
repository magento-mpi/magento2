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
class Mage_Listener_EventListenerTest extends Unit_PHPUnit_TestCase
{
    protected $observersMocks = array();

    protected function tearDown()
    {
        foreach ($this->observersMocks as $mockObj) {
            Mage_Listener_EventListener::detach($mockObj);
        }
    }

    /**
     * @covers Mage_Listener_EventListener::attach
     * @covers Mage_Listener_EventListener::getObservers
     */
    public function testAttach()
    {
        $this->observersMocks[] = $this->getMock('Mage_Listener_Observers_EmptyObserver');

        Mage_Listener_EventListener::attach($this->observersMocks[0]);
        $this->assertInternalType('array', Mage_Listener_EventListener::getObservers());
        $this->assertNotEmpty(Mage_Listener_EventListener::getObservers());
        $this->assertInstanceOf('Mage_Listener_Observers_EmptyObserver', $this->observersMocks[0]);
        $this->assertCount(1, Mage_Listener_EventListener::getObservers());

        $this->observersMocks[] = $this->getMock('Mage_Listener_Observers_EmptyObserver');
        Mage_Listener_EventListener::attach($this->observersMocks[1]);
        $this->assertCount(2, Mage_Listener_EventListener::getObservers());
    }

    /**
     * @covers Mage_Listener_EventListener::detach
     * @covers Mage_Listener_EventListener::getObservers
     */
    public function testDetach()
    {
        $this->observersMocks[] = $obj1 = $this->getMock('Mage_Listener_Observers_EmptyObserver');
        $this->observersMocks[] = $obj2 = $this->getMock('Mage_Listener_Observers_EmptyObserver');

        Mage_Listener_EventListener::attach($obj1);
        $this->assertInternalType('array', Mage_Listener_EventListener::getObservers());
        $this->assertNotEmpty(Mage_Listener_EventListener::getObservers());

        $this->observersMocks[] = $this->getMock('Mage_Listener_Observers_EmptyObserver');
        Mage_Listener_EventListener::attach($obj2);
        $this->assertCount(2, Mage_Listener_EventListener::getObservers());

        Mage_Listener_EventListener::detach($obj2);
        $observers = Mage_Listener_EventListener::getObservers();
        $this->assertCount(1, $observers);
        $this->assertTrue(array_shift($observers) === $obj1);
    }

    /**
     * @covers Mage_Listener_EventListener::autoAttach
     *
     * @param string $path Input parameter for autoAttach()
     * @param $attachTimes Number of times attach() should be invoked
     *
     * @dataProvider testAutoAttachDataProvider
     */
    public function testAutoAttach($path, $attachTimes)
    {
        $obj = $this->getMockClass('Mage_Listener_EventListener', array('attach'));
        $obj::staticExpects($this->exactly($attachTimes))->method('attach');
        $obj::autoAttach($path);
    }

    public function testAutoAttachDataProvider()
    {
        return array(
            array(SELENIUM_TESTS_BASEDIR . implode('/',
                array('', 'framework', 'Mage', 'Listener', 'Observers', '*.php')), 2),
            array('foo', 0),
        );
    }

    /**
     * @covers Mage_Listener_EventListener::addError
     * @covers Mage_Listener_EventListener::getCurrentTest
     */
    public function testAddError()
    {
        $observerMock = $this->getMock('Mage_Listener_Observers_EmptyObserver', array('testFailed'));
        $testCaseMock = $this->getMockForAbstractClass('PHPUnit_Framework_TestCase');

        Mage_Listener_EventListener::attach($observerMock);
        $this->observersMocks[] = $observerMock;
        $instance = new Mage_Listener_EventListener();
        $observerMock->expects($this->once())
            ->method('testFailed')
            ->with($this->equalTo($instance));
        $instance->addError($testCaseMock, new Exception(), time());
        $this->assertEquals($testCaseMock, $instance->getCurrentTest());
    }

    /**
     * @covers Mage_Listener_EventListener::addFailure
     * @covers Mage_Listener_EventListener::getCurrentTest
     */
    public function testAddFailure()
    {
        $observerMock = $this->getMock('Mage_Listener_Observers_EmptyObserver', array('testFailed'));
        $testCaseMock = $this->getMockForAbstractClass('PHPUnit_Framework_TestCase');

        Mage_Listener_EventListener::attach($observerMock);
        $this->observersMocks[] = $observerMock;
        $instance = new Mage_Listener_EventListener();
        $observerMock->expects($this->once())
            ->method('testFailed')
            ->with($this->equalTo($instance));
        $instance->addFailure($testCaseMock, new PHPUnit_Framework_AssertionFailedError, time());
        $this->assertEquals($testCaseMock, $instance->getCurrentTest());
    }

    /**
     * @covers Mage_Listener_EventListener::addIncompleteTest
     * @covers Mage_Listener_EventListener::getCurrentTest
     */
    public function testAddIncompleteTest()
    {
        $observerMock = $this->getMock('Mage_Listener_Observers_EmptyObserver', array('testSkipped'));
        $testCaseMock = $this->getMockForAbstractClass('PHPUnit_Framework_TestCase');

        Mage_Listener_EventListener::attach($observerMock);
        $this->observersMocks[] = $observerMock;
        $instance = new Mage_Listener_EventListener();
        $observerMock->expects($this->once())
            ->method('testSkipped')
            ->with($this->equalTo($instance));
        $instance->addIncompleteTest($testCaseMock, new Exception(), time());
        $this->assertEquals($testCaseMock, $instance->getCurrentTest());
    }

    /**
     * @covers Mage_Listener_EventListener::addSkippedTest
     * @covers Mage_Listener_EventListener::getCurrentTest
     */
    public function testAddSkippedTest()
    {
        $observerMock = $this->getMock('Mage_Listener_Observers_EmptyObserver', array('testSkipped'));
        $testCaseMock = $this->getMockForAbstractClass('PHPUnit_Framework_TestCase');

        Mage_Listener_EventListener::attach($observerMock);
        $this->observersMocks[] = $observerMock;
        $instance = new Mage_Listener_EventListener();
        $observerMock->expects($this->once())
            ->method('testSkipped')
            ->with($this->equalTo($instance));
        $instance->addSkippedTest($testCaseMock, new Exception(), time());
        $this->assertEquals($testCaseMock, $instance->getCurrentTest());
    }

    /**
     * @covers Mage_Listener_EventListener::startTestSuite
     * @covers Mage_Listener_EventListener::getCurrentSuite
     */
    public function testStartTestSuite()
    {
        $observerMock = $this->getMock('Mage_Listener_Observers_EmptyObserver', array('startTestSuite'));
        $testCaseMock = $this->getMockForAbstractClass('PHPUnit_Framework_TestSuite');

        Mage_Listener_EventListener::attach($observerMock);
        $this->observersMocks[] = $observerMock;
        $instance = new Mage_Listener_EventListener();
        $observerMock->expects($this->once())
            ->method('startTestSuite')
            ->with($this->equalTo($instance));
        $instance->startTestSuite($testCaseMock);
        $this->assertEquals($testCaseMock, $instance->getCurrentSuite());
    }

    /**
     * @covers Mage_Listener_EventListener::endTestSuite
     * @covers Mage_Listener_EventListener::getCurrentTest
     */
    public function testEndTestSuite()
    {
        $observerMock = $this->getMock('Mage_Listener_Observers_EmptyObserver', array('endTestSuite'));
        $testSuiteMock = $this->getMock('PHPUnit_Framework_TestSuite');

        Mage_Listener_EventListener::attach($observerMock);
        $this->observersMocks[] = $observerMock;
        $instance = new Mage_Listener_EventListener();
        $observerMock->expects($this->once())
            ->method('endTestSuite')
            ->with($this->equalTo($instance));
        $instance->startTestSuite($testSuiteMock);
        $instance->endTestSuite($testSuiteMock);
        $this->assertEquals($testSuiteMock, $instance->getCurrentSuite());
    }

    /**
     * @covers Mage_Listener_EventListener::startTest
     * @covers Mage_Listener_EventListener::getCurrentTest
     */
    public function testStartTest()
    {
        $observerMock = $this->getMock('Mage_Listener_Observers_EmptyObserver', array('startTest'));
        $testCaseMock = $this->getMockForAbstractClass('PHPUnit_Framework_TestCase');

        Mage_Listener_EventListener::attach($observerMock);
        $this->_observersMock[] = $observerMock;
        $instance = new Mage_Listener_EventListener();
        $observerMock->expects($this->once())
            ->method('startTest')
            ->with($this->equalTo($instance));
        $instance->startTest($testCaseMock, new Exception(), time());
        $this->assertEquals($testCaseMock, $instance->getCurrentTest());
    }

    /**
     * @covers Mage_Listener_EventListener::endTest
     * @covers Mage_Listener_EventListener::getCurrentTest
     */
    public function testEndTest()
    {
        $observerMock = $this->getMock('Mage_Listener_Observers_EmptyObserver', array('endTest'));
        $testCaseMock = $this->getMockForAbstractClass('PHPUnit_Framework_TestCase');

        Mage_Listener_EventListener::attach($observerMock);
        $this->_observersMock[] = $observerMock;
        $instance = new Mage_Listener_EventListener();
        $observerMock->expects($this->once())
            ->method('endTest')
            ->with($this->equalTo($instance));
        $instance->startTest($testCaseMock, new Exception(), time());
        $instance->endTest($testCaseMock, new Exception(), time());
        $this->assertEquals($testCaseMock, $instance->getCurrentTest());
    }
}
